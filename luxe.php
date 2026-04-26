<?php
require_once '../config.php';

$status  = '';
$erreurs = [];
$nom = $email = $tel = $sujet = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom     = trim($_POST['nom']       ?? '');
    $email   = trim($_POST['email']     ?? '');
    $tel     = trim($_POST['telephone'] ?? '');
    $sujet   = trim($_POST['sujet']     ?? '');
    $message = trim($_POST['message']   ?? '');

    // Validation PHP côté serveur
    if (empty($nom))                                   $erreurs[] = "Le nom est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $erreurs[] = "L'email n'est pas valide.";
    if (strlen($message) < 10)                         $erreurs[] = "Le message doit contenir au moins 10 caractères.";

    if (empty($erreurs)) {
        try {
            // INSERT avec prepare() + execute() nommé
            $pdo->prepare("INSERT INTO clients (nom_complet, email, tel, mot_de_passe) VALUES (:nom, :email, :tel, 'contact')")
                ->execute([':nom' => $nom, ':email' => $email, ':tel' => $tel]);
            $status = 'success';
        } catch (PDOException $e) {
            $status = ($e->getCode() == 23000) ? 'duplicate' : 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Locavo - Contact</title>
    <link rel="stylesheet" href="../css/style.css">
     <link rel="icon" type="image/png" href="../images/logo.png">
    <style>
        .box { max-width:600px; margin:150px auto; padding:40px; background:#fff; border-radius:12px;
               box-shadow:0 8px 25px rgba(0,0,0,0.08); text-align:center; }
        .erreurs { background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:15px; margin:15px 0; color:#721c24; text-align:left; }
        table { width:100%; border-collapse:collapse; margin-top:20px; text-align:left; }
        th { background:#0e6fa5; color:#fff; padding:10px; }
        td { padding:10px; border-bottom:1px solid #eee; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo-container">
        <img src="../images/logo.png" alt="Locavo" class="logo-img">

        <span class="logo-text">Locavo</span>
    </div>
    <ul class="nav-links">
        <li><a href="../index.html">Accueil</a></li>
        <li><a href="../php/flotte.php">Nos Voitures</a></li>
        <li><a href="../php/recherche.php">Recherche</a></li>
        <li><a href="../php/reservation.php">Réservation</a></li>
        <li><a href="../html/contact.html" class="active">Contact</a></li>
        <li><a href="../php/admin.php">Admin</a></li>
    </ul>
</nav>

<div class="box">
    <?php if (!empty($erreurs)): ?>
        <div style="font-size:50px;">⚠️</div>
        <h2 style="color:#e74c3c;">Formulaire incomplet</h2>
        <div class="erreurs">
            <?php foreach ($erreurs as $e): ?>
                <p>• <?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
        <a href="../html/contact.html" class="btn-orange" style="text-decoration:none;">Corriger</a>

    <?php elseif ($status === 'success'): ?>
        <div style="font-size:50px;">✅</div>
        <h2 style="color:#2c3e50;">Merci <?= htmlspecialchars($nom) ?> !</h2>
        <p style="color:#888; margin-top:10px;">Votre message a bien été enregistré.</p>

        <table>
            <thead><tr><th>Champ</th><th>Valeur</th></tr></thead>
            <tbody>
                <tr><td>Nom</td><td><?= htmlspecialchars($nom) ?></td></tr>
                <tr><td>Email</td><td><?= htmlspecialchars($email) ?></td></tr>
                <tr><td>Téléphone</td><td><?= htmlspecialchars($tel ?: '—') ?></td></tr>
                <?php if ($sujet): ?><tr><td>Sujet</td><td><?= htmlspecialchars($sujet) ?></td></tr><?php endif; ?>
                <?php if ($message): ?><tr><td>Message</td><td><?= htmlspecialchars($message) ?></td></tr><?php endif; ?>
            </tbody>
        </table>

    <?php elseif ($status === 'duplicate'): ?>
        <div style="font-size:50px;">⚠️</div>
        <h2 style="color:#e67e22;">Email déjà enregistré</h2>
        <p>Votre message a tout de même été pris en compte.</p>

    <?php else: ?>
        <p style="color:#888;">Aucune donnée reçue.</p>
    <?php endif; ?>

    <div style="margin-top:25px;">
        <a href="../index.html" class="btn-orange" style="text-decoration:none;">Retour à l'accueil</a>
    </div>
</div>

<footer style="margin-top:50px;"><p>© 2026 Locavo</p></footer>
</body>
</html>
