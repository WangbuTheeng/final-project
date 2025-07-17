/**
 * Global Keyboard Shortcuts System
 * Provides comprehensive keyboard navigation and shortcuts for the application
 */

class KeyboardShortcuts {
    constructor() {
        this.shortcuts = new Map();
        this.contexts = new Map();
        this.currentContext = 'global';
        this.isEnabled = true;
        this.helpVisible = false;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.registerDefaultShortcuts();
        this.loadUserPreferences();
    }

    setupEventListeners() {
        document.addEventListener('keydown', (e) => {
            if (!this.isEnabled) return;
            
            this.handleKeyDown(e);
        });

        // Prevent default browser shortcuts that might conflict
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                const key = e.key.toLowerCase();
                const preventKeys = ['s', 'p', 'f', 'r', 'w', 't', 'n'];
                
                if (preventKeys.includes(key) && this.hasShortcut(this.getKeyCombo(e))) {
                    e.preventDefault();
                }
            }
        });
    }

    registerDefaultShortcuts() {
        // Global shortcuts
        this.register('global', {
            'ctrl+/': {
                description: 'Show keyboard shortcuts help',
                action: () => this.toggleHelp(),
                category: 'General'
            },
            'ctrl+k': {
                description: 'Open command palette',
                action: () => this.openCommandPalette(),
                category: 'Navigation'
            },
            'ctrl+shift+d': {
                description: 'Go to dashboard',
                action: () => window.location.href = '/dashboard',
                category: 'Navigation'
            },
            'ctrl+shift+n': {
                description: 'Open notifications',
                action: () => this.openNotifications(),
                category: 'Navigation'
            },
            'ctrl+shift+a': {
                description: 'Open activity feed',
                action: () => window.location.href = '/activities',
                category: 'Navigation'
            },
            'ctrl+shift+p': {
                description: 'Open profile',
                action: () => window.location.href = '/profile',
                category: 'Navigation'
            },
            'ctrl+shift+,': {
                description: 'Open settings',
                action: () => window.location.href = '/settings',
                category: 'Navigation'
            },
            'escape': {
                description: 'Close modals/dropdowns',
                action: () => this.closeModals(),
                category: 'General'
            },
            'ctrl+shift+l': {
                description: 'Logout',
                action: () => this.logout(),
                category: 'General'
            }
        });

        // Student management shortcuts
        this.register('students', {
            'ctrl+shift+s': {
                description: 'Add new student',
                action: () => window.location.href = '/students/create',
                category: 'Students'
            },
            'ctrl+e': {
                description: 'Edit selected student',
                action: () => this.editSelected('student'),
                category: 'Students'
            },
            'ctrl+d': {
                description: 'Delete selected student',
                action: () => this.deleteSelected('student'),
                category: 'Students'
            }
        });

        // Teacher management shortcuts
        this.register('teachers', {
            'ctrl+shift+t': {
                description: 'Add new teacher',
                action: () => window.location.href = '/teachers/create',
                category: 'Teachers'
            },
            'ctrl+e': {
                description: 'Edit selected teacher',
                action: () => this.editSelected('teacher'),
                category: 'Teachers'
            }
        });

        // Course management shortcuts
        this.register('courses', {
            'ctrl+shift+c': {
                description: 'Add new course',
                action: () => window.location.href = '/courses/create',
                category: 'Courses'
            },
            'ctrl+e': {
                description: 'Edit selected course',
                action: () => this.editSelected('course'),
                category: 'Courses'
            }
        });

        // Form shortcuts
        this.register('forms', {
            'ctrl+s': {
                description: 'Save form',
                action: () => this.saveForm(),
                category: 'Forms'
            },
            'ctrl+shift+enter': {
                description: 'Submit form',
                action: () => this.submitForm(),
                category: 'Forms'
            },
            'escape': {
                description: 'Cancel form',
                action: () => this.cancelForm(),
                category: 'Forms'
            }
        });

        // Table shortcuts
        this.register('tables', {
            'ctrl+a': {
                description: 'Select all rows',
                action: () => this.selectAllRows(),
                category: 'Tables'
            },
            'ctrl+shift+a': {
                description: 'Deselect all rows',
                action: () => this.deselectAllRows(),
                category: 'Tables'
            },
            'delete': {
                description: 'Delete selected rows',
                action: () => this.deleteSelectedRows(),
                category: 'Tables'
            },
            'ctrl+f': {
                description: 'Focus search',
                action: () => this.focusSearch(),
                category: 'Tables'
            }
        });
    }

    register(context, shortcuts) {
        if (!this.contexts.has(context)) {
            this.contexts.set(context, new Map());
        }

        const contextShortcuts = this.contexts.get(context);
        
        Object.entries(shortcuts).forEach(([key, config]) => {
            contextShortcuts.set(key, config);
        });
    }

    unregister(context, key = null) {
        if (!this.contexts.has(context)) return;

        if (key) {
            this.contexts.get(context).delete(key);
        } else {
            this.contexts.delete(context);
        }
    }

    setContext(context) {
        this.currentContext = context;
    }

    handleKeyDown(e) {
        const keyCombo = this.getKeyCombo(e);
        
        // Check current context first
        if (this.executeShortcut(this.currentContext, keyCombo, e)) {
            return;
        }

        // Fall back to global context
        if (this.currentContext !== 'global') {
            this.executeShortcut('global', keyCombo, e);
        }
    }

    executeShortcut(context, keyCombo, event) {
        if (!this.contexts.has(context)) return false;

        const contextShortcuts = this.contexts.get(context);
        const shortcut = contextShortcuts.get(keyCombo);

        if (shortcut) {
            // Check if we should prevent default
            if (shortcut.preventDefault !== false) {
                event.preventDefault();
            }

            // Execute the action
            if (typeof shortcut.action === 'function') {
                shortcut.action(event);
            }

            // Track usage
            this.trackShortcutUsage(context, keyCombo);

            return true;
        }

        return false;
    }

    getKeyCombo(event) {
        const parts = [];
        
        if (event.ctrlKey || event.metaKey) parts.push('ctrl');
        if (event.altKey) parts.push('alt');
        if (event.shiftKey) parts.push('shift');
        
        const key = event.key.toLowerCase();
        if (key !== 'control' && key !== 'alt' && key !== 'shift' && key !== 'meta') {
            parts.push(key);
        }

        return parts.join('+');
    }

    hasShortcut(keyCombo) {
        // Check current context
        if (this.contexts.has(this.currentContext)) {
            if (this.contexts.get(this.currentContext).has(keyCombo)) {
                return true;
            }
        }

        // Check global context
        if (this.currentContext !== 'global' && this.contexts.has('global')) {
            return this.contexts.get('global').has(keyCombo);
        }

        return false;
    }

    // Action implementations
    toggleHelp() {
        this.helpVisible = !this.helpVisible;
        
        if (this.helpVisible) {
            this.showHelpModal();
        } else {
            this.hideHelpModal();
        }
    }

    showHelpModal() {
        const modal = this.createHelpModal();
        document.body.appendChild(modal);
        
        // Initialize Alpine.js for the modal
        if (window.Alpine) {
            Alpine.initTree(modal);
        }
    }

    hideHelpModal() {
        const modal = document.getElementById('keyboard-shortcuts-help');
        if (modal) {
            modal.remove();
        }
        this.helpVisible = false;
    }

    createHelpModal() {
        const modal = document.createElement('div');
        modal.id = 'keyboard-shortcuts-help';
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.setAttribute('x-data', '{ show: true }');
        modal.setAttribute('x-show', 'show');
        modal.setAttribute('x-transition:enter', 'transition ease-out duration-300');
        modal.setAttribute('x-transition:enter-start', 'opacity-0');
        modal.setAttribute('x-transition:enter-end', 'opacity-100');

        const shortcuts = this.getAllShortcuts();
        const categories = this.groupShortcutsByCategory(shortcuts);

        modal.innerHTML = `
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                Keyboard Shortcuts
                            </h3>
                            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            ${Object.entries(categories).map(([category, shortcuts]) => `
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">${category}</h4>
                                    <div class="space-y-2">
                                        ${shortcuts.map(shortcut => `
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-600 dark:text-gray-300">${shortcut.description}</span>
                                                <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs font-mono text-gray-600 dark:text-gray-300">${shortcut.key}</kbd>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;

        return modal;
    }

    getAllShortcuts() {
        const allShortcuts = [];
        
        this.contexts.forEach((shortcuts, context) => {
            shortcuts.forEach((config, key) => {
                allShortcuts.push({
                    context,
                    key,
                    ...config
                });
            });
        });

        return allShortcuts;
    }

    groupShortcutsByCategory(shortcuts) {
        const categories = {};
        
        shortcuts.forEach(shortcut => {
            const category = shortcut.category || 'Other';
            if (!categories[category]) {
                categories[category] = [];
            }
            categories[category].push(shortcut);
        });

        return categories;
    }

    openCommandPalette() {
        // This would open a command palette component
        console.log('Opening command palette...');
        window.showInfoToast('Command palette coming soon!');
    }

    openNotifications() {
        // Trigger notification center
        const notificationButton = document.querySelector('[x-data*="notificationCenter"]');
        if (notificationButton) {
            notificationButton.click();
        }
    }

    closeModals() {
        // Close any open modals or dropdowns
        document.querySelectorAll('[x-data]').forEach(el => {
            if (el._x_dataStack && el._x_dataStack[0]) {
                const data = el._x_dataStack[0];
                if (data.isOpen !== undefined) data.isOpen = false;
                if (data.show !== undefined) data.show = false;
                if (data.visible !== undefined) data.visible = false;
            }
        });

        // Close help modal
        if (this.helpVisible) {
            this.hideHelpModal();
        }
    }

    logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '/logout';
        }
    }

    // Form actions
    saveForm() {
        const form = document.querySelector('form');
        if (form) {
            // Trigger save action (usually a draft save)
            const saveButton = form.querySelector('button[type="button"][data-action="save"]');
            if (saveButton) {
                saveButton.click();
            } else {
                window.showInfoToast('Save shortcut triggered');
            }
        }
    }

    submitForm() {
        const form = document.querySelector('form');
        if (form) {
            form.submit();
        }
    }

    cancelForm() {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            window.history.back();
        }
    }

    // Table actions
    selectAllRows() {
        const checkboxes = document.querySelectorAll('table input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = true);
    }

    deselectAllRows() {
        const checkboxes = document.querySelectorAll('table input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
    }

    deleteSelectedRows() {
        const selected = document.querySelectorAll('table input[type="checkbox"]:checked');
        if (selected.length > 0) {
            if (confirm(`Delete ${selected.length} selected item(s)?`)) {
                // Trigger bulk delete
                window.showInfoToast(`${selected.length} items selected for deletion`);
            }
        }
    }

    focusSearch() {
        const searchInput = document.querySelector('input[type="search"], input[placeholder*="search" i], input[placeholder*="filter" i]');
        if (searchInput) {
            searchInput.focus();
        }
    }

    // Generic actions
    editSelected(type) {
        const selected = document.querySelector('table input[type="checkbox"]:checked');
        if (selected) {
            const row = selected.closest('tr');
            const editButton = row.querySelector('a[href*="edit"], button[data-action="edit"]');
            if (editButton) {
                editButton.click();
            }
        } else {
            window.showWarningToast(`Please select a ${type} to edit`);
        }
    }

    deleteSelected(type) {
        const selected = document.querySelector('table input[type="checkbox"]:checked');
        if (selected) {
            if (confirm(`Are you sure you want to delete this ${type}?`)) {
                const row = selected.closest('tr');
                const deleteButton = row.querySelector('button[data-action="delete"], form[method*="delete"] button');
                if (deleteButton) {
                    deleteButton.click();
                }
            }
        } else {
            window.showWarningToast(`Please select a ${type} to delete`);
        }
    }

    trackShortcutUsage(context, keyCombo) {
        const usage = JSON.parse(localStorage.getItem('keyboard_shortcut_usage') || '{}');
        const key = `${context}:${keyCombo}`;
        usage[key] = (usage[key] || 0) + 1;
        localStorage.setItem('keyboard_shortcut_usage', JSON.stringify(usage));
    }

    loadUserPreferences() {
        const prefs = JSON.parse(localStorage.getItem('keyboard_shortcut_preferences') || '{}');
        this.isEnabled = prefs.enabled !== false;
    }

    saveUserPreferences() {
        const prefs = {
            enabled: this.isEnabled
        };
        localStorage.setItem('keyboard_shortcut_preferences', JSON.stringify(prefs));
    }

    enable() {
        this.isEnabled = true;
        this.saveUserPreferences();
    }

    disable() {
        this.isEnabled = false;
        this.saveUserPreferences();
    }

    toggle() {
        this.isEnabled = !this.isEnabled;
        this.saveUserPreferences();
    }
}

// Initialize global keyboard shortcuts
const keyboardShortcuts = new KeyboardShortcuts();

// Make it globally available
window.keyboardShortcuts = keyboardShortcuts;

// Auto-detect context based on current page
document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;
    
    if (path.includes('/students')) {
        keyboardShortcuts.setContext('students');
    } else if (path.includes('/teachers')) {
        keyboardShortcuts.setContext('teachers');
    } else if (path.includes('/courses')) {
        keyboardShortcuts.setContext('courses');
    } else if (document.querySelector('form')) {
        keyboardShortcuts.setContext('forms');
    } else if (document.querySelector('table')) {
        keyboardShortcuts.setContext('tables');
    }
});

export default KeyboardShortcuts;
