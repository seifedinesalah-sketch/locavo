/*
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : reservation.js
   Description : Validation JavaScript complète du formulaire de réservation
                 - Champs obligatoires
                 - Regex (email, téléphone, permis)
                 - Contraintes de plage (âge, dates)
                 - Validation logique (date retour > date départ)
                 - Messages d'erreur dans la page (pas d'alert)
*/

function validerReservation() {
    let ok = true;

    // ── Récupération des champs ──────────────────────────────
    const nom       = document.getElementById("nom");
    const email     = document.getElementById("email");
    const tel       = document.getElementById("telephone");
    const age       = document.getElementById("age");
    const permis    = document.getElementById("permis");
    const depart    = document.getElementById("date_depart");
    const retour    = document.getElementById("date_retour");
    const categorie = document.getElementById("categorie");

    // ── Récupération des spans d'erreur ──────────────────────
    const errNom       = document.getElementById("err_nom");
    const errEmail     = document.getElementById("err_email");
    const errTel       = document.getElementById("err_telephone");
    const errAge       = document.getElementById("err_age");
    const errPermis    = document.getElementById("err_permis");
    const errDepart    = document.getElementById("err_date_depart");
    const errRetour    = document.getElementById("err_date_retour");
    const errCategorie = document.getElementById("err_categorie");

    // ── Reset de tous les messages ───────────────────────────
    [errNom, errEmail, errTel, errAge, errPermis,
     errDepart, errRetour, errCategorie].forEach(function(el) {
        if (el) el.innerText = "";
    });

    // 1. Nom obligatoire (min 2 caractères)
    if (nom.value.trim().length < 2) {
        errNom.innerText = "Le nom complet est obligatoire (min. 2 caractères).";
        ok = false;
    }

    // 2. Email : validation par expression régulière
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email.value.trim())) {
        errEmail.innerText = "Veuillez entrer une adresse email valide (ex: nom@domaine.com).";
        ok = false;
    }

    // 3. Téléphone : exactement 8 chiffres (regex)
    const regexTel = /^[0-9]{8}$/;
    if (!regexTel.test(tel.value.trim())) {
        errTel.innerText = "Le téléphone doit contenir exactement 8 chiffres.";
        ok = false;
    }

    // 4. Âge : contrainte de plage (18 – 99 ans)
    const ageVal = parseInt(age.value);
    if (isNaN(ageVal) || ageVal < 18 || ageVal > 99) {
        errAge.innerText = "Vous devez avoir entre 18 et 99 ans pour louer un véhicule.";
        ok = false;
    }

    // 5. Permis : si renseigné, doit contenir 8 chiffres (regex)
    if (permis && permis.value.trim() !== "") {
        const regexPermis = /^[0-9]{8}$/;
        if (!regexPermis.test(permis.value.trim())) {
            errPermis.innerText = "Le numéro de permis doit contenir exactement 8 chiffres.";
            ok = false;
        }
    }

    // 6. Date de départ obligatoire et >= aujourd'hui
    const aujourd_hui = new Date();
    aujourd_hui.setHours(0, 0, 0, 0);
    if (depart.value === "") {
        errDepart.innerText = "La date de départ est requise.";
        ok = false;
    } else if (new Date(depart.value) < aujourd_hui) {
        errDepart.innerText = "La date de départ ne peut pas être dans le passé.";
        ok = false;
    }

    // 7. Date de retour obligatoire et validation logique > départ
    if (retour.value === "") {
        errRetour.innerText = "La date de retour est requise.";
        ok = false;
    } else if (depart.value !== "" && new Date(retour.value) <= new Date(depart.value)) {
        errRetour.innerText = "La date de retour doit être strictement après la date de départ.";
        ok = false;
    }

    // 8. Catégorie obligatoire
    if (categorie && categorie.value === "") {
        errCategorie.innerText = "Veuillez sélectionner une catégorie de véhicule.";
        ok = false;
    }

    return ok;
}
