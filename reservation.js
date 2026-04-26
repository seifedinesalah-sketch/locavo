/* =============================================================
   Projet Locavo – Technologies Web – ENSI 2025/2026
   Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
   Fichier : funpage.js
   Description : Jeu Memory Cars
                 - Création dynamique du plateau avec le DOM
                 - Gestion des clics avec addEventListener
                 - Propagation des événements (bubbling)
                 - Blocage avec stopPropagation()
   ============================================================= */

// On attend que la page soit complètement chargée
document.addEventListener("DOMContentLoaded", function () {

    // =============================================
    // DONNÉES DU JEU
    // 6 marques = 12 cartes (chaque marque apparaît 2 fois)
    // =============================================
    var marques = [
        { nom: "Ferrari" },
        { nom: "BMW" },
        { nom: "Mercedes" },
        { nom: "Lamborghini" },
        { nom: "Porsche" },
        { nom: "Audi" }
    ];

    // =============================================
    // VARIABLES D'ÉTAT DU JEU
    // =============================================

    var cartes = [];       // tableau qui contiendra les 12 cartes mélangées
    var carte1 = null;     // première carte retournée par le joueur
    var carte2 = null;     // deuxième carte retournée par le joueur
    var verrouille = false; // empêche de cliquer pendant la vérification
    var tentatives = 0;    // nombre de tentatives du joueur
    var paires = 0;        // nombre de paires trouvées

    // =============================================
    // SÉLECTION DES ÉLÉMENTS HTML
    // =============================================

    // La zone où les cartes seront affichées
    var plateau = document.getElementById("plateau");

    // Les compteurs affichés sur la page
    var spanTent = document.getElementById("compteur-tentatives");
    var spanPaires = document.getElementById("compteur-paires");

    // Le message de fin de partie
    var msgFin = document.getElementById("msg-fin");

    // Le journal qui affiche les événements de propagation
    var eventLog = document.getElementById("event-log");

    // =============================================
    // FONCTION : MÉLANGER UN TABLEAU
    // Algorithme de Fisher-Yates
    // Mélange les éléments de façon aléatoire
    // =============================================
    function melanger(tab) {
        for (var i = tab.length - 1; i > 0; i--) {
            // Choisir un index aléatoire entre 0 et i
            var j = Math.floor(Math.random() * (i + 1));

            // Échanger les deux éléments
            var temp = tab[i];
            tab[i] = tab[j];
            tab[j] = temp;
        }
        return tab;
    }

    // =============================================
    // FONCTION : ÉCRIRE DANS LE JOURNAL
    // Ajoute un message coloré dans le journal
    // =============================================
    function logEvent(texte, couleur) {
        // Créer un nouveau paragraphe
        var p = document.createElement("p");
        p.textContent = texte;
        p.style.color = couleur || "#333";
        p.style.margin = "4px 0";

        // Ajouter le paragraphe au journal
        eventLog.appendChild(p);

        // Faire défiler le journal vers le bas
        eventLog.scrollTop = eventLog.scrollHeight;
    }

    // =============================================
    // FONCTION : CRÉER LE PLATEAU DE JEU
    // Génère dynamiquement les 12 cartes
    // =============================================
    function creerPlateau() {

        // Vider le plateau actuel
        plateau.innerHTML = "";

        // Réinitialiser toutes les variables
        carte1 = null;
        carte2 = null;
        verrouille = false;
        tentatives = 0;
        paires = 0;

        // Remettre les compteurs à zéro sur la page
        spanTent.textContent = "0";
        spanPaires.textContent = "0";
        msgFin.textContent = "";

        // Remettre le journal à son état initial
        eventLog.innerHTML = "<p><em>Cliquez sur une carte pour voir la propagation…</em></p>";

        // Vider le tableau de cartes
        cartes = [];

        // Créer 2 cartes par marque (pour faire des paires)
        for (var i = 0; i < marques.length; i++) {
            cartes.push({ nom: marques[i].nom }); // première carte
            cartes.push({ nom: marques[i].nom }); // deuxième carte (la paire)
        }

        // Mélanger les cartes aléatoirement
        cartes = melanger(cartes);

        // Créer les éléments HTML pour chaque carte
        for (var k = 0; k < cartes.length; k++) {

            // Créer un div pour la carte
            var div = document.createElement("div");

            // Ajouter la classe CSS pour le style
            div.className = "memory-carte";

            // Stocker le nom de la marque dans un attribut data
            div.dataset.nom = cartes[k].nom;

            // Stocker l'index de la carte (utile pour stopPropagation)
            div.dataset.index = k;

            // Afficher un "?" car la carte est face cachée
            div.textContent = "?";

            // Ajouter un écouteur de clic sur cette carte
            div.addEventListener("click", gererClic);

            // Ajouter la carte au plateau
            plateau.appendChild(div);
        }
    }

    // =============================================
    // FONCTION : GÉRER LE CLIC SUR UNE CARTE
    // C'est ici que se passe toute la logique du jeu
    // =============================================
    function gererClic(e) {

        // Si le plateau est verrouillé, on ignore le clic
        // (cela arrive quand 2 cartes sont en train d'être vérifiées)
        if (verrouille) return;

        // Si la carte est déjà retournée ou déjà trouvée, on ignore
        if (this.classList.contains("retournee") || this.classList.contains("trouvee")) return;

        // ---- STOP PROPAGATION ----
        // Si c'est la carte d'index 6, on bloque la propagation
        // L'événement ne remontera pas vers les zones parentes
        if (this.dataset.index === "6") {
            e.stopPropagation();
            logEvent("stopPropagation() sur la carte index 6 : l'événement ne remonte pas.", "#e74c3c");
        }

        // Retourner la carte : afficher le nom de la marque
        this.textContent = this.dataset.nom;

        // Ajouter la classe "retournee" pour changer le style
        this.classList.add("retournee");

        // ---- LOGIQUE DU JEU ----

        if (carte1 === null) {
            // C'est la première carte retournée
            // On la garde en mémoire
            carte1 = this;

        } else {
            // C'est la deuxième carte retournée
            carte2 = this;

            // Verrouiller le plateau pendant la vérification
            verrouille = true;

            // Incrémenter le compteur de tentatives
            tentatives++;
            spanTent.textContent = tentatives;

            // Vérifier si les deux cartes ont le même nom
            if (carte1.dataset.nom === carte2.dataset.nom) {

                // ---- PAIRE TROUVÉE ----

                // Marquer les deux cartes comme "trouvées"
                carte1.classList.add("trouvee");
                carte2.classList.add("trouvee");

                // Incrémenter le compteur de paires
                paires++;
                spanPaires.textContent = paires;

                // Écrire dans le journal
                logEvent("Paire trouvée : " + carte1.dataset.nom, "#27ae60");

                // Réinitialiser les cartes temporaires
                carte1 = null;
                carte2 = null;

                // Déverrouiller le plateau
                verrouille = false;

                // Vérifier si toutes les paires sont trouvées
                if (paires === marques.length) {
                    msgFin.textContent = "Bravo ! Toutes les paires ont été trouvées en " + tentatives + " tentatives.";
                    msgFin.style.color = "#27ae60";
                }

            } else {

                // ---- PAS DE PAIRE ----

                // Écrire dans le journal
                logEvent("Raté : " + carte1.dataset.nom + " n'est pas égal à " + carte2.dataset.nom, "#c0392b");

                // Attendre 900ms puis retourner les cartes face cachée
                setTimeout(function () {

                    // Remettre le "?" sur les deux cartes
                    carte1.textContent = "?";
                    carte2.textContent = "?";

                    // Enlever la classe "retournee"
                    carte1.classList.remove("retournee");
                    carte2.classList.remove("retournee");

                    // Réinitialiser les cartes temporaires
                    carte1 = null;
                    carte2 = null;

                    // Déverrouiller le plateau
                    verrouille = false;

                }, 900); // 900ms = temps d'attente avant de retourner
            }
        }
    }

    // =============================================
    // PROPAGATION DES ÉVÉNEMENTS
    // 3 zones imbriquées pour montrer le bubbling
    // Quand on clique sur une carte, l'événement remonte :
    //   carte → zone-interne → zone-milieu → zone-externe
    // =============================================

    // Niveau 1 : zone la plus extérieure
    document.getElementById("zone-externe").addEventListener("click", function () {
        logEvent("Événement reçu par : zone-externe", "#2980b9");
    });

    // Niveau 2 : zone du milieu
    document.getElementById("zone-milieu").addEventListener("click", function () {
        logEvent("Événement reçu par : zone-milieu", "#16a085");
    });

    // Niveau 3 : zone la plus intérieure (contient le plateau)
    document.getElementById("zone-interne").addEventListener("click", function () {
        logEvent("Événement reçu par : zone-interne", "#f39c12");
    });

    // =============================================
    // BOUTONS
    // =============================================

    // Bouton "Rejouer" : recrée le plateau depuis zéro
    document.getElementById("btn-rejouer").addEventListener("click", function () {
        creerPlateau();
    });

    // Bouton "Vider le journal" : efface le contenu du journal
    document.getElementById("btn-clear-log").addEventListener("click", function () {
        eventLog.innerHTML = "";
    });

    // =============================================
    // INITIALISATION
    // On crée le plateau dès le chargement de la page
    // =============================================
    creerPlateau();
});