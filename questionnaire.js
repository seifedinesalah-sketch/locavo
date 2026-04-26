/* =============================================================
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : contact.js
   Description : Galerie d'images automatique
                 Les images changent toutes les 4 secondes
   ============================================================= */

// On attend que la page soit complètement chargée
document.addEventListener("DOMContentLoaded", function () {

    // Tableau d'objets contenant les images de la galerie
    // Chaque objet a 2 propriétés : src (chemin) et legende (texte)
    var images = [
        { src: "../images/cabrio1.jpg", legende: "Mini Cooper Convertible" },
        { src: "../images/suv1.jpg",    legende: "Peugeot 3008" },
        { src: "../images/sport1.jpg",  legende: "Lamborghini SVJ" },
        { src: "../images/eco1.jpg",    legende: "Tesla Model Y" },
        { src: "../images/cabrio3.jpg", legende: "BMW Z4" }
    ];

    // Variable qui garde l'index de l'image actuellement affichée
    // On commence à 0 (première image)
    var indexImg = 0;

    // On récupère l'élément <img> de la galerie dans le HTML
    var imgEl = document.getElementById("galerie-img");

    // On récupère le paragraphe qui affiche la légende
    var legendeEl = document.getElementById("galerie-legende");

    // Si les éléments n'existent pas sur cette page, on arrête
    // Cela évite les erreurs sur les autres pages
    if (!imgEl || !legendeEl) return;

    // Fonction qui passe à l'image suivante
    function imageSuivante() {

        // On passe à l'index suivant
        indexImg++;

        // Si on dépasse la dernière image, on revient à la première
        if (indexImg >= images.length) indexImg = 0;

        // On rend l'image invisible (opacité à 0)
        // Cela crée un effet de transition douce
        imgEl.style.opacity = "0";

        // Après 400 millisecondes (0.4 seconde)
        // on change l'image et on la rend visible
        setTimeout(function () {

            // On change la source de l'image
            imgEl.src = images[indexImg].src;

            // On change le texte alternatif
            imgEl.alt = images[indexImg].legende;

            // On met à jour la légende sous l'image
            // Exemple : "BMW Z4  (5 / 5)"
            legendeEl.textContent =
                images[indexImg].legende +
                "  (" + (indexImg + 1) + " / " + images.length + ")";

            // On rend l'image visible à nouveau
            imgEl.style.opacity = "1";

        }, 400); // 400ms = durée de la transition
    }

    // On appelle imageSuivante() toutes les 4000 millisecondes (= 4 secondes)
    // Chaque image reste affichée au moins 4 secondes
    setInterval(imageSuivante, 4000);
});