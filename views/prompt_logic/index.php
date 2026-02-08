<?php
// require_once __DIR__ . '/../includes/init.php'; // ALREADY LOADED BY ROUTER

// 1. CHARGEMENT ET TRI DES DONNÉES
$jsonFile = __DIR__ . '/jobs.json';
$content = file_get_contents($jsonFile);
$db = json_decode($content, true);

// Tri alphabétique (Uses helper cleanForSort from functions.php)
uasort($db, function ($a, $b) use ($lang) {
    // Fallback to 'fr' title for sorting if current lang title is missing
    $tA = $a['translations'][$lang]['title'] ?? $a['translations']['fr']['title'] ?? '';
    $tB = $b['translations'][$lang]['title'] ?? $b['translations']['fr']['title'] ?? '';
    return strnatcasecmp(cleanForSort($tA), cleanForSort($tB));
});

// Page Metadata
$pageTitle = t('pl_title') . " - " . t('pl_subtitle');
$pageDesc = t('c1_desc');
$canonicalUrl = BASE_URL . '/PromptLogic/index.php?lang=' . $lang; // TODO update
$alternates = [
    'fr' => BASE_URL . '/PromptLogic/index.php?lang=fr',
    'en' => BASE_URL . '/PromptLogic/index.php?lang=en'
];

// Open Graph
$extraHead = '
    <meta property="og:title" content="' . $pageTitle . '">
    <meta property="og:description" content="' . $pageDesc . '">
    <meta property="og:type" content="website">
    <meta property="og:url" content="' . $canonicalUrl . '">
    <style>
        /* Animation des cartes */
        .job-card { transition: all 0.2s ease-in-out; opacity: 0; animation: fadeIn 0.5s forwards; }
        .job-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); }
        @keyframes fadeIn { to { opacity: 1; } }

        /* Délais d\'animation */
        ' . implode("\n", array_map(function ($i) {
    return ".job-card:nth-child(" . ($i + 1) . ") { animation-delay: " . ($i * 0.03) . "s; }";
}, array_keys(array_values($db)))) . '

        .filter-btn.active { background-color: #1e293b; color: white; border-color: #1e293b; }
    </style>
';

include BASE_PATH . '/includes/head.php';
?>

<!-- Custom Header for PromptLogic (Simplified) -->
<nav class="bg-white/90 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <div
                class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-robot text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-800">PromptLogic</h1>
                <p class="text-xs text-gray-500 font-medium">
                    <?php echo t('pl_subtitle'); ?>
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

            <a href="<?php echo url('/vision'); ?>"
                class="flex items-center gap-2 px-3 py-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs font-bold">
                <i class="fas fa-camera-retro"></i> <span class="hidden sm:inline">
                    <?php echo t('pv_app_title'); ?>
                </span>
            </a>

            <div class="flex bg-gray-100 rounded-lg p-1">
                <a href="?lang=fr"
                    class="px-3 py-1 rounded-md text-xs font-bold transition <?php echo $lang === 'fr' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:bg-gray-200'; ?>">FR</a>
                <a href="?lang=en"
                    class="px-3 py-1 rounded-md text-xs font-bold transition <?php echo $lang === 'en' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:bg-gray-200'; ?>">EN</a>
            </div>

            <a href="https://paypal.me/FFaucheux" target="_blank"
                class="flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl text-xs font-bold hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 transition-all duration-300">
                <i class="fas fa-mug-hot"></i> <span class="hidden sm:inline">
                    <?php echo t('header_tip'); ?>
                </span>
            </a>
        </div>
    </div>
</nav>

<main class="flex-grow max-w-6xl mx-auto px-4 py-8 w-full">
    <div class="flex flex-wrap justify-center gap-2 mb-10">
        <button onclick="filterJobs('all')"
            class="filter-btn active px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition">
            <?php echo t('pl_tab_all'); ?>
        </button>
        <button onclick="filterJobs('gestion')"
            class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition">
            <?php echo t('pl_tab_gestion'); ?>
        </button>
        <button onclick="filterJobs('terrain')"
            class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition">
            <?php echo t('pl_tab_terrain'); ?>
        </button>
        <button onclick="filterJobs('tech')"
            class="filter-btn px-4 py-2 rounded-full text-sm font-medium border border-gray-200 bg-white hover:bg-gray-50 transition">
            <?php echo t('pl_tab_tech'); ?>
        </button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6" id="jobs-grid">
        <?php foreach ($db as $id => $job): ?>
        <?php
    $cat = $job['category'] ?? 'autre';
    $bg = $job['theme']['surface'] ?? '#f3f4f6';
    $fg = $job['theme']['primary'] ?? '#4b5563';
    $icon = $job['icon'] ?? 'fa-briefcase';
    // Server-side translation logic
    $title = $job['translations'][$lang]['title'] ?? $job['translations']['fr']['title'] ?? ucfirst($id);
?>
        <a href="<?php echo url('/logic/builder'); ?>?job=<?php echo $id; ?>"
            class="job-card group block bg-white rounded-2xl border border-gray-100 overflow-hidden hover:border-blue-200 relative"
            data-category="<?php echo $cat; ?>">
            <div style="background-color: <?php echo $bg; ?>;"
                class="h-24 flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                <i class="fa-solid <?php echo $icon; ?> text-3xl transition-transform group-hover:rotate-12 duration-300"
                    style="color: <?php echo $fg; ?>;"></i>
            </div>
            <div class="p-5 text-center">
                <h3 class="font-bold text-gray-800 text-lg group-hover:text-blue-600 transition-colors">
                    <?php echo htmlspecialchars($title); ?>
                </h3>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide font-medium">
                    <?php echo ucfirst($cat); ?>
                </p>
            </div>
        </a>
        <?php
endforeach; ?>
    </div>

    <div class="mt-20 border-t border-gray-200 pt-10">
        <h2 class="text-center text-xl font-bold text-gray-800 mb-8">
            <?php echo t('pl_how_title'); ?>
        </h2>
        <div class="grid md:grid-cols-3 gap-8 text-center max-w-4xl mx-auto">
            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                <div
                    class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                    1</div>
                <h3 class="font-bold text-gray-800 mb-2">
                    <?php echo t('pl_how_step1_title'); ?>
                </h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    <?php echo t('pl_how_step1_text'); ?>
                </p>
            </div>
            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                <div
                    class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                    2</div>
                <h3 class="font-bold text-gray-800 mb-2">
                    <?php echo t('pl_how_step2_title'); ?>
                </h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    <?php echo t('pl_how_step2_text'); ?>
                </p>
            </div>
            <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition">
                <div
                    class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                    3</div>
                <h3 class="font-bold text-gray-800 mb-2">
                    <?php echo t('pl_how_step3_title'); ?>
                </h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    <?php echo t('pl_how_step3_text'); ?>
                </p>
            </div>
        </div>
    </div>

</main>

<?php include BASE_PATH . '/includes/footer.php'; ?>

<script>
    function filterJobs(category) {
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
</script>