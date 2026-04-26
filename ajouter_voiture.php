<?php
// Classe représentant un enregistrement de la table voitures
class Voiture {

    private $id, $marque, $modele, $prixJour, $image, $categorie;

    public function __construct($id, $marque, $modele, $prixJour, $image, $categorie) {
        $this->id        = $id;
        $this->marque    = $marque;
        $this->modele    = $modele;
        $this->prixJour  = $prixJour;
        $this->image     = $image;
        $this->categorie = $categorie;
    }

    // Getters
    public function getId()        { return $this->id; }
    public function getMarque()    { return $this->marque; }
    public function getModele()    { return $this->modele; }
    public function getPrixJour()  { return $this->prixJour; }
    public function getImage()     { return $this->image; }
    public function getCategorie() { return $this->categorie; }

    // Setters
    public function setMarque($v)    { $this->marque    = $v; }
    public function setModele($v)    { $this->modele    = $v; }
    public function setPrixJour($v)  { $this->prixJour  = $v; }
    public function setImage($v)     { $this->image     = $v; }
    public function setCategorie($v) { $this->categorie = $v; }

    // Retourne une description courte
    public function getDescription() {
        return $this->marque . ' ' . $this->modele . ' — ' . $this->prixJour . ' DT/jour';
    }

    // Retourne le badge de la catégorie
    public function getBadgeCategorie() {
        $badges = [
            'economique' => '🟢 Économique',
            'suv'        => '🔵 SUV',
            'cabriolet'  => '🟡 Cabriolet',
            'sport'      => '🔴 Sport',
            'luxe'       => '💎 Luxe',
        ];
        return $badges[$this->categorie] ?? $this->categorie;
    }
}

// Charge toutes les voitures depuis la BD, retourne un tableau d'objets Voiture
function chargerVoitures(PDO $pdo) {
    $stmt = $pdo->query("SELECT * FROM voitures ORDER BY prix_jour ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetchAll()

    $liste = [];
    foreach ($rows as $row) {
        $liste[] = new Voiture(
            $row['id_v'], $row['marque'], $row['modele'],
            $row['prix_jour'], $row['image'], $row['categorie']
        );
    }
    return $liste;
}

// Affiche toutes les voitures dans un tableau HTML
function afficherTableauVoitures($voitures) {
    if (empty($voitures)) {
        echo '<p>Aucun véhicule disponible.</p>';
        return;
    }
    echo '<table style="width:100%; border-collapse:collapse; margin-top:20px;">';
    echo '<thead><tr style="background:#0e6fa5; color:#fff;">
            <th style="padding:10px;">#</th>
            <th style="padding:10px;">Image</th>
            <th style="padding:10px;">Marque / Modèle</th>
            <th style="padding:10px;">Catégorie</th>
            <th style="padding:10px;">Prix / Jour</th>
            <th style="padding:10px;">Action</th>
          </tr></thead><tbody>';

    foreach ($voitures as $v) {
        $marque = urlencode($v->getMarque());
        $modele = urlencode($v->getModele());
        // Couleur de ligne selon la gamme de prix
        if ($v->getPrixJour() < 100)     $bg = '#f0fff4';
        elseif ($v->getPrixJour() < 200) $bg = '#fff8e1';
        else                             $bg = '#fff0f0';

        echo "<tr style='background:$bg; border-bottom:1px solid #ddd;'>
                <td style='padding:10px; text-align:center;'>{$v->getId()}</td>
                <td style='padding:10px; text-align:center;'>
                    <img src='../images/{$v->getImage()}' style='height:55px; width:85px; object-fit:cover; border-radius:6px;'>
                </td>
                <td style='padding:10px;'>
                    <strong>{$v->getMarque()}</strong><br>
                    <span style='color:#666; font-size:0.9rem;'>{$v->getModele()}</span>
                </td>
                <td style='padding:10px; text-align:center;'>{$v->getBadgeCategorie()}</td>
                <td style='padding:10px; text-align:center; font-weight:bold; color:#0e6fa5;'>
                    {$v->getPrixJour()} DT
                </td>
                <td style='padding:10px; text-align:center;'>
                    <a href='../php/reservation.php?marque={$marque}&modele={$modele}&prix={$v->getPrixJour()}&categorie={$v->getCategorie()}'
                       style='background:#f39c12; color:white; padding:7px 15px; border-radius:5px; text-decoration:none; font-weight:bold; font-size:0.85rem;'>
                       Réserver
                    </a>
                </td>
              </tr>";
    }
    echo '</tbody></table>';
}
?>
