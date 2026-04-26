/*
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : script.js
   Description : Validation JavaScript des formulaires Contact et Réservation
                 - Champs obligatoires
                 - Regex (email, téléphone)
                 - Contraintes de longueur et de plage
                 - Validation logique (dates)
                 - Messages d'erreur dans la page (pas d'alert)
*/

// ============================================
// 1. Validation du formulaire Contact
// ============================================
function validerContact() {
    let ok = true;

    const nom       = document.getElementById("contact_nom");
    const email     = document.getElementById("contact_email");
    const telephone = document.getElementById("contact_telephone");
    const sujet     = document.getElementById("contact_sujet");
    const message   = document.getElementById("contact_message");

    const errNom   = document.getElementById("err_nom");
    const errEmail = document.getElementById("err_email");
    const errTel   = document.getElementById("err_telephone");
    const errSujet = document.getElementById("err_sujet");
    const errMsg   = document.getElementById("err_message");

    // Reset de tous les messages d'erreur
    [errNom, errEmail, errTel, errSujet, errMsg].forEach(function(el) {
        if (el) el.innerText = "";
    });

    // 1. Nom obligatoire (min 2 caractères — contrainte de longueur)
    if (nom.value.trim().length < 2) {
        errNom.innerText = "Le nom est obligatoire (minimum 2 caractères).";
        ok = false;
    }

    // 2. Email : validation par expression régulière
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email.value.trim())) {
        errEmail.innerText = "Veuillez entrer une adresse email valide (ex: nom@domaine.com).";
        ok = false;
    }

    // 3. Téléphone : si renseigné, doit contenir exactement 8 chiffres (regex)
    if (telephone && telephone.value.trim() !== "") {
        const regexTel = /^[0-9]{8}$/;
        if (!regexTel.test(telephone.value.trim())) {
            errTel.innerText = "Le numéro de téléphone doit contenir exactement 8 chiffres.";
            ok = false;
        }
    }

    // 4. Sujet obligatoire
    if (sujet.value === "") {
        errSujet.innerText = "Veuillez choisir un sujet pour votre demande.";
        ok = false;
    }

    // 5. Message : contrainte de longueur (min 10, max 1000 caractères)
    const msgLen = message.value.trim().length;
    if (msgLen < 10) {
        errMsg.innerText = "Votre message doit contenir au moins 10 caractères.";
        ok = false;
    } else if (msgLen > 1000) {
        errMsg.innerText = "Votre message ne peut pas dépasser 1000 caractères.";
        ok = false;
    }

    return ok;
}
