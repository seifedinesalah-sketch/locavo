<?php
require_once '../config.php';

// Chargement des voitures depuis la BD (pour le formulaire)
$stmt = $pdo->query("SELECT id_v, marque, modele, prix_jour, categorie FROM voitures ORDER BY categorie, marque, modele");
$voitures_par_cat = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $voitures_par_cat[$row['categorie']][] = $row;
}
$labels_cat = ['economique'=>'Économiques','suv'=>'SUV & Crossovers','cabriolet'=>'Cabriolets','sport'=>'Sport / Coupés','luxe'=>'Luxe'];

// Pré-remplissage depuis GET (venant d'un bouton "Réserver" sur une fiche voiture)
$get_marque    = htmlspecialchars($_GET['marque']    ?? '');
$get_modele    = htmlspecialchars($_GET['modele']    ?? '');
$get_categorie = htmlspecialchars($_GET['categorie'] ?? '');

// Traitement du formulaire soumis
$success = false;
$erreurs = [];
$nom = $email = $tel = $dDep = $dRet = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom   = trim($_POST['nom']         ?? '');
    $email = trim($_POST['email']       ?? '');
    $tel   = trim($_POST['telephone']   ?? '');
    $dDep  = trim($_POST['date_depart'] ?? '');
    $dRet  = trim($_POST['date_retour'] ?? '');

    // Validation PHP cote serveur
    if (strlen($nom) < 2)                                  $erreurs[] = "Le nom est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))        $erreurs[] = "L'email n'est pas valide.";
    if (!empty($tel) && !preg_match('/^[0-9]{8}$/', $tel)) $erreurs[] = "Le telephone doit contenir 8 chiffres.";
    if (empty($dDep))                                      $erreurs[] = "La date de depart est obligatoire.";
    if (empty($dRet))                                      $erreurs[] = "La date de retour est obligatoire.";

    if (!empty($dDep) && !empty($dRet)) {
        if (new DateTime($dRet) <= new DateTime($dDep))
            $erreurs[] = "La date de retour doit etre apres le depart.";
    }

    if (empty($erreurs)) {
        try {
            $pdo->beginTransaction();

            // INSERT client avec prepare() positionnel
            $pdo->prepare("INSERT INTO clients (nom_complet, email, tel, mot_de_passe)
                           VALUES (?,?,?,'pass_temp')
                           ON DUPLICATE KEY UPDATE id_c=LAST_INSERT_ID(id_c)")
                ->execute([$nom, $email, $tel]);
            $idClient = $pdo->lastInsertId();

            // INSERT reservation
            $pdo->prepare("INSERT INTO reservations (id_client, id_voiture, date_debut, date_fin)
                           VALUES (?,?,?,?)")
                ->execute([$idClient, 1, $dDep, $dRet]);

            $pdo->commit();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $erreurs[] = "Erreur technique : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locavo - Reservation</title>
    <link rel="stylesheet" href="../css/style.css">
 <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="../js/banner.js" defer></script>
    <script src="../js/reservation.js" defer></script>
    <style>
        .box { max-width:650px; margin:80px auto; padding:40px; background:#fff; border-radius:12px;
               box-shadow:0 8px 25px rgba(0,0,0,0.08); text-align:center;
               border-top:6px solid <?= $success ? '#2ecc71' : '#e74c3c' ?>; }
        .erreurs { background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px;
                   padding:15px; margin:15px 0; color:#721c24; text-align:left; }
        .recap-table { width:100%; border-collapse:collapse; margin-top:20px; text-align:left; }
        .recap-table th { background:#0e6fa5; color:#fff; padding:10px; }
        .recap-table td { padding:10px; border-bottom:1px solid #eee; }
    </style>
</head>
<body>

<div class="banniere-container">
    <div class="banniere-texte" id="banniere-texte">
        <img src="../images/logo.png" alt="Locavo" class="banniere-logo">
        <span id="banniere-msg"></span>
    </div>
</div>

<nav class="navbar">
    <div class="logo-container">
        <img src="../images/logo.png" alt="Locavo Logo" class="logo-img">
        <span class="logo-text">Locavo</span>
    </div>
    <ul class="nav-links">
        <li><a href="../index.html">Accueil</a></li>
        <li><a href="../php/flotte.php">Nos Voitures</a></li>
        <li><a href="../php/recherche.php">Recherche</a></li>
        <li><a href="../php/reservation.php" class="active">Réservation</a></li>
        <li><a href="../php/sport.php">Sport / Coupés</a></li>
        <li><a href="../php/suv.php">SUV &amp; Crossovers</a></li>
        <li><a href="../php/economiques.php">Économiques</a></li>
        <li><a href="../php/modeles.php">Cabriolets</a></li>
        <li><a href="../php/luxe.php">Luxe</a></li>
        <li><a href="../html/contact.html">Contact</a></li>
        <li><a href="../php/about.php">À propos</a></li>
        <li><a href="../html/funpage.html">Fun Page</a></li>
    </ul>
</nav>

<?php if ($success): ?>
<!-- ===== CONFIRMATION ===== -->
<div class="box">
    <div style="font-size:50px;">&#x2705;</div>
    <h2 style="color:#2c3e50;">Reservation confirmee !</h2>
    <p style="color:#888; margin-top:10px;">
        Merci <strong><?= htmlspecialchars($nom) ?></strong>, votre demande a ete enregistree.
    </p>
    <table class="recap-table">
        <thead><tr><th>Champ</th><th>Valeur</th></tr></thead>
        <tbody>
            <tr><td>Nom</td><td><?= htmlspecialchars($nom) ?></td></tr>
            <tr><td>Email</td><td><?= htmlspecialchars($email) ?></td></tr>
            <tr><td>Telephone</td><td><?= htmlspecialchars($tel ?: '-') ?></td></tr>
            <tr><td>Date de depart</td><td><?= date('d/m/Y', strtotime($dDep)) ?></td></tr>
            <tr><td>Date de retour</td><td><?= date('d/m/Y', strtotime($dRet)) ?></td></tr>
            <tr><td>Statut</td><td style="color:#2ecc71;"><strong>En attente de confirmation</strong></td></tr>
        </tbody>
    </table>
    <div style="margin-top:25px;">
        <a href="../index.html" class="btn-orange" style="text-decoration:none;">Retour a l'accueil</a>
        <a href="reservation.php" style="margin-left:15px; color:#888;">Nouvelle reservation</a>
    </div>
</div>

<?php else: ?>
<!-- ===== FORMULAIRE ===== -->
<header class="res-hero" style="background:linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5)),url('../images/hero-home.jpg');background-size:cover;background-position:center;">
    <h2>Reservation Rapide</h2>
    <p>Remplissez le formulaire, nous vous recontactons en moins d'une heure.</p>
</header>

<div class="form-container">
    <h2>Formulaire de Reservation</h2>

    <?php if (!empty($erreurs)): ?>
    <div class="erreurs">
        <strong>Veuillez corriger les erreurs suivantes :</strong>
        <?php foreach ($erreurs as $e): ?>
            <p>. <?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="reservation.php" method="POST" onsubmit="return validerReservation()">

        <fieldset>
            <legend>Informations Personnelles</legend>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input type="text" id="nom" name="nom" placeholder="Ex: Foulen Ben Foulen"
                           required minlength="2" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                    <span id="err_nom" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" placeholder="Ex: mail@email.com"
                           required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <span id="err_email" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="telephone">Telephone *</label>
                    <input type="tel" id="telephone" name="telephone" placeholder="Ex: 12345678"
                           required pattern="[0-9]{8}" maxlength="8"
                           value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                    <span id="err_telephone" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="age">Age *</label>
                    <input type="number" id="age" name="age" min="18" max="99" placeholder="25"
                           required value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
                    <span id="err_age" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" placeholder="Votre adresse complete"
                           value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="permis">Numero de permis</label>
                    <input type="text" id="permis" name="permis" placeholder="Ex: 12345678"
                           value="<?= htmlspecialchars($_POST['permis'] ?? '') ?>">
                    <span id="err_permis" class="error-msg"></span>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Details de la Location</legend>
            <div class="form-grid">
                <div class="form-group">
                    <label for="date_depart">Date de depart *</label>
                    <input type="date" id="date_depart" name="date_depart" required
                           value="<?= htmlspecialchars($_POST['date_depart'] ?? '') ?>">
                    <span id="err_date_depart" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="date_retour">Date de retour *</label>
                    <input type="date" id="date_retour" name="date_retour" required
                           value="<?= htmlspecialchars($_POST['date_retour'] ?? '') ?>">
                    <span id="err_date_retour" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label for="heure_depart">Heure de depart</label>
                    <input type="time" id="heure_depart" name="heure_depart">
                </div>
                <div class="form-group">
                    <label for="heure_retour">Heure de retour</label>
                    <input type="time" id="heure_retour" name="heure_retour">
                </div>
            </div>

            <div class="form-group">
                <label for="categorie">Categorie de vehicule *</label>
                <select id="categorie" name="categorie" required onchange="filtrerModeles()">
                    <option value="">-- Selectionnez une categorie --</option>
                    <?php foreach (array_keys($labels_cat) as $cat):
                        $sel = ($get_categorie === $cat) ? 'selected' : ''; ?>
                    <option value="<?= $cat ?>" <?= $sel ?>><?= $labels_cat[$cat] ?></option>
                    <?php endforeach; ?>
                </select>
                <span id="err_categorie" class="error-msg"></span>
            </div>

            <div class="form-group">
                <label for="modele">Modele souhaite</label>
                <select id="modele" name="modele">
                    <option value="">-- Choisissez d'abord une categorie --</option>
                    <?php foreach ($voitures_par_cat as $cat => $voitures):
                        $label = $labels_cat[$cat] ?? ucfirst($cat); ?>
                    <optgroup label="<?= htmlspecialchars($label) ?>" data-cat="<?= $cat ?>">
                        <?php foreach ($voitures as $v):
                            $val = htmlspecialchars($v['marque'] . ' ' . $v['modele']);
                            $sel = ($get_categorie === $cat &&
                                   (stripos($v['marque'], $get_marque) !== false ||
                                    stripos($v['modele'], $get_modele) !== false)) ? 'selected' : ''; ?>
                        <option value="<?= $val ?>" <?= $sel ?>
                                data-prix="<?= $v['prix_jour'] ?>" data-cat="<?= $cat ?>">
                            <?= $val ?> - <?= $v['prix_jour'] ?> DT/jour
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
                <span id="err_modele" class="error-msg"></span>
            </div>

            <div class="form-group">
                <label for="lieu_prise">Lieu de prise en charge</label>
                <input type="text" id="lieu_prise" name="lieu_prise"
                       placeholder="Ex: Aeroport Tunis-Carthage" list="lieux">
                <datalist id="lieux">
                    <option value="Aeroport Tunis-Carthage">
                    <option value="Aeroport Enfidha-Hammamet">
                </datalist>
                <span id="err_lieu_prise" class="error-msg"></span>
            </div>
        </fieldset>

        <fieldset>
            <legend>Options Supplementaires</legend>
            <div class="form-group">
                <label>Options souhaitees :</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="options" value="gps"> GPS integre</label>
                    <label><input type="checkbox" name="options" value="siege_bebe"> Siege bebe</label>
                </div>
            </div>
            <div class="form-group">
                <label>Type de transmission prefere :</label>
                <div class="radio-group">
                    <label><input type="radio" name="transmission" value="automatique" checked> Automatique</label>
                    <label><input type="radio" name="transmission" value="manuelle"> Manuelle</label>
                </div>
            </div>
            <div class="form-group">
                <label for="km_max">Kilométrage maximum estimé (<span id="km_val">500</span> km) :</label>
                <div style="display:flex; align-items:center; gap:15px;">
                    <span style="font-weight:bold; color:#0e6fa5;">0 km</span>
                    <input type="range" id="km_max" name="km_max" min="0" max="5000" value="500" step="50"
                           style="flex:1; cursor:pointer;"
                           oninput="document.getElementById('km_val').innerText=this.value">
                    <span style="font-weight:bold; color:#0e6fa5;">5000 km</span>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Informations Complementaires</legend>
            <div class="form-group">
                <label for="commentaire">Commentaires ou demandes speciales</label>
                <textarea id="commentaire" name="commentaire" placeholder="Indiquez vos besoins particuliers..."></textarea>
            </div>
            <div class="form-group">
                <label for="fichier">Joindre un document (permis, piece d'identite)</label>
                <input type="file" id="fichier" name="fichier" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="conditions" value="accepte" required>
                    J'accepte les conditions generales *</label>
                <span id="err_conditions" class="error-msg"></span>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="rgpd" value="accepte" required>
                    J'accepte la politique de confidentialite *</label>
                <span id="err_rgpd" class="error-msg"></span>
            </div>
        </fieldset>

        <button type="submit" class="btn-orange">Verifier la disponibilite</button>
        <button type="reset" class="btn-reset">Reinitialiser le formulaire</button>
    </form>
</div>

<footer>
    <p>&copy; 2026 Locavo - Tous droits reserves</p>
    <p>Contact : <a href="tel:+21612345678">+216 12 345 678</a> | <a href="mailto:contact@locavo.tn">contact@locavo.tn</a></p>
    <div class="footer-links">
        <a href="../index.html">Accueil</a>
        <a href="about.php">A propos</a>
        <a href="../html/contact.html">Contact</a>
        <a href="../html/questionnaire.html">Questionnaire</a>
        <a href="../html/funpage.html">Fun Page</a>
    </div>
</footer>

<script src="../js/script.js"></script>
<script>
const _allGroups = Array.from(document.getElementById('modele').querySelectorAll('optgroup'))
                        .map(g => g.cloneNode(true));

function filtrerModeles(preselectVal) {
    const cat    = document.getElementById('categorie').value;
    const selMod = document.getElementById('modele');
    selMod.innerHTML = '<option value="">-- Selectionnez un modele --</option>';
    if (!cat) return;
    _allGroups.forEach(grp => {
        if (grp.getAttribute('data-cat') === cat) selMod.appendChild(grp.cloneNode(true));
    });
    if (preselectVal && preselectVal.trim()) {
        const s = preselectVal.trim().toLowerCase();
        for (let opt of selMod.options) {
            if (opt.text.toLowerCase().includes(s) || opt.value.toLowerCase().includes(s)) {
                opt.selected = true; break;
            }
        }
    }
}

(function() {
    const cat = document.getElementById('categorie').value;
    if (cat) filtrerModeles(<?= json_encode($get_marque . ' ' . $get_modele) ?>);
})();
</script>
<?php endif; ?>

</body>
</html>
