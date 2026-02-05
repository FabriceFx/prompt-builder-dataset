<?php
// --- CONFIGURATION & LOGIQUE DE LANGUE ---

// 1. Détection de la langue
session_start();
$available_langs = ['fr', 'en'];
$default_lang = 'fr';

// Choix : URL > Session > Navigateur > Défaut
if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs)) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $available_langs)) {
    $lang = $_SESSION['lang'];
} else {
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
    $lang = in_array($browser_lang, $available_langs) ? $browser_lang : $default_lang;
}

// 2. Configuration SEO (A ADAPTER AVEC VOTRE VRAI DOMAINE)
$baseUrl = "https://atelier-informatique.com/hub.php"; 

// 3. Dictionnaire de traduction
$trans = [
    'fr' => [
        // Meta SEO
        'meta_title' => 'Atelier IA - Outils et Générateurs de Prompts Gratuits',
        'meta_desc' => 'Optimisez vos résultats avec ChatGPT, Gemini et Midjourney grâce à nos générateurs de prompts gratuits.',
        
        // Navigation
        'nav_logo' => 'Atelier<span class="text-blue-600">IA</span>',
        'nav_contact' => 'Me contacter',
        
        // Hero Section
        'hero_badge' => 'Vulgarisation & outils',
        'hero_h1_prefix' => 'Maîtrisez l\'art du',
        'hero_h1_highlight' => 'prompting génératif',
        'hero_p' => 'L\'intelligence artificielle ne remplace pas l\'humain, elle l\'augmente. Découvrez mes outils gratuits pour structurer vos demandes et obtenir des résultats professionnels.',
        
        // Card 1 (Texte)
        'c1_title' => 'PromptLogic',
        'c1_desc' => 'Idéal pour Gemini, ChatGPT ou ClaudeAI. Sélectionnez votre métier, définissez le contexte et laissez l\'outil construire la structure parfaite.',
        'c1_list_1' => 'Modèles par métiers (RH, Dev, Marketing...)',
        'c1_list_2' => 'Optimisation "Chain of Thought"',
        'c1_list_3' => 'Compatible tous LLM',
        'c1_btn' => 'Ouvrir PromptLogic',
        
        // Card 2 (Image)
        'c2_title' => 'PromptVision',
        'c2_desc' => 'Conçu pour Gemini, Midjourney et Dall-E. Composez votre scène, choisissez le style, l\'éclairage et la caméra visuellement.',
        'c2_list_1' => 'Bibliothèques de styles artistiques',
        'c2_list_2' => 'Paramètres techniques (ISO, Lens)',
        'c2_list_3' => 'Mode Photographie & illustration',
        'c2_btn' => 'Ouvrir PromptVision',
        
        // Why Section
        'why_title' => 'Pourquoi utiliser des assistants de prompt ?',
        'why_1_title' => 'Précision',
        'why_1_desc' => 'Une IA répond exactement à ce qu\'on lui demande. Ces outils vous aident à formuler ce que vous avez vraiment en tête.',
        'why_2_title' => 'Gain de temps',
        'why_2_desc' => 'Arrêtez de tâtonner. Utilisez des structures éprouvées pour obtenir un résultat exploitable dès le premier essai.',
        'why_3_title' => 'Apprentissage',
        'why_3_desc' => 'En observant comment les prompts sont construits par l\'outil, vous deviendrez vous-même meilleur prompteur.',
        
        // Footer
        'footer_copy' => 'Facilitateur de solutions IA.',
        'footer_tip' => 'Si mon travail vous aide, un café fait toujours plaisir ☕'
    ],
    'en' => [
        // Meta SEO
        'meta_title' => 'AI Workshop - Free Prompt Generators & Tools',
        'meta_desc' => 'Optimize your results with ChatGPT, Gemini, and Midjourney using our free prompt generators.',
        
        // Navigation
        'nav_logo' => 'AI<span class="text-blue-600">Workshop</span>',
        'nav_contact' => 'Contact me',
        
        // Hero Section
        'hero_badge' => 'Tools & Insights',
        'hero_h1_prefix' => 'Master the art of',
        'hero_h1_highlight' => 'generative prompting',
        'hero_p' => 'Artificial intelligence doesn\'t replace humans; it augments them. Discover my free tools to structure your requests and achieve professional results.',
        
        // Card 1 (Text)
        'c1_title' => 'PromptLogic',
        'c1_desc' => 'Perfect for Gemini, ChatGPT, or ClaudeAI. Select your profession, define the context, and let the tool build the perfect structure.',
        'c1_list_1' => 'Role-based templates (HR, Dev, Marketing...)',
        'c1_list_2' => '"Chain of Thought" optimization',
        'c1_list_3' => 'Compatible with all LLMs',
        'c1_btn' => 'Open PromptLogic',
        
        // Card 2 (Image)
        'c2_title' => 'PromptVision',
        'c2_desc' => 'Designed for Gemini, Midjourney, and Dall-E. Compose your scene, choose the style, lighting, and camera settings visually.',
        'c2_list_1' => 'Artistic style libraries',
        'c2_list_2' => 'Technical settings (ISO, Lens)',
        'c2_list_3' => 'Photography & Illustration modes',
        'c2_btn' => 'Open PromptVision',
        
        // Why Section
        'why_title' => 'Why use prompt assistants?',
        'why_1_title' => 'Precision',
        'why_1_desc' => 'AI answers exactly what you ask. These tools help you formulate what you really have in mind.',
        'why_2_title' => 'Time Saving',
        'why_2_desc' => 'Stop guessing. Use proven structures to get usable results on the very first try.',
        'why_3_title' => 'Learning',
        'why_3_desc' => 'By observing how prompts are built by the tool, you will become a better prompter yourself.',
        
        // Footer
        'footer_copy' => 'AI Solutions Facilitator.',
        'footer_tip' => 'If my work helps you, a coffee is always appreciated ☕'
    ]
];

// Helper function
function t($key) {
    global $trans, $lang;
    return $trans[$lang][$key] ?? $trans['fr'][$key]; // Fallback FR
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo t('meta_title'); ?></title>
    <meta name="description" content="<?php echo t('meta_desc'); ?>">
    
    <link rel="alternate" hreflang="fr" href="<?php echo $baseUrl; ?>?lang=fr" />
    <link rel="alternate" hreflang="en" href="<?php echo $baseUrl; ?>?lang=en" />
    <link rel="alternate" hreflang="x-default" href="<?php echo $baseUrl; ?>" />
    <link rel="canonical" href="<?php echo $baseUrl; ?>?lang=<?php echo $lang; ?>" />

    <meta property="og:title" content="<?php echo t('meta_title'); ?>">
    <meta property="og:description" content="<?php echo t('meta_desc'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $baseUrl; ?>?lang=<?php echo $lang; ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .hero-gradient { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .text-gradient { background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-image: linear-gradient(to right, #3b82f6, #8b5cf6); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-brain"></i>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-800"><?php echo t('nav_logo'); ?></span>
            </div>
            
            <div class="flex items-center gap-6">

                <div class="flex bg-slate-100 rounded-lg p-1 text-xs font-bold">
                    <a href="?lang=fr" class="px-3 py-1 rounded-md transition-all <?php echo $lang === 'fr' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'; ?>">FR</a>
                    <a href="?lang=en" class="px-3 py-1 rounded-md transition-all <?php echo $lang === 'en' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'; ?>">EN</a>
                </div>

                <a href="mailto:contact@atelier-informatique.com" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition hidden sm:block">
                    <?php echo t('nav_contact'); ?>
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        
        <div class="relative overflow-hidden bg-white pt-16 pb-20 lg:pt-24 lg:pb-28">
            <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold tracking-wider mb-6 border border-blue-100 uppercase">
                    <?php echo t('hero_badge'); ?>
                </span>
                <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                    <?php echo t('hero_h1_prefix'); ?> <br>
                    <span class="text-gradient"><?php echo t('hero_h1_highlight'); ?></span>
                </h1>
                <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed mb-10">
                    <?php echo t('hero_p'); ?>
                </p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 pb-24">
            <div class="grid md:grid-cols-2 gap-8">

                <div class="card-hover group relative bg-white rounded-3xl border border-slate-200 p-8 flex flex-col h-full overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-robot text-9xl text-indigo-600 transform rotate-12"></i>
                    </div>
                    
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-pen-nib"></i>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-slate-800 mb-2"><?php echo t('c1_title'); ?></h2>
                    <p class="text-slate-500 mb-6 flex-grow">
                        <?php echo t('c1_desc'); ?>
                    </p>
                    
                    <ul class="space-y-3 mb-8 text-sm text-slate-600">
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> <?php echo t('c1_list_1'); ?></li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> <?php echo t('c1_list_2'); ?></li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> <?php echo t('c1_list_3'); ?></li>
                    </ul>

                    <a href="promptLogic/index.php" class="block w-full py-4 rounded-xl bg-slate-900 text-white font-bold text-center hover:bg-indigo-600 transition-colors shadow-lg shadow-indigo-900/20">
                        <?php echo t('c1_btn'); ?> <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <div class="card-hover group relative bg-white rounded-3xl border border-slate-200 p-8 flex flex-col h-full overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-camera-retro text-9xl text-blue-600 transform -rotate-12"></i>
                    </div>
                    
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-image"></i>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-slate-800 mb-2"><?php echo t('c2_title'); ?></h2>
                    <p class="text-slate-500 mb-6 flex-grow">
                        <?php echo t('c2_desc'); ?>
                    </p>

                    <ul class="space-y-3 mb-8 text-sm text-slate-600">
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> <?php echo t('c2_list_1'); ?></li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> <?php echo t('c2_list_2'); ?></li>
                        <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i> <?php echo t('c2_list_3'); ?></li>
                    </ul>

                    <a href="promptVision/index.php" class="block w-full py-4 rounded-xl bg-white border-2 border-slate-200 text-slate-700 font-bold text-center hover:border-blue-500 hover:text-blue-600 transition-colors">
                        <?php echo t('c2_btn'); ?> <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

            </div>
        </div>

        <div class="bg-slate-50 border-t border-slate-200 py-16">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <h3 class="text-2xl font-bold text-slate-800 mb-8"><?php echo t('why_title'); ?></h3>
                <div class="grid md:grid-cols-3 gap-8">
                    <div>
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-purple-500">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 mb-2"><?php echo t('why_1_title'); ?></h4>
                        <p class="text-sm text-slate-500"><?php echo t('why_1_desc'); ?></p>
                    </div>
                    <div>
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-purple-500">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 mb-2"><?php echo t('why_2_title'); ?></h4>
                        <p class="text-sm text-slate-500"><?php echo t('why_2_desc'); ?></p>
                    </div>
                    <div>
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-purple-500">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 mb-2"><?php echo t('why_3_title'); ?></h4>
                        <p class="text-sm text-slate-500"><?php echo t('why_3_desc'); ?></p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-10">
        <div class="max-w-6xl mx-auto px-4 text-center">
            
            <div class="mb-6">
                <a href="https://paypal.me/FFaucheux" target="_blank" rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-50 text-slate-500 hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 border border-transparent transition-all duration-300 text-xs font-medium group">
                    <i class="fas fa-mug-hot group-hover:animate-bounce"></i> 
                    <span><?php echo t('footer_tip'); ?></span>
                </a>
            </div>

            <p class="text-slate-500 text-sm">
                © <?php echo date('Y'); ?> <strong>Fabrice Faucheux</strong> — <?php echo t('footer_copy'); ?>
            </p>
            
            <div class="mt-4 flex justify-center gap-4">
                <a href="https://www.linkedin.com/in/fabricefaucheux" class="text-slate-400 hover:text-blue-600 transition" aria-label="LinkedIn"><i class="fab fa-linkedin text-xl"></i></a>
                <a href="https://atelier-informatique.com/" class="text-slate-400 hover:text-blue-600 transition" aria-label="Website"><i class="fas fa-globe text-xl"></i></a>
            </div>
        </div>
    </footer>

</body>
</html>