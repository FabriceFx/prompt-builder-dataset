<?php
$jsonFile = __DIR__ . '/image_data.json';
if (!file_exists($jsonFile)) die("Erreur: image_data.json introuvable.");
$data = json_decode(file_get_contents($jsonFile), true);
if (json_last_error() !== JSON_ERROR_NONE) die("Erreur JSON");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromptVision</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .mode-card { transition: all 0.2s ease-in-out; }
        .mode-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-camera-retro text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-800" data-key="app_title">PromptVision</h1>
                    <p class="text-xs text-gray-500 font-medium">Générateur de prompts visuels</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                
                <a href="/promptLogic" class="hidden sm:flex items-center gap-2 px-3 py-2 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-xl text-xs font-bold hover:bg-indigo-100 transition" title="Aller vers le générateur de texte">
                    <i class="fas fa-pen-nib"></i> <span>PromptLogic</span>
                </a>

                <div class="bg-gray-100 p-1 rounded-lg flex">
                    <button onclick="setLang('fr')" id="btn-fr" class="px-3 py-1 rounded-md text-xs font-bold transition bg-white shadow-sm text-gray-800">FR</button>
                    <button onclick="setLang('en')" id="btn-en" class="px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200">EN</button>
                </div>

                <a href="https://paypal.me/FFaucheux" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl text-xs font-bold hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 transition-all duration-300">
                    <i class="fas fa-mug-hot"></i> <span class="hidden sm:inline">Offrir un café</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-6xl mx-auto px-4 py-12 w-full">
        
        <div class="text-center mb-12 max-w-2xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-800 mb-4" data-key="select_mode">Sélectionnez un mode de création</h2>
            <p class="text-gray-500">Choisissez le type d'image que vous souhaitez générer pour accéder aux paramètres spécialisés.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($data['modes'] as $key => $mode): ?>
                <?php 
                    $primary = $mode['theme']['primary'];
                    $surface = $mode['theme']['surface'];
                ?>
                <a href="generator.php?mode=<?php echo $key; ?>" class="mode-card group block bg-white rounded-2xl border border-gray-100 overflow-hidden hover:border-blue-200 relative p-6 cursor-pointer">
                    <div class="absolute top-0 left-0 w-full h-1 opacity-0 group-hover:opacity-100 transition-opacity" style="background-color: <?php echo $primary; ?>"></div>
                    
                    <div style="background-color: <?php echo $surface; ?>; color: <?php echo $primary; ?>;" class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5 mx-auto text-3xl group-hover:scale-110 transition-transform duration-300 shadow-inner">
                        <i class="fas <?php echo $mode['icon']; ?>"></i>
                    </div>
                    
                    <div class="text-center">
                        <h3 class="font-bold text-gray-800 text-lg group-hover:text-blue-600 transition-colors mode-title" 
                            data-fr="<?php echo $mode['translations']['fr']; ?>" 
                            data-en="<?php echo $mode['translations']['en']; ?>">
                            <?php echo $mode['translations']['fr']; ?>
                        </h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

    </main>

    <footer class="bg-white border-t border-gray-200 py-8 mt-auto">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <p class="text-sm text-gray-500 mb-3">
                © <?php echo date('Y'); ?> <strong>Fabrice Faucheux</strong>. 
                <span class="hidden md:inline"> | </span> 
                <br class="md:hidden">
                <span>Fait avec ❤️ pour les créateurs.</span>
            </p>
            <div class="flex justify-center gap-6 text-sm">
                <a href="mailto:contact@atelier-informatique.com?subject=Idée PromptGen" class="text-blue-600 hover:text-blue-800 font-medium transition flex items-center gap-2">
                    <i class="fas fa-lightbulb"></i> <span>Proposer une idée</span>
                </a>
            </div>
        </div>
    </footer>

    <script>
        const uiData = <?php echo json_encode($data['ui_translations']); ?>;
        
        function setLang(lang) {
            localStorage.setItem('img_lang', lang);
            
            // Buttons
            document.getElementById('btn-fr').className = lang === 'fr' ? "px-3 py-1 rounded-md text-xs font-bold transition bg-white shadow-sm text-gray-800" : "px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200";
            document.getElementById('btn-en').className = lang === 'en' ? "px-3 py-1 rounded-md text-xs font-bold transition bg-white shadow-sm text-gray-800" : "px-3 py-1 rounded-md text-xs font-bold transition text-gray-500 hover:bg-gray-200";

            // UI Text
            document.querySelectorAll('[data-key]').forEach(el => {
                if(uiData[lang][el.dataset.key]) el.textContent = uiData[lang][el.dataset.key];
            });

            // Modes
            document.querySelectorAll('.mode-title').forEach(el => {
                el.textContent = el.dataset[lang];
            });
        }

        const savedLang = localStorage.getItem('img_lang') || 'fr';
        setLang(savedLang);
    </script>
</body>
</html>