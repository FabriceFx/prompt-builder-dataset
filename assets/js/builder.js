/**
 * PromptBuilder Core Logic
 * Recreated from missing assets
 */

document.addEventListener('DOMContentLoaded', () => {
    initBuilder();
});

// Global state
let state = {
    jobId: null,
    lang: 'fr',
    trads: {},
    templates: {}
};

function initBuilder() {
    // 1. Load Data from DOM
    try {
        state.jobId = JSON.parse(document.getElementById('data-job-id').textContent);
        state.lang = JSON.parse(document.getElementById('data-lang').textContent);
        state.trads = JSON.parse(document.getElementById('data-prompt-trads').textContent);
        state.templates = JSON.parse(document.getElementById('data-job-templates').textContent);
    } catch (e) {
        console.error("Error loading JSON data:", e);
    }

    // 2. Populate Templates
    populateTemplates();

    // 3. check localStorage for saved work
    loadLocalWork();

    // 4. Initial Prompt Build
    updatePrompt();
}

function populateTemplates() {
    const select = document.getElementById('template-selector');
    const group = document.getElementById('official-tpl-group');

    // Clear existing (keep default option)
    group.innerHTML = '';

    Object.entries(state.templates).forEach(([key, tpl]) => {
        // Handle localized template data
        const data = tpl[state.lang] || tpl['fr'] || tpl;
        const option = document.createElement('option');
        option.value = key;
        option.textContent = data.name || key;
        group.appendChild(option);
    });
}

// Exposed globally for HTML event handlers
window.applyTemplate = function (tplKey) {
    if (!tplKey || !state.templates[tplKey]) return;

    const tplCanvas = state.templates[tplKey];
    // Resolve lang
    const tpl = tplCanvas[state.lang] || tplCanvas['fr'] || tplCanvas;

    // Fill fields
    setValue('role', tpl.role);
    setValue('task', tpl.task);
    setValue('context', tpl.context);
    setValue('tone', tpl.tone);
    setValue('format', tpl.format);
    setValue('constraints', tpl.constraints);
    setValue('example', tpl.example || '');

    // Handle instructions list
    const instContainer = document.getElementById('inst-container');
    instContainer.innerHTML = ''; // Clear
    if (Array.isArray(tpl.instructions)) {
        tpl.instructions.forEach(txt => addInst(txt));
    }

    updatePrompt();
    showToast("Modèle appliqué !");
};

window.updatePrompt = function () {
    const role = getValue('role');
    const task = getValue('task');
    const context = getValue('context');
    const tone = getValue('tone');
    const format = getValue('format');
    const constraints = getValue('constraints');
    const example = getValue('example');

    // Instructions
    const instructions = [];
    document.querySelectorAll('.inst-input').forEach(input => {
        if (input.value.trim()) instructions.push(input.value.trim());
    });

    const interactive = document.getElementById('feedback-loop').checked;
    const cot = document.getElementById('refine-loop').checked;

    // Build the PROMPT
    let p = "";

    // Header / ACT
    if (role) {
        p += `### ${state.trads.act} ${role}\n`;
        p += `${state.trads.expert}\n\n`;
    }

    // MISSION
    if (task) {
        p += `### ${state.trads.mission}\n${task}\n\n`;
    }

    // CONTEXT
    if (context) {
        p += `### ${state.trads.context}\n${context}\n\n`;
    }

    // METHOD / CRITERIA
    if (tone || format || constraints || instructions.length > 0) {
        p += `### ${state.trads.criteria || 'CRITERIA'}\n`;
        if (tone) p += `- ${state.trads.tone}: ${tone}\n`;
        if (format) p += `- ${state.trads.format}: ${format}\n`;
        if (constraints) p += `- ${state.trads.constr}: ${constraints}\n`;

        instructions.forEach(inst => {
            p += `- ${inst}\n`;
        });
        p += "\n";
    }

    // EXAMPLE
    if (example) {
        p += `### ${state.trads.example_title}\n${state.trads.example_desc}\n"""\n${example}\n"""\n\n`;
    }

    // BONUS MODES
    if (interactive || cot) {
        p += `### ${state.trads.method}\n`;
        if (interactive) {
            p += `**${state.trads.interactive_t}**: ${state.trads.interactive_d}\n`;
        }
        if (cot) {
            p += `**${state.trads.cot_t}**: ${state.trads.cot_d}\n`;
        }
        p += "\n";
    }

    // FOOTER
    p += `${state.trads.footer}`;

    // Render to output
    document.getElementById('output').textContent = p;

    // Auto-save to local storage
    saveLocalWork(false);
};

window.addInst = function (val = '') {
    const container = document.getElementById('inst-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2 instruction-item';
    div.innerHTML = `
        <input type="text" class="modern-input text-xs inst-input" value="${val}" placeholder="..." oninput="updatePrompt()">
        <button onclick="this.parentElement.remove(); updatePrompt()" class="text-red-400 hover:text-red-600 px-2"><i class="fas fa-times"></i></button>
    `;
    container.appendChild(div);
};

window.saveLocal = function () {
    saveLocalWork(true);
};

// Helper to save state
function saveLocalWork(notify = false) {
    const data = {
        role: getValue('role'),
        task: getValue('task'),
        context: getValue('context'),
        tone: getValue('tone'),
        format: getValue('format'),
        example: getValue('example'),
        constraints: getValue('constraints'),
        interactive: document.getElementById('feedback-loop').checked,
        cot: document.getElementById('refine-loop').checked,
        instructions: Array.from(document.querySelectorAll('.inst-input')).map(i => i.value)
    };

    localStorage.setItem('pb_save_' + state.jobId, JSON.stringify(data));

    if (notify) showToast("Sauvegardé localement");
}

function loadLocalWork() {
    const saved = localStorage.getItem('pb_save_' + state.jobId);
    if (!saved) return;

    try {
        const data = JSON.parse(saved);
        setValue('role', data.role);
        setValue('task', data.task);
        setValue('context', data.context);
        setValue('tone', data.tone);
        setValue('format', data.format);
        setValue('example', data.example);
        setValue('constraints', data.constraints);

        if (data.interactive) document.getElementById('feedback-loop').checked = true;
        if (data.cot) document.getElementById('refine-loop').checked = true;

        const instContainer = document.getElementById('inst-container');
        instContainer.innerHTML = '';
        if (Array.isArray(data.instructions)) {
            data.instructions.forEach(txt => addInst(txt));
        }
    } catch (e) {
        console.error("Error restoring save", e);
    }
}

window.copyText = function () {
    const text = document.getElementById('output').textContent;
    navigator.clipboard.writeText(text).then(() => {
        showToast("Copié dans le presse-papier !");
    });
};

window.copyAndOpen = function (url) {
    const text = document.getElementById('output').textContent;
    navigator.clipboard.writeText(text).then(() => {
        window.open(url, '_blank');
    });
};

window.scrollToTopPanel = function () {
    document.getElementById('panel-edit').scrollTop = 0;
};

// Mobile Tab Switching
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
        panelRes.classList.remove('flex'); // Remove flex when hidden
    } else {
        resBtn.className = resBtn.className.replace('tab-inactive', 'tab-active');
        editBtn.className = editBtn.className.replace('tab-active', 'tab-inactive');

        panelEdit.classList.add('hidden');
        panelRes.classList.remove('hidden');
        panelRes.classList.add('flex'); // Add flex back
    }
};

window.shareToServer = function () {
    alert("Cette fonctionnalité nécéssite un backend connecté.");
};

// Helpers
function getValue(id) {
    const el = document.getElementById(id);
    return el ? el.value : '';
}

function setValue(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val || '';
}

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('toast-show');
    setTimeout(() => {
        t.classList.remove('toast-show');
    }, 2000);
}
