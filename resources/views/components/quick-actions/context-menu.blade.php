@props([
    'actions' => [],
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'trigger' => 'click', // click, hover, manual
    'size' => 'md', // sm, md, lg
])

@php
    $positionClasses = [
        'bottom-right' => 'top-full right-0 mt-2',
        'bottom-left' => 'top-full left-0 mt-2',
        'top-right' => 'bottom-full right-0 mb-2',
        'top-left' => 'bottom-full left-0 mb-2',
    ];

    $sizeClasses = [
        'sm' => 'w-48',
        'md' => 'w-56',
        'lg' => 'w-64',
    ];
@endphp

<div class="relative inline-block" 
     x-data="contextMenu({{ json_encode($actions) }}, '{{ $trigger }}')"
     x-init="init()">
    
    <!-- Trigger Button -->
    <button @click="toggle()"
            @mouseenter="trigger === 'hover' && show()"
            @mouseleave="trigger === 'hover' && hide()"
            class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
            :class="{ 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20': isOpen }">
        <i class="fas fa-ellipsis-v"></i>
    </button>

    <!-- Context Menu -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="hide()"
         class="absolute {{ $positionClasses[$position] }} {{ $sizeClasses[$size] }} bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
         style="display: none;">
        
        <template x-for="(action, index) in filteredActions" :key="action.id || index">
            <div>
                <!-- Divider -->
                <div x-show="action.type === 'divider'" 
                     class="my-2 border-t border-gray-200 dark:border-gray-700"></div>
                
                <!-- Header -->
                <div x-show="action.type === 'header'" 
                     class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                     x-text="action.label"></div>
                
                <!-- Action Item -->
                <div x-show="!action.type || action.type === 'action'">
                    <!-- Link Action -->
                    <a x-show="action.url" 
                       :href="action.url"
                       @click="handleAction(action)"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                       :class="{
                           'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20': action.variant === 'danger',
                           'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20': action.variant === 'success',
                           'text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20': action.variant === 'warning'
                       }">
                        
                        <i x-show="action.icon" :class="action.icon" class="mr-3 text-sm flex-shrink-0"></i>
                        <span class="flex-1" x-text="action.label"></span>
                        <span x-show="action.shortcut" 
                              class="ml-2 text-xs text-gray-400 dark:text-gray-500 font-mono" 
                              x-text="action.shortcut"></span>
                    </a>
                    
                    <!-- Button Action -->
                    <button x-show="!action.url" 
                            @click="handleAction(action)"
                            class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 text-left"
                            :class="{
                                'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20': action.variant === 'danger',
                                'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20': action.variant === 'success',
                                'text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20': action.variant === 'warning'
                            }"
                            :disabled="action.disabled">
                        
                        <i x-show="action.icon" :class="action.icon" class="mr-3 text-sm flex-shrink-0"></i>
                        <span class="flex-1" x-text="action.label"></span>
                        <span x-show="action.shortcut" 
                              class="ml-2 text-xs text-gray-400 dark:text-gray-500 font-mono" 
                              x-text="action.shortcut"></span>
                    </button>
                </div>
            </div>
        </template>
        
        <!-- Empty State -->
        <div x-show="filteredActions.length === 0" 
             class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
            No actions available
        </div>
    </div>
</div>

<script>
function contextMenu(actions, triggerType) {
    return {
        isOpen: false,
        actions: actions,
        trigger: triggerType,
        
        init() {
            // Setup keyboard shortcuts for actions
            this.setupKeyboardShortcuts();
        },
        
        get filteredActions() {
            return this.actions.filter(action => {
                // Filter based on permissions
                if (action.permission && !this.hasPermission(action.permission)) {
                    return false;
                }
                
                // Filter based on conditions
                if (action.condition && !this.evaluateCondition(action.condition)) {
                    return false;
                }
                
                return true;
            });
        },
        
        show() {
            this.isOpen = true;
        },
        
        hide() {
            this.isOpen = false;
        },
        
        toggle() {
            this.isOpen = !this.isOpen;
        },
        
        handleAction(action) {
            // Close menu
            this.hide();
            
            // Execute action callback if provided
            if (action.callback && typeof action.callback === 'function') {
                action.callback();
            }
            
            // Handle confirmation for dangerous actions
            if (action.variant === 'danger' && action.confirm) {
                if (!confirm(action.confirm)) {
                    return;
                }
            }
            
            // Track action usage
            this.trackActionUsage(action);
            
            // Show toast notification if specified
            if (action.toast) {
                window.showToast({
                    type: action.variant || 'info',
                    message: action.toast
                });
            }
        },
        
        hasPermission(permission) {
            // This would integrate with your permission system
            // For now, return true
            return true;
        },
        
        evaluateCondition(condition) {
            // This would evaluate dynamic conditions
            // For now, return true
            return true;
        },
        
        trackActionUsage(action) {
            // Track action usage for analytics
            if (action.id) {
                const usage = JSON.parse(localStorage.getItem('context_action_usage') || '{}');
                usage[action.id] = (usage[action.id] || 0) + 1;
                localStorage.setItem('context_action_usage', JSON.stringify(usage));
            }
        },
        
        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                if (!this.isOpen) return;
                
                // Handle escape key
                if (e.key === 'Escape') {
                    this.hide();
                    return;
                }
                
                // Handle action shortcuts
                const action = this.filteredActions.find(a => {
                    if (!a.shortcut) return false;
                    
                    const keys = a.shortcut.toLowerCase().split('+').map(k => k.trim());
                    const hasCtrl = keys.includes('ctrl') && (e.ctrlKey || e.metaKey);
                    const hasShift = keys.includes('shift') && e.shiftKey;
                    const hasAlt = keys.includes('alt') && e.altKey;
                    const key = keys[keys.length - 1];
                    
                    return (!keys.includes('ctrl') || hasCtrl) &&
                           (!keys.includes('shift') || hasShift) &&
                           (!keys.includes('alt') || hasAlt) &&
                           e.key.toLowerCase() === key;
                });
                
                if (action) {
                    e.preventDefault();
                    this.handleAction(action);
                }
            });
        }
    }
}
</script>

<style>
/* Custom scrollbar for long menus */
.context-menu::-webkit-scrollbar {
    width: 4px;
}

.context-menu::-webkit-scrollbar-track {
    background: transparent;
}

.context-menu::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.context-menu::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
