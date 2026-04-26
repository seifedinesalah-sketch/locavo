<?php
/*
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : questionnaire.php
   Description : Traitement PHP du formulaire questionnaire de satisfaction
                 - Revalidation côté serveur
                 - Affichage des données dans un tableau HTML
                 - Insertion du client dans la BD via PDO
*/
require_once '../config.php';

class ReponseQuestionnaire {
    private $nom, $email, $age, $ville, $satisfaction, $note, $commentaire, $points;

    public function __construct($nom, $email, $age, $ville, $satisfaction, $note, $commentaire, $points) {
        $this->nom = $nom; $this->email = $email; $this->age = $age;
        $this->ville = $ville; $this->satisfaction = $satisfaction;
        $this->note = $note; $this->commentaire = $commentaire; $this->points = $points;
    }

    public function getNom()          { return $this->nom; }
    public function getEmail()        { return $this->email; }
    public function getAge()          { return $this->age; }
    public function getVille()        { return $this->ville; }
    public function getSatisfaction() { return $this->satisfaction; }
    public function getNote()         { return $this->note; }
    public function getCommentaire()  { return $this->commentaire; }
    public function getPoints()       { return $this->points; }
    public function getNoteEtoiles()  {
        $nb = round($this->note / 2);
        return str_repeat("★", $nb) . str_repeat("☆", 5 - $nb);
    }
}

function afficherRecapQuestionnaire(array $reponses, array $labels_satisfaction, array $labels_points): void {
    if (empty($reponses)) return;
    echo "<table class='recap-table'><thead><tr><th>Champ</th><th>Valeur</th></tr></thead><tbody>";
    foreach ($reponses as $champ => $valeur) {
        if ($champ === "Points appréciés" && is_array($valeur)) {
            $vals = [];
            foreach ($valeur as $p) { $vals[] = htmlspecialchars($labels_points[$p] ?? $p); }
            $valeur = implode(", ", $vals);
        } elseif ($champ === "Satisfaction") {
            $valeur = htmlspecialchars($labels_satisfaction[$valeur] ?? $valeur);
        } elseif ($champ === "Note") {
            $nb = round((int)$valeur / 2);
            $valeur = str_repeat("★", $nb) . str_repeat("☆", 5 - $nb) . " (" . $valeur . "/10)";
        } else {
            $valeur = htmlspecialchars((string)$valeur);
        }
        echo "<tr><td><strong>" . htmlspecialchars($champ) . "</strong></td><td>$valeur</td></tr>";
    }
    echo "</tbody></table>";
}


$erreurs = [];
$nom = $email = $age = $ville = $satisfaction = $note = $commentaire = '';
$points = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── Récupération et nettoyage des données ─────────────────────
    $nom          = trim($_POST['nom']          ?? '');
    $email        = trim($_POST['email']        ?? '');
    $age          = trim($_POST['age']          ?? '');
    $ville        = trim($_POST['ville']        ?? '');
    $satisfaction = trim($_POST['satisfaction'] ?? '');
    $note         = trim($_POST['note']         ?? '7');
    $commentaire  = trim($_POST['commentaire']  ?? '');
    $points       = isset($_POST['points']) ? (array)$_POST['points'] : [];

    // ── Revalidation PHP côté serveur ─────────────────────────────
    if (strlen($nom) < 3)
        $erreurs[] = "Le nom doit contenir au moins 3 caractères.";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $erreurs[] = "Veuillez entrer un email valide.";

    $ageInt = (int)$age;
    if ($age === '' || $ageInt < 18 || $ageInt > 99)
        $erreurs[] = "L'âge doit être compris entre 18 et 99.";

    $satisfactions_valides = ['excellent', 'bien', 'moyen', 'mauvais'];
    if (!in_array($satisfaction, $satisfactions_valides))
        $erreurs[] = "Veuillez indiquer votre satisfaction.";

    if (strlen($commentaire) < 10)
        $erreurs[] = "Le commentaire doit contenir au moins 10 caractères.";

    $noteInt = (int)$note;
    if ($noteInt < 1 || $noteInt > 10)
        $erreurs[] = "La note doit être comprise entre 1 et 10.";

    // ── Insertion dans la BD si pas d'erreurs ─────────────────────
    if (empty($erreurs)) {
        try {
            // INSERT avec prepare() + execute() nommé
            $pdo->prepare(
                "INSERT INTO clients (nom_complet, email, tel, mot_de_passe)
                 VALUES (:nom, :email, '', 'questionnaire')
                 ON DUPLICATE KEY UPDATE id_c = LAST_INSERT_ID(id_c)"
            )->execute([':nom' => $nom, ':email' => $email]);
        } catch (PDOException $e) {
            // Email déjà enregistré : on continue quand même l'affichage
        }
    }

} else {
    // Accès direct sans POST → retour au formulaire
    header('Location: ../html/questionnaire.html');
    exit;
}

// Labels lisibles pour l'affichage
$labels_satisfaction = [
    'excellent' => 'Excellent',
    'bien'      => 'Bien',
    'moyen'     => 'Moyen',
    'mauvais'   => 'Mauvais',
];
$labels_points = [
    'prix'      => 'Prix compétitifs',
    'vehicules' => 'Qualité des véhicules',
    'accueil'   => 'Accueil chaleureux',
    'rapidite'  => 'Rapidité du service',
    'site'      => 'Site web intuitif',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locavo - Merci pour votre avis</title>
    <link rel="stylesheet" href="../css/style.css">
 <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="../js/banner.js" defer></script>
    <style>
        .box { max-width:680px; margin:80px auto; padding:40px; background:#fff; border-radius:12px;
               box-shadow:0 8px 25px rgba(0,0,0,0.08); }
        .erreurs { background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px;
                   padding:15px; margin-bottom:20px; color:#721c24; }
        .succes-header { text-align:center; margin-bottom:25px; }
        .succes-header .icon { font-size:52px; }
        .succes-header h2 { color:#2c3e50; margin-top:10px; }
        .recap-table { width:100%; border-collapse:collapse; margin-top:10px; }
        .recap-table th { background:#0e6fa5; color:#fff; padding:11px 14px; text-align:left; }
        .recap-table td { padding:10px 14px; border-bottom:1px solid #eee; }
        .recap-table tr:last-child td { border-bottom:none; }
        .note-bar { display:inline-block; height:12px; background:#0e6fa5;
                    border-radius:6px; vertical-align:middle; margin-right:8px; }
        .btn-group { display:flex; gap:12px; justify-content:center; margin-top:28px; flex-wrap:wrap; }
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
        <li><a href="../php/reservation.php">Réservation</a></li>
        <li><a href="../php/sport.php">Sport / Coupés</a></li>
        <li><a href="../php/suv.php">SUV &amp; Crossovers</a></li>
        <li><a href="../php/economiques.php">Économiques</a></li>
        <li><a href="../php/modeles.php">Cabriolets</a></li>
        <li><a href="../php/luxe.php">Luxe</a></li>
            <li><a href="../html/contact.html">Contact</a></li>
        <li><a href="../php/about.php">À propos</a></li>
        <li><a href="../html/funpage.html" class="active">Fun Page</a></li>
    </ul>
</nav>

<div class="box">

    <?php if (!empty($erreurs)): ?>
        <!-- ── Affichage des erreurs de revalidation PHP ── -->
        <div class="succes-header">
            <div class="icon">⚠️</div>
            <h2 style="color:#e74c3c;">Formulaire incomplet</h2>
            <p style="color:#888; margin-top:8px;">Veuillez corriger les erreurs suivantes :</p>
        </div>
        <div class="erreurs">
            <?php foreach ($erreurs as $e): ?>
                <p>• <?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
        <div class="btn-group">
            <a href="../html/questionnaire.html" class="btn-orange" style="text-decoration:none;">
                ← Corriger le formulaire
            </a>
        </div>

    <?php else: ?>
        <!-- ── Confirmation de réception ── -->
        <div class="succes-header">
            <div class="icon">✅</div>
            <h2>Merci <?= htmlspecialchars($nom) ?> !</h2>
            <p style="color:#888; margin-top:8px;">Votre avis a bien été enregistré.</p>
        </div>

        <?php
        $obj = new ReponseQuestionnaire($nom, $email, $age, $ville, $satisfaction, $note, $commentaire, $points);
        $reponses_affichage = [
            "Nom"              => $obj->getNom(),
            "Email"            => $obj->getEmail(),
            "Âge"              => $obj->getAge() . " ans",
            "Ville"            => $obj->getVille(),
            "Satisfaction"     => $obj->getSatisfaction(),
            "Note"             => $obj->getNote(),
            "Points appréciés" => $obj->getPoints(),
            "Commentaire"      => $obj->getCommentaire(),
        ];
        afficherRecapQuestionnaire($reponses_affichage, $labels_satisfaction, $labels_points);
        ?>

        <div class="btn-group">
            <a href="../index.html" class="btn-orange" style="text-decoration:none;">Retour à l'accueil</a>
            <a href="../html/questionnaire.html" style="color:#888;">Donner un autre avis</a>
        </div>

    <?php endif; ?>

</div>

<footer style="margin-top:50px;">
    <p>© 2026 Locavo - Tous droits réservés</p>
    <p>Contact : <a href="tel:+21612345678">+216 12 345 678</a> |
       <a href="mailto:contact@locavo.tn">contact@locavo.tn</a></p>
    <div class="footer-links">
        <a href="../index.html">Accueil</a>
        <a href="../php/about.php">À propos</a>
        <a href="../html/contact.html">Contact</a>
        <a href="../html/questionnaire.html">Questionnaire</a>
        <a href="../html/funpage.html">Fun Page</a>
    </div>
</footer>

</body>
</html>
