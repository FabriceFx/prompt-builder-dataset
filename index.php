<?php
// ==================================================================
// 1. CHARGEMENT ET TRI DES DONNÉES
// ==================================================================
$jsonFile = __DIR__ . '/jobs.json';

if (!file_exists($jsonFile)) {
    die('<div style="text-align:center; margin-top:50px; font-family:sans-serif;">⚠️ Le fichier <strong>jobs.json</strong> est introuvable.<br>Veuillez générer le JSON depuis le Google Sheet.</div>');
}

$json_content = file_get_contents($jsonFile);
$db = json_decode($json_content, true);

if (!$db) {
    die('<div style="text-align:center; margin-top:50px; font-family:sans-serif;">⚠️ Le fichier <strong>jobs.json</strong> est vide ou corrompu.</div>');
}

// Fonction de nettoyage pour le tri
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromptBuilder - Accueil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@8..144,100..1000&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5494384683259982"
     crossorigin="anonymous"></script>

    <style>
        body { font-family: 'Google Sans Flex', 'Outfit', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .job-card { transition: all 0.2s ease-in-out; opacity: 0; animation: fadeIn 0.5s forwards; }
        .job-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }
        @keyframes fadeIn { to { opacity: 1; } }
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
                    <h1 class="text-2xl font-bold tracking-tight text-gray-800">PromptBuilder</h1>
                    <p class="text-xs text-gray-500 font-medium" id="subtitle">Le constructeur de prompts pour les pros</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-gray-100 p-1 rounded-lg flex">
                    <button onclick="setLang('fr')" id="btn-fr" class="px-3 py-1 rounded-md text-xs font-bold transition bg-white shadow-sm text-gray-800">FR</button>
                    <button onclick="setLang('en')" id="btn-en" class="px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200">EN</button>
                </div>
                <a href="https://docs.google.com/spreadsheets" target="_blank" class="hidden md:flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition">
                    <i class="fas fa-cog"></i> <span data-translate="admin">Gérer</span>
                </a>
                <a href="https://paypal.me/FFaucheux" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-[#003087] text-white rounded-xl text-xs font-bold hover:bg-[#001c64] transition shadow-md shadow-blue-900/20">
                    <i class="fab fa-paypal"></i> <span data-translate="tip" class="hidden sm:inline">Pourboire</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-6xl mx-auto px-4 py-8 w-full">
        
        <div class="mb-8 p-4 bg-white border border-gray-100 rounded-2xl shadow-sm text-center min-h-[120px] flex flex-col justify-center">
            <span class="text-[10px] text-gray-300 uppercase tracking-widest font-bold mb-2 block">Publicité</span>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-5494384683259982"
                 data-ad-slot="9037377916"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>

        <div class="flex flex-wrap justify-center gap-2 mb-10">
            <button onclick="filterJobs('all')" class="filter-btn active px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-translate="tab_all">Tout voir</button>
            <button onclick="filterJobs('gestion')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-translate="tab_gestion">Gestion & Bureau</button>
            <button onclick="filterJobs('terrain')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-translate="tab_terrain">Terrain & Artisanat</button>
            <button onclick="filterJobs('tech')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-translate="tab_tech">Tech & Savoir</button>
            <button onclick="filterJobs('autre')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition" data-translate="tab_other">Autre</button>
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

        <div id="no-results" class="hidden text-center py-20 text-gray-400">
            <i class="fas fa-search text-4xl mb-3 opacity-20"></i>
            <p>Aucun résultat.</p>
        </div>

        <div class="mt-20 border-t border-gray-200 pt-10">
            <h2 class="text-center text-xl font-bold text-gray-800 mb-8" data-translate="how_title">Pourquoi utiliser cet outil ?</h2>
            <div class="grid md:grid-cols-3 gap-8 text-center max-w-4xl mx-auto">
                <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">1</div>
                    <h3 class="font-bold text-gray-800 mb-2" data-translate="how_step1_title">Choisis un modèle expert</h3>
                    <p class="text-sm text-gray-500 leading-relaxed" data-translate="how_step1_text">Ne pars pas d'une page blanche. Accède à des structures de prompts testées pour ton métier spécifique.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">2</div>
                    <h3 class="font-bold text-gray-800 mb-2" data-translate="how_step2_title">Guide l'IA facilement</h3>
                    <p class="text-sm text-gray-500 leading-relaxed" data-translate="how_step2_text">Remplis simplement les cases. L'outil assemble automatiquement les meilleures techniques de prompting.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">3</div>
                    <h3 class="font-bold text-gray-800 mb-2" data-translate="how_step3_title">Obtiens une réponse pro</h3>
                    <p class="text-sm text-gray-500 leading-relaxed" data-translate="how_step3_text">Copie le résultat optimisé et colle-le dans ChatGPT, Gemini ou Claude pour un résultat parfait.</p>
                </div>
            </div>
        </div>

        <div class="mt-12 p-4 bg-white border border-gray-100 rounded-2xl shadow-sm text-center max-w-4xl mx-auto min-h-[120px] flex flex-col justify-center">
            <span class="text-[10px] text-gray-300 uppercase tracking-widest font-bold mb-2 block">Publicité</span>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-5494384683259982"
                 data-ad-slot="9037377916"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>

    </main>

    <footer class="bg-white border-t border-gray-200 py-8 mt-auto">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <p class="text-sm text-gray-500 mb-3">
                © <?php echo date('Y'); ?> <strong>Fabrice Faucheux</strong>. 
                <span class="hidden md:inline"> | </span> 
                <br class="md:hidden">
                <span data-translate="footer_made">Fait avec ❤️ pour gagner du temps.</span>
            </p>
            <div class="flex justify-center gap-6 text-sm">
                <a href="mailto:contact@atelier-informatique.com?subject=Idée PromptBuilder" class="text-blue-600 hover:text-blue-800 font-medium transition flex items-center gap-2">
                    <i class="fas fa-lightbulb"></i> <span data-translate="footer_idea">Proposer une idée</span>
                </a>
            </div>
        </div>
    </footer>

    <script>
        const texts = {
            fr: {
                sub: "Le constructeur de prompts pour les pros",
                admin: "Gérer",
                tip: "Pourboire",
                tab_all: "Tout voir", tab_gestion: "Gestion & Bureau", tab_terrain: "Terrain & Artisanat", tab_tech: "Tech & Savoir", tab_other: "Autre",
                how_title: "Pourquoi utiliser cet outil ?",
                how_step1_title: "1. Choisis un modèle expert", how_step1_text: "Ne pars pas d'une page blanche. Accède à des structures de prompts testées pour ton métier spécifique.",
                how_step2_title: "2. Guide l'IA facilement", how_step2_text: "Remplis simplement les cases. L'outil assemble automatiquement les meilleures techniques de prompting.",
                how_step3_title: "3. Obtiens une réponse pro", how_step3_text: "Copie le résultat optimisé et colle-le dans ChatGPT, Gemini ou Claude pour un résultat parfait.",
                footer_made: "Fait avec ❤️ pour gagner du temps.", footer_idea: "Proposer une idée"
            },
            en: {
                sub: "The prompt builder for pros",
                admin: "Manage",
                tip: "Send a tip",
                tab_all: "View All", tab_gestion: "Business & Office", tab_terrain: "Field & Craft", tab_tech: "Tech & Knowledge", tab_other: "Other",
                how_title: "Why use this tool?",
                how_step1_title: "1. Pick an expert template", how_step1_text: "Don't start from a blank page. Access battle-tested prompt structures designed for your job.",
                how_step2_title: "2. Guide AI easily", how_step2_text: "Just fill in the blocks. The tool automatically assembles the best prompting techniques.",
                how_step3_title: "3. Get pro results", how_step3_text: "Copy the optimized prompt into ChatGPT or Gemini to get the perfect answer.",
                footer_made: "Made with ❤️ to save time.", footer_idea: "Suggest an idea"
            }
        };

        function setLang(lang) {
            localStorage.setItem('pb_lang', lang);
            const btnFr = document.getElementById('btn-fr');
            const btnEn = document.getElementById('btn-en');
            
            if (lang === 'fr') {
                btnFr.className = "px-3 py-1 rounded-md text-xs font-bold transition bg-white shadow-sm text-gray-800";
                btnEn.className = "px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200";
            } else {
                btnEn.className = "px-3 py-1 rounded-md text-xs font-bold transition bg-white shadow-sm text-gray-800";
                btnFr.className = "px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200";
            }

            document.getElementById('subtitle').textContent = texts[lang].sub;
            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.getAttribute('data-translate');
                if (texts[lang][key]) el.textContent = texts[lang][key];
            });
            document.querySelectorAll('.job-title').forEach(el => {
                const title = el.getAttribute('data-title-' + lang);
                if (title) el.textContent = title;
            });
        }

        function filterJobs(category) {
            const cards = document.querySelectorAll('.job-card');
            let visibleCount = 0;
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-gray-800', 'text-white');
                btn.classList.add('bg-white', 'text-gray-700');
            });
            event.target.classList.add('active', 'bg-gray-800', 'text-white'); 
            event.target.classList.remove('bg-white', 'text-gray-700');

            cards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = 'block'; visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            const noRes = document.getElementById('no-results');
            visibleCount === 0 ? noRes.classList.remove('hidden') : noRes.classList.add('hidden');
        }

        window.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('pb_lang');
            const userLang = navigator.language || navigator.userLanguage; 
            if (savedLang) setLang(savedLang);
            else if (userLang.startsWith('en')) setLang('en');
            else setLang('fr');
        });
    </script>
</body>
</html>
