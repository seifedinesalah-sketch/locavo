<?php
require_once '../config.php';

$resultats       = [];
$recherche_faite = false;
$erreurs         = [];

$marque    = trim($_GET['marque']    ?? '');
$categorie = trim($_GET['categorie'] ?? '');
$prix_max  = trim($_GET['prix_max']  ?? '');

// Validation PHP
if (!empty($prix_max) && (!is_numeric($prix_max) || $prix_max < 0)) {
    $erreurs[] = "Le prix maximum doit être un nombre positif.";
}

if (isset($_GET['rechercher']) && empty($erreurs)) {
    $recherche_faite = true;

    // Construction dynamique de la requête avec paramètres nommés
    $sql    = "SELECT * FROM voitures WHERE 1=1";
    $params = [];

    if (!empty($marque)) {
        $sql .= " AND (marque LIKE :marque OR modele LIKE :marque2)";
        $params[':marque']  = '%' . $marque . '%';
        $params[':marque2'] = '%' . $marque . '%';
    }
    if (!empty($categorie)) {
        $sql .= " AND categorie = :categorie";
        $params[':categorie'] = $categorie;
    }
    if (!empty($prix_max)) {
        $sql .= " AND prix_jour <= :prix_max";
        $params[':prix_max'] = (float)$prix_max;
    }
    $sql .= " ORDER BY prix_jour ASC";

    // prepare() + execute() avec paramètres nommés
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // fetch() en boucle pour récupérer les résultats
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resultats[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Locavo - Recherche</title>
    <link rel="stylesheet" href="../css/style.css">
     <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="../js/banner.js" defer></script>
    <style>
        .search-box { background:#f8f9fa; border:1px solid #dee2e6; border-radius:10px; padding:25px; margin-bottom:25px; }
        .search-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:15px; margin-bottom:15px; }
        .search-grid label { display:block; font-weight:bold; margin-bottom:4px; }
        .search-grid input, .search-grid select { width:100%; padding:9px; border:1px solid #ccc; border-radius:5px; }
        .result-card { display:flex; align-items:center; gap:20px; background:#fff; border:1px solid #ddd; border-radius:10px; padding:15px; margin-bottom:12px; }
        .result-card img { width:110px; height:75px; object-fit:cover; border-radius:8px; flex-shrink:0; }
        .result-card h3 { color:#0e6fa5; margin-bottom:5px; }
        .result-card .prix { font-size:1.2rem; font-weight:bold; color:#e67e22; }
        .badge { background:#0e6fa5; color:#fff; font-size:0.8rem; padding:2px 10px; border-radius:12px; margin-left:6px; }
        .nb { background:#d4edda; border:1px solid #c3e6cb; border-radius:6px; padding:10px 15px; color:#155724; margin-bottom:20px; }
        .vide { background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:20px; text-align:center; color:#856404; }
        .erreurs { background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:15px; color:#721c24; margin-bottom:20px; }
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
            <li><a href="../php/flotte.php">Nos Voitures</a></li>
            <li><a href="../php/recherche.php" class="active">Recherche</a></li>
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
    <h1>Recherche de Véhicules</h1>
    <p>Filtrez selon vos critères</p>
</header>

<div style="max-width:950px; margin:0 auto; padding:20px;">

    <!-- Formulaire de recherche -->
    <div class="search-box">
        <form method="GET" action="recherche.php">
            <div class="search-grid">
                <div>
                    <label>Marque ou modèle</label>
                    <input type="text" name="marque" value="<?= htmlspecialchars($marque) ?>" placeholder="Ex: Renault, BMW...">
                </div>
                <div>
                    <label>Catégorie</label>
                    <select name="categorie">
                        <option value="">-- Toutes --</option>
                        <?php
                        $cats = ['economique' => 'Économique', 'suv' => 'SUV', 'cabriolet' => 'Cabriolet', 'sport' => 'Sport', 'luxe' => 'Luxe'];
                        foreach ($cats as $val => $label):
                        ?>
                        <option value="<?= $val ?>" <?= $categorie === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Prix max / jour (DT)</label>
                    <input type="number" name="prix_max" min="0" value="<?= htmlspecialchars($prix_max) ?>" placeholder="Ex: 200">
                </div>
            </div>
            <button type="submit" name="rechercher" class="btn-orange">Rechercher</button>
            <a href="../php/recherche.php" style="margin-left:10px; color:#888;">Réinitialiser</a>
        </form>
    </div>

    <!-- Erreurs PHP -->
    <?php if (!empty($erreurs)): ?>
        <div class="erreurs"><?php foreach ($erreurs as $e) echo "<p>• $e</p>"; ?></div>
    <?php endif; ?>

    <!-- Résultats -->
    <?php if ($recherche_faite): ?>
        <?php if (!empty($resultats)): ?>
            <div class="nb"><?= count($resultats) ?> véhicule(s) trouvé(s)</div>
            <?php foreach ($resultats as $v): ?>
            <div class="result-card">
                <img src="../images/<?= htmlspecialchars($v['image']) ?>" alt="<?= htmlspecialchars($v['marque']) ?>">
                <div style="flex:1;">
                    <h3>
                        <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                        <span class="badge"><?= htmlspecialchars($v['categorie']) ?></span>
                    </h3>
                    <div class="prix"><?= $v['prix_jour'] ?> DT <small style="font-size:0.75rem; color:#888;">/ jour</small></div>
                </div>
                <a href="../php/reservation.php?marque=<?= urlencode($v['marque']) ?>&modele=<?= urlencode($v['modele']) ?>&prix=<?= urlencode($v['prix_jour']) ?>&categorie=<?= urlencode($v['categorie']) ?>" class="btn-orange" style="text-decoration:none;">Réserver</a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="vide">
                <p>Aucun véhicule ne correspond à vos critères.</p>
                <p style="margin-top:8px; font-size:0.9rem;">Essayez d'élargir votre recherche.</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p style="text-align:center; color:#888; margin-top:20px;">Entrez vos critères et cliquez sur "Rechercher".</p>
    <?php endif; ?>

</div>

    <footer>
        <p>© 2026 Locavo - Tous droits réservés</p>
        <p>Contact : <a href="tel:+21612345678">+216 12 345 678</a> | <a href="mailto:contact@locavo.tn">contact@locavo.tn</a></p>
        <div class="footer-links"><a href="../index.html">Accueil</a><a href="../php/about.php">À propos</a><a href="../html/contact.html">Contact</a><a href="../html/questionnaire.html">Questionnaire</a><a href="../html/funpage.html">Fun Page</a></div>
    </footer>
</body>
</html>
