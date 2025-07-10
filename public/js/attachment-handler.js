/**
 * Format bytes as human-readable string.
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Update preview of selected files.
 * @param {HTMLInputElement} input
 * @param {HTMLElement} preview
 */
function updateFilePreview(input, preview) {
    preview.innerHTML = '';
    const files = Array.from(input.files);

    if (files.length === 0) {
        preview.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">No files selected</p>';
        return;
    }

    const fileList = document.createElement('div');
    fileList.className = 'space-y-2';

    files.forEach(file => {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'flex items-center justify-between p-2 rounded bg-gray-50 dark:bg-gray-700';

        const fileInfo = document.createElement('div');
        fileInfo.className = 'flex items-center space-x-2';
        fileInfo.innerHTML = `
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
            </svg>
            <span class="text-sm text-gray-700 dark:text-gray-300">${file.name}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">(${formatFileSize(file.size)})</span>
        `;

        fileDiv.appendChild(fileInfo);
        fileList.appendChild(fileDiv);
    });

    preview.appendChild(fileList);
}

/**
 * Initialize attachment handler.
 * @param {string} inputSelector CSS selector for file input
 * @param {string} previewSelector CSS selector for preview container
 * @param {string} dropZoneSelector CSS selector for drop zone (optional)
 */
function initAttachmentHandler(inputSelector, previewSelector, dropZoneSelector = null) {
    const input = document.querySelector(inputSelector);
    const preview = document.querySelector(previewSelector);

    if (!input || !preview) return;

    input.addEventListener('change', () => updateFilePreview(input, preview));

    if (dropZoneSelector) {
        const dropZone = document.querySelector(dropZoneSelector);
        if (!dropZone) return;

        // Prevent default behavior for all drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => highlight(dropZone), false);
        });

        ['dragleave'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => unhighlight(dropZone), false);
        });

        // Handle drop event properly
        dropZone.addEventListener('drop', (e) => {
            preventDefaults(e);
            unhighlight(dropZone);

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                // Create a new DataTransfer object and add files
                const dt = new DataTransfer();
                Array.from(files).forEach(file => dt.items.add(file));

                // Set the files to the input element
                input.files = dt.files;
                updateFilePreview(input, preview);
            }
        }, false);
    }
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(dropZone) {
    dropZone.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-blue-900', 'dark:bg-opacity-20');
}

function unhighlight(dropZone) {
    dropZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-blue-900', 'dark:bg-opacity-20');
}

/**
 * Remove attachment (only for edit view).
 * @param {number|string} attachmentId
 */
function removeAttachment(attachmentId) {
    if (!confirm('Are you sure you want to remove this attachment?')) return;

    fetch(`/tickets/attachments/${attachmentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
        .then(async response => {
            const data = await response.json();
            if (response.ok) {
                const element = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
                if (element) element.remove();

                // Optionally remove header if empty
                const container = document.querySelector('.grid');
                if (container && !container.hasChildNodes()) {
                    const header = document.querySelector('.mb-4');
                    if (header) header.remove();
                }
            } else {
                throw new Error(data.message || 'Failed to delete attachment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Failed to delete attachment');
        });
}
