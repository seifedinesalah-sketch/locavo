<?php
/*
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : luxe.php
   Description : Page dynamique – catégorie Véhicules de Luxe
*/
require_once '../config.php';

$stmt = $pdo->prepare("SELECT * FROM voitures WHERE categorie = ? ORDER BY prix_jour ASC");
$stmt->execute(['luxe']);
$voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locavo - Véhicules de Luxe</title>
    <link rel="stylesheet" href="../css/style.css">
     <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="../js/banner.js" defer></script>
</head>
<body>
    <div class="banniere-container">
        <div class="banniere-texte" id="banniere-texte"><img src="../images/logo.png" alt="Locavo" class="banniere-logo"><span id="banniere-msg"></span></div>
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
            <li><a href="../php/luxe.php" class="active">Luxe</a></li>
            <li><a href="../html/contact.html">Contact</a></li>
            <li><a href="../php/about.php">À propos</a></li>
            <li><a href="../html/funpage.html">Fun Page</a></li>
        </ul>
    </nav>

    <header class="hero" style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('../images/hero-home.jpg'); background-size: cover; background-position: center;">
        <h1>Véhicules de Luxe</h1>
    </header>

    <main class="container">
        <p style="text-align: center; color: #555; margin-bottom: 30px;">L'élégance et le raffinement à l'état pur — découvrez notre collection exclusive.</p>

        <?php if (empty($voitures)): ?>
            <p style="text-align:center; color:#888;">Aucun véhicule de luxe disponible pour le moment.</p>
        <?php else: ?>
        <div class="grid-3">
            <?php foreach ($voitures as $v): ?>
            <div class="card">
                <img src="../images/<?= htmlspecialchars($v['image']) ?>" class="card-img" alt="<?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>">
                <div class="card-overlay"><a href="../php/reservation.php?marque=<?= urlencode($v['marque']) ?>&modele=<?= urlencode($v['modele']) ?>&prix=<?= urlencode($v['prix_jour']) ?>&categorie=<?= urlencode($v['categorie']) ?>">Réserver</a></div>
                <div class="card-content">
                    <span class="badge-info">À partir de <?= $v['prix_jour'] ?> DT/jour</span>
                    <h3><?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

    <section class="cta-section">
        <h2>L'excellence vous attend</h2>
        <p>Vivez une expérience de conduite inoubliable avec nos véhicules de prestige.</p>
        <a href="../php/reservation.php" class="btn-white">Réserver maintenant</a>
    </section>

    <footer>
        <p>© 2026 Locavo - Tous droits réservés</p>
        <p>Contact : <a href="tel:+21612345678">+216 12 345 678</a> | <a href="mailto:contact@locavo.tn">contact@locavo.tn</a></p>
        <div class="footer-links"><a href="../index.html">Accueil</a><a href="../php/about.php">À propos</a><a href="../html/contact.html">Contact</a><a href="../html/questionnaire.html">Questionnaire</a><a href="../html/funpage.html">Fun Page</a></div>
    </footer>
</body>
</html>
