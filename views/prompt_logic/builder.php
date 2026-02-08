<?php
// require_once __DIR__ . '/../includes/init.php';

// 1. SÉCURITÉ & DONNÉES
$jobId = isset($_GET['job']) ? preg_replace('/[^a-z0-9_]/i', '', $_GET['job']) : 'general';
$jsonFile = __DIR__ . '/jobs.json';
$db = loadJson($jsonFile);

// Fallback logic
if (!isset($db[$jobId]))
    $jobId = array_key_first($db);
$jobData = $db[$jobId];

// Javascript Data Preparation
// Extract current lang strings to pass to JS
$promptTrads = [
    'act' => t('pl_p_act'),
    'expert' => t('pl_p_expert'),
    'mission' => t('pl_p_mission'),
    'context' => t('pl_p_context'),
    'example_title' => t('pl_p_ex_title'),
    'example_desc' => t('pl_p_ex_desc'),
    'criteria' => t('pl_p_criteria'),
    'format' => t('pl_p_format'),
    'tone' => t('pl_p_tone'),
    'constr' => t('pl_p_constr'),
    'method' => t('pl_p_method'),
    'interactive_t' => t('pl_p_inter_t'),
    'interactive_d' => t('pl_p_inter_d'),
    'cot_t' => t('pl_p_cot_t'),
    'cot_d' => t('pl_p_cot_d'),
    'footer' => t('pl_p_footer')
];

// Helper to force UTF-8 (recursive)
function recursive_utf8_encode($data)
{
    if (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    if (is_array($data)) {
        $ret = [];
        foreach ($data as $i => $d)
            $ret[$i] = recursive_utf8_encode($d);
        return $ret;
    }
    if (is_object($data)) {
        foreach ($data as $i => $d)
            $data->$i = recursive_utf8_encode($d);
        return $data;
    }
    return $data;
}

// Ensure UTF-8 encoding for Templates to prevent json_encode failure
if (isset($jobData['templates']) && is_array($jobData['templates'])) {
    array_walk_recursive($jobData['templates'], function (&$item, $key) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });
}


// Page Metadata
$title = $jobData['translations'][$lang]['title'] ?? $jobData['translations']['fr']['title'] ?? ucfirst($jobId);
$pageTitle = "Builder - " . $title;
// Use shared head, but inject custom CSS/JS
$customCss = "
    :root { 
        --primary: " . ($jobData['theme']['primary'] ?? '#3b82f6') . "; 
        --surface: #f8fafc;
    }
";

// Prepare extra head with custom CSS
$extraHead = '<style>' . $customCss . '</style>';
$extraHead .= '<link rel="stylesheet" href="' . url('/assets/css/style.css') . '">';

// Start Output
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>
        <?php echo htmlspecialchars($pageTitle); ?>
    </title>

    <!-- Shared Assets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <?php echo $extraHead; ?>
</head>

<body class="flex flex-col h-screen w-screen bg-gray-50">

    <div
        class="flex-grow flex flex-col md:flex-row overflow-hidden relative h-full pb-[calc(60px+env(safe-area-inset-bottom))] md:pb-0">

        <aside id="panel-edit"
            class="w-full md:w-[480px] flex flex-col h-full bg-white border-r border-gray-200 z-20 shadow-xl relative md:flex">

            <button onclick="scrollToTopPanel()" id="scroll-top-btn"
                class="md:hidden absolute bottom-6 right-4 z-40 bg-white/90 backdrop-blur border border-gray-200 text-gray-500 shadow-lg rounded-full w-10 h-10 flex items-center justify-center transition-all duration-300 scroll-btn-hidden hover:bg-blue-50 hover:text-blue-600">
                <i class="fas fa-arrow-up"></i>
            </button>

            <header class="p-4 border-b border-gray-100 bg-white sticky top-0 z-10 flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <a href="<?php echo url('/logic'); ?>"
                            class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white transition hover:bg-slate-800">
                            <i class="fas fa-arrow-left text-xs"></i>
                        </a>
                        <h1 class="text-lg font-bold text-gray-800 text-dynamic truncate max-w-[200px]" id="job-title">
                            <?php echo htmlspecialchars($title); ?>
                        </h1>
                    </div>

                    <div class="bg-gray-100 p-1 rounded-lg flex">
                        <a href="?job=<?php echo $jobId; ?>&lang=fr"
                            class="px-3 py-1 rounded-md text-[10px] font-bold transition <?php echo $lang === 'fr' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:bg-gray-200'; ?>">FR</a>
                        <a href="?job=<?php echo $jobId; ?>&lang=en"
                            class="px-3 py-1 rounded-md text-[10px] font-bold transition <?php echo $lang === 'en' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:bg-gray-200'; ?>">EN</a>
                    </div>
                </div>

                <select id="template-selector" onchange="applyTemplate(this.value)"
                    class="text-xs font-bold bg-gray-50 border border-gray-200 rounded-lg py-2 px-3 text-gray-600 outline-none cursor-pointer hover:bg-gray-100 transition w-full">
                    <option value="">✨
                        <?php echo t('pl_b_models'); ?>
                    </option>
                    <optgroup label="Officiels" id="official-tpl-group"></optgroup>
                    <optgroup label="Mes Sauvegardes" id="local-templates-group"></optgroup>
                </select>
            </header>

            <div id="edit-container" class="flex-grow overflow-y-auto p-5 space-y-4 pb-20">

                <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                    <div class="input-group border-0 bg-transparent p-0">
                        <label class="input-label text-blue-600">
                            <?php echo t('pl_b_role'); ?>
                        </label>
                        <input type="text" id="role" class="modern-input font-bold text-blue-900"
                            placeholder="<?php echo t('pl_b_ph_role'); ?>" oninput="updatePrompt()">
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="input-group">
                        <label class="input-label">
                            <?php echo t('pl_b_task'); ?>
                        </label>
                        <textarea id="task" rows="3" class="modern-input resize-none"
                            placeholder="<?php echo t('pl_b_ph_task'); ?>" oninput="updatePrompt()"></textarea>
                    </div>
                    <div class="input-group">
                        <label class="input-label">
                            <?php echo t('pl_b_context'); ?>
                        </label>
                        <textarea id="context" rows="2" class="modern-input resize-none"
                            placeholder="<?php echo t('pl_b_ph_context'); ?>" oninput="updatePrompt()"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="input-group">
                        <label class="input-label">
                            <?php echo t('pl_b_tone'); ?>
                        </label>
                        <select id="tone" class="modern-input bg-transparent cursor-pointer" onchange="updatePrompt()">
                            <option value="">Défaut</option>
                            <?php
if (isset($jobData['options']['tones'])) {
    foreach ($jobData['options']['tones'] as $t) {
        $label = $t['label'][$lang] ?? $t['label']['fr'] ?? $t['val'];
        echo '<option value="' . htmlspecialchars($t['val']) . '">' . htmlspecialchars($label) . '</option>';
    }
}
?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label class="input-label">
                            <?php echo t('pl_b_format'); ?>
                        </label>
                        <select id="format" class="modern-input bg-transparent cursor-pointer"
                            onchange="updatePrompt()">
                            <option value="">Défaut</option>
                            <?php
if (isset($jobData['options']['formats'])) {
    foreach ($jobData['options']['formats'] as $f) {
        $label = $f['label'][$lang] ?? $f['label']['fr'] ?? $f['val'];
        echo '<option value="' . htmlspecialchars($f['val']) . '">' . htmlspecialchars($label) . '</option>';
    }
}
?>
                        </select>
                    </div>
                </div>

                <div class="input-group border-l-4 border-l-purple-300">
                    <label class="input-label text-purple-600">
                        <?php echo t('pl_b_example'); ?>
                    </label>
                    <textarea id="example" rows="2" class="modern-input resize-none text-xs"
                        placeholder="<?php echo t('pl_b_ph_example'); ?>" oninput="updatePrompt()"></textarea>
                </div>

                <div class="input-group">
                    <label class="input-label">
                        <?php echo t('pl_b_constraints'); ?>
                    </label>
                    <input type="text" id="constraints" class="modern-input"
                        placeholder="<?php echo t('pl_b_ph_constraints'); ?>" oninput="updatePrompt()">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                            <?php echo t('pl_b_instructions'); ?>
                        </span>
                        <button onclick="addInst()"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full w-5 h-5 flex items-center justify-center transition"><i
                                class="fas fa-plus text-[8px]"></i></button>
                    </div>
                    <div id="inst-container" class="space-y-2"></div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <label
                        class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition border border-transparent hover:border-gray-200">
                        <input type="checkbox" id="feedback-loop" class="w-4 h-4 text-blue-600 rounded"
                            onchange="updatePrompt()">
                        <div class="leading-none">
                            <span class="text-xs font-bold text-gray-700 block">
                                <?php echo t('pl_b_interactive'); ?>
                            </span>
                        </div>
                    </label>
                    <label
                        class="flex items-center gap-2 p-3 bg-indigo-50 rounded-xl cursor-pointer hover:bg-indigo-100 transition border border-transparent hover:border-indigo-200">
                        <input type="checkbox" id="refine-loop" class="w-4 h-4 text-indigo-600 rounded"
                            onchange="updatePrompt()">
                        <div class="leading-none">
                            <span class="text-xs font-bold text-indigo-700 block">
                                <?php echo t('pl_b_cot'); ?>
                            </span>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-4">
                    <button onclick="saveLocal()"
                        class="btn-action w-full py-3 rounded-xl font-bold text-xs bg-white border border-gray-200 text-gray-600 hover:text-blue-600 shadow-sm flex items-center justify-center gap-2">
                        <i class="far fa-bookmark"></i> <span>
                            <?php echo t('pl_b_save'); ?>
                        </span>
                    </button>
                    <button onclick="shareToServer()" id="btn-share"
                        class="btn-action w-full py-3 rounded-xl font-bold text-xs bg-white border border-gray-200 text-gray-600 hover:text-purple-600 shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-cloud-upload-alt"></i> <span>
                            <?php echo t('pl_b_share'); ?>
                        </span>
                    </button>
                </div>

                <div class="h-10 md:hidden"></div>
            </div>

            <div id="toast"
                class="absolute bottom-20 md:bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-2 px-4 rounded-full opacity-0 transition-opacity duration-300 pointer-events-none z-50">
                Sauvegardé
            </div>
        </aside>

        <main id="panel-result" class="hidden md:flex flex-grow bg-gray-50 flex-col h-full relative">
            <div class="flex-grow p-4 md:p-8 overflow-y-auto">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 min-h-full p-8 relative group">

                    <button onclick="copyText()" id="btn-copy" class="
                        fixed bottom-24 right-5 z-50 
                        bg-blue-600 text-white border border-blue-600 shadow-xl rounded-full 
                        px-4 py-4 md:px-3 md:py-1.5
                        md:absolute md:top-4 md:right-4 md:bottom-auto md:z-10 
                        md:bg-white md:text-gray-400 md:border-gray-200 md:shadow-sm md:rounded-lg 
                        font-bold text-xs transition flex items-center gap-2 btn-action 
                        hover:text-white hover:bg-blue-700 hover:border-blue-700
                        md:hover:text-blue-600 md:hover:bg-white md:hover:border-blue-200
                        opacity-100 focus:opacity-100">
                        <i class="far fa-copy text-lg md:text-sm"></i>
                        <span class="hidden md:inline">
                            <?php echo t('pl_b_copy'); ?>
                        </span>
                    </button>

                    <div id="output" class="whitespace-pre-wrap text-gray-700 font-mono text-sm leading-relaxed pt-2">
                    </div>
                </div>
            </div>

            <div
                class="p-4 bg-white border-t border-gray-200 flex flex-col xl:flex-row justify-between items-center gap-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex-shrink-0">
                    <i class="fas fa-rocket mr-1"></i> <span>
                        <?php echo t('pl_b_launch'); ?>
                    </span>
                </span>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:flex flex-wrap gap-2 w-full xl:w-auto">
                    <button onclick="copyAndOpen('https://gemini.google.com')"
                        class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-blue-600 transition">Gemini</span>
                    </button>
                    <button onclick="copyAndOpen('https://chat.openai.com')"
                        class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-green-400 hover:bg-green-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-green-600 transition">ChatGPT</span>
                    </button>
                    <button onclick="copyAndOpen('https://claude.ai')"
                        class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-orange-400 hover:bg-orange-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-orange-600 transition">Claude</span>
                    </button>
                    <button onclick="copyText()"
                        class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <i class="far fa-copy"></i> <span>
                            <?php echo t('pl_b_copy'); ?>
                        </span>
                    </button>
                </div>
            </div>
            <div class="h-6 md:hidden"></div>
        </main>
    </div>

    <!-- Mobile Nav -->
    <div
        class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] pb-[env(safe-area-inset-bottom)]">
        <button onclick="switchTab('edit')" id="tab-btn-edit"
            class="flex-1 py-3 text-sm transition-colors tab-active flex flex-col items-center justify-center gap-1 h-16">
            <i class="fas fa-edit text-lg"></i>
            <span class="text-[10px] uppercase tracking-wide">
                <?php echo t('pl_b_tab_edit'); ?>
            </span>
        </button>
        <button onclick="switchTab('result')" id="tab-btn-result"
            class="flex-1 py-3 text-sm transition-colors tab-inactive flex flex-col items-center justify-center gap-1 h-16">
            <i class="fas fa-magic text-lg"></i>
            <span class="text-[10px] uppercase tracking-wide">
                <?php echo t('pl_b_tab_result'); ?>
            </span>
        </button>
    </div>

    <!-- Safe Data Injection -->
    <script type="application/json" id="data-job-id"><?php echo json_encode($jobId); ?></script>
    <script type="application/json" id="data-lang"><?php echo json_encode($lang); ?></script>
    <script type="application/json" id="data-prompt-trads"><?php echo json_encode($promptTrads) ?: '{}'; ?></script>
    <script type="application/json"
        id="data-job-templates"><?php echo json_encode($jobData['templates'] ?? []) ?: '{}'; ?></script>

    <script src="<?php echo url('/assets/js/builder.js'); ?>" defer></script>
</body>

</html>