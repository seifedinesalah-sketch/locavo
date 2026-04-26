<?php
require_once '../config.php';

$success = false;
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $marque    = trim($_POST['marque']    ?? '');
    $modele    = trim($_POST['modele']    ?? '');
    $prix      = trim($_POST['prix']      ?? '');
    $image     = trim($_POST['image']     ?? '');
    $categorie = trim($_POST['categorie'] ?? '');

    // Revalidation PHP côté serveur (complète les validations HTML)
    if (strlen($marque) < 2 || strlen($marque) > 50)
        $erreurs[] = "La marque est obligatoire (2 à 50 caractères).";
    elseif (!preg_match('/^[a-zA-ZÀ-ÿ0-9\s\-]+$/', $marque))
        $erreurs[] = "La marque ne doit contenir que des lettres, chiffres et tirets.";

    if (strlen($modele) < 2 || strlen($modele) > 100)
        $erreurs[] = "Le modèle est obligatoire (2 à 100 caractères).";

    if (!is_numeric($prix) || (float)$prix <= 0 || (float)$prix > 9999)
        $erreurs[] = "Le prix doit être un nombre positif entre 1 et 9999 DT.";

    $cats_valides = ['economique', 'suv', 'cabriolet', 'sport', 'luxe'];
    if (!in_array($categorie, $cats_valides))
        $erreurs[] = "Veuillez choisir une catégorie valide.";

    if (!empty($image) && !preg_match('/\.(jpg|jpeg|png|webp)$/i', $image))
        $erreurs[] = "Le nom de l'image doit se terminer par .jpg, .jpeg, .png ou .webp.";

    if (empty($erreurs)) {
        // INSERT avec prepare() + execute() positionnel
        $stmt = $pdo->prepare("INSERT INTO voitures (marque, modele, prix_jour, image, categorie) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$marque, $modele, (float)$prix, $image, $categorie]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Locavo - Ajouter un véhicule</title>
    <link rel="stylesheet" href="../css/style.css">
     <link rel="icon" type="image/png" href="../images/logo.png">
    <style>
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; font-weight:bold; margin-bottom:4px; }
        .form-group input, .form-group select { width:100%; padding:9px; border:1px solid #ccc; border-radius:5px; }
        .erreurs { background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; padding:15px; margin-bottom:20px; color:#721c24; }
        .succes  { background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; padding:15px; margin-bottom:20px; color:#155724; text-align:center; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo-container">
        <img src="../images/logo.png" alt="Locavo" class="logo-img">
        <span class="logo-text">Locavo Admin</span>
    </div>
    <ul class="nav-links">
        <li><a href="../php/admin.php" class="active">Gestion Flotte</a></li>
        <li><a href="../php/ajouter_voiture.php">Ajouter Voiture</a></li>
        <li><a href="../index.html" style="opacity:0.7;">← Retour site</a></li>
    </ul>
</nav>

<div class="form-container" style="margin-top:150px; max-width:500px;">
    <h2>Ajouter un véhicule</h2>

    <?php if ($success): ?>
        <div class="succes">
            <p><strong>Véhicule ajouté avec succès !</strong></p>
            <a href="../php/admin.php" class="btn-orange" style="text-decoration:none; display:inline-block; margin-top:10px;">Retour à l'admin</a>
            <a href="../php/ajouter_voiture.php" style="margin-left:10px;">Ajouter un autre</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($erreurs)): ?>
        <div class="erreurs">
            <?php foreach ($erreurs as $e): ?>
                <p>• <?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Marque *</label>
            <input type="text" name="marque" required minlength="2"
                   value="<?= htmlspecialchars($_POST['marque'] ?? '') ?>" placeholder="Ex: BMW">
        </div>
        <div class="form-group">
            <label>Modèle *</label>
            <input type="text" name="modele" required minlength="2"
                   value="<?= htmlspecialchars($_POST['modele'] ?? '') ?>" placeholder="Ex: Série 4">
        </div>
        <div class="form-group">
            <label>Catégorie *</label>
            <select name="categorie" required>
                <option value="">-- Choisir --</option>
                <?php
                $cats = ['economique' => 'Économique', 'suv' => 'SUV', 'cabriolet' => 'Cabriolet', 'sport' => 'Sport', 'luxe' => 'Luxe'];
                foreach ($cats as $val => $label):
                ?>
                <option value="<?= $val ?>" <?= ($_POST['categorie'] ?? '') === $val ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Prix par jour (DT) *</label>
            <input type="number" name="prix" required min="1"
                   value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" placeholder="Ex: 150">
        </div>
        <div class="form-group">
            <label>Nom de l'image</label>
            <input type="text" name="image"
                   value="<?= htmlspecialchars($_POST['image'] ?? '') ?>" placeholder="Ex: bmw4.jpg">
        </div>
        <button type="submit" class="btn-orange">Enregistrer</button>
        <a href="../php/admin.php" style="margin-left:15px; color:#888;">Annuler</a>
    </form>
</div>

<footer style="margin-top:50px;"><p>© 2026 Locavo</p></footer>
</body>
</html>
