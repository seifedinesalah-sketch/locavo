<?php
require_once '../config.php';
require_once 'Voiture.php';

// query() + fetchAll() → tableau d'objets Voiture (via la fonction)
$voitures = chargerVoitures($pdo);

// fetch() → la voiture la moins chère
$moinsChere = $pdo->query("SELECT * FROM voitures ORDER BY prix_jour ASC LIMIT 1")
                  ->fetch(PDO::FETCH_ASSOC);

// fetchObject() → la voiture la plus chère
$plusChere = $pdo->query("SELECT * FROM voitures ORDER BY prix_jour DESC LIMIT 1")
                 ->fetchObject();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Locavo - Notre Flotte</title>
    <link rel="stylesheet" href="../css/style.css">
	 <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="../js/banner.js" defer></script>
    <style>
        .encarts { display:flex; gap:20px; margin:20px 0; flex-wrap:wrap; }
        .encart  { flex:1; min-width:200px; background:#fff; border:1px solid #ddd;
                   border-radius:10px; padding:15px 20px; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
        .encart h4 { color:#0e6fa5; margin-bottom:5px; font-size:0.85rem; text-transform:uppercase; }
        .encart p  { font-size:1.1rem; font-weight:bold; color:#2c3e50; margin:0; }
        .encart small { color:#888; }
    </style>
</head>
<body>

<div class="banniere-container">
    <div class="banniere-texte" id="banniere-texte"><img src="../images/logo.png" alt="Locavo" class="banniere-logo"><span id="banniere-msg"></span></div>
</div>

<nav class="navbar">
        <div class="logo-container"><img src="../images/logo.png" alt="Locavo Logo" class="logo-img"><span class="logo-text">Locavo</span></div>
        <ul class="nav-links">
            <li><a href="../index.html">Accueil</a></li>
            <li><a href="../php/flotte.php" class="active">Nos Voitures</a></li>
            <li><a href="../php/recherche.php">Recherche</a></li>
            <li><a href="../php/reservation.php">Réservation</a></li>
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

<header class="contact-hero" style="background:linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)),url('../images/hero-home.jpg');background-size:cover;background-position:center;margin-bottom:40px;">
    <h1>Notre Flotte</h1>
    <p>Trouvez le véhicule qui vous correspond</p>
</header>

<div style="max-width:1100px; margin:0 auto; padding:20px;">

    <!-- Encarts : démontre fetch() et fetchObject() -->
    <div class="encarts">
        <?php if ($moinsChere): ?>
        <div class="encart">
            <h4>Meilleur prix — fetch()</h4>
            <p><?= htmlspecialchars($moinsChere['marque'] . ' ' . $moinsChere['modele']) ?></p>
            <small><?= $moinsChere['prix_jour'] ?> DT / jour</small>
        </div>
        <?php endif; ?>

        <?php if ($plusChere): ?>
        <div class="encart">
            <h4>Véhicule premium — fetchObject()</h4>
            <p><?= htmlspecialchars($plusChere->marque . ' ' . $plusChere->modele) ?></p>
            <small><?= $plusChere->prix_jour ?> DT / jour</small>
        </div>
        <?php endif; ?>

        <div class="encart">
            <h4>Flotte totale</h4>
            <p><?= count($voitures) ?> véhicules</p>
            <small>Mis à jour en temps réel</small>
        </div>
    </div>

    <!-- Tableau généré par afficherTableauVoitures() -->
    <h2 style="color:#0e6fa5; margin-bottom:10px;">Tous nos véhicules</h2>
    <?php afficherTableauVoitures($voitures); ?>

    <div style="margin-top:25px; text-align:center;">
        <a href="../php/reservation.php" class="btn-orange" style="text-decoration:none;">Réserver un véhicule</a>
    </div>
</div>

    <footer>
        <p>© 2026 Locavo - Tous droits réservés</p>
        <p>Contact : <a href="tel:+21612345678">+216 12 345 678</a> | <a href="mailto:contact@locavo.tn">contact@locavo.tn</a></p>
        <div class="footer-links"><a href="../index.html">Accueil</a><a href="../php/about.php">À propos</a><a href="../html/contact.html">Contact</a><a href="../html/questionnaire.html">Questionnaire</a><a href="../html/funpage.html">Fun Page</a></div>
    </footer>
</body>
</html>
