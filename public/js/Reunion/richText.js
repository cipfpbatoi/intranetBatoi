'use strict';

(function () {
    function createButton(label, command, title, asHtml) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-default btn-sm';
        if (asHtml) {
            button.innerHTML = label;
        } else {
            button.textContent = label;
        }
        button.setAttribute('data-command', command);
        button.setAttribute('title', title);
        button.setAttribute('aria-label', title);
        return button;
    }

    function createToolbar() {
        var toolbar = document.createElement('div');
        toolbar.className = 'reunion-richtext-toolbar';

        toolbar.appendChild(createButton('↶', 'undo', 'Desfer'));
        toolbar.appendChild(createButton('↷', 'redo', 'Rehacer'));
        toolbar.appendChild(createButton('B', 'bold', 'Negreta'));
        toolbar.appendChild(createButton('I', 'italic', 'Cursiva'));
        toolbar.appendChild(createButton('<i class="fa fa-list-ul" aria-hidden="true"></i>', 'insertUnorderedList', 'Llista amb punts', true));
        toolbar.appendChild(createButton('<i class="fa fa-list-ol" aria-hidden="true"></i>', 'insertOrderedList', 'Llista numerada', true));
        toolbar.appendChild(createButton('<i class="fa fa-link" aria-hidden="true"></i>', 'createLink', 'Inserir enllaç', true));
        toolbar.appendChild(createButton('<i class="fa fa-trash" aria-hidden="true"></i>', 'clearContent', 'Buidar resum', true));

        return toolbar;
    }

    function syncToTextarea(editor, textarea) {
        textarea.value = editor.innerHTML.trim();
    }

    function initRichEditor(textarea) {
        if (!textarea || textarea.dataset.richEditorReady === '1') {
            return;
        }

        var wrapper = document.createElement('div');
        wrapper.className = 'reunion-richtext-wrapper';

        var toolbar = createToolbar();
        var editor = document.createElement('div');
        editor.className = 'reunion-richtext-editor form-control';
        editor.setAttribute('contenteditable', 'true');
        editor.setAttribute('role', 'textbox');
        editor.setAttribute('aria-label', textarea.getAttribute('placeholder') || 'Editor de text');
        editor.innerHTML = textarea.value || '';

        textarea.style.display = 'none';
        textarea.parentNode.insertBefore(wrapper, textarea);
        wrapper.appendChild(toolbar);
        wrapper.appendChild(editor);
        wrapper.appendChild(textarea);

        toolbar.addEventListener('click', function (event) {
            var button = event.target.closest('button[data-command]');
            if (!button) {
                return;
            }

            event.preventDefault();
            editor.focus();
            var command = button.getAttribute('data-command');
            if (command === 'createLink') {
                var url = window.prompt('URL de l\'enllaç:', 'https://');
                if (!url) {
                    return;
                }
                document.execCommand('createLink', false, url);
            } else if (command === 'clearContent') {
                editor.innerHTML = '';
            } else {
                document.execCommand(command, false, null);
            }
            syncToTextarea(editor, textarea);
        });

        editor.addEventListener('input', function () {
            syncToTextarea(editor, textarea);
        });

        editor.addEventListener('blur', function () {
            syncToTextarea(editor, textarea);
        });

        var form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                syncToTextarea(editor, textarea);
            });
        }

        textarea.dataset.richEditorReady = '1';
    }

    function initResumenEditors(root) {
        var scope = root || document;
        var textareas = scope.querySelectorAll('textarea[name="resumen"]');
        Array.prototype.forEach.call(textareas, function (textarea) {
            initRichEditor(textarea);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initResumenEditors(document);

        var table = document.querySelector('table[name="ordenreunion"]');
        if (!table) {
            return;
        }

        table.addEventListener('click', function (event) {
            if (!event.target.closest('.editGrupo')) {
                return;
            }

            // Tabledit injecta el textarea després del click.
            setTimeout(function () {
                initResumenEditors(table);
            }, 0);
        });

        var observer = new MutationObserver(function () {
            initResumenEditors(table);
        });
        observer.observe(table, { childList: true, subtree: true });
    });
})();
