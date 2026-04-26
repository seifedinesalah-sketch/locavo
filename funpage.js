/* =============================================================
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : banner.js
   Description : Bannière animée commune à toutes les pages
                 Affiche la date et l'heure en temps réel
   ============================================================= */

// On attend que la page soit complètement chargée
document.addEventListener("DOMContentLoaded", function () {

    // On récupère le span qui va contenir le message de la bannière
    var msg = document.getElementById("banniere-msg");

    // Si le span n'existe pas sur cette page, on arrête tout
    // Cela évite les erreurs sur les pages sans bannière
    if (!msg) return;

    // Fonction qui met à jour le texte de la bannière
    function majBanniere() {

        // On crée un objet Date qui contient la date et l'heure actuelles
        var now = new Date();

        // On formate la date en français
        // Exemple : "lundi 24 mars 2026"
        var dateStr = now.toLocaleDateString("fr-FR", {
            weekday: "long",   // jour de la semaine en toutes lettres
            year: "numeric",   // année en chiffres
            month: "long",     // mois en toutes lettres
            day: "numeric"     // jour du mois en chiffres
        });

        // On formate l'heure en français
        // Exemple : "14:30:05"
        var heureStr = now.toLocaleTimeString("fr-FR");

        // On met à jour le texte de la bannière
        msg.textContent =
            "Bienvenu au site web Locavo ! Aujourd'hui " +
            dateStr +
            ", et l'heure actuelle est " +
            heureStr;
    }

    // On appelle la fonction une première fois immédiatement
    // pour que la bannière s'affiche tout de suite
    majBanniere();

    // Ensuite on rappelle la fonction toutes les 1000 millisecondes (= 1 seconde)
    // Cela permet de mettre à jour l'heure en temps réel
    setInterval(majBanniere, 1000);
});