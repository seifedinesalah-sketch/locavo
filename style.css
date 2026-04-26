-- =============================================================
-- Projet Locavo – Technologies Web – ENSI 2025/2026
-- Membres : Yosri Nawach – Houcine Tajouri – Seif Eddine Salah
-- Fichier : create.sql
-- Description : Creation de la base de donnees et des tables
-- =============================================================

CREATE DATABASE IF NOT EXISTS locavo_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE locavo_db;

DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS voitures;

CREATE TABLE voitures (
    id_v       INT          NOT NULL AUTO_INCREMENT,
    marque     VARCHAR(50)  NOT NULL,
    modele     VARCHAR(100) NOT NULL,
    prix_jour  DECIMAL(8,2) NOT NULL CHECK (prix_jour > 0),
    image      VARCHAR(200) NOT NULL DEFAULT 'default.jpg',
    categorie  ENUM('economique','suv','cabriolet','sport','luxe') NOT NULL DEFAULT 'economique',
    PRIMARY KEY (id_v),
    UNIQUE KEY uq_voiture (marque, modele)
);

DROP TABLE IF EXISTS clients;

CREATE TABLE clients (
    id_c         INT          NOT NULL AUTO_INCREMENT,
    nom_complet  VARCHAR(100) NOT NULL,
    email        VARCHAR(150) NOT NULL,
    tel          VARCHAR(20)  NOT NULL DEFAULT '',
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_c),
    UNIQUE KEY uq_email (email)
);

CREATE TABLE IF NOT EXISTS reservations (
    id_r          INT      NOT NULL AUTO_INCREMENT,
    id_client     INT      NOT NULL,
    id_voiture    INT      NOT NULL,
    date_debut    DATE     NOT NULL,
    date_fin      DATE     NOT NULL,
    statut        ENUM('en_attente','confirmee','annulee') NOT NULL DEFAULT 'en_attente',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_r),
    FOREIGN KEY (id_client)  REFERENCES clients(id_c)  ON DELETE CASCADE,
    FOREIGN KEY (id_voiture) REFERENCES voitures(id_v) ON DELETE CASCADE
);

DROP TABLE IF EXISTS tarifs;

CREATE TABLE tarifs (
    id_t         INT          NOT NULL AUTO_INCREMENT,
    categorie    ENUM('economique','suv','cabriolet','sport','luxe') NOT NULL,
    prix_3jours  DECIMAL(8,2) NOT NULL CHECK (prix_3jours > 0),
    prix_semaine DECIMAL(8,2) NOT NULL CHECK (prix_semaine > 0),
    prix_mois    DECIMAL(8,2) NOT NULL CHECK (prix_mois > 0),
    PRIMARY KEY (id_t),
    UNIQUE KEY uq_categorie (categorie)
);
