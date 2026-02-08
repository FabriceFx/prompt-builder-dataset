# 🚀 Prompt Builder Dataset - Atelier IA

![Version](https://img.shields.io/badge/version-2.0-blue.svg)
![Language](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)
![Frontend](https://img.shields.io/badge/Tailwind_CSS-3.0-38B2AC?logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 📝 Description

**Prompt Builder Dataset** (aussi appelé **Atelier IA**) est une application web conçue pour structurer, optimiser et standardiser les interactions avec les Intelligences Artificielles génératives textuelles (Gemini, ChatGPT, Claude) et visuelles (Midjourney, Dall-E).

Contrairement à de simples collections de prompts, ce projet est une **interface dynamique** qui aide l'utilisateur à construire des requêtes complexes en utilisant des techniques d'ingénierie de prompt avancées (Chain of Thought, Few-Shot, Personas) sans avoir à les rédiger manuellement.

### 🎯 Objectifs
* **Standardisation** : Utiliser des structures éprouvées pour garantir la qualité des réponses IA.
* **Productivité** : Réduire le temps de rédaction grâce à des modèles pré-remplis (JSON).
* **Pédagogie** : Apprendre à prompter en utilisant l'outil (l'utilisateur voit la structure se construire).

---

## ✨ Modules Principaux

L'application est divisée en deux écosystèmes distincts accessibles via un routeur central.

### 🧠 1. PromptLogic (Générateur Textuel)
Dédié à la génération de textes, de code et d'analyses stratégiques.
* **Système de Personas** : Une bibliothèque de métiers (RH, Développeur, Marketing...) stockée dans `jobs.json`.
* **Constructeur par étapes** :
    * **Rôle** : Définition automatique de l'expert.
    * **Contexte & Tâche** : Champs guidés pour l'utilisateur.
    * **Options Avancées** : Activation en un clic du *Chain of Thought* (auto-critique de l'IA) ou du *Mode Interactif* (l'IA pose des questions avant de répondre).
    * **Format** : Few-Shot prompting (exemples) et contraintes de sortie.
* **Tri dynamique** : Filtrage des métiers par catégories (Gestion, Terrain, Tech).

### 🎨 2. PromptVision (Générateur Visuel)
Dédié à la création d'images génératives.
* **Modes Spécialisés** : Photographie, Illustration, 3D Render, etc. (configurés dans `image_data.json`).
* **Paramètres Techniques** :
    * Gestion de la lumière et de la scène.
    * Choix de l'objectif (Camera/Lens).
    * Styles artistiques prédéfinis.
* **Export** : Génération d'un prompt descriptif complet incluant les paramètres techniques et les prompts négatifs.

---

## 🛠 Architecture Technique

Le projet a été refondu pour passer d'un script autonome à une architecture web modulaire en PHP natif (sans framework lourd).

### Structure des dossiers
```text
/prompt-builder-dataset
├── includes/          # Cœur du backend
│   ├── Router.php     # Gestionnaire de routes personnalisé
│   ├── config.php     # Variables globales (URL, Langues)
│   ├── functions.php  # Fonctions utilitaires (nettoyage, tri)
│   └── routes.php     # Définition des URL (/logic, /vision...)
├── views/             # Frontend (Vues)
│   ├── prompt_logic/  # Logique du constructeur textuel
│   │   ├── jobs.json  # Base de données des métiers
│   │   └── builder.php
│   └── prompt_vision/ # Logique du constructeur visuel
│       ├── image_data.json # Config des styles/modes
│       └── generator.php
├── lang/              # Internationalisation (i18n)
│   ├── fr.php         # Traductions Françaises
│   └── en.php         # Traductions Anglaises
├── index.php          # Point d'entrée unique (Dispatch)
└── .htaccess          # Réécriture d'URL pour le routeur
