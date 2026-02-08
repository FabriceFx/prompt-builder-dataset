<?php
// require_once __DIR__ . '/../includes/init.php';

// 1. DATA LOADING
$jsonFile = __DIR__ . '/image_data.json';
$content = file_get_contents($jsonFile);
$data = json_decode($content, true);

// Page Metadata
$pageTitle = t('pv_app_title') . " - " . t('pv_select_mode');
$pageDesc = t('pv_select_mode_desc');
$canonicalUrl = BASE_URL . '/promptVision/index.php?lang=' . $lang;
$alternates = [
    'fr' => BASE_URL . '/promptVision/index.php?lang=fr',
    'en' => BASE_URL . '/promptVision/index.php?lang=en'
];

$extraHead = '
    <meta property="og:title" content="' . $pageTitle . '">
    <meta property="og:description" content="' . $pageDesc . '">
    <meta property="og:type" content="website">
    <meta property="og:url" content="' . $canonicalUrl . '">
    <style>
        .mode-card { opacity: 0; animation: fadeIn 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; transition: all 0.3s ease; }
        .mode-card:hover { transform: translateY(-6px); box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15); border-color: #93c5fd; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        ' . implode("\n", array_map(function ($i) {
    return ".mode-card:nth-child(" . ($i + 1) . ") { animation-delay: " . ($i * 0.05) . "s; }";
}, array_keys(array_values($data['modes'] ?? [])))) . '
    </style>
';

include BASE_PATH . '/includes/head.php';
?>

<nav class="bg-white/90 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">

        <div class="flex items-center gap-3">
            <div class="bg-slate-900 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-camera-retro text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-800">Prompt<span
                        class="text-blue-600">Vision</span></h1>
                <p class="text-xs text-gray-500 font-medium">
                    <?php echo t('pv_app_title'); ?>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">

            <a href="<?php echo url('/'); ?>"
                class="flex items-center gap-2 px-3 py-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs font-bold">
                <i class="fas fa-home"></i> <span class="hidden sm:inline">
                    <?php echo t('nav_home'); ?>
                </span>
            </a>

            <a href="<?php echo url('/logic'); ?>"
                class="flex items-center gap-2 px-3 py-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs font-bold">
                <i class="fas fa-brain"></i> <span class="hidden sm:inline">
                    <?php echo t('pl_title'); ?>
                </span>
            </a>

            <div class="flex bg-gray-100 rounded-lg p-1">
                <a href="?lang=fr"
                    class="px-3 py-1 rounded-md text-xs font-bold transition <?php echo $lang === 'fr' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:bg-gray-200'; ?>">FR</a>
                <a href="?lang=en"
                    class="px-3 py-1 rounded-md text-xs font-bold transition <?php echo $lang === 'en' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:bg-gray-200'; ?>">EN</a>
            </div>

            <a href="https://paypal.me/FFaucheux" target="_blank"
                class="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-500 border border-gray-200 rounded-xl text-xs font-bold hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 transition-all duration-300">
                <i class="fas fa-mug-hot"></i> <span class="hidden sm:inline">
                    <?php echo t('header_tip'); ?>
                </span>
            </a>
        </div>
    </div>
</nav>

<main class="flex-grow max-w-6xl mx-auto px-4 py-12 w-full">

    <div class="text-center mb-16 max-w-2xl mx-auto">
        <h2 class="text-3xl font-extrabold text-slate-800 mb-4">
            <?php echo t('pv_select_mode'); ?>
        </h2>
        <p class="text-slate-500 text-lg">
            <?php echo t('pv_select_mode_desc'); ?>
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($data['modes'] as $key => $mode): ?>
        <?php
    $primary = $mode['theme']['primary'];
    $surface = $mode['theme']['surface'];
    // Mode translations are inside the JSON
    $modeTitle = $mode['translations'][$lang] ?? $mode['translations']['fr'] ?? ucfirst($key);
?>
        <a href="<?php echo url('/vision/generator'); ?>?mode=<?php echo $key; ?>"
            class="mode-card group block bg-white rounded-2xl border border-slate-100 overflow-hidden relative p-6 cursor-pointer">
            <div class="absolute top-0 left-0 w-full h-1.5 opacity-0 group-hover:opacity-100 transition-opacity"
                style="background-color: <?php echo $primary; ?>"></div>

            <div style="background-color: <?php echo $surface; ?>; color: <?php echo $primary; ?>;"
                class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 mx-auto text-3xl group-hover:scale-110 transition-transform duration-300 shadow-sm">
                <i class="fas <?php echo $mode['icon']; ?>"></i>
            </div>

            <div class="text-center">
                <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition-colors">
                    <?php echo htmlspecialchars($modeTitle); ?>
                </h3>
            </div>
        </a>
        <?php
endforeach; ?>
    </div>
    <?php include BASE_PATH . '/includes/footer.php'; ?>