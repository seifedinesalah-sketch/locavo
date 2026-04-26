# 🚗 Locavo — Car Rental Web Application

A full-stack dynamic car rental platform built as part of the Web Technologies course at ENSI (2025–2026).

## 🛠️ Tech Stack
- **Frontend:** HTML5, CSS3, JavaScript (ES6)
- **Backend:** PHP 8 (OOP, PDO)
- **Database:** MySQL
- **Architecture:** MVC-inspired, multi-page application

## ✨ Features
- 🔍 **Advanced Search** — filter cars by brand, category, and max price
- 📅 **Online Reservation System** — with full client-side (JS) and server-side (PHP) validation
- 🗄️ **Full CRUD Operations** — Insert, Read, Update, Delete via PDO (query, exec, prepare/execute positional & named)
- 🧱 **OOP PHP** — `Voiture` class with private attributes, constructor, getters, setters and business methods
- 📋 **Satisfaction Questionnaire** — validated in JS, processed and stored in MySQL via PHP
- 📬 **Contact Form** — with regex validation and database insertion
- 🛡️ **Security** — server-side revalidation on all forms, PDO prepared statements (SQL injection protection)
- 🎨 **Responsive UI** — across all device sizes

## 📁 Project Structure
```
├── index.html
├── config.php          # PDO connection
├── php/                # PHP scripts (CRUD, forms, OOP)
├── html/               # Static pages
├── js/                 # Client-side validation
├── css/                # Stylesheets
├── sql/                # create.sql + init.sql
└── images/
```

## 👥 Team — Groupe 13
- Yosri Nawach
- Houcine Tajouri
- Seif Eddine Salah

## 🎓 Course
PHP & MySQL — Technologies Web · ENSI 2025–2026
