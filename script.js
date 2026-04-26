/* =============================================================
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : questionnaire.js
   Description : Validation JavaScript du questionnaire
                 Validation de 5 champs : nom, email, âge,
                 satisfaction et commentaire
   ============================================================= */

// On attend que la page soit complètement chargée
document.addEventListener("DOMContentLoaded", function () {

    // =============================================
    // SÉLECTION DES ÉLÉMENTS HTML
    // =============================================

    // Le formulaire complet
    var form = document.getElementById("form-questionnaire");

    // Le curseur de note (range)
    var rangeNote = document.getElementById("q-note");

    // Le texte qui affiche la valeur actuelle de la note
    var noteDisplay = document.getElementById("note-display");

    // =============================================
    // MISE À JOUR DE LA NOTE EN TEMPS RÉEL
    // Quand le curseur bouge, on met à jour le texte affiché
    // =============================================
    rangeNote.addEventListener("input", function () {
        // On affiche la valeur actuelle du curseur
        noteDisplay.textContent = rangeNote.value;
    });

    // =============================================
    // RÉINITIALISATION DU FORMULAIRE
    // Quand on clique sur le bouton "Réinitialiser"
    // on remet la note affichée à 7 (valeur par défaut)
    // =============================================
    form.addEventListener("reset", function () {
        // setTimeout avec 0ms pour que le reset se fasse d'abord
        // puis on met à jour l'affichage après
        setTimeout(function () {
            noteDisplay.textContent = "7";
        }, 0);
    });

    // =============================================
    // SOUMISSION ET VALIDATION DU FORMULAIRE
    // =============================================
    form.addEventListener("submit", function (e) {

        // Empêcher le rechargement de la page
        e.preventDefault();

        // ---- Récupérer les valeurs des champs ----

        // Récupérer le nom et enlever les espaces au début et à la fin
        var nom = document.getElementById("q-nom").value.trim();

        // Récupérer l'email
        var email = document.getElementById("q-email").value.trim();

        // Récupérer l'âge
        var age = document.getElementById("q-age").value;

        // Récupérer le commentaire
        var commentaire = document.getElementById("q-commentaire").value.trim();

        // ---- Vérifier les boutons radio de satisfaction ----

        // Récupérer tous les boutons radio qui ont le name "satisfaction"
        var radios = document.getElementsByName("satisfaction");

        // Variable pour savoir si un bouton radio est coché
        var satisfOk = false;

        // Parcourir tous les boutons radio
        for (var i = 0; i < radios.length; i++) {
            // Si un bouton est coché, on met satisfOk à true
            if (radios[i].checked) {
                satisfOk = true;
                break; // On arrête la boucle car on a trouvé
            }
        }

        // ---- Effacer les anciens messages d'erreur ----

        // Sélectionner tous les éléments qui ont la classe "err"
        var erreurs = document.querySelectorAll(".err");

        // Vider chaque message d'erreur
        for (var j = 0; j < erreurs.length; j++) {
            erreurs[j].textContent = "";
        }

        // Vider aussi le message de résultat
        document.getElementById("resultat-questionnaire").innerHTML = "";

        // ---- Validation des champs ----

        // Variable qui sera false si au moins un champ est invalide
        var valide = true;

        // 1) Validation du nom : au moins 3 caractères
        if (nom.length < 3) {
            document.getElementById("err-nom").textContent =
                "Le nom doit contenir au moins 3 caractères.";
            valide = false;
        }

        // 2) Validation de l'email : doit contenir @ et . et au moins 5 caractères
        if (email.indexOf("@") === -1 || email.indexOf(".") === -1 || email.length < 5) {
            document.getElementById("err-email").textContent =
                "Veuillez entrer un email valide.";
            valide = false;
        }

        // 3) Validation de l'âge : doit être entre 18 et 99
        if (age === "" || parseInt(age) < 18 || parseInt(age) > 99) {
            document.getElementById("err-age").textContent =
                "L'âge doit être compris entre 18 et 99.";
            valide = false;
        }

        // 4) Validation de la satisfaction : un bouton radio doit être coché
        if (!satisfOk) {
            document.getElementById("err-satisfaction").textContent =
                "Veuillez indiquer votre satisfaction.";
            valide = false;
        }

        // 5) Validation du commentaire : au moins 10 caractères
        if (commentaire.length < 10) {
            document.getElementById("err-commentaire").textContent =
                "Le commentaire doit contenir au moins 10 caractères.";
            valide = false;
        }

        // ---- Si tous les champs sont valides ----
        if (valide) {

            // Soumettre le formulaire vers le script PHP (questionnaire.php)
            // La validation JS est passée : on laisse PHP recevoir et traiter les données
            form.submit();
        }
    });
});