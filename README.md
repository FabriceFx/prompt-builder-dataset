# 🚀 PromptBuilder (PHP Edition)

![License MIT](https://img.shields.io/badge/License-MIT-blue.svg)
![Platform](https://img.shields.io/badge/Platform-Web%20%2F%20PHP-blue)
![Data](https://img.shields.io/badge/Data-JSON%20Driven-orange)
![Author](https://img.shields.io/badge/Auteur-Fabrice%20Faucheux-green)

**[Français]**
PromptBuilder est une application web PHP dynamique conçue pour générer des prompts de haute qualité pour les LLMs (ChatGPT, Gemini, Claude, etc.). Contrairement aux générateurs statiques, cet outil est entièrement piloté par un fichier de configuration `jobs.json`, permettant de gérer des dizaines de métiers, de tons et de formats sans toucher au code source.

**[English]**
PromptBuilder is a dynamic PHP web application designed to generate high-quality prompts for LLMs (ChatGPT, Gemini, Claude, etc.). Unlike static generators, this tool is entirely driven by a `jobs.json` configuration file, allowing for the management of dozens of professions, tones, and formats without touching the source code.

---

## ✨ Fonctionnalités / features

### 🇫🇷 Français
* **Architecture orientée données** : Tout (métiers, templates, options) est défini dans `jobs.json`.
* **Interface bilingue** : Basculez entre FR/EN instantanément (traductions gérées dans le JSON).
* **+80 Modèles experts** : De l'agriculteur au développeur, en passant par le marketing et la santé.
* **Multi-modèles** : Liens directs pour lancer le prompt dans Gemini, ChatGPT, Claude, Mistral, Perplexity et DeepSeek.
* **Personnalisation locale** : Sauvegarde des templates personnalisés dans le navigateur (LocalStorage).
* **Système de feedback** : API intégrée (`save_suggestion.php`) pour collecter les suggestions utilisateurs.

### 🇬🇧 English
* **Data-Driven Architecture**: Everything (jobs, templates, options) is defined in `jobs.json`.
* **Bilingual Interface**: Toggle FR/EN instantly (translations managed in JSON).
* **80+ Expert Templates**: From agriculture to development, marketing, and healthcare.
* **Multi-Model Support**: Direct links to launch prompts in Gemini, ChatGPT, Claude, Mistral, Perplexity, and DeepSeek.
* **Local Customization**: Save custom templates in the browser (LocalStorage).
* **Feedback System**: Built-in API (`save_suggestion.php`) to collect user suggestions.

---

## 📂 Structure du projet / Project Structure

```text
/
├── index.php             # 🏠 Landing page (Liste les métiers depuis jobs.json)
├── builder.php           # 🛠️ Interface de construction de prompt
├── jobs.json             # 🧠 Cœur du système (Base de données des modèles)
├── save_suggestion.php   # 🔌 API de sauvegarde (Backend léger)
└── data/                 # 🔒 Dossier de stockage des suggestions (généré auto)
    ├── .htaccess         # Sécurité (Deny from all)
    └── suggestions.jsonl # Logs des suggestions
