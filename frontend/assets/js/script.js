document.addEventListener('DOMContentLoaded', function() {
    initDarkMode();
    initBulkSelect();
    initFilters();
    initNotes();
    initAutoHideAlerts();
});

function initDarkMode() {
    const themeToggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('theme') || 'light';

    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        if (themeToggle) {
            themeToggle.innerHTML = '<svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>';
        }
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');

            if (isDark) {
                themeToggle.innerHTML = '<svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>';
            } else {
                themeToggle.innerHTML = '<svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>';
            }
        });
    }
}

function initBulkSelect() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const taskCheckboxes = document.querySelectorAll('.task-checkbox');
    const bulkActions = document.getElementById('bulkActions');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            taskCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                updateTaskItemStyle(checkbox);
            });
            updateBulkActionsVisibility();
        });
    }

    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateTaskItemStyle(this);
            updateBulkActionsVisibility();
            updateSelectAllState();
        });
    });

    window.executeBulkAction = function(action) {
        const selectedIds = Array.from(document.querySelectorAll('.task-checkbox:checked'))
            .map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one task');
            return;
        }

        if (action === 'delete' && !confirm('Are you sure you want to delete selected tasks?')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', action);
        selectedIds.forEach(id => formData.append('ids[]', id));

        fetch('../backend/bulk_operations.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'index.php?success=' + encodeURIComponent(data.message);
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    };

    function updateTaskItemStyle(checkbox) {
        const taskItem = checkbox.closest('.task-item');
        if (checkbox.checked) {
            taskItem.classList.add('selected');
        } else {
            taskItem.classList.remove('selected');
        }
    }

    function updateBulkActionsVisibility() {
        const checkedCount = document.querySelectorAll('.task-checkbox:checked').length;
        if (bulkActions) {
            if (checkedCount > 0) {
                bulkActions.classList.add('show');
                const countEl = document.getElementById('selectedCount');
                if (countEl) countEl.textContent = checkedCount;
            } else {
                bulkActions.classList.remove('show');
            }
        }
    }

    function updateSelectAllState() {
        if (selectAllCheckbox) {
            const allChecked = Array.from(taskCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(taskCheckboxes).some(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }
    }
}

function initFilters() {
    const filterForm = document.getElementById('filterForm');
    if (!filterForm) return;

    const inputs = filterForm.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    const clearFiltersBtn = document.getElementById('clearFilters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            window.location.href = 'index.php';
        });
    }
}

function initNotes() {
    const noteForm = document.getElementById('noteForm');
    if (!noteForm) return;

    noteForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(noteForm);

        fetch('../backend/notes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });

    loadNotes();
}

function loadNotes() {
    const notesContainer = document.getElementById('notesContainer');
    if (!notesContainer) return;

    const taskId = document.getElementById('task_id_for_notes')?.value;
    if (!taskId) return;

    fetch(`../backend/notes.php?task_id=${taskId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.notes.length > 0) {
                notesContainer.innerHTML = data.notes.map(note => `
                    <div class="note-item">
                        <p class="mb-1">${escapeHtml(note.note_text)}</p>
                        <small class="text-muted">${formatDate(note.created_at)}</small>
                    </div>
                `).join('');
            } else {
                notesContainer.innerHTML = '<p class="text-muted">No notes yet.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading notes:', error);
        });
}

function initAutoHideAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

window.quickComplete = function(taskId) {
    const formData = new FormData();
    formData.append('id', taskId);
    formData.append('status', 'completed');

    fetch('../backend/quick_update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
};
