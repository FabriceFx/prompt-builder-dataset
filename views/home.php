<?php
// Init is already loaded by index.php
// require_once __DIR__ . '/includes/init.php';

// Page Metadata
$pageTitle = t('meta_title');
$pageDesc = t('meta_desc');
$canonicalUrl = BASE_URL . '/hub.php?lang=' . $lang; // TODO: Update canonicals later

$alternates = [
    'fr' => BASE_URL . '/hub.php?lang=fr', // TODO: Update alternates later
    'en' => BASE_URL . '/hub.php?lang=en'
];
$ogImage = BASE_URL . '/assets/og-image.jpg';

// Open Graph
$extraHead = '
    <meta property="og:title" content="' . $pageTitle . '">
    <meta property="og:description" content="' . $pageDesc . '">
    <meta property="og:type" content="website">
    <meta property="og:url" content="' . $canonicalUrl . '">
';

include BASE_PATH . '/includes/head.php';
include BASE_PATH . '/includes/nav.php';
?>

<main class="flex-grow">

    <div class="relative overflow-hidden bg-white pt-16 pb-20 lg:pt-24 lg:pb-28">
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <span
                class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold tracking-wider mb-6 border border-blue-100 uppercase">
                <?php echo t('hero_badge'); ?>
            </span>
            <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                <?php echo t('hero_h1_prefix'); ?> <br>
                <span class="text-gradient">
                    <?php echo t('hero_h1_highlight'); ?>
                </span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed mb-10">
                <?php echo t('hero_p'); ?>
            </p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 pb-24">
        <div class="grid md:grid-cols-2 gap-8">

            <div
                class="card-hover group relative bg-white rounded-3xl border border-slate-200 p-8 flex flex-col h-full overflow-hidden">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fas fa-robot text-9xl text-indigo-600 transform rotate-12"></i>
                </div>

                <div
                    class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-pen-nib"></i>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 mb-2">
                    <?php echo t('c1_title'); ?>
                </h2>
                <p class="text-slate-500 mb-6 flex-grow">
                    <?php echo t('c1_desc'); ?>
                </p>

                <ul class="space-y-3 mb-8 text-sm text-slate-600">
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>
                        <?php echo t('c1_list_1'); ?>
                    </li>
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>
                        <?php echo t('c1_list_2'); ?>
                    </li>
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>
                        <?php echo t('c1_list_3'); ?>
                    </li>
                </ul>

                <a href="<?php echo url('/logic'); ?>"
                    class="block w-full py-4 rounded-xl bg-slate-900 text-white font-bold text-center hover:bg-indigo-600 transition-colors shadow-lg shadow-indigo-900/20">
                    <?php echo t('c1_btn'); ?> <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div
                class="card-hover group relative bg-white rounded-3xl border border-slate-200 p-8 flex flex-col h-full overflow-hidden">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fas fa-camera-retro text-9xl text-blue-600 transform -rotate-12"></i>
                </div>

                <div
                    class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="fas fa-image"></i>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 mb-2">
                    <?php echo t('c2_title'); ?>
                </h2>
                <p class="text-slate-500 mb-6 flex-grow">
                    <?php echo t('c2_desc'); ?>
                </p>

                <ul class="space-y-3 mb-8 text-sm text-slate-600">
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>
                        <?php echo t('c2_list_1'); ?>
                    </li>
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>
                        <?php echo t('c2_list_2'); ?>
                    </li>
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>
                        <?php echo t('c2_list_3'); ?>
                    </li>
                </ul>

                <a href="<?php echo url('/vision'); ?>"
                    class="block w-full py-4 rounded-xl bg-white border-2 border-slate-200 text-slate-700 font-bold text-center hover:border-blue-500 hover:text-blue-600 transition-colors">
                    <?php echo t('c2_btn'); ?> <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

        </div>
    </div>

    <div class="bg-slate-50 border-t border-slate-200 py-16">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h3 class="text-2xl font-bold text-slate-800 mb-8">
                <?php echo t('why_title'); ?>
            </h3>
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div
                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-purple-500">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 mb-2">
                        <?php echo t('why_1_title'); ?>
                    </h4>
                    <p class="text-sm text-slate-500">
                        <?php echo t('why_1_desc'); ?>
                    </p>
                </div>
                <div>
                    <div
                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-purple-500">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 mb-2">
                        <?php echo t('why_2_title'); ?>
                    </h4>
                    <p class="text-sm text-slate-500">
                        <?php echo t('why_2_desc'); ?>
                    </p>
                </div>
                <div>
                    <div
                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4 text-purple-500">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4 class="font-bold text-slate-700 mb-2">
                        <?php echo t('why_3_title'); ?>
                    </h4>
                    <p class="text-sm text-slate-500">
                        <?php echo t('why_3_desc'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

</main>

<?php include BASE_PATH . '/includes/footer.php'; ?>