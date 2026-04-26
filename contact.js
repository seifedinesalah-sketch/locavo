/* =============================================================
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : about.js
   Description : Tableau dynamique avec constructeur, ajout, recherche
   ============================================================= */

// =============================================
// a) DÉFINITION DES OBJETS
// Fonction constructeur pour créer un objet Tarif
// Chaque tarif a 6 propriétés
// =============================================
function Tarif(categorie, jour1, jour3, semaine, mois, modeles) {
    this.categorie = categorie;  // nom de la catégorie (ex: "SUV")
    this.jour1     = jour1;      // prix pour 1 jour en DT
    this.jour3     = jour3;      // prix pour 3 jours en DT
    this.semaine   = semaine;    // prix pour 1 semaine en DT
    this.mois      = mois;       // prix pour 1 mois en DT
    this.modeles   = modeles;    // liste des modèles disponibles
}

// =============================================
// b) COLLECTIONS
// Tableau (array) contenant des objets Tarif
// Initialisé avec les données réelles du site
// =============================================
var tarifs = [
    // Chaque élément est un objet créé avec le constructeur Tarif
    new Tarif("Économique", 90, 250, 530, 1700, "Hyundai Ioniq 6, Renault 5 E-TECH, Tesla Model Y"),
    new Tarif("SUV", 180, 500, 1100, 3400, "Peugeot 3008, Mercedes-Benz GLS, Lamborghini URUS"),
    new Tarif("Cabriolet", 250, 680, 1550, 5000, "Mini Cooper, Mazda MX-5, BMW Z4"),
    new Tarif("Sport", 620, 1700, 3700, 12500, "Ferrari SF90, Aston-Martin Vantage, Lamborghini SVJ")
];

// =============================================
// c) + d) GÉNÉRATION DYNAMIQUE DU TABLEAU
// Fonction qui affiche les données dans le tableau HTML
// Elle reçoit une liste d'objets Tarif en paramètre
// =============================================
function afficherTarifs(liste) {

    // Récupérer le corps du tableau HTML par son id
    var tbody = document.getElementById("tarif-body");

    // Vider le contenu actuel du tableau
    // pour éviter de répéter les anciennes lignes
    tbody.innerHTML = "";

    // Parcourir tous les objets de la liste
    for (var i = 0; i < liste.length; i++) {

        // Créer une nouvelle ligne <tr>
        var tr = document.createElement("tr");

        // Créer la cellule pour la catégorie
        var tdCat = document.createElement("td");
        tdCat.textContent = liste[i].categorie; // mettre le texte
        tdCat.className = "category-cell";       // ajouter le style CSS
        tr.appendChild(tdCat);                   // ajouter la cellule à la ligne

        // Mettre les 4 prix dans un tableau temporaire
        var prix = [liste[i].jour1, liste[i].jour3, liste[i].semaine, liste[i].mois];

        // Créer une cellule pour chaque prix
        for (var j = 0; j < prix.length; j++) {
            var td = document.createElement("td");
            td.textContent = prix[j] + " DT";  // afficher le prix avec "DT"
            td.className = "price";              // ajouter le style CSS
            tr.appendChild(td);                  // ajouter la cellule à la ligne
        }

        // Créer la cellule pour les modèles
        var tdMod = document.createElement("td");
        tdMod.innerHTML = "<em>" + liste[i].modeles + "</em>"; // en italique
        tr.appendChild(tdMod); // ajouter la cellule à la ligne

        // Ajouter la ligne complète au corps du tableau
        tbody.appendChild(tr);
    }
}

// =============================================
// d) FONCTION D'AJOUT DYNAMIQUE
// Crée un nouvel objet Tarif et l'ajoute au tableau
// puis met à jour l'affichage
// =============================================
function ajouterTarif(categorie, jour1, jour3, semaine, mois, modeles) {

    // Créer un nouvel objet avec le constructeur
    var nouveau = new Tarif(categorie, jour1, jour3, semaine, mois, modeles);

    // Ajouter cet objet à la fin du tableau tarifs
    tarifs.push(nouveau);

    // Réafficher le tableau avec la nouvelle ligne
    afficherTarifs(tarifs);
}

// =============================================
// d) FONCTION DE RECHERCHE
// Cherche un mot-clé dans les catégories et les modèles
// Retourne un tableau contenant les résultats trouvés
// =============================================
function rechercherTarif(motCle) {

    // Tableau vide pour stocker les résultats
    var resultats = [];

    // Convertir le mot-clé en minuscules pour ignorer la casse
    var mot = motCle.toLowerCase();

    // Parcourir tous les tarifs
    for (var i = 0; i < tarifs.length; i++) {

        // Vérifier si le mot-clé est dans la catégorie OU dans les modèles
        if (tarifs[i].categorie.toLowerCase().indexOf(mot) !== -1 ||
            tarifs[i].modeles.toLowerCase().indexOf(mot) !== -1) {

            // Si oui, on ajoute ce tarif dans les résultats
            resultats.push(tarifs[i]);
        }
    }

    // Retourner le tableau des résultats
    return resultats;
}

// =============================================
// e) GESTION DES FORMULAIRES
// On attend que la page soit complètement chargée
// avant d'accéder aux éléments HTML
// =============================================
document.addEventListener("DOMContentLoaded", function () {

    // Afficher le tableau complet dès le chargement de la page
    afficherTarifs(tarifs);

    // ----- FORMULAIRE D'AJOUT -----
    // On écoute l'événement "submit" du formulaire d'ajout
    document.getElementById("form-ajout").addEventListener("submit", function (e) {

        // Empêcher le rechargement de la page
        e.preventDefault();

        // Récupérer les valeurs saisies dans les champs
        var cat = document.getElementById("add-categorie").value.trim(); // catégorie
        var j1  = parseInt(document.getElementById("add-jour1").value);  // prix 1 jour
        var j3  = parseInt(document.getElementById("add-jour3").value);  // prix 3 jours
        var sem = parseInt(document.getElementById("add-semaine").value); // prix 1 semaine
        var mo  = parseInt(document.getElementById("add-mois").value);   // prix 1 mois
        var mod = document.getElementById("add-modeles").value.trim();   // modèles

        // Appeler la fonction pour ajouter le nouveau tarif
        ajouterTarif(cat, j1, j3, sem, mo, mod);

        // Afficher un message de confirmation
        var msg = document.getElementById("msg-ajout");
        msg.textContent = "Catégorie " + cat + " ajoutée !";
        msg.style.color = "#27ae60"; // couleur verte

        // Vider les champs du formulaire
        this.reset();
    });

    // ----- FORMULAIRE DE RECHERCHE -----
    // On écoute l'événement "submit" du formulaire de recherche
    document.getElementById("form-recherche").addEventListener("submit", function (e) {

        // Empêcher le rechargement de la page
        e.preventDefault();

        // Récupérer le mot-clé saisi
        var mot = document.getElementById("search-mot").value.trim();

        // Sélectionner la zone de message
        var msg = document.getElementById("msg-recherche");

        // Si le champ est vide, on affiche tout et on sort
        if (mot === "") {
            afficherTarifs(tarifs);
            msg.textContent = "";
            return;
        }

        // Appeler la fonction de recherche
        var resultats = rechercherTarif(mot);

        // Afficher uniquement les résultats trouvés
        afficherTarifs(resultats);

        // Afficher un message selon le nombre de résultats
        if (resultats.length > 0) {
            // Résultats trouvés
            msg.textContent = resultats.length + " résultat(s) pour " + mot + ".";
            msg.style.color = "#0e6fa5"; // couleur bleue
        } else {
            // Aucun résultat
            msg.textContent = "Aucun résultat pour " + mot + ".";
            msg.style.color = "#c0392b"; // couleur rouge
        }
    });

    // ----- BOUTON AFFICHER TOUT -----
    // Quand on clique sur ce bouton, on réaffiche tout le tableau
    document.getElementById("btn-tout").addEventListener("click", function () {

        // Réafficher le tableau complet
        afficherTarifs(tarifs);

        // Vider le message de recherche
        document.getElementById("msg-recherche").textContent = "";

        // Vider le champ de recherche
        document.getElementById("search-mot").value = "";
    });
});