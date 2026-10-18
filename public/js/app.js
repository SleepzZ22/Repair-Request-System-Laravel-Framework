/* ==========================================================================
   IT ServiceDesk - Frontend Interactive Scripts
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Sidebar Toggle
    const mobileToggle = document.getElementById('mobileNavToggle');
    const sidebar = document.querySelector('.app-sidebar');
    
    if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target) && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // 2. Interactive Category Card Picker
    const categoryCards = document.querySelectorAll('.category-card');
    const categoryInput = document.getElementById('selectedCategoryInput');

    categoryCards.forEach(card => {
        card.addEventListener('click', () => {
            categoryCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            if (categoryInput) {
                categoryInput.value = card.getAttribute('data-value');
            }
        });
    });

    // 3. Priority Selector Buttons
    const priorityButtons = document.querySelectorAll('.priority-btn');
    const priorityInput = document.getElementById('selectedPriorityInput');

    priorityButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            priorityButtons.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            if (priorityInput) {
                priorityInput.value = btn.getAttribute('data-val');
            }
        });
    });

    // 4. File Upload Drag & Drop and Instant Image Preview
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('imageFilesInput');
    const previewContainer = document.getElementById('imagePreviewContainer');

    if (uploadZone && fileInput) {
        uploadZone.addEventListener('click', () => fileInput.click());

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFilesPreview(fileInput.files);
            }
        });

        fileInput.addEventListener('change', () => {
            handleFilesPreview(fileInput.files);
        });
    }

    function handleFilesPreview(files) {
        if (!previewContainer) return;
        previewContainer.innerHTML = '';

        Array.from(files).forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const item = document.createElement('div');
                item.className = 'preview-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                    <button type="button" class="remove-btn" title="ลบรูป">&times;</button>
                `;

                item.querySelector('.remove-btn').addEventListener('click', (ev) => {
                    ev.stopPropagation();
                    item.remove();
                });

                previewContainer.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }

    // 5. Filter Tabs on Dashboard / Ticket List
    const filterTabs = document.querySelectorAll('.filter-tab-btn');
    const ticketRows = document.querySelectorAll('.ticket-table-row');

    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const filterStatus = tab.getAttribute('data-filter');

            ticketRows.forEach(row => {
                if (filterStatus === 'all' || row.getAttribute('data-status') === filterStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // 6. Modal Toggles
    const modalOpenButtons = document.querySelectorAll('[data-open-modal]');
    const modalCloseButtons = document.querySelectorAll('[data-close-modal]');

    modalOpenButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetModalId = btn.getAttribute('data-open-modal');
            const targetModal = document.getElementById(targetModalId);
            if (targetModal) {
                targetModal.style.display = 'flex';
            }
        });
    });

    modalCloseButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal-overlay');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Close modal on background click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
});
