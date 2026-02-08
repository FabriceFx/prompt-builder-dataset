<?php
// Default to relative query string for lang toggle if no specific URL provided
$currentQuery = $_GET;
$queryFr = array_merge($currentQuery, ['lang' => 'fr']);
$queryEn = array_merge($currentQuery, ['lang' => 'en']);
$urlFr = '?' . http_build_query($queryFr);
$urlEn = '?' . http_build_query($queryEn);
?>
<nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <a href="<?php echo url('/'); ?>" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-brain"></i>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-800">
                    <?php echo t('nav_logo'); ?>
                </span>
            </a>
        </div>

        <div class="flex items-center gap-3">

            <!-- Links to Tools -->
            <a href="<?php echo url('/logic'); ?>"
                class="flex items-center gap-2 px-3 py-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs font-bold">
                <i class="fas fa-brain"></i> <span class="hidden sm:inline">
                    <?php echo t('pl_title'); ?>
                </span>
            </a>

            <a href="<?php echo url('/vision'); ?>"
                class="flex items-center gap-2 px-3 py-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs font-bold">
                <i class="fas fa-camera-retro"></i> <span class="hidden sm:inline">
                    <?php echo t('pv_app_title'); ?>
                </span>
            </a>

            <!-- Lang -->
            <div class="flex bg-slate-100 rounded-lg p-1 text-xs font-bold">
                <a href="<?php echo $urlFr; ?>"
                    class="px-3 py-1 rounded-md transition-all <?php echo $lang === 'fr' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'; ?>">FR</a>
                <a href="<?php echo $urlEn; ?>"
                    class="px-3 py-1 rounded-md transition-all <?php echo $lang === 'en' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'; ?>">EN</a>
            </div>

            <!-- Tip -->
            <a href="https://paypal.me/FFaucheux" target="_blank"
                class="flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl text-xs font-bold hover:bg-yellow-50 hover:text-yellow-700 hover:border-yellow-200 transition-all duration-300">
                <i class="fas fa-mug-hot"></i> <span class="hidden sm:inline">
                    <?php echo t('header_tip'); ?>
                </span>
            </a>
        </div>
    </div>
</nav>