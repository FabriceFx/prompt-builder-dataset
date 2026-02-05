<?php
// ==================================================================
// 1. CHARGEMENT ET TRI DES DONNÉES
// ==================================================================
$jsonFile = __DIR__ . '/jobs.json';
if (!file_exists($jsonFile)) die("Erreur critique : jobs.json introuvable.");

$json_content = file_get_contents($jsonFile);
$db = json_decode($json_content, true);

if (!$db) die("Erreur critique : Format JSON invalide.");

// Fonction de nettoyage pour le tri (Robustesse accents)
function cleanForSort($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(
        ['à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ'],
        ['a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y'],
        $str
    );
    return $str;
}

// Tri alphabétique
uasort($db, function($a, $b) {
    $titreA = cleanForSort($a['translations']['fr']['title'] ?? '');
    $titreB = cleanForSort($b['translations']['fr']['title'] ?? '');
    return strnatcasecmp($titreA, $titreB);
});

// Données UI centralisées (Pour injection JS)
$uiData = [
    'fr' => [
        'home' => "Accueil",
        'subtitle' => "Le constructeur de prompts pour les pros",
        'admin' => "Gérer", 'tip' => "Offrir un café",
        'tab_all' => "Tout voir", 'tab_gestion' => "Gestion & bureau",
        'tab_terrain' => "Terrain & artisanat", 'tab_tech' => "Tech & savoir", 'tab_other' => "Autre",
        'how_title' => "Pourquoi utiliser cet outil ?",
        'how_step1_title' => "1. Choisis un modèle expert", 'how_step1_text' => "Ne pars pas d'une page blanche. Accède à des structures de prompts testées.",
        'how_step2_title' => "2. Guide l'IA facilement", 'how_step2_text' => "Remplis simplement les cases. L'outil assemble les techniques.",
        'how_step3_title' => "3. Obtiens une réponse pro", 'how_step3_text' => "Copie le résultat optimisé pour Gemini, ChatGPT ou Claude."
    ],
    'en' => [
        'home' => "Home",
        'subtitle' => "The prompt builder for pros",
        'admin' => "Manage", 'tip' => "Buy me a coffee",
        'tab_all' => "View All", 'tab_gestion' => "Business & Office",
        'tab_terrain' => "Field & Craft", 'tab_tech' => "Tech & Knowledge", 'tab_other' => "Other",
        'how_title' => "Why use this tool?",
        'how_step1_title' => "1. Pick an expert template", 'how_step1_text' => "Don't start blank. Access battle-tested prompt structures.",
        'how_step2_title' => "2. Guide AI easily", 'how_step2_text' => "Just fill in the blocks. The tool assembles the techniques.",
        'how_step3_title' => "3. Get pro results", 'how_step3_text' => "Copy the optimized prompt for Gemini, ChatGPT or Claude."
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromptLogic - Accueil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #1e293b; }

        /* Animation des cartes */
        .job-card { transition: all 0.2s ease-in-out; opacity: 0; animation: fadeIn 0.5s forwards; }
        .job-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }
        @keyframes fadeIn { to { opacity: 1; } }

        /* Délais d'animation */
        <?php $i = 0; foreach($db as $k => $v) { echo ".job-card:nth-child(".($i+1).") { animation-delay: ".($i * 0.03)."s; }\n"; $i++; } ?>

        .filter-btn.active { background-color: #1e293b; color: white; border-color: #1e293b; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-robot text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-800">PromptLogic</h1>
                    <p class="text-xs text-gray-500 font-medium" id="subtitle">Le constructeur de prompts pour les pros</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="../hub.php" class="flex items-center gap-2 px-3 py-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs font-bold">
                    <i class="fas fa-home"></i> <span class="hidden sm:inline" data-ui="home">Accueil</span>
                </a>

                <div class="bg-gray-100 p-1 rounded-lg flex">
                    <button onclick="setLang('fr')" id="btn-fr" class="px-3 py-1 rounded-md text-xs font-bold transition bg-white shadow-sm text-gray-800">FR</button>
                    <button onclick="setLang('en')" id="btn-en" class="px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200">EN</button>
                </div>
                <a href="https://paypal.me/FFaucheux" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl text-xs font-bold hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 transition-all duration-300">
                    <i class="fas fa-mug-hot"></i> <span data-ui="tip" class="hidden sm:inline">Offrir un café</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-6xl mx-auto px-4 py-8 w-full">
        <div class="flex flex-wrap justify-center gap-2 mb-10">
            <button onclick="filterJobs('all')" class="filter-btn active px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-ui="tab_all">Tout voir</button>
            <button onclick="filterJobs('gestion')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-ui="tab_gestion">Gestion & Bureau</button>
            <button onclick="filterJobs('terrain')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-ui="tab_terrain">Terrain & Artisanat</button>
            <button onclick="filterJobs('tech')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-ui="tab_tech">Tech & Savoir</button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6" id="jobs-grid">
            <?php foreach ($db as $id => $job): ?>
                <?php 
                    $cat = $job['category'] ?? 'autre';
                    $bg = $job['theme']['surface'] ?? '#f3f4f6';
                    $fg = $job['theme']['primary'] ?? '#4b5563';
                    $icon = $job['icon'] ?? 'fa-briefcase';
                    $titleFR = htmlspecialchars($job['translations']['fr']['title'] ?? ucfirst($id));
                    $titleEN = htmlspecialchars($job['translations']['en']['title'] ?? $titleFR);
                ?>
                <a href="builder.php?job=<?php echo $id; ?>" 
                   class="job-card group block bg-white rounded-2xl border border-gray-100 overflow-hidden hover:border-blue-200 relative"
                   data-category="<?php echo $cat; ?>">
                    <div style="background-color: <?php echo $bg; ?>;" class="h-24 flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                        <i class="fa-solid <?php echo $icon; ?> text-3xl transition-transform group-hover:rotate-12 duration-300" style="color: <?php echo $fg; ?>;"></i>
                    </div>
                    <div class="p-5 text-center">
                        <h3 class="font-bold text-gray-800 text-lg group-hover:text-blue-600 transition-colors job-title" 
                            data-title-fr="<?php echo $titleFR; ?>" 
                            data-title-en="<?php echo $titleEN; ?>">
                            <?php echo $titleFR; ?>
                        </h3>
                        <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide font-medium"><?php echo ucfirst($cat); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="mt-20 border-t border-gray-200 pt-10">
            <h2 class="text-center text-xl font-bold text-gray-800 mb-8" data-ui="how_title">Pourquoi utiliser cet outil ?</h2>
            <div class="grid md:grid-cols-3 gap-8 text-center max-w-4xl mx-auto">
                <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">1</div>
                    <h3 class="font-bold text-gray-800 mb-2" data-ui="how_step1_title">1. Choisis un modèle expert</h3>
                    <p class="text-sm text-gray-500 leading-relaxed" data-ui="how_step1_text">Ne pars pas d'une page blanche. Accède à des structures de prompts testées.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">2</div>
                    <h3 class="font-bold text-gray-800 mb-2" data-ui="how_step2_title">2. Guide l'IA facilement</h3>
                    <p class="text-sm text-gray-500 leading-relaxed" data-ui="how_step2_text">Remplis simplement les cases. L'outil assemble les techniques.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">3</div>
                    <h3 class="font-bold text-gray-800 mb-2" data-ui="how_step3_title">3. Obtiens une réponse pro</h3>
                    <p class="text-sm text-gray-500 leading-relaxed" data-ui="how_step3_text">Copie le résultat optimisé pour Gemini, ChatGPT ou Claude.</p>
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-8 mt-auto">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <p class="text-sm text-slate-500 mb-3">
                © <?php echo date('Y'); ?> <strong>Fabrice Faucheux</strong>. 
                <span class="hidden md:inline"> | </span> 
                <br class="md:hidden">
                <span>Fait avec ❤️ pour les créateurs.</span>
            </p>
        </div>
    </footer>

    <script>
        const uiData = <?php echo json_encode($uiData); ?>;

        function setLang(lang) {
            localStorage.setItem('atelier_lang', lang);
            
            // Buttons
            const isFr = lang === 'fr';
            document.getElementById('btn-fr').className = isFr ? "px-3 py-1 rounded-md text-xs font-bold transition bg-slate-800 text-white shadow-sm" : "px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200";
            document.getElementById('btn-en').className = !isFr ? "px-3 py-1 rounded-md text-xs font-bold transition bg-slate-800 text-white shadow-sm" : "px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200";

            // UI
            document.querySelectorAll('[data-ui]').forEach(el => {
                if(uiData[lang] && uiData[lang][el.dataset.ui]) el.textContent = uiData[lang][el.dataset.ui];
            });
            // Job Titles
            document.querySelectorAll('.job-title').forEach(el => {
                if(el.dataset['title-' + lang]) el.textContent = el.dataset['title-' + lang];
            });
        }

        function filterJobs(category) { /* ...Logique inchangée... */
            const cards = document.querySelectorAll('.job-card');
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-slate-800', 'text-white');
                btn.classList.add('bg-white', 'text-gray-700');
            });
            event.target.classList.add('active', 'bg-slate-800', 'text-white'); 
            event.target.classList.remove('bg-white', 'text-gray-700');

            cards.forEach(card => {
                card.style.display = (category === 'all' || card.dataset.category === category) ? 'block' : 'none';
            });
        }

        window.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('atelier_lang') || 'fr';
            setLang(savedLang);
        });
    </script>
</body>
</html>