/**
 * PromptVision Generator Logic
 * Recreated from missing assets
 */

document.addEventListener('DOMContentLoaded', () => {
    initGenerator();
});

let state = {
    mode: 'portrait',
    lang: 'fr',
    activeGroups: [],
    templates: {}
};

function initGenerator() {
    // 1. Load Data
    try {
        state.mode = JSON.parse(document.getElementById('data-mode').textContent);
        state.lang = JSON.parse(document.getElementById('data-lang').textContent);
        state.activeGroups = JSON.parse(document.getElementById('data-active-groups').textContent);
        state.templates = JSON.parse(document.getElementById('data-templates').textContent);
    } catch (e) {
        console.error("Error loading JSON data:", e);
    }

    // 2. Load Local Save
    loadLocalWork();

    // 3. Initial Build
    updateJson();
}

window.updateJson = function () {
    const json = {
        mode: state.mode,
        concept: getValue('global_desc'),
        subject: {},
        environment: {},
        technique: {},
        negative_prompt: getValue('negative')
    };

    // 1. Subject Fields
    document.querySelectorAll('.dynamic-field').forEach(el => {
        if (el.value) {
            json.subject[el.id] = el.value;
        }
    });

    // 2. Common Groups (Action, Scene, Camera)
    document.querySelectorAll('.common-field').forEach(el => {
        if (!el.value) return;

        const group = el.dataset.group; // action, scene, camera

        if (group === 'camera') {
            json.technique[el.id] = el.value;
        } else if (group === 'scene') {
            json.environment[el.id] = el.value;
        } else if (group === 'action') {
            // Action is mixed, usually part of subject description or separate
            // For this structure, let's put it in subject for now or a separate root if needed.
            // Based on typical image prompting, 'pose' and 'expression' fit in subject.
            json.subject[el.id] = el.value;
        }
    });

    // 3. Details
    const detailsAction = getValue('details_action');
    const detailsScene = getValue('details_scene');
    const detailsCamera = getValue('details_camera');

    if (detailsAction) json.subject.details = detailsAction;
    if (detailsScene) json.environment.details = detailsScene;
    if (detailsCamera) json.technique.details = detailsCamera;

    // Clean empty objects
    if (Object.keys(json.subject).length === 0) delete json.subject;
    if (Object.keys(json.environment).length === 0) delete json.environment;
    if (Object.keys(json.technique).length === 0) delete json.technique;

    // Output
    const jsonStr = JSON.stringify(json, null, 2);
    document.getElementById('jsonOutput').textContent = jsonStr;

    // Auto-save
    // saveToLocal(false);
};

window.loadTemplate = function (tplKey) {
    if (!tplKey || !state.templates[tplKey]) return;

    // Resolve lang
    const tplCanvas = state.templates[tplKey];
    const tplData = (tplCanvas[state.lang] || tplCanvas['fr']).data;

    // Reset first
    resetForm(false);

    // Apply (generic loop)
    Object.entries(tplData).forEach(([key, val]) => {
        if (key === 'global_desc') {
            setValue('global_desc', val);
        } else {
            // Try enabling fields if they exist
            setValue(key, val);
        }
    });

    updateJson();
    showToast("Modèle chargé");
};

window.saveToLocal = function (notify = true) {
    const data = {};
    // Capture all inputs
    document.querySelectorAll('input, select, textarea').forEach(el => {
        if (el.id) data[el.id] = el.value;
    });

    localStorage.setItem('pv_save_' + state.mode, JSON.stringify(data));
    if (notify) showToast("Sauvegardé localement");
};

function loadLocalWork() {
    const saved = localStorage.getItem('pv_save_' + state.mode);
    if (!saved) return;

    try {
        const data = JSON.parse(saved);
        Object.entries(data).forEach(([key, val]) => {
            setValue(key, val);
        });
    } catch (e) {
        console.error("Error loading save", e);
    }
}

window.resetForm = function (update = true) {
    document.querySelectorAll('input, select, textarea').forEach(el => {
        if (el.id === 'negative') return; // Keep negative default
        el.value = '';
    });
    if (update) updateJson();
};

window.copyJson = function () {
    const text = document.getElementById('jsonOutput').textContent;
    navigator.clipboard.writeText(text).then(() => {
        showToast("JSON Copié !");
    });
};

window.switchTab = function (mode) {
    const editBtn = document.getElementById('tab-btn-edit');
    const resBtn = document.getElementById('tab-btn-result');
    const panelEdit = document.getElementById('panel-edit');
    const panelRes = document.getElementById('panel-result');

    if (mode === 'edit') {
        editBtn.className = editBtn.className.replace('tab-inactive', 'tab-active');
        resBtn.className = resBtn.className.replace('tab-active', 'tab-inactive');

        panelEdit.classList.remove('hidden');
        panelRes.classList.add('hidden');
        panelRes.classList.remove('flex');
    } else {
        resBtn.className = resBtn.className.replace('tab-inactive', 'tab-active');
        editBtn.className = editBtn.className.replace('tab-active', 'tab-inactive');

        panelEdit.classList.add('hidden');
        panelRes.classList.remove('hidden');
        panelRes.classList.add('flex');
    }
};

// Helpers
function getValue(id) {
    const el = document.getElementById(id);
    return el ? el.value : '';
}

function setValue(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val;
}

function showToast(msg) {
    const t = document.getElementById('toast');
    // Simple way to change text if it has a span
    const span = t.querySelector('span');
    if (span) span.textContent = msg;
    else t.textContent = msg;

    t.style.opacity = '1';
    t.style.transform = 'translateY(0)';

    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translateY(24px)'; // down
    }, 2000);
}
