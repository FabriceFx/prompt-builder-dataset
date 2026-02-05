<?php
// ==================================================================
// 1. SÉCURITÉ & CHARGEMENT
// ==================================================================
$jobId = isset($_GET['job']) ? preg_replace('/[^a-z0-9_]/i', '', $_GET['job']) : 'general';
$jsonFile = __DIR__ . '/jobs.json';

if (!file_exists($jsonFile)) die("Erreur critique : Base de données introuvable.");
$json_content = file_get_contents($jsonFile);
$db = json_decode($json_content, true);

if (!$db) die("Erreur critique : Format JSON invalide.");

// Fallback si le métier n'existe pas
if (!isset($db[$jobId])) $jobId = array_key_first($db);
$jobData = $db[$jobId];

// Sécurisation des données pour le JavaScript (évite les erreurs "undefined")
$jobData['options'] = $jobData['options'] ?? [];
$jobData['options']['tones'] = $jobData['options']['tones'] ?? [];
$jobData['options']['formats'] = $jobData['options']['formats'] ?? [];
$jobData['templates'] = $jobData['templates'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Builder - <?php echo htmlspecialchars($jobData['translations']['fr']['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Variable Standardisée */
        :root { 
            --primary: <?php echo htmlspecialchars($jobData['theme']['primary'] ?? '#3b82f6'); ?>; 
            --surface: #f8fafc;
        }
        body { font-family: 'Outfit', sans-serif; background-color: var(--surface); color: #1e293b; overflow: hidden; }
        
        /* Styles des Inputs */
        .input-group { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.75rem; transition: border-color 0.2s; }
        .input-group:focus-within { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(var(--primary), 0.1); }
        .input-label { display: block; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.25rem; }
        
        .modern-input { width: 100%; background: transparent; border: none; outline: none; font-size: 0.9rem; color: #0f172a; }
        .modern-input::placeholder { color: #cbd5e1; font-style: italic; }
        
        /* Utilitaires */
        .btn-action { transition: all 0.2s; transform: scale(1); }
        .btn-action:active { transform: scale(0.98); }
        
        .text-dynamic { color: var(--primary); }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        /* Onglets Mobiles */
        .tab-active { color: var(--primary); font-weight: 700; background-color: rgba(59, 130, 246, 0.05); }
        .tab-inactive { color: #94a3b8; font-weight: 500; }
        
        /* Animation bouton scroll */
        .scroll-btn-hidden { transform: translateY(20px); opacity: 0; pointer-events: none; }
        .scroll-btn-visible { transform: translateY(0); opacity: 1; pointer-events: auto; }
        
        /* Animation d'apparition */
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="flex flex-col h-screen w-screen bg-gray-50">

    <div class="flex-grow flex flex-col md:flex-row overflow-hidden relative h-full pb-[calc(60px+env(safe-area-inset-bottom))] md:pb-0">

        <aside id="panel-edit" class="w-full md:w-[480px] flex flex-col h-full bg-white border-r border-gray-200 z-20 shadow-xl relative md:flex">
            
            <button onclick="scrollToTopPanel()" id="scroll-top-btn" class="md:hidden absolute bottom-6 right-4 z-40 bg-white/90 backdrop-blur border border-gray-200 text-gray-500 shadow-lg rounded-full w-10 h-10 flex items-center justify-center transition-all duration-300 scroll-btn-hidden hover:bg-blue-50 hover:text-blue-600">
                <i class="fas fa-arrow-up"></i>
            </button>

            <header class="p-4 border-b border-gray-100 bg-white sticky top-0 z-10 flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <a href="index.php" class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white transition hover:bg-slate-800">
                            <i class="fas fa-arrow-left text-xs"></i>
                        </a>
                        <h1 class="text-lg font-bold text-gray-800 text-dynamic truncate max-w-[200px]" id="job-title">
                            <?php echo htmlspecialchars($jobData['translations']['fr']['title']); ?>
                        </h1>
                    </div>
                    
                    <div class="bg-gray-100 p-1 rounded-lg flex">
                        <button onclick="setLang('fr')" id="btn-fr" class="px-3 py-1 rounded-md text-[10px] font-bold transition bg-white shadow-sm text-gray-800">FR</button>
                        <button onclick="setLang('en')" id="btn-en" class="px-3 py-1 rounded-md text-[10px] font-bold transition text-gray-500 hover:bg-gray-200">EN</button>
                    </div>
                </div>

                <select id="template-selector" onchange="applyTemplate(this.value)" class="text-xs font-bold bg-gray-50 border border-gray-200 rounded-lg py-2 px-3 text-gray-600 outline-none cursor-pointer hover:bg-gray-100 transition w-full">
                    <option value="">✨ <span data-ui="models">Modèles...</span></option>
                    <optgroup label="Officiels" id="official-tpl-group"></optgroup>
                    <optgroup label="Mes Sauvegardes" id="local-templates-group"></optgroup>
                </select>
            </header>

            <div id="edit-container" class="flex-grow overflow-y-auto p-5 space-y-4 pb-20">
                
                <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                    <div class="input-group border-0 bg-transparent p-0">
                        <label class="input-label text-blue-600" data-ui="role">1. Persona (Rôle)</label>
                        <input type="text" id="role" class="modern-input font-bold text-blue-900" placeholder="Ex: Expert..." oninput="updatePrompt()">
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="input-group">
                        <label class="input-label" data-ui="task">2. Tâche à accomplir</label>
                        <textarea id="task" rows="3" class="modern-input resize-none" placeholder="..." oninput="updatePrompt()"></textarea>
                    </div>
                    <div class="input-group">
                        <label class="input-label" data-ui="context">3. Contexte</label>
                        <textarea id="context" rows="2" class="modern-input resize-none" placeholder="..." oninput="updatePrompt()"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="input-group">
                        <label class="input-label" data-ui="tone">Ton</label>
                        <select id="tone" class="modern-input bg-transparent cursor-pointer" onchange="updatePrompt()"></select>
                    </div>
                    <div class="input-group">
                        <label class="input-label" data-ui="format">Format</label>
                        <select id="format" class="modern-input bg-transparent cursor-pointer" onchange="updatePrompt()"></select>
                    </div>
                </div>

                <div class="input-group border-l-4 border-l-purple-300">
                    <label class="input-label text-purple-600" data-ui="example">★ Exemple (Few-Shot)</label>
                    <textarea id="example" rows="2" class="modern-input resize-none text-xs" placeholder="..." oninput="updatePrompt()"></textarea>
                </div>

                <div class="input-group">
                    <label class="input-label" data-ui="constraints">Contraintes</label>
                    <input type="text" id="constraints" class="modern-input" placeholder="..." oninput="updatePrompt()">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider" data-ui="instructions">Instructions</span>
                        <button onclick="addInst()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full w-5 h-5 flex items-center justify-center transition"><i class="fas fa-plus text-[8px]"></i></button>
                    </div>
                    <div id="inst-container" class="space-y-2"></div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <label class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition border border-transparent hover:border-gray-200">
                        <input type="checkbox" id="feedback-loop" class="w-4 h-4 text-blue-600 rounded" onchange="updatePrompt()">
                        <div class="leading-none">
                            <span class="text-xs font-bold text-gray-700 block" data-ui="interactive">Mode Interactif</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-3 bg-indigo-50 rounded-xl cursor-pointer hover:bg-indigo-100 transition border border-transparent hover:border-indigo-200">
                        <input type="checkbox" id="refine-loop" class="w-4 h-4 text-indigo-600 rounded" onchange="updatePrompt()">
                        <div class="leading-none">
                            <span class="text-xs font-bold text-indigo-700 block" data-ui="cot">Auto-Critique</span>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-4">
                    <button onclick="saveLocal()" class="btn-action w-full py-3 rounded-xl font-bold text-xs bg-white border border-gray-200 text-gray-600 hover:text-blue-600 shadow-sm flex items-center justify-center gap-2">
                        <i class="far fa-bookmark"></i> <span data-ui="save">Sauver</span>
                    </button>
                    <button onclick="shareToServer()" id="btn-share" class="btn-action w-full py-3 rounded-xl font-bold text-xs bg-white border border-gray-200 text-gray-600 hover:text-purple-600 shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-cloud-upload-alt"></i> <span data-ui="share">Partager</span>
                    </button>
                </div>
                
                <div class="h-10 md:hidden"></div>
            </div>
            
            <div id="toast" class="absolute bottom-20 md:bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-2 px-4 rounded-full opacity-0 transition-opacity duration-300 pointer-events-none z-50">
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
                        <span data-ui="copy" class="hidden md:inline">Copier</span>
                    </button>

                    <div id="output" class="whitespace-pre-wrap text-gray-700 font-mono text-sm leading-relaxed pt-2"></div>
                </div>
            </div>

            <div class="p-4 bg-white border-t border-gray-200 flex flex-col xl:flex-row justify-between items-center gap-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex-shrink-0">
                    <i class="fas fa-rocket mr-1"></i> <span data-ui="launch">Lancer avec :</span>
                </span>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:flex flex-wrap gap-2 w-full xl:w-auto">
                    <button onclick="copyAndOpen('https://gemini.google.com')" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-blue-600 transition">Gemini</span>
                    </button>
                    <button onclick="copyAndOpen('https://chat.mistral.ai')" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-yellow-400 hover:bg-yellow-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-yellow-600 transition">Mistral</span>
                    </button>
                    <button onclick="copyAndOpen('https://claude.ai')" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-[#D97757] hover:bg-[#FFF8F5] rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-[#D97757] transition">Claude</span>
                    </button>
                    <button onclick="copyAndOpen('https://chat.openai.com')" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-green-500 hover:bg-green-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-green-600 transition">ChatGPT</span>
                    </button>
                    <button onclick="copyAndOpen('https://www.perplexity.ai')" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-teal-500 hover:bg-teal-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-teal-600 transition">Perplexity</span>
                    </button>
                    <button onclick="copyAndOpen('https://chat.deepseek.com')" class="group flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-blue-600 hover:bg-blue-50 rounded-lg text-xs font-bold text-gray-600 transition shadow-sm hover:shadow-md">
                        <span class="group-hover:text-blue-700 transition">DeepSeek</span>
                    </button>
                </div>
            </div>
            <div class="h-6 md:hidden"></div>
        </main>
    </div>

    <div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] pb-[env(safe-area-inset-bottom)]">
        <button onclick="switchTab('edit')" id="tab-btn-edit" class="flex-1 py-3 text-sm transition-colors tab-active flex flex-col items-center justify-center gap-1 h-16">
            <i class="fas fa-edit text-lg"></i> 
            <span class="text-[10px] uppercase tracking-wide" data-ui="tab_edit">Édition</span>
        </button>
        <button onclick="switchTab('result')" id="tab-btn-result" class="flex-1 py-3 text-sm transition-colors tab-inactive flex flex-col items-center justify-center gap-1 h-16">
            <i class="fas fa-magic text-lg"></i> 
            <span class="text-[10px] uppercase tracking-wide" data-ui="tab_result">Résultat</span>
        </button>
    </div>

    <script>
        const jobId = "<?php echo $jobId; ?>";
        // Injection PHP sécurisée des données JSON
        const jobData = <?php echo json_encode($jobData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
        
        let templateRegistry = {}; 

        // GESTION DU SCROLL TO TOP
        const editContainer = document.getElementById('edit-container');
        const scrollBtn = document.getElementById('scroll-top-btn');

        if(editContainer) {
            editContainer.addEventListener('scroll', () => {
                if (editContainer.scrollTop > 300) {
                    scrollBtn.classList.remove('scroll-btn-hidden');
                    scrollBtn.classList.add('scroll-btn-visible');
                } else {
                    scrollBtn.classList.remove('scroll-btn-visible');
                    scrollBtn.classList.add('scroll-btn-hidden');
                }
            });
        }

        function scrollToTopPanel() {
            if(editContainer) {
                editContainer.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // GESTION DES ONGLETS MOBILES
        function switchTab(tabName) {
            const editPanel = document.getElementById('panel-edit');
            const resultPanel = document.getElementById('panel-result');
            const editBtn = document.getElementById('tab-btn-edit');
            const resultBtn = document.getElementById('tab-btn-result');

            if (tabName === 'edit') {
                editPanel.classList.remove('hidden');
                editPanel.classList.add('flex');
                resultPanel.classList.add('hidden');
                resultPanel.classList.remove('flex');
                
                editBtn.className = "flex-1 py-3 text-sm transition-colors tab-active flex flex-col items-center justify-center gap-1 h-16";
                resultBtn.className = "flex-1 py-3 text-sm transition-colors tab-inactive flex flex-col items-center justify-center gap-1 h-16";
            } else {
                resultPanel.classList.remove('hidden');
                resultPanel.classList.add('flex');
                editPanel.classList.add('hidden');
                editPanel.classList.remove('flex');

                editBtn.className = "flex-1 py-3 text-sm transition-colors tab-inactive flex flex-col items-center justify-center gap-1 h-16";
                resultBtn.className = "flex-1 py-3 text-sm transition-colors tab-active flex flex-col items-center justify-center gap-1 h-16";
            }
        }

        // TRADUCTIONS UI
        const uiTrads = {
            fr: {
                role: "1. Persona (Rôle)", placeholder_role: "Ex: Expert Marketing...",
                task: "2. Tâche à accomplir", placeholder_task: "Quelle est l'action précise ?",
                context: "3. Contexte", placeholder_context: "Situation, cible, problématique...",
                tone: "Ton", format: "Format",
                example: "★ Exemple (Few-Shot)", placeholder_example: "Collez ici un texte dont l'IA doit imiter le style",
                constraints: "Contraintes", placeholder_constraints: "Ex: Moins de 200 mots, liste à puces...",
                instructions: "Instructions Spécifiques",
                interactive: "Mode Interactif", 
                cot: "Auto-Critique", 
                save: "Sauver", share: "Partager", copy: "Copier", launch: "Lancer avec :",
                models: "Modèles...", default: "Défaut",
                tab_edit: "Édition", tab_result: "Résultat"
            },
            en: {
                role: "1. Persona (Role)", placeholder_role: "Ex: Marketing Expert...",
                task: "2. Task to accomplish", placeholder_task: "What is the specific action?",
                context: "3. Context", placeholder_context: "Situation, target, issue...",
                tone: "Tone", format: "Format",
                example: "★ Example (Few-Shot)", placeholder_example: "Paste a text here for AI to mimic style",
                constraints: "Constraints", placeholder_constraints: "Ex: Under 200 words, bullet points...",
                instructions: "Specific Instructions",
                interactive: "Interactive Mode",
                cot: "Self-Correction", 
                save: "Save", share: "Share", copy: "Copy", launch: "Launch with:",
                models: "Templates...", default: "Default",
                tab_edit: "Edit", tab_result: "Result"
            }
        };

        // TRADUCTIONS PROMPT
        const promptTrads = {
            fr: {
                act: "Agis en tant que", expert: "Tu es un expert reconnu dans ce domaine, apprécié pour ta précision et ton professionnalisme.",
                mission: "🎯 MISSION", context: "Contexte", example_title: "💡 EXEMPLE DE STYLE/STRUCTURE",
                example_desc: "Inspire-toi de cet exemple pour le ton ou la structure, mais adapte le contenu à ma demande :",
                criteria: "📝 CRITÈRES DE RÉUSSITE", format: "Format attendu", tone: "Ton", constr: "Contraintes",
                method: "🧠 MÉTHODE DE TRAVAIL",
                interactive_t: "Mode Interactif", interactive_d: "Ne génère pas la réponse finale tout de suite. Si tu as besoin de précisions pour faire un travail parfait, pose-moi d'abord des questions.",
                cot_t: "Chain of Thought", cot_d: "Prends le temps de réfléchir étape par étape. Rédige une version mentale, critique-la pour vérifier qu'elle respecte tous les critères, puis affiche uniquement la version améliorée.",
                footer: "Tu peux commencer maintenant."
            },
            en: {
                act: "Act as", expert: "You are a recognized expert in this field, known for your precision and professionalism.",
                mission: "🎯 MISSION", context: "Context", example_title: "💡 STYLE/STRUCTURE EXAMPLE",
                example_desc: "Draw inspiration from this example for tone or structure, but adapt the content to my request:",
                criteria: "📝 SUCCESS CRITERIA", format: "Expected Format", tone: "Tone", constr: "Constraints",
                method: "🧠 WORK METHOD",
                interactive_t: "Interactive Mode", interactive_d: "Do not generate the final answer yet. If you need clarification to do a perfect job, ask me questions first.",
                cot_t: "Chain of Thought", cot_d: "Take time to think step by step. Draft a mental version, critique it to ensure it meets all criteria, then output only the improved version.",
                footer: "You can start now."
            }
        };

        let currentLang = 'fr';

        function setLang(lang) {
            currentLang = lang;
            localStorage.setItem('atelier_lang', lang);
            document.getElementById('btn-fr').className = lang === 'fr' ? "px-3 py-1 rounded-md text-[10px] font-bold transition bg-white shadow-sm text-gray-800" : "px-3 py-1 rounded-md text-[10px] font-bold transition text-gray-500 hover:bg-gray-200";
            document.getElementById('btn-en').className = lang === 'en' ? "px-3 py-1 rounded-md text-[10px] font-bold transition bg-white shadow-sm text-gray-800" : "px-3 py-1 rounded-md text-[10px] font-bold transition text-gray-500 hover:bg-gray-200";

            if (jobData.translations) {
                const title = jobData.translations[lang] ? jobData.translations[lang].title : (jobData.translations['fr'] ? jobData.translations['fr'].title : jobId);
                document.getElementById('job-title').textContent = title;
                document.title = "Builder - " + title;
            }

            const t = uiTrads[lang];
            for (const [key, value] of Object.entries(t)) {
                const els = document.querySelectorAll(`[data-ui="${key}"]`);
                els.forEach(el => el.textContent = value);
                const input = document.getElementById(key);
                if (input && t[`placeholder_${key}`]) input.placeholder = t[`placeholder_${key}`];
            }

            populateSelects(lang);
            populateTemplates(lang);
            updatePrompt();
        }

        function populateSelects(lang) {
            const toneSel = document.getElementById('tone');
            const fmtSel = document.getElementById('format');
            const defText = uiTrads[lang].default;
            const curTone = toneSel.value;
            const curFmt = fmtSel.value;

            toneSel.innerHTML = `<option value="">${defText}</option>`;
            if (jobData.options && jobData.options.tones) {
                jobData.options.tones.forEach(t => {
                    const label = (t.label && t.label[lang]) ? t.label[lang] : (t.label && t.label['fr'] ? t.label['fr'] : t.val);
                    toneSel.add(new Option(label, t.val));
                });
            }
            toneSel.value = curTone;

            fmtSel.innerHTML = `<option value="">${defText}</option>`;
            if (jobData.options && jobData.options.formats) {
                jobData.options.formats.forEach(f => {
                    const label = (f.label && f.label[lang]) ? f.label[lang] : (f.label && f.label['fr'] ? f.label['fr'] : f.val);
                    fmtSel.add(new Option(label, f.val));
                });
            }
            fmtSel.value = curFmt;
        }

        function populateTemplates(lang) {
            const group = document.getElementById('official-tpl-group');
            group.innerHTML = "";
            templateRegistry = {}; 

            if (jobData.templates && Object.keys(jobData.templates).length > 0) {
                for (const [key, tpl] of Object.entries(jobData.templates)) {
                    const tplData = tpl[lang] || tpl['fr'];
                    if (tplData) {
                        templateRegistry[key] = tplData;
                        const opt = document.createElement('option');
                        opt.value = key; 
                        opt.textContent = tplData.name;
                        group.appendChild(opt);
                    }
                }
            }
            
            loadLocalTemplates();
        }

        function applyTemplate(key) {
            if(!key) return;
            
            let tpl = templateRegistry[key];
            
            if (!tpl) {
                try {
                    tpl = JSON.parse(key); 
                } catch(e) {
                    console.error("Template introuvable", key);
                    return;
                }
            }

            document.getElementById('role').value = tpl.role || '';
            document.getElementById('task').value = tpl.task || '';
            document.getElementById('context').value = tpl.context || '';
            document.getElementById('example').value = tpl.example || '';
            document.getElementById('constraints').value = tpl.constraints || '';
            document.getElementById('tone').value = tpl.tone || '';
            document.getElementById('format').value = tpl.format || '';
            document.getElementById('feedback-loop').checked = !!tpl.feedback;
            
            document.getElementById('inst-container').innerHTML = '';
            if(tpl.instructions && Array.isArray(tpl.instructions)) {
                tpl.instructions.forEach(i => addInst(i));
            }
            
            updatePrompt();
            scrollToTopPanel();
        }

        function updatePrompt() {
            const d = {
                role: document.getElementById('role').value.trim(),
                task: document.getElementById('task').value.trim(),
                context: document.getElementById('context').value.trim(),
                example: document.getElementById('example').value.trim(),
                constraints: document.getElementById('constraints').value.trim(),
                feedback: document.getElementById('feedback-loop').checked,
                refine: document.getElementById('refine-loop').checked,
                tone: getSelectText('tone'),
                format: getSelectText('format'),
                instructions: Array.from(document.querySelectorAll('.inst-input')).map(i => i.value.trim()).filter(v => v)
            };

            const pt = promptTrads[currentLang];
            let p = "";

            if (d.role) {
                p += `${pt.act} **${d.role}**.\n`;
                p += `${pt.expert}\n\n`;
            }

            p += `### ${pt.mission}\n`;
            if (d.task) p += `${d.task}\n`;
            if (d.context) p += `\n**${pt.context} :** ${d.context}\n`;
            p += `\n`;

            if (d.example) {
                p += `### ${pt.example_title}\n`;
                p += `${pt.example_desc}\n`;
                p += `"""\n${d.example}\n"""\n\n`;
            }

            let hasCriteria = d.tone || d.format || d.constraints || d.instructions.length > 0;
            if (hasCriteria) {
                p += `### ${pt.criteria}\n`;
                if (d.format) p += `- **${pt.format} :** ${d.format}\n`;
                if (d.tone) p += `- **${pt.tone} :** ${d.tone}\n`;
                if (d.constraints) p += `- **${pt.constr} :** ${d.constraints}\n`;
                if (d.instructions.length > 0) {
                    d.instructions.forEach(inst => p += `- ${inst}\n`);
                }
                p += `\n`;
            }

            if (d.feedback || d.refine) {
                p += `### ${pt.method}\n`;
                if (d.feedback) p += `- **${pt.interactive_t} :** ${pt.interactive_d}\n`;
                if (d.refine) p += `- **${pt.cot_t} :** ${pt.cot_d}\n`;
            }

            p += `\n---\n**${pt.footer}**`;

            document.getElementById('output').textContent = p;
        }

        function getSelectText(id) {
            const sel = document.getElementById(id);
            return sel.selectedIndex >= 0 && sel.value ? sel.options[sel.selectedIndex].text : "";
        }

        function addInst(val = '') {
            const div = document.createElement('div');
            div.className = "flex items-center gap-2 animate-fade-in";
            div.innerHTML = `
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <input type="text" class="modern-input border-b border-gray-100 py-1 text-xs focus:border-blue-300 inst-input" value="${val}" oninput="updatePrompt()" placeholder="...">
                <button onclick="this.parentElement.remove(); updatePrompt()" class="text-gray-300 hover:text-red-400"><i class="fas fa-times text-xs"></i></button>
            `;
            document.getElementById('inst-container').appendChild(div);
        }

        async function shareToServer() {
            const btn = document.getElementById('btn-share');
            const originalText = btn.innerHTML;
            const data = {
                jobId: jobId,
                role: document.getElementById('role').value,
                task: document.getElementById('task').value,
                context: document.getElementById('context').value,
                prompt: document.getElementById('output').textContent
            };
            if(!data.task) { alert("Veuillez au moins remplir la tâche."); return; }
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            try {
                // Simuler un appel API (à adapter selon votre backend)
                // const response = await fetch('api/save.php', ...);
                await new Promise(r => setTimeout(r, 800));
                showToast("Merci ! (Simulation envoi)");
            } catch (e) { console.error(e); showToast("Erreur réseau."); } 
            finally { btn.innerHTML = originalText; }
        }

        function copyText() {
            const textBlock = document.getElementById('output');
            if (!textBlock || !textBlock.textContent.trim()) { alert("Prompt vide !"); return; }
            const text = textBlock.textContent;
            
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text)
                    .then(() => showToast("Prompt copié !"))
                    .catch(err => { console.error(err); alert("Erreur copie."); });
            } else {
                 const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                document.body.appendChild(ta);
                ta.focus(); ta.select();
                try {
                    document.execCommand('copy');
                    showToast("Prompt copié !");
                } catch (e) {
                    alert("Copie manuelle requise.");
                }
                document.body.removeChild(ta);
            }
        }

        function copyAndOpen(url) {
            const textBlock = document.getElementById('output');
            if (!textBlock || !textBlock.textContent.trim()) { alert("Prompt vide !"); return; }
            const text = textBlock.textContent;
            const openUrl = () => {
                const win = window.open(url, '_blank');
                if (!win || win.closed || typeof win.closed == 'undefined') alert("⚠️ Pop-up bloqué. Autorisez les pop-ups.");
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => { openUrl(); showToast("Copié ! Ouverture..."); })
                .catch(err => { console.error(err); openUrl(); alert("Copie auto échouée. Faites CTRL+C."); });
            } else {
                openUrl(); alert("⚠️ Site non sécurisé (HTTP). Copiez le texte manuellement.");
            }
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg; t.style.opacity = '1';
            setTimeout(() => t.style.opacity = '0', 3000);
        }

        function saveLocal() {
            const name = prompt("Nom de la sauvegarde :");
            if(!name) return;
            const data = {
                name: name,
                role: document.getElementById('role').value,
                task: document.getElementById('task').value,
                context: document.getElementById('context').value,
                example: document.getElementById('example').value,
                tone: document.getElementById('tone').value,
                format: document.getElementById('format').value,
                constraints: document.getElementById('constraints').value,
                feedback: document.getElementById('feedback-loop').checked,
                instructions: Array.from(document.querySelectorAll('.inst-input')).map(i => i.value)
            };
            let saves = JSON.parse(localStorage.getItem('atelier_saves_' + jobId) || "[]");
            saves.push(data);
            localStorage.setItem('atelier_saves_' + jobId, JSON.stringify(saves));
            loadLocalTemplates();
            showToast("Sauvegardé");
        }

        function loadLocalTemplates() {
            const saves = JSON.parse(localStorage.getItem('atelier_saves_' + jobId) || "[]");
            const group = document.getElementById('local-templates-group');
            group.innerHTML = "";
            saves.forEach(tpl => {
                const opt = document.createElement('option');
                opt.value = JSON.stringify(tpl); 
                opt.textContent = tpl.name;
                group.appendChild(opt);
            });
        }

        window.onload = function() {
            const saved = localStorage.getItem('atelier_lang') || 'fr';
            addInst();
            setLang(saved); 
        };
    </script>
</body>
</html>