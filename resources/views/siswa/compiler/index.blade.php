@php $noSidebar = true; @endphp
<x-app-layout>
    <div class="m-0 min-h-screen flex flex-col bg-gray-100">
        <div class="w-full px-0 bg-gray-100 flex-1 flex min-h-0">
            <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-none flex-1 flex flex-col min-h-0">
                    <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100 flex-1 flex flex-col min-h-0">
                    <div class="flex-1 flex flex-col gap-4 h-full">
                        <div class="flex-1 min-h-[300px] bg-gray-900 rounded-md overflow-hidden shadow-inner">
                            <div class="flex items-center justify-between px-2 py-2 sm:p-3 bg-gray-800 border-b border-gray-700">
                                <div class="text-white font-semibold text-sm sm:text-base">Java Compiler</div>
                                <div class="flex items-center space-x-2">
                                    <button id="openButton" class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7.414A2 2 0 0016.414 6L13 2.586A2 2 0 0011.586 2H4z"/></svg>
                                        <span class="ml-2 hidden sm:inline">Buka</span>
                                    </button>
                                    <button id="saveButton" class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7.414A2 2 0 0016.414 6L13 2.586A2 2 0 0011.586 2H5z"/></svg>
                                        <span class="ml-2 hidden sm:inline">Simpan</span>
                                    </button>
                                    <button id="runButton" class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm">
                                        <svg id="runIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-6.518-3.76A1 1 0 007 8.239v7.522a1 1 0 001.234.97l6.518-1.567a1 1 0 00.752-.97v-4.726a1 1 0 00-.752-.696z"/></svg>
                                        <svg id="loadingSpinner" xmlns="http://www.w3.org/2000/svg" class="hidden animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                        <span id="runButtonText" class="ml-2 hidden sm:inline">Jalankan Kode</span>
                                    </button>
                                </div>
                            </div>
                            <div id="editor" style="height:60vh; min-height:360px;"></div>

                            
                            <input id="fileInput" type="file" accept=".java" class="hidden" />
                            <!-- Save filename confirmation modal -->
                            <div id="saveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                                <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md mx-4 shadow-lg overflow-hidden">
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">Save File</div>
                                    </div>
                                    <div class="px-4 py-4">
                                        <label class="block text-sm text-gray-700 dark:text-gray-300">Filename</label>
                                        <input id="saveFilenameInput" type="text" class="mt-2 w-full px-3 py-2 border rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Main.java" />
                                        <p class="mt-2 text-xs text-gray-500">Masukkan nama file yang akan disimpan. Klik "Save" untuk mengunduh, atau "Cancel" untuk membatalkan.</p>
                                    </div>
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 flex justify-end space-x-2">
                                        <button id="saveCancelBtn" class="px-3 py-1 bg-white dark:bg-gray-700 border rounded text-gray-700 dark:text-gray-200">Cancel</button>
                                        <button id="saveConfirmBtn" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full flex flex-col">
                            <div class="px-3 py-2 sm:p-3 bg-gray-800 text-white font-semibold text-sm">Output</div>
                            <pre id="output" class="p-3 bg-gray-900 text-white overflow-auto min-h-[160px] max-h-[40vh]">Ready.</pre>

                            <!-- Trace panel trigger (moved below Output) -->
                            <div class="px-2 py-2 sm:p-3 bg-gray-800 border-t border-gray-700 flex items-center justify-between mt-3">
                                <div class="text-sm text-gray-300">Trace: Jelaskan langkah demi langkah bagaimana kode dieksekusi</div>
                                <div class="flex items-center space-x-2">
                                    <button id="traceToggleBtn" class="inline-flex items-center px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm">Trace</button>
                                </div>
                            </div>

                            <!-- Trace panel (hidden by default). On small screens it's a fixed bottom-sheet overlay, on larger screens it's inline -->
                            <div id="tracePanel" class="hidden fixed bottom-0 left-0 right-0 max-h-[80vh] overflow-auto p-4 bg-gray-100 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 z-40 sm:static sm:max-h-none sm:overflow-visible sm:border-none sm:bg-transparent sm:p-0">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <button id="tracePlay" class="px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm">Play</button>
                                        <button id="tracePause" class="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm">Pause</button>
                                        <button id="traceStep" class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm">Step</button>
                                        <button id="traceReset" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">Reset</button>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-sm text-gray-500">Speed: <input id="traceSpeed" type="range" min="200" max="2000" step="100" value="800" class="align-middle" /></div>
                                        <button id="traceCloseBtn" class="ml-2 sm:hidden px-2 py-1 bg-gray-700 text-white rounded-md text-sm">Tutup</button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="md:col-span-2">
                                        <div class="mb-2 text-sm text-gray-400">Penelusuran langkah (klik sebuah langkah untuk lompat)</div>
                                        <ul id="traceStepsList" class="space-y-1 max-h-48 overflow-auto p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700"></ul>
                                    </div>
                                    <div class="md:col-span-1">
                                        <div class="mb-2 text-sm text-gray-400">Penjelasan </div>
                                        <div id="traceExplanation" class="p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 min-h-[120px] text-sm"></div>
                                    </div>
                                    <div class="md:col-span-1">
                                        <div class="mb-2 text-sm text-gray-400">Variabel & Nilai (simulasi)</div>
                                        <div id="traceVariables" class="p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 min-h-[120px] text-sm">
                                            <div id="varsList" class="space-y-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        // Monaco Loader & basic editor + run support
                        var monacoScript = document.createElement('script');
                        monacoScript.src = '{{ asset('js/monaco/vs/loader.js') }}';
                        document.head.appendChild(monacoScript);

                        monacoScript.onload = () => {
                            require.config({ paths: { 'vs': '{{ asset('js/monaco/vs') }}' }});
                            require(['vs/editor/editor.main'], function () {
                                var savedCode = localStorage.getItem('javaCompilerCode');
                                var defaultCode = [
                                    'public class Main {',
                                    '    public static void main(String[] args) {',
                                    '        System.out.println("Hello, World!");',
                                    '    }',
                                    '}'
                                ].join('\n');

                                var editor = monaco.editor.create(document.getElementById('editor'), {
                                    value: savedCode || defaultCode,
                                    language: 'java',
                                    theme: 'vs-dark',
                                    automaticLayout: true
                                });
                                window.editor = editor;

                                editor.onDidChangeModelContent(function() {
                                    localStorage.setItem('javaCompilerCode', editor.getValue());
                                });

                                // Open & Save buttons: open from file input, save as .java
                                const openButton = document.getElementById('openButton');
                                const saveButton = document.getElementById('saveButton');
                                const fileInput = document.getElementById('fileInput');

                                if (openButton && fileInput) {
                                    openButton.addEventListener('click', () => fileInput.click());
                                    fileInput.addEventListener('change', (e) => {
                                        const f = e.target.files && e.target.files[0];
                                        if (!f) return;
                                        if (!f.name.toLowerCase().endsWith('.java')) {
                                            alert('Please select a .java file');
                                            return;
                                        }
                                        const reader = new FileReader();
                                        reader.onload = function(ev) {
                                            const content = ev.target.result;
                                            editor.setValue(content);
                                            localStorage.setItem('javaCompilerCode', content);
                                        };
                                        reader.readAsText(f);
                                        // clear input so same file can be reselected later
                                        fileInput.value = '';
                                    });
                                }

                                if (saveButton) {
                                    const saveModal = document.getElementById('saveModal');
                                    const saveFilenameInput = document.getElementById('saveFilenameInput');
                                    const saveCancelBtn = document.getElementById('saveCancelBtn');
                                    const saveConfirmBtn = document.getElementById('saveConfirmBtn');

                                    function openSaveModal(defaultName) {
                                        if (!saveModal) return;
                                        saveFilenameInput.value = defaultName || 'Main.java';
                                        saveModal.classList.remove('hidden');
                                        setTimeout(() => saveFilenameInput.focus(), 50);
                                    }

                                    function closeSaveModal() {
                                        if (!saveModal) return;
                                        saveModal.classList.add('hidden');
                                    }

                                    saveButton.addEventListener('click', () => {
                                        const code = editor.getValue();
                                        // store pending code on element for confirm handler
                                        saveButton._pendingCode = code;
                                        openSaveModal('Main.java');
                                    });

                                    // Cancel
                                    saveCancelBtn && saveCancelBtn.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        // simply close modal and do not save
                                        closeSaveModal();
                                    });

                                    // Confirm save -> perform download
                                    saveConfirmBtn && saveConfirmBtn.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        const filenameRaw = (saveFilenameInput.value || '').trim();
                                        if (!filenameRaw) { saveFilenameInput.focus(); return; }
                                        let filename = filenameRaw;
                                        if (!filename.toLowerCase().endsWith('.java')) filename += '.java';
                                        const code = saveButton._pendingCode || editor.getValue();
                                        const blob = new Blob([code], { type: 'text/plain;charset=utf-8' });
                                        const url = URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download = filename;
                                        document.body.appendChild(a);
                                        a.click();
                                        a.remove();
                                        URL.revokeObjectURL(url);
                                        closeSaveModal();
                                    });

                                    // Close modal on overlay click or Escape
                                    saveModal && saveModal.addEventListener('click', (ev) => {
                                        if (ev.target === saveModal) closeSaveModal();
                                    });
                                    document.addEventListener('keydown', (ev) => {
                                        if (ev.key === 'Escape' && saveModal && !saveModal.classList.contains('hidden')) {
                                            closeSaveModal();
                                        }
                                    });
                                }

                                // Run button logic
                                const runButton = document.getElementById('runButton');
                                const runButtonText = document.getElementById('runButtonText');
                                const runIcon = document.getElementById('runIcon');
                                const loadingSpinner = document.getElementById('loadingSpinner');
                                const outputElement = document.getElementById('output');
                                const outputTab = document.getElementById('outputTab');

                                function setButtonsState(running) { if (!runButton) return; runButton.disabled = running; if (running) { runIcon && runIcon.classList.add('hidden'); loadingSpinner && loadingSpinner.classList.remove('hidden'); } else { runIcon && runIcon.classList.remove('hidden'); loadingSpinner && loadingSpinner.classList.add('hidden'); } }

                                runButton && runButton.addEventListener('click', function() {
                                    const code = editor.getValue();
                                    setButtonsState(true);
                                    outputElement.textContent = 'Compiling and running...';
                                    outputElement.classList.remove('text-red-400');
                                    runButtonText.textContent = 'Running...';
                                    outputTab && outputTab.click();

                                    fetch('{{ route('mahasiswa.compiler.run') }}', {
                                        method: 'POST',
                                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                                        body: JSON.stringify({ code: code })
                                    })
                                    .then(response => {
                                        const contentType = response.headers.get('content-type') || '';
                                        if (!response.ok) {
                                            if (contentType.indexOf('application/json') !== -1) return response.json().then(err => { throw err; });
                                            return response.text().then(text => { throw { output: text, status: response.status }; });
                                        }
                                        if (contentType.indexOf('application/json') !== -1) return response.json();
                                        return response.text().then(text => ({ status: 'error', output: text }));
                                    })
                                    .then(data => {
                                        outputElement.textContent = '';
                                        if (data.status === 'error') { outputElement.textContent = data.output || 'An unknown error occurred.'; outputElement.classList.add('text-red-400'); }
                                        else { outputElement.textContent = data.output.trim() === '' ? '(Program executed successfully with no output)' : data.output; }
                                    })
                                    .catch(error => { console.error('Error:', error); outputElement.textContent = error.output || 'An error occurred while running the code. Please check the browser console for details.'; outputElement.classList.add('text-red-400'); })
                                    .finally(() => { setButtonsState(false); runButtonText.textContent = 'Run Code'; });
                                });

                                // --- Trace functionality ---
                                const traceToggleBtn = document.getElementById('traceToggleBtn');
                                const tracePanel = document.getElementById('tracePanel');
                                const tracePlay = document.getElementById('tracePlay');
                                const tracePause = document.getElementById('tracePause');
                                const traceStep = document.getElementById('traceStep');
                                const traceReset = document.getElementById('traceReset');
                                const traceStepsList = document.getElementById('traceStepsList');
                                const traceExplanation = document.getElementById('traceExplanation');
                                const traceSpeed = document.getElementById('traceSpeed');

                                let traceSteps = [];
                                let traceIndex = -1;
                                let traceTimer = null;
                                let traceDecorations = [];
                                let vars = {}; // simulated variables map

                                function simpleExplainLine(lineText, lineNumber) {
                                    const t = lineText.trim();
                                    if (!t) return `Baris ${lineNumber}: (kosong)`;
                                    if (/System\.out\.println\(/.test(t)) return `Baris ${lineNumber}: Cetak teks ke layar (System.out.println). Menampilkan apa yang ada di dalam tanda kurung.`;
                                    if (/for\s*\(/.test(t)) return `Baris ${lineNumber}: Mulai perulangan "for". Ini mengulang blok kode beberapa kali.`;
                                    if (/while\s*\(/.test(t)) return `Baris ${lineNumber}: Mulai perulangan "while" yang berjalan selama kondisinya benar.`;
                                    if (/if\s*\(/.test(t)) return `Baris ${lineNumber}: Cek kondisi "if". Jika benar, jalankan blok kode di dalamnya.`;
                                    if (/return\s+/.test(t)) return `Baris ${lineNumber}: Mengembalikan nilai dari sebuah fungsi/method (return).`;
                                    if (/class\s+\w+/.test(t)) return `Baris ${lineNumber}: Deklarasi kelas Java.`;
                                    if (/public\s+static\s+void\s+main/.test(t)) return `Baris ${lineNumber}: Titik masuk program Java: method main.`;
                                    // variable declaration with init
                                    const decl = t.match(/^\s*(int|double|long|float|String|boolean)\s+([A-Za-z_]\w*)\s*=\s*(.+);$/);
                                    if (decl) return `Baris ${lineNumber}: Deklarasi variabel ${decl[2]} tipe ${decl[1]} dan diberi nilai.`;
                                    // assignment
                                    const assign = t.match(/^\s*([A-Za-z_]\w*)\s*=\s*(.+);$/);
                                    if (assign) return `Baris ${lineNumber}: Menetapkan nilai ke variabel ${assign[1]}.`;
                                    if (/new\s+\w+\(/.test(t)) return `Baris ${lineNumber}: Membuat objek baru dengan kata kunci "new".`;
                                    if (/\w+\(.*\)\s*;/.test(t)) return `Baris ${lineNumber}: Memanggil sebuah method/fungsi.`;
                                    return `Baris ${lineNumber}: ${t.replace(/</g,'&lt;').replace(/>/g,'&gt;')}`;
                                }

                                function evaluateExpression(expr) {
                                    if (!expr) return undefined;
                                    expr = expr.trim();
                                    // string literal
                                    const strMatch = expr.match(/^"([\s\S]*)"$/) || expr.match(/^'([\s\S]*)'$/);
                                    if (strMatch) return strMatch[1];
                                    // numeric literal
                                    if (/^[+-]?\d+$/.test(expr)) return parseInt(expr, 10);
                                    if (/^[+-]?(?:\d+\.\d+|\d+)$/.test(expr)) return parseFloat(expr);
                                    // simple concatenation with + (strings or numbers)
                                    if (expr.includes('+')) {
                                        // split by + not inside quotes (simple)
                                        const parts = expr.split('+').map(p => p.trim());
                                        let anyString = false;
                                        const vals = parts.map(part => {
                                            // literal string
                                            const sm = part.match(/^"([\s\S]*)"$/) || part.match(/^'([\s\S]*)'$/);
                                            if (sm) { anyString = true; return sm[1]; }
                                            // variable
                                            if (/^[A-Za-z_]\w*$/.test(part)) {
                                                const v = vars[part];
                                                if (v === undefined) return '?';
                                                if (typeof v === 'string') anyString = true;
                                                return v;
                                            }
                                            // numeric
                                            if (/^[+-]?\d+$/.test(part)) return parseInt(part,10);
                                            if (/^[+-]?(?:\d+\.\d+|\d+)$/.test(part)) return parseFloat(part);
                                            return '?';
                                        });
                                        if (anyString) return vals.map(v => String(v)).join('');
                                        // sum numbers
                                        if (vals.every(v => typeof v === 'number')) return vals.reduce((a,b)=>a+b,0);
                                        return vals.join(' + ');
                                    }
                                    // variable reference
                                    if (/^[A-Za-z_]\w*$/.test(expr)) return vars[expr];
                                    // unknown - give raw
                                    return undefined;
                                }

                                function applyLineEffects(lineText) {
                                    const t = lineText.trim();
                                    const decl = t.match(/^\s*(int|double|long|float|String|boolean)\s+([A-Za-z_]\w*)\s*=\s*(.+);$/);
                                    if (decl) {
                                        const type = decl[1]; const name = decl[2]; const rhs = decl[3];
                                        const val = evaluateExpression(rhs);
                                        vars[name] = val !== undefined ? val : ('<unknown>');
                                        return { action: 'decl', name, value: vars[name] };
                                    }
                                    const assign = t.match(/^\s*([A-Za-z_]\w*)\s*=\s*(.+);$/);
                                    if (assign) {
                                        const name = assign[1]; const rhs = assign[2];
                                        const val = evaluateExpression(rhs);
                                        vars[name] = val !== undefined ? val : ('<unknown>');
                                        return { action: 'assign', name, value: vars[name] };
                                    }
                                    return null;
                                }

                                function renderVars() {
                                    const container = document.getElementById('varsList');
                                    if (!container) return;
                                    container.innerHTML = '';
                                    Object.keys(vars).forEach(k => {
                                        const v = vars[k];
                                        const div = document.createElement('div');
                                        div.className = 'flex items-center justify-between p-2 border-b last:border-b-0';
                                        div.innerHTML = `<div class="text-sm text-gray-700 dark:text-gray-200">${k}</div><div class="text-sm font-medium text-gray-900 dark:text-gray-100">${String(v)}</div>`;
                                        container.appendChild(div);
                                    });
                                }

                                function generateTrace(code) {
                                    vars = {}; // reset simulation
                                    const lines = code.split(/\r?\n/);
                                    const steps = [];
                                    for (let i = 0; i < lines.length; i++) {
                                        const ln = i + 1;
                                        const text = lines[i];
                                        const explanation = simpleExplainLine(text, ln);
                                        steps.push({ line: ln, text: text, explanation: explanation });
                                    }
                                    return steps;
                                }

                                function renderTraceList() {
                                    traceStepsList.innerHTML = '';
                                    traceSteps.forEach((s, idx) => {
                                        const li = document.createElement('li');
                                        li.className = 'p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer';
                                        li.innerHTML = `<div class="text-xs text-gray-500">Baris ${s.line}</div><div class="text-sm text-gray-700 dark:text-gray-200">${s.text.trim() || '(kosong)'}</div>`;
                                        li.addEventListener('click', () => showTraceStep(idx));
                                        traceStepsList.appendChild(li);
                                    });
                                }

                                function showTraceStep(index) {
                                    if (index < 0 || index >= traceSteps.length) return;
                                    traceIndex = index;
                                    const s = traceSteps[index];
                                    try {
                                        const model = editor.getModel();
                                        const startCol = 1;
                                        const endCol = model.getLineMaxColumn(Math.min(s.line, model.getLineCount()));
                                        traceDecorations = editor.deltaDecorations(traceDecorations, [
                                            { range: new monaco.Range(s.line, startCol, s.line, endCol), options: { className: 'traceHighlight', isWholeLine: true } }
                                        ]);
                                        editor.revealPositionInCenter({ lineNumber: s.line, column: 1 });
                                    } catch (e) { console.warn(e); }
                                    // apply effects (variable updates) for this line
                                    const effect = applyLineEffects(s.text);
                                    const header = `<div class="font-semibold mb-1">Langkah ${index+1} / ${traceSteps.length}</div>`;
                                    let explanationHtml = '';
                                    const explText = (s.explanation || '').trim();
                                    // only show explanation when it has meaningful content (skip '(kosong)')
                                    if (explText && !(/^[\s]*Baris\s+\d+:\s*\(kosong\)\s*$/i.test(explText))) {
                                        explanationHtml = `<div>${explText}</div>`;
                                    }
                                    const parentCol = traceExplanation && traceExplanation.parentElement;
                                    const hasContent = explanationHtml || effect;
                                    if (!hasContent) {
                                        // hide the whole explanation column when there's no explanation and no effect
                                        if (parentCol) parentCol.classList.add('hidden');
                                        traceExplanation.innerHTML = '';
                                    } else {
                                        if (parentCol) parentCol.classList.remove('hidden');
                                        if (effect) {
                                            traceExplanation.innerHTML = header + explanationHtml + `<div class="mt-2 text-sm text-green-700">Perubahan: ${effect.name} = ${String(effect.value)}</div>`;
                                        } else {
                                            traceExplanation.innerHTML = header + explanationHtml;
                                        }
                                    }
                                    renderVars();
                                }

                                function resetTrace() {
                                    traceIndex = -1;
                                    traceSteps = [];
                                    traceStepsList.innerHTML = '';
                                    traceExplanation.innerHTML = '';
                                    // ensure explanation column is visible again for next usage
                                    try { const parentCol = traceExplanation && traceExplanation.parentElement; if (parentCol) parentCol.classList.remove('hidden'); } catch(e) {}
                                    traceDecorations = editor.deltaDecorations(traceDecorations, []);
                                    vars = {};
                                    renderVars();
                                    if (traceTimer) { clearInterval(traceTimer); traceTimer = null; }
                                }

                                function startAutoPlay() {
                                    if (traceSteps.length === 0) return;
                                    if (traceTimer) clearInterval(traceTimer);
                                    traceTimer = setInterval(() => {
                                        if (traceIndex >= traceSteps.length - 1) { clearInterval(traceTimer); traceTimer = null; return; }
                                        showTraceStep(traceIndex + 1);
                                    }, parseInt(traceSpeed.value, 10));
                                }

                                traceToggleBtn && traceToggleBtn.addEventListener('click', () => {
                                    if (!tracePanel) return;
                                    if (tracePanel.classList.contains('hidden')) {
                                        const code = editor.getValue();
                                        traceSteps = generateTrace(code);
                                        renderTraceList();
                                        tracePanel.classList.remove('hidden');
                                        showTraceStep(0);
                                    } else {
                                        tracePanel.classList.add('hidden');
                                        resetTrace();
                                    }
                                });

                                tracePlay && tracePlay.addEventListener('click', () => {
                                    if (traceSteps.length === 0) { traceSteps = generateTrace(editor.getValue()); renderTraceList(); }
                                    if (traceIndex < 0) showTraceStep(0);
                                    startAutoPlay();
                                });
                                tracePause && tracePause.addEventListener('click', () => { if (traceTimer) { clearInterval(traceTimer); traceTimer = null; } });
                                traceStep && traceStep.addEventListener('click', () => { if (traceSteps.length === 0) { traceSteps = generateTrace(editor.getValue()); renderTraceList(); showTraceStep(0); return; } const next = Math.min(traceIndex+1, traceSteps.length-1); showTraceStep(next); });
                                traceReset && traceReset.addEventListener('click', () => { resetTrace(); tracePanel.classList.add('hidden'); });
                                const traceCloseBtn = document.getElementById('traceCloseBtn');
                                traceCloseBtn && traceCloseBtn.addEventListener('click', () => { if (tracePanel) { resetTrace(); tracePanel.classList.add('hidden'); } });

                                (function(){
                                    const css = `.traceHighlight { background: rgba(250, 204, 21, 0.12); border-left: 4px solid rgba(250,204,21,0.9); }`;
                                    const style = document.createElement('style'); style.appendChild(document.createTextNode(css)); document.head.appendChild(style);
                                })();
                            });
                        };
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
