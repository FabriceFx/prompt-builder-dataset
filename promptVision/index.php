<?php
// ==================================================================
// 1. CHARGEMENT DES DONNÉES
// ==================================================================
$jsonFile = __DIR__ . '/image_data.json';
if (!file_exists($jsonFile)) die("Erreur critique : image_data.json introuvable.");

$content = file_get_contents($jsonFile);
$data = json_decode($content, true);

if (json_last_error() !== JSON_ERROR_NONE) die("Erreur JSON : " . json_last_error_msg());

// Extraction des traductions UI existantes
$uiTranslations = $data['ui_translations'] ?? [];

// 2. INJECTION DES TRADUCTIONS DU MENU (Pour ne pas toucher au JSON)
// On ajoute ici les textes manquants pour la navigation
$menuTrans = [
    'fr' => ['home' => 'Accueil', 'go_logic' => 'Ouvrir PromptLogic'],
    'en' => ['home' => 'Home', 'go_logic' => 'Open PromptLogic']
];

// Fusion intelligente : on garde le JSON, on ajoute le menu
$uiTranslations['fr'] = array_merge($menuTrans['fr'], $uiTranslations['fr'] ?? []);
$uiTranslations['en'] = array_merge($menuTrans['en'], $uiTranslations['en'] ?? []);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromptVision - Accueil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #1e293b; }
        
        /* Animation d'apparition en cascade */
        .mode-card { opacity: 0; animation: fadeIn 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; transition: all 0.3s ease; }
        .mode-card:hover { transform: translateY(-6px); box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15); border-color: #93c5fd; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Délais d'animation générés dynamiquement */
        <?php $i = 0; foreach($data['modes'] as $k => $v) { echo ".mode-card:nth-child(".($i+1).") { animation-delay: ".($i * 0.05)."s; }\n"; $i++; } ?>
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-camera-retro text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">Prompt<span class="text-blue-600">Vision</span></h1>
                    <p class="text-xs text-slate-500 font-medium tracking-wide" data-ui="app_title">Générateur de prompts visuels</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                
                <a href="../hub.php" class="flex items-center gap-2 px-3 py-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs font-bold">
                    <i class="fas fa-home"></i> <span class="hidden sm:inline" data-ui="home">Accueil</span>
                </a>    

                <div class="bg-slate-100 p-1 rounded-lg flex">
                    <button onclick="setLang('fr')" id="btn-fr" class="px-3 py-1 rounded-md text-xs font-bold transition bg-white shadow-sm text-slate-800">FR</button>
                    <button onclick="setLang('en')" id="btn-en" class="px-3 py-1 rounded-md text-xs font-bold transition text-slate-500 hover:bg-slate-200">EN</button>
                </div>

                <a href="https://paypal.me/FFaucheux" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl text-xs font-bold hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 transition-all duration-300">
                    <i class="fas fa-mug-hot"></i> <span class="hidden sm:inline">Offrir un café</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-6xl mx-auto px-4 py-12 w-full">
        
        <div class="text-center mb-16 max-w-2xl mx-auto">
            <h2 class="text-3xl font-extrabold text-slate-800 mb-4" data-ui="select_mode">Sélectionnez un mode</h2>
            <p class="text-slate-500 text-lg">Choisissez le type d'image que vous souhaitez générer pour accéder aux paramètres spécialisés.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($data['modes'] as $key => $mode): ?>
                <?php 
                    $primary = $mode['theme']['primary'];
                    $surface = $mode['theme']['surface'];
                ?>
                <a href="generator.php?mode=<?php echo $key; ?>" class="mode-card group block bg-white rounded-2xl border border-slate-100 overflow-hidden relative p-6 cursor-pointer">
                    <div class="absolute top-0 left-0 w-full h-1.5 opacity-0 group-hover:opacity-100 transition-opacity" style="background-color: <?php echo $primary; ?>"></div>
                    
                    <div style="background-color: <?php echo $surface; ?>; color: <?php echo $primary; ?>;" class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 mx-auto text-3xl group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <i class="fas <?php echo $mode['icon']; ?>"></i>
                    </div>
                    
                    <div class="text-center">
                        <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition-colors mode-title" 
                            data-fr="<?php echo $mode['translations']['fr']; ?>" 
                            data-en="<?php echo $mode['translations']['en']; ?>">
                            <?php echo $mode['translations']['fr']; ?>
                        </h3>
                    </div>
                </a>
            <?php endforeach; ?>
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
        const uiData = <?php echo json_encode($uiTranslations); ?>;
        
        function setLang(lang) {
            localStorage.setItem('atelier_lang', lang); // Clé unifiée
            
            // Mise à jour boutons
            const isFr = lang === 'fr';
            document.getElementById('btn-fr').className = isFr ? "px-3 py-1 rounded-md text-xs font-bold transition bg-slate-800 text-white shadow-sm" : "px-3 py-1 rounded-md text-xs font-bold transition text-slate-500 hover:bg-slate-200";
            document.getElementById('btn-en').className = !isFr ? "px-3 py-1 rounded-md text-xs font-bold transition bg-slate-800 text-white shadow-sm" : "px-3 py-1 rounded-md text-xs font-bold transition text-slate-500 hover:bg-slate-200";

            // Mise à jour textes UI via data-ui
            document.querySelectorAll('[data-ui]').forEach(el => {
                if(uiData[lang] && uiData[lang][el.dataset.ui]) el.textContent = uiData[lang][el.dataset.ui];
            });

            // Mise à jour titres modes
            document.querySelectorAll('.mode-title').forEach(el => {
                if(el.dataset[lang]) el.textContent = el.dataset[lang];
            });
        }

        // Chargement langue sauvegardée
        const savedLang = localStorage.getItem('atelier_lang') || 'fr';
        setLang(savedLang);
    </script>
</body>
</html>