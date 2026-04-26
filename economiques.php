<?php
require_once '../config.php';

// Suppression d'un véhicule (DELETE)
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $stmt = $pdo->prepare("DELETE FROM voitures WHERE id_v = ?"); // prepare() positionnel
    $stmt->execute([(int)$_GET['supprimer']]);
    header("Location: admin.php?msg=supprime");
    exit;
}

// Récupération de tous les véhicules (SELECT + fetchAll)
$voitures = $pdo->query("SELECT * FROM voitures ORDER BY id_v ASC")->fetchAll();

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Locavo - Administration</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <style>
        table { width:100%; border-collapse:collapse; }
        th { background:#0e6fa5; color:#fff; padding:12px; text-align:left; }
        td { padding:10px; border-bottom:1px solid #eee; vertical-align:middle; }
        tr:hover { background:#f5f5f5; }
        .btn-edit   { background:#f39c12; color:#fff; padding:5px 12px; border-radius:5px; text-decoration:none; font-size:0.85rem; margin-right:5px; }
        .btn-delete { background:#e74c3c; color:#fff; padding:5px 12px; border-radius:5px; text-decoration:none; font-size:0.85rem; }
        .alert { background:#d4edda; border:1px solid #c3e6cb; color:#155724; padding:12px 20px; border-radius:8px; margin-bottom:20px; }
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

<div style="max-width:1000px; margin:150px auto 0; padding:20px;">

    <h2>Gestion de la Flotte</h2>
    <p>Ajouter, modifier ou supprimer des véhicules.</p>

    <?php if ($msg === 'supprime'): ?>
        <div class="alert">Véhicule supprimé avec succès.</div>
    <?php endif; ?>

    <a href="../php/ajouter_voiture.php" class="btn-orange" style="display:inline-block; margin-bottom:20px; text-decoration:none;">
        + Ajouter un véhicule
    </a>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Marque</th><th>Modèle</th>
                <th>Catégorie</th><th>Prix/Jour</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($voitures as $v): ?>
            <tr>
                <td><?= $v['id_v'] ?></td>
                <td><strong><?= htmlspecialchars($v['marque']) ?></strong></td>
                <td><?= htmlspecialchars($v['modele']) ?></td>
                <td><?= htmlspecialchars($v['categorie'] ?? '-') ?></td>
                <td><?= $v['prix_jour'] ?> DT</td>
                <td>
                    <a href="modifier_voiture.php?id=<?= $v['id_v'] ?>" class="btn-edit">Modifier</a>
                    <a href="admin.php?supprimer=<?= $v['id_v'] ?>"
                       onclick="return confirm('Supprimer ce véhicule ?')"
                       class="btn-delete">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<footer style="margin-top:50px;"><p>© 2026 Locavo - Administration</p></footer>
</body>
</html>
