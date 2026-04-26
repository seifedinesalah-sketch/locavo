<?php
/*
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : about.php
   Description : Page À Propos dynamique connectée à la BD
                 - Grille tarifaire chargée depuis la table tarifs
                 - Formulaire d'ajout de tarif → INSERT dans tarifs
                 - Formulaire de recherche → SELECT tarifs JOIN voitures
                 - Validation PHP côté serveur
*/
require_once '../config.php';

$success  = false;
$erreurs  = [];
$resultats_recherche = [];
$recherche_faite = false;
$mot_cle = '';

// ── TRAITEMENT FORMULAIRE AJOUT TARIF (POST) ─────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajout') {

    $categorie = trim($_POST['categorie']    ?? '');
    $jour3     = trim($_POST['prix_3jours']  ?? '');
    $semaine   = trim($_POST['prix_semaine'] ?? '');
    $mois      = trim($_POST['prix_mois']    ?? '');

    // Revalidation PHP côté serveur
    $cats_valides = ['economique', 'suv', 'cabriolet', 'sport', 'luxe'];
    if (!in_array($categorie, $cats_valides))
        $erreurs[] = "Veuillez choisir une catégorie valide.";
    if (!is_numeric($jour3)   || (float)$jour3   <= 0 || (float)$jour3   > 29999)
        $erreurs[] = "Le prix 3 jours doit être un nombre positif.";
    if (!is_numeric($semaine) || (float)$semaine <= 0 || (float)$semaine > 69999)
        $erreurs[] = "Le prix 1 semaine doit être un nombre positif.";
    if (!is_numeric($mois)    || (float)$mois    <= 0 || (float)$mois    > 299999)
        $erreurs[] = "Le prix 1 mois doit être un nombre positif.";
    if ((float)$semaine <= (float)$jour3)
        $erreurs[] = "Le prix 1 semaine doit être supérieur au prix 3 jours.";

    if (empty($erreurs)) {
        // INSERT avec prepare() + execute() nommé — met à jour si la catégorie existe déjà
        $stmt = $pdo->prepare(
            "INSERT INTO tarifs (categorie, prix_3jours, prix_semaine, prix_mois)
             VALUES (:cat, :j3, :sem, :mois)
             ON DUPLICATE KEY UPDATE
               prix_3jours=:j3b, prix_semaine=:semb, prix_mois=:moisb"
        );
        $stmt->execute([
            ':cat'  => $categorie,
            ':j3'   => (float)$jour3,   ':j3b'   => (float)$jour3,
            ':sem'  => (float)$semaine, ':semb'  => (float)$semaine,
            ':mois' => (float)$mois,    ':moisb' => (float)$mois,
        ]);
        $success = true;
    }
}

// ── TRAITEMENT FORMULAIRE RECHERCHE (GET) ────────────────────
if (isset($_GET['rechercher'])) {
    $recherche_faite = true;
    $mot_cle = trim($_GET['mot_cle'] ?? '');

    // SELECT tarifs JOIN voitures sur la catégorie — fetch() en boucle
    $sql = "SELECT t.*, v.marque, v.modele, v.prix_jour AS prix_voiture, v.image
            FROM tarifs t
            LEFT JOIN voitures v ON v.categorie = t.categorie";
    $params = [];

    if (!empty($mot_cle)) {
        $sql .= " WHERE t.categorie LIKE :mot
                  OR v.marque LIKE :mot2
                  OR v.modele LIKE :mot3";
        $params[':mot']  = '%' . $mot_cle . '%';
        $params[':mot2'] = '%' . $mot_cle . '%';
        $params[':mot3'] = '%' . $mot_cle . '%';
    }
    $sql .= " ORDER BY t.categorie, v.prix_jour ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // fetch() en boucle (comme demandé par le cahier des charges)
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resultats_recherche[] = $row;
    }
}

// ── CHARGEMENT GRILLE TARIFAIRE + MODELES BD ─────────────────
// query() + fetchAll() — pour chaque tarif, récupère les modèles réels depuis voitures
$tarifs = $pdo->query("SELECT * FROM tarifs ORDER BY prix_3jours ASC")->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque catégorie tarifaire, récupérer les modèles depuis la BD
$modeles_par_cat = [];
$stmt_mod = $pdo->query("SELECT categorie, marque, modele FROM voitures ORDER BY categorie, marque");
while ($row = $stmt_mod->fetch(PDO::FETCH_ASSOC)) {
    $modeles_par_cat[$row['categorie']][] = $row['marque'] . ' ' . $row['modele'];
}

$labels_cat = [
    'economique' => 'Économique',
    'suv'        => 'SUV',
    'cabriolet'  => 'Cabriolet',
    'sport'      => 'Sport',
    'luxe'       => 'Luxe',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locavo - À Propos</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="../js/banner.js" defer></script>
    <style>
        .erreurs { background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:15px; color:#721c24; margin-bottom:15px; }
        .succes  { background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; padding:15px; color:#155724; margin-bottom:15px; text-align:center; }
        .error-msg { color:#e74c3c; font-size:0.82rem; display:block; margin-top:3px; }
        .result-card { background:#fff; border:1px solid #ddd; border-radius:10px; padding:15px; margin-bottom:12px; display:flex; align-items:center; gap:20px; }
        .result-card img { width:100px; height:68px; object-fit:cover; border-radius:8px; flex-shrink:0; }
        .badge-cat { background:#0e6fa5; color:#fff; font-size:0.78rem; padding:2px 10px; border-radius:12px; }
        .nb-resultats { background:#d4edda; border:1px solid #c3e6cb; border-radius:6px; padding:10px 15px; color:#155724; margin-bottom:15px; }
        .vide { background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:20px; text-align:center; color:#856404; }
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
        <div class="logo-container"><img src="../images/logo.png" alt="Locavo Logo" class="logo-img"><span class="logo-text">Locavo</span></div>
        <ul class="nav-links">
            <li><a href="../index.html">Accueil</a></li>
            <li><a href="../php/flotte.php">Nos Voitures</a></li>
            <li><a href="../php/recherche.php">Recherche</a></li>
            <li><a href="../php/reservation.php">Réservation</a></li>
            <li><a href="../php/sport.php">Sport / Coupés</a></li>
            <li><a href="../php/suv.php">SUV &amp; Crossovers</a></li>
            <li><a href="../php/economiques.php">Économiques</a></li>
            <li><a href="../php/modeles.php">Cabriolets</a></li>
            <li><a href="../php/luxe.php">Luxe</a></li>
            <li><a href="../html/contact.html">Contact</a></li>
            <li><a href="../php/about.php" class="active">À propos</a></li>
            <li><a href="../html/funpage.html">Fun Page</a></li>
        </ul>
    </nav>

    <header class="about-hero" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('../images/hero-home.jpg'); background-size: cover; background-position: center;">
        <h1>À Propos</h1>
        <p>Votre liberté, notre moteur</p>
    </header>

    <section class="text-block">
        <h2>Qui sommes-nous ?</h2>
        <p>Bienvenue chez Locavo, votre partenaire de confiance pour tous vos déplacements en Tunisie. Plus qu'une simple agence de location, Locavo est née d'une ambition claire : simplifier la mobilité pour tous. Que vous soyez un particulier en quête d'évasion ou un professionnel aux impératifs précis, nous mettons la route à votre portée en quelques clics.</p>
    </section>

    <section class="text-block alt-bg">
        <div class="text-inner">
            <h2>Pourquoi choisir Locavo ?</h2>
            <p>Nous savons que la location de voiture peut parfois sembler complexe. C'est pourquoi nous avons bâti notre service sur trois piliers fondamentaux :</p>
        </div>
        <div class="features-grid">
            <div class="feature-item">
                <div class="icon">🤝</div>
                <h3>La Proximité</h3>
                <p>Fidèles à notre nom, nous restons proches de nos clients avec un service personnalisé et une écoute attentive.</p>
            </div>
            <div class="feature-item">
                <div class="icon">💎</div>
                <h3>La Transparence</h3>
                <p>Chez Locavo, pas de mauvaises surprises. Nos tarifs sont clairs, honnêtes et sans frais cachés.</p>
            </div>
            <div class="feature-item">
                <div class="icon">🛡️</div>
                <h3>La Qualité</h3>
                <p>Notre flotte est rigoureusement entretenue et renouvelée régulièrement pour vous garantir confort et sécurité optimale.</p>
            </div>
        </div>
    </section>

    <section class="text-block">
        <h2>Notre Engagement</h2>
        <p>Votre satisfaction est notre seule boussole.</p>
        <h3 style="color: #0e6fa5; margin-top: 30px;">Nos garanties :</h3>
        <ol>
            <li>Véhicules contrôlés avant chaque location</li>
            <li>Assistance dépannage 24h/24 et 7j/7</li>
            <li>Kilométrage illimité sur certaines offres</li>
            <li>Annulation gratuite jusqu'à 48h avant</li>
            <li>Programme de fidélité avantageux</li>
        </ol>
        <h3 style="color: #0e6fa5; margin-top: 30px;">Services inclus :</h3>
        <ul>
            <li>Nettoyage complet du véhicule</li>
            <li>Plein de carburant à la prise en charge</li>
            <li>Assurance de base incluse</li>
            <li>Support client multilingue</li>
        </ul>
        <div class="quote-box">« Locavo, c'est l'assurance d'un voyage serein, où que vous alliez. »</div>
    </section>

    <!-- ── GRILLE TARIFAIRE DYNAMIQUE (depuis BD) ── -->
    <div class="table-container">
        <h2>Grille Tarifaire</h2>
        <p style="text-align:center; color:#666; margin-bottom:15px; font-size:0.9rem;">
            Tarifs en DT — Modèles chargés depuis la base de données
        </p>
        <?php if (empty($tarifs)): ?>
            <p style="text-align:center; color:#888;">Aucun tarif disponible pour le moment.</p>
        <?php else: ?>
        <table class="pricing-table" id="table-tarifs">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>3 Jours</th>
                    <th>1 Semaine</th>
                    <th>1 Mois</th>
                    <th>Modèles disponibles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tarifs as $t):
                    $cat     = $t['categorie'];
                    $label   = $labels_cat[$cat] ?? ucfirst($cat);
                    // Modèles réels depuis la BD voitures
                    $modeles = isset($modeles_par_cat[$cat])
                        ? implode(', ', $modeles_par_cat[$cat])
                        : '—';
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($label) ?></strong></td>
                    <td><?= number_format($t['prix_3jours'],  2) ?> DT</td>
                    <td><?= number_format($t['prix_semaine'], 2) ?> DT</td>
                    <td><?= number_format($t['prix_mois'],    2) ?> DT</td>
                    <td style="font-size:0.85rem; color:#555;"><?= htmlspecialchars($modeles) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- ── FORMULAIRE AJOUT TARIF ── -->
    <div class="form-container" style="margin-top: 40px;">
        <h2>Ajouter / Mettre à jour un tarif</h2>
        <p style="color:#666; font-size:0.9rem; margin-bottom:15px;">
            Si la catégorie existe déjà, ses tarifs seront mis à jour.
        </p>

        <?php if ($success): ?>
            <div class="succes">✅ Tarif enregistré avec succès dans la base de données !</div>
        <?php endif; ?>
        <?php if (!empty($erreurs)): ?>
            <div class="erreurs">
                <?php foreach ($erreurs as $e): ?>
                    <p>• <?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="about.php" onsubmit="return validerAjoutTarif()">
            <input type="hidden" name="action" value="ajout">
            <fieldset>
                <legend>Nouvelle catégorie tarifaire</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="add-categorie">Catégorie *</label>
                        <select id="add-categorie" name="categorie" required>
                            <option value="">-- Choisir --</option>
                            <option value="economique" <?= ($_POST['categorie'] ?? '') === 'economique' ? 'selected' : '' ?>>Économique</option>
                            <option value="suv"        <?= ($_POST['categorie'] ?? '') === 'suv'        ? 'selected' : '' ?>>SUV</option>
                            <option value="cabriolet"  <?= ($_POST['categorie'] ?? '') === 'cabriolet'  ? 'selected' : '' ?>>Cabriolet</option>
                            <option value="sport"      <?= ($_POST['categorie'] ?? '') === 'sport'      ? 'selected' : '' ?>>Sport</option>
                            <option value="luxe"       <?= ($_POST['categorie'] ?? '') === 'luxe'       ? 'selected' : '' ?>>Luxe</option>
                        </select>
                        <span id="err_categorie" class="error-msg"></span>
                    </div>
                    <div class="form-group">
                        <label for="add-jour3">Prix 3 jours (DT) *</label>
                        <input type="number" id="add-jour3" name="prix_3jours" min="1" step="0.01"
                               placeholder="Ex : 180" required
                               value="<?= htmlspecialchars($_POST['prix_3jours'] ?? '') ?>">
                        <span id="err_jour3" class="error-msg"></span>
                    </div>
                    <div class="form-group">
                        <label for="add-semaine">Prix 1 semaine (DT) *</label>
                        <input type="number" id="add-semaine" name="prix_semaine" min="1" step="0.01"
                               placeholder="Ex : 400" required
                               value="<?= htmlspecialchars($_POST['prix_semaine'] ?? '') ?>">
                        <span id="err_semaine" class="error-msg"></span>
                    </div>
                    <div class="form-group">
                        <label for="add-mois">Prix 1 mois (DT) *</label>
                        <input type="number" id="add-mois" name="prix_mois" min="1" step="0.01"
                               placeholder="Ex : 1200" required
                               value="<?= htmlspecialchars($_POST['prix_mois'] ?? '') ?>">
                        <span id="err_mois" class="error-msg"></span>
                    </div>
                </div>
            </fieldset>
            <button type="submit" class="btn-orange">Enregistrer dans la BD</button>
        </form>
    </div>

    <!-- ── FORMULAIRE RECHERCHE TARIFS + VOITURES ── -->
    <div class="form-container" style="margin-top: 20px;" id="section-recherche">
        <h2>Rechercher tarifs &amp; voitures</h2>
        <form method="GET" action="about.php#section-recherche">
            <fieldset>
                <legend>Recherche par catégorie ou modèle</legend>
                <div class="form-group">
                    <label for="search-mot">Mot-clé (catégorie ou marque/modèle)</label>
                    <input type="text" id="search-mot" name="mot_cle"
                           placeholder="Ex : suv, BMW, Renault..."
                           value="<?= htmlspecialchars($mot_cle) ?>">
                </div>
            </fieldset>
            <button type="submit" name="rechercher" class="btn-orange">Rechercher</button>
            <a href="../php/about.php" style="margin-left:10px; color:#888;">Réinitialiser</a>
        </form>

        <!-- Résultats de la recherche -->
        <?php if ($recherche_faite): ?>
            <?php if (!empty($resultats_recherche)):
                // Grouper par catégorie pour l'affichage
                $grouped = [];
                foreach ($resultats_recherche as $r) {
                    $grouped[$r['categorie']][] = $r;
                }
            ?>
                <div class="nb-resultats">
                    <?= count($resultats_recherche) ?> résultat(s) trouvé(s)
                    <?= !empty($mot_cle) ? 'pour "' . htmlspecialchars($mot_cle) . '"' : '' ?>
                </div>
                <?php foreach ($grouped as $cat => $voitures): ?>
                    <h3 style="color:#0e6fa5; margin:20px 0 10px;">
                        <?= htmlspecialchars($labels_cat[$cat] ?? ucfirst($cat)) ?>
                    </h3>
                    <?php foreach ($voitures as $v): if (!$v['marque']) continue; ?>
                    <div class="result-card">
                        <img src="../images/<?= htmlspecialchars($v['image']) ?>"
                             alt="<?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>">
                        <div style="flex:1;">
                            <strong><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?></strong>
                            <span class="badge-cat" style="margin-left:8px;">
                                <?= htmlspecialchars($labels_cat[$v['categorie']] ?? $v['categorie']) ?>
                            </span>
                            <div style="color:#e67e22; font-weight:bold; margin-top:5px;">
                                <?= $v['prix_voiture'] ?> DT/jour
                            </div>
                        </div>
                        <a href="../php/reservation.php?marque=<?= urlencode($v['marque']) ?>&modele=<?= urlencode($v['modele']) ?>&prix=<?= urlencode($v['prix_voiture']) ?>&categorie=<?= urlencode($v['categorie']) ?>" class="btn-orange" style="text-decoration:none;">Réserver</a>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="vide">
                    <p>Aucun résultat pour "<?= htmlspecialchars($mot_cle) ?>".</p>
                    <p style="font-size:0.9rem; margin-top:8px;">Essayez : economique, suv, cabriolet, sport, BMW, Renault...</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <section class="text-block alt-bg">
        <h2>Contactez-nous</h2>
        <p>Une question ? Notre équipe est à votre disposition.</p>
        <div style="margin-top: 30px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; margin-left: auto; margin-right: auto;">
            <p style="margin-bottom: 15px;"><strong>📍</strong> ENSI - Manouba, Tunisie</p>
            <p style="margin-bottom: 15px;"><strong>📞</strong> <a href="tel:+21612345678" style="color:#0e6fa5;font-weight:bold;">+216 12 345 678</a></p>
            <p style="margin-bottom: 0;"><strong>✉️</strong> <a href="mailto:contact@locavo.tn" style="color:#0e6fa5;font-weight:bold;">contact@locavo.tn</a></p>
        </div>
    </section>

    <section class="newsletter-section">
        <h2>Restez informé</h2>
        <p>Inscrivez-vous à notre newsletter pour recevoir nos offres exclusives.</p>
        <form action="https://httpbin.org/get" method="GET" class="newsletter-form" id="newsletter-form">
            <input type="email" name="newsletter_email" placeholder="Votre adresse email" required>
            <button type="submit">S'inscrire</button>
        </form>
    </section>

    <section class="cta-section">
        <h2>Prêt à prendre la route ?</h2>
        <p>Réservez votre véhicule en quelques clics et partez l'esprit tranquille.</p>
        <a href="../php/reservation.php" class="btn-white pulse-animation">Réserver maintenant</a>
    </section>

    <footer>
        <p>© 2026 Locavo - Tous droits réservés</p>
        <p>Contact : <a href="tel:+21612345678">+216 12 345 678</a> | <a href="mailto:contact@locavo.tn">contact@locavo.tn</a></p>
        <div class="footer-links">
            <a href="../index.html">Accueil</a>
            <a href="../php/about.php">À propos</a>
            <a href="../html/contact.html">Contact</a>
            <a href="../php/reservation.php">Réservation</a>
            <a href="../html/questionnaire.html">Questionnaire</a>
            <a href="../html/funpage.html">Fun Page</a>
        </div>
    </footer>

    <script>
    /*
       Validation JS du formulaire d'ajout de tarif
       Messages d'erreur dans la page, pas d'alert
    */
    function validerAjoutTarif() {
        let ok = true;

        const cat     = document.getElementById('add-categorie');
        const jour3   = document.getElementById('add-jour3');
        const semaine = document.getElementById('add-semaine');
        const mois    = document.getElementById('add-mois');

        const errCat     = document.getElementById('err_categorie');
        const errJour3   = document.getElementById('err_jour3');
        const errSemaine = document.getElementById('err_semaine');
        const errMois    = document.getElementById('err_mois');

        // Reset
        [errCat, errJour3, errSemaine, errMois].forEach(el => { if(el) el.innerText = ''; });

        // 1. Catégorie obligatoire
        if (cat.value === '') {
            errCat.innerText = 'Veuillez choisir une catégorie.';
            ok = false;
        }

        // 2. Prix 3 jours : nombre positif
        const v3 = parseFloat(jour3.value);
        if (isNaN(v3) || v3 <= 0) {
            errJour3.innerText = 'Le prix 3 jours doit être un nombre positif.';
            ok = false;
        }

        // 3. Prix semaine > prix 3 jours (validation logique)
        const vsem = parseFloat(semaine.value);
        if (isNaN(vsem) || vsem <= 0) {
            errSemaine.innerText = 'Le prix 1 semaine doit être un nombre positif.';
            ok = false;
        } else if (!isNaN(v3) && vsem <= v3) {
            errSemaine.innerText = 'Le prix 1 semaine doit être supérieur au prix 3 jours.';
            ok = false;
        }

        // 4. Prix mois > prix semaine (validation logique)
        const vmois = parseFloat(mois.value);
        if (isNaN(vmois) || vmois <= 0) {
            errMois.innerText = 'Le prix 1 mois doit être un nombre positif.';
            ok = false;
        } else if (!isNaN(vsem) && vmois <= vsem) {
            errMois.innerText = 'Le prix 1 mois doit être supérieur au prix 1 semaine.';
            ok = false;
        }

        return ok;
    }
    </script>
</body>
</html>
