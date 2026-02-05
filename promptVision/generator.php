<?php
// ==================================================================
// 1. CONFIGURATION & DONNÉES
// ==================================================================
$jsonFile = __DIR__ . '/image_data.json';
if (!file_exists($jsonFile)) die("Erreur critique : Fichier JSON manquant.");

$content = file_get_contents($jsonFile);
$data = json_decode($content, true);
if (json_last_error() !== JSON_ERROR_NONE) die("Erreur JSON : " . json_last_error_msg());

// Initialisation du mode (Portrait, Paysage, Architecture, etc.)
$modeKey = isset($_GET['mode']) ? $_GET['mode'] : 'portrait';
$currentMode = $data['modes'][$modeKey] ?? $data['modes']['portrait'];

// Couleurs et configuration
$primaryColor = $currentMode['theme']['primary'] ?? '#3b82f6';
$activeGroups = $currentMode['groups'] ?? ['action', 'scene', 'camera'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Builder - <?php echo ucfirst($modeKey); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Variable standardisée pour l'harmonisation */
        :root { --primary: <?php echo $primaryColor; ?>; }
        
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #1e293b; overflow: hidden; }
        
        /* Styles des Inputs Modernes */
        .modern-select, .modern-input { 
            width: 100%; padding: 10px; border: 1px solid #e2e8f0; 
            border-radius: 8px; font-size: 0.9rem; background: #fff; color: #1e293b;
            outline: none; transition: all 0.2s; 
        }
        .modern-select:focus, .modern-input:focus { 
            border-color: var(--primary); 
            box-shadow: 0 0 0 3px rgba(0,0,0,0.05); 
        }
        
        .input-label { 
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase; 
            letter-spacing: 0.05em; color: #64748b; margin-bottom: 6px; display: block; 
        }
        
        /* Utilitaires de thème */
        .theme-text { color: var(--primary); }
        .theme-border-l { border-left-color: var(--primary); }
        
        /* Coloration JSON */
        pre { white-space: pre-wrap; word-wrap: break-word; }
        .json-key { color: #0284c7; } .json-string { color: #16a34a; } .json-number { color: #d97706; }
        
        /* Onglets Mobile (Style unifié avec Builder) */
        .tab-active { color: var(--primary); font-weight: 700; background-color: rgba(59, 130, 246, 0.05); }
        .tab-inactive { color: #94a3b8; font-weight: 500; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; } 
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    </style>
</head>
<body class="flex flex-col h-screen w-screen bg-slate-50">

    <div class="flex-grow flex flex-col md:flex-row overflow-hidden relative h-full pb-[calc(60px+env(safe-area-inset-bottom))] md:pb-0">

        <aside id="panel-edit" class="w-full md:w-[480px] flex flex-col border-r border-slate-200 bg-white h-full z-20 shadow-xl relative md:flex">
            
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <a href="index.php" class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white transition hover:bg-slate-800">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    <div>
                        <h1 class="font-bold text-slate-800 text-base uppercase tracking-wide dynamic-text theme-text" 
                            data-fr="<?php echo $currentMode['translations']['fr']; ?>" 
                            data-en="<?php echo $currentMode['translations']['en']; ?>">
                            <?php echo $currentMode['translations']['fr']; ?>
                        </h1>
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="bg-slate-100 p-1 rounded-lg flex">
                        <button onclick="setLang('fr')" id="btn-fr" class="px-3 py-1 text-[10px] font-bold rounded transition">FR</button>
                        <button onclick="setLang('en')" id="btn-en" class="px-3 py-1 text-[10px] font-bold rounded transition">EN</button>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 pb-24 custom-scrollbar" id="formContainer">
                
                <div class="mb-6">
                   <select id="templateSelector" onchange="loadTemplate(this.value)" class="modern-select bg-slate-50 border-dashed text-sm font-medium text-slate-600 cursor-pointer hover:bg-slate-100">
                       <option value="" data-ui="templates">✨ Modèles...</option>
                   </select>
                </div>

                <div class="mb-8 p-4 bg-slate-50 rounded-xl border-l-4 theme-border-l shadow-sm">
                    <label class="input-label text-slate-600" data-ui="concept">📝 Concept Global</label>
                    <textarea id="global_desc" class="modern-input h-24 resize-none" data-ui-placeholder="concept_placeholder" oninput="updateJson()"></textarea>
                </div>

                <div class="mb-8">
                    <h3 class="text-xs font-bold theme-text uppercase mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="fas <?php echo $currentMode['icon']; ?>"></i> <span data-ui="subject">Sujet</span>
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <?php foreach($currentMode['fields'] as $field): 
                            $isText = (isset($field['type']) && $field['type'] === 'text');
                            $colSpanClass = $isText ? 'col-span-2' : '';
                            $defaultValue = isset($field['default']) ? $field['default'] : '';
                            $optionsJson = isset($field['options']) ? json_encode($field['options'], JSON_HEX_APOS) : '[]';
                        ?>
                            <div class="<?php echo $colSpanClass; ?>">
                                <label class="input-label field-label" 
                                       data-fr="<?php echo $field['label']['fr']; ?>" 
                                       data-en="<?php echo $field['label']['en']; ?>">
                                       <?php echo $field['label']['fr']; ?>
                                </label>
                                
                                <?php if($isText): ?>
                                    <input type="text" id="<?php echo $field['id']; ?>" class="modern-input dynamic-field" value="<?php echo $defaultValue; ?>" oninput="updateJson()">
                                <?php else: ?>
                                    <select id="<?php echo $field['id']; ?>" class="modern-select dynamic-field translatable-select cursor-pointer" 
                                            data-options='<?php echo $optionsJson; ?>' 
                                            onchange="updateJson()">
                                    </select>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php 
                    $icons = ['action' => 'fa-bolt', 'scene' => 'fa-lightbulb', 'camera' => 'fa-camera'];
                    $groups = ['action', 'scene', 'camera'];
                    
                    foreach($groups as $group): 
                        if(in_array($group, $activeGroups) && isset($data['common'][$group])):
                ?>
                    <div class="mb-8">
                        <h3 class="text-xs font-bold text-slate-500 uppercase mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <i class="fas <?php echo $icons[$group]; ?>"></i> <span data-ui="<?php echo $group; ?>"><?php echo ucfirst($group); ?></span>
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <?php foreach($data['common'][$group] as $field): 
                                 $optionsJson = json_encode($field['options'], JSON_HEX_APOS);
                            ?>
                                <div>
                                    <label class="input-label field-label" 
                                           data-fr="<?php echo $field['label']['fr']; ?>" 
                                           data-en="<?php echo $field['label']['en']; ?>">
                                           <?php echo $field['label']['fr']; ?>
                                    </label>
                                    <select id="<?php echo $field['id']; ?>" class="modern-select common-field translatable-select cursor-pointer" 
                                            data-group="<?php echo $group; ?>" 
                                            data-options='<?php echo $optionsJson; ?>' 
                                            onchange="updateJson()">
                                    </select>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-span-2">
                                <input type="text" id="details_<?php echo $group; ?>" class="modern-input text-xs border-dashed bg-slate-50/50" data-ui-placeholder="details_placeholder" oninput="updateJson()">
                            </div>
                        </div>
                    </div>
                <?php 
                        endif;
                    endforeach; 
                ?>

                <div class="mt-6 p-4 bg-red-50 rounded-xl border border-red-100">
                    <label class="input-label text-red-500 mb-2" data-ui="negative">🚫 Prompt Négatif</label>
                    <textarea id="negative" class="modern-input h-16 text-red-600 text-xs bg-white border-red-200" oninput="updateJson()">blurry, deformed, bad anatomy, text, watermark, low quality</textarea>
                </div>
            </div>

            <div class="p-4 border-t border-slate-200 bg-white absolute bottom-0 w-full flex gap-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-30">
                <button onclick="saveToLocal()" class="flex-1 bg-slate-800 text-white py-3 rounded-xl text-xs font-bold hover:bg-slate-900 transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="far fa-save"></i> <span data-ui="save">SAUVER</span>
                </button>
                <button onclick="resetForm()" class="px-4 bg-white border border-slate-200 text-slate-500 py-3 rounded-xl text-xs hover:text-red-500 hover:border-red-200 transition">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </aside>

        <main id="panel-result" class="hidden md:flex flex-1 bg-slate-900 flex-col relative h-full">
            <div class="absolute top-6 right-6 z-20">
                <button onclick="copyJson()" id="btnCopy" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-2.5 px-5 rounded-lg shadow-lg transition flex items-center gap-2 transform active:scale-95">
                    <i class="far fa-copy"></i> <span data-ui="copy">COPIER JSON</span>
                </button>
            </div>

            <div class="flex-1 overflow-auto p-4 md:p-12 custom-scrollbar">
                <div class="max-w-3xl mx-auto h-full flex flex-col justify-center">
                    <div class="bg-slate-800 rounded-2xl p-6 md:p-8 border border-slate-700 shadow-2xl relative overflow-hidden group">
                        <div class="absolute top-0 left-0 bg-slate-700 text-slate-400 text-[10px] font-bold px-3 py-1 rounded-br-lg">JSON OUTPUT</div>
                        <pre id="jsonOutput" class="font-mono text-xs md:text-sm leading-relaxed text-slate-300 overflow-x-auto"></pre>
                    </div>
                </div>
            </div>
            
            <div id="toast" class="fixed bottom-8 right-8 bg-emerald-500 text-white px-6 py-3 rounded-full shadow-xl transform translate-y-24 transition-all duration-300 font-bold flex items-center gap-2 opacity-0 z-50">
                <i class="fas fa-check-circle"></i> <span data-ui="copied">Copié !</span>
            </div>
        </main>
    </div>

    <div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] pb-[env(safe-area-inset-bottom)]">
        <button onclick="switchTab('edit')" id="tab-btn-edit" class="flex-1 py-3 text-sm transition-colors tab-active flex flex-col items-center justify-center gap-1 h-16">
            <i class="fas fa-sliders-h text-lg"></i> 
            <span class="text-[10px] uppercase tracking-wide" data-ui="concept">ÉDITION</span>
        </button>
        <button onclick="switchTab('result')" id="tab-btn-result" class="flex-1 py-3 text-sm transition-colors tab-inactive flex flex-col items-center justify-center gap-1 h-16">
            <i class="fas fa-code text-lg"></i> 
            <span class="text-[10px] uppercase tracking-wide" data-ui="copy">RÉSULTAT</span>
        </button>
    </div>

    <script>
        const mode = "<?php echo $modeKey; ?>";
        // Injection PHP sécurisée
        const uiData = <?php echo json_encode($data['ui_translations']); ?>;
        const templates = <?php echo json_encode($data['templates']); ?>;
        const activeGroups = <?php echo json_encode($activeGroups); ?>;
        let currentLang = 'fr';
        let currentJson = "";

        // Gestion Onglets Mobile
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

        function setLang(lang) {
            currentLang = lang;
            localStorage.setItem('atelier_lang', lang); // Clé unifiée

            // Buttons Style
            document.getElementById('btn-fr').className = lang === 'fr' ? "px-3 py-1 rounded-md text-[10px] font-bold transition bg-slate-800 text-white shadow-sm" : "px-3 py-1 rounded-md text-[10px] font-bold transition text-gray-500 hover:bg-gray-200";
            document.getElementById('btn-en').className = lang === 'en' ? "px-3 py-1 rounded-md text-[10px] font-bold transition bg-slate-800 text-white shadow-sm" : "px-3 py-1 rounded-md text-[10px] font-bold transition text-gray-500 hover:bg-gray-200";

            // UI Text
            document.querySelectorAll('[data-ui]').forEach(el => {
                if(uiData[lang] && uiData[lang][el.dataset.ui]) el.textContent = uiData[lang][el.dataset.ui];
            });
            document.querySelectorAll('[data-ui-placeholder]').forEach(el => {
                if(uiData[lang] && uiData[lang][el.dataset.uiPlaceholder]) el.placeholder = uiData[lang][el.dataset.uiPlaceholder];
            });
            document.querySelectorAll('.field-label, .dynamic-text').forEach(el => {
                if(el.dataset[lang]) el.textContent = el.dataset[lang];
            });

            // Mise à jour des selects traduisibles
            document.querySelectorAll('.translatable-select').forEach(sel => {
                const savedVal = sel.value;
                try {
                    const options = JSON.parse(sel.dataset.options);
                    sel.innerHTML = '<option value="">--</option>';
                    options.forEach(opt => {
                        const label = opt.label[lang] || opt.val;
                        sel.add(new Option(label, opt.val));
                    });
                    sel.value = savedVal;
                } catch(e) {}
            });
            
            loadTemplateMenu();
        }

        function loadTemplateMenu() {
            const tplSelect = document.getElementById('templateSelector');
            const defaultText = uiData[currentLang] ? uiData[currentLang].templates : "Templates...";
            tplSelect.innerHTML = `<option value="" data-ui="templates">${defaultText}</option>`;
            
            // Templates officiels
            Object.keys(templates).forEach(key => {
                const t = templates[key];
                if(t.mode === mode && t[currentLang]) {
                    tplSelect.add(new Option(t[currentLang].name, key));
                }
            });
            
            // Sauvegardes locales
            const saves = JSON.parse(localStorage.getItem('img_prompts') || "[]");
            if(saves.length > 0) {
                const group = document.createElement('optgroup');
                group.label = uiData[currentLang] ? uiData[currentLang].my_saves : "My Saves";
                saves.forEach(s => {
                    if(s.mode === mode) group.appendChild(new Option("💾 " + s.name, JSON.stringify(s.data)));
                });
                if(group.children.length > 0) tplSelect.add(group);
            }
        }

        function loadTemplate(key) {
            if(!key) return;
            let data = key.startsWith('{') ? JSON.parse(key) : templates[key][currentLang].data;
            
            document.querySelectorAll('input, textarea, select').forEach(el => {
                if(el.id !== 'templateSelector') el.value = '';
            });

            for (const [id, val] of Object.entries(data)) {
                const el = document.getElementById(id);
                if(el) el.value = val;
            }
            updateJson();
        }

        function updateJson() {
            let json = {
                "meta": { "mode": mode, "version": "4.0" },
                "prompt": { "core_description": document.getElementById('global_desc').value }
            };

            // Champs dynamiques (Sujet)
            let subject = {};
            document.querySelectorAll('.dynamic-field').forEach(el => {
                if(el.value) subject[el.id] = el.value;
            });
            if(Object.keys(subject).length > 0) json.subject = subject;

            // Groupes
            activeGroups.forEach(group => {
                let groupObj = {};
                document.querySelectorAll(`.common-field[data-group="${group}"]`).forEach(el => {
                    if(el.value) groupObj[el.id] = el.value;
                });
                const detailsInput = document.getElementById('details_' + group);
                if(detailsInput && detailsInput.value) groupObj.details = detailsInput.value;
                if(Object.keys(groupObj).length > 0) json[group] = groupObj;
            });

            const neg = document.getElementById('negative').value;
            if(neg) json.negative_prompt = neg.split(',').map(s => s.trim()).filter(s => s);

            const str = JSON.stringify(json, null, 2);
            document.getElementById('jsonOutput').innerHTML = syntaxHighlight(str);
            currentJson = str;
        }

        function copyJson() {
            navigator.clipboard.writeText(currentJson).then(() => {
                const t = document.getElementById('toast');
                t.style.opacity = '1'; t.style.transform = 'translateY(0)';
                setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(96px)'; }, 2000);
            });
        }

        function saveToLocal() {
            const name = prompt(currentLang === 'fr' ? "Nom :" : "Name:");
            if(!name) return;
            let data = {};
            document.querySelectorAll('input, select, textarea').forEach(el => {
                if(el.id && el.id !== 'templateSelector' && el.value) data[el.id] = el.value;
            });
            let saves = JSON.parse(localStorage.getItem('img_prompts') || "[]");
            saves.push({ name: name, mode: mode, data: data });
            localStorage.setItem('img_prompts', JSON.stringify(saves));
            loadTemplateMenu();
        }

        function resetForm() {
            if(confirm('Reset?')) {
                document.querySelectorAll('input, textarea').forEach(e => e.value = '');
                document.querySelectorAll('select').forEach(e => {
                    if(e.id !== 'templateSelector') e.selectedIndex = 0;
                });
                updateJson();
            }
        }

        function syntaxHighlight(json) {
            if (!json) return "";
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                let cls = 'json-number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) { cls = 'json-key'; } else { cls = 'json-string'; }
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('atelier_lang') || 'fr';
            setLang(savedLang);
            updateJson();
        });
    </script>
</body>
</html>