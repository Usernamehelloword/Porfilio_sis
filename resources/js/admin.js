/* Studio Volume — Admin Panel interactions */

document.addEventListener('DOMContentLoaded', () => {

    /* ------------------------------------------------ sidebar collapse */
    const body = document.body;
    if (localStorage.getItem('admin-sidebar') === 'collapsed') {
        body.classList.add('sidebar-collapsed');
    }

    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
        body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('admin-sidebar', body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'open');
    });

    /* ------------------------------------------------ delete confirmation */
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (! window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    /* ------------------------------------------------ rich text editors */
    document.querySelectorAll('textarea.rich').forEach((textarea) => {
        const editor = document.createElement('div');
        const toolbar = document.createElement('div');

        toolbar.className = 'rich-toolbar';
        editor.className = 'rich-editor';
        editor.contentEditable = 'true';

        const commands = [
            ['B', 'bold', 'Bold'],
            ['I', 'italic', 'Italic'],
            ['H2', 'formatBlock:h2', 'Heading'],
            ['H3', 'formatBlock:h3', 'Subheading'],
            ['¶', 'formatBlock:p', 'Paragraph'],
            ['“ ”', 'formatBlock:blockquote', 'Quote'],
            ['• List', 'insertUnorderedList', 'Bullet list'],
            ['1. List', 'insertOrderedList', 'Numbered list'],
            ['Link', 'createLink', 'Insert link'],
            ['✕', 'removeFormat', 'Clear formatting'],
        ];

        const sync = () => {
            textarea.value = editor.innerHTML;
        };

        commands.forEach(([label, command, title]) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.title = title;
            button.addEventListener('mousedown', (event) => event.preventDefault());
            button.addEventListener('click', () => {
                if (command === 'createLink') {
                    const url = window.prompt('Link URL:', 'https://');
                    if (url) document.execCommand('createLink', false, url);
                    sync();
                    return;
                }

                if (command.startsWith('formatBlock')) {
                    document.execCommand('formatBlock', false, command.split(':')[1]);
                    sync();
                    return;
                }

                document.execCommand(command, false, null);
                sync();
            });
            toolbar.appendChild(button);
        });

        editor.innerHTML = textarea.value;
        editor.addEventListener('input', sync);

        textarea.hidden = true;
        textarea.parentNode.insertBefore(toolbar, textarea);
        textarea.parentNode.insertBefore(editor, textarea);

        textarea.closest('form')?.addEventListener('submit', sync);
    });

    /* ------------------------------------------------ drag & drop reorder */
    document.querySelectorAll('[data-reorder-list]').forEach((list) => {
        const endpoint = list.dataset.reorderList;

        if (! endpoint) {
            return;
        }

        let dragged = null;

        const saveOrder = () => {
            const ids = [...list.querySelectorAll('[data-id]')].map((el) => el.dataset.id);
            const data = new FormData();
            ids.forEach((id) => data.append('ids[]', id));

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: data,
            });
        };

        list.querySelectorAll('[data-id]').forEach((row) => {
            row.draggable = true;

            row.addEventListener('dragstart', () => {
                dragged = row;
                row.classList.add('dragging');
            });

            row.addEventListener('dragend', () => {
                row.classList.remove('dragging');
                saveOrder();
            });

            row.addEventListener('dragover', (event) => {
                event.preventDefault();

                if (dragged === null || dragged === row) {
                    return;
                }

                const rect = row.getBoundingClientRect();
                const after = (event.clientY - rect.top) / rect.height > 0.5;

                row.parentNode.insertBefore(dragged, after ? row.nextSibling : row);
            });
        });
    });

    /* ------------------------------------------------ media helpers */
    document.querySelectorAll('[data-copy-url]').forEach((button) => {
        button.addEventListener('click', () => {
            const url = button.closest('[data-url]').dataset.url;
            navigator.clipboard?.writeText(url);
            const original = button.textContent;
            button.textContent = 'Copied!';
            setTimeout(() => { button.textContent = original; }, 1400);
        });
    });

    // "Replace" labels hide a file input — submit when chosen.
    document.querySelectorAll('[data-autosubmit]').forEach((input) => {
        input.addEventListener('change', () => input.closest('form')?.submit());
    });

    // Drag files anywhere onto the media upload box to fill the input.
    const upload = document.getElementById('media-upload');

    if (upload) {
        const fileInput = upload.querySelector('input[type="file"]');

        upload.addEventListener('dragover', (event) => {
            event.preventDefault();
            upload.classList.add('dragover');
        });

        upload.addEventListener('dragleave', () => upload.classList.remove('dragover'));

        upload.addEventListener('drop', (event) => {
            event.preventDefault();
            upload.classList.remove('dragover');

            if (fileInput !== null && event.dataTransfer.files.length > 0) {
                fileInput.files = event.dataTransfer.files;
                upload.submit();
            }
        });
    }
});
