@props([
    'shortcuts' => [],
    'customizable' => true,
    'columns' => 4,
])

@php
    $defaultShortcuts = [
        [
            'id' => 'add-student',
            'title' => 'Add Student',
            'description' => 'Register a new student',
            'icon' => 'fas fa-user-plus',
            'color' => 'blue',
            'url' => route('students.create'),
            'permission' => 'create-students',
            'category' => 'students',
            'shortcut' => 'Ctrl+Shift+S'
        ],
        [
            'id' => 'add-teacher',
            'title' => 'Add Teacher',
            'description' => 'Register a new teacher',
            'icon' => 'fas fa-chalkboard-teacher',
            'color' => 'green',
            'url' => route('teachers.create'),
            'permission' => 'create-teachers',
            'category' => 'teachers',
            'shortcut' => 'Ctrl+Shift+T'
        ],
        [
            'id' => 'create-course',
            'title' => 'Create Course',
            'description' => 'Add a new course',
            'icon' => 'fas fa-book',
            'color' => 'purple',
            'url' => route('courses.create'),
            'permission' => 'create-courses',
            'category' => 'courses',
            'shortcut' => 'Ctrl+Shift+C'
        ],
        [
            'id' => 'view-reports',
            'title' => 'Reports',
            'description' => 'View system reports',
            'icon' => 'fas fa-chart-bar',
            'color' => 'indigo',
            'url' => route('reports.index'),
            'permission' => 'view-reports',
            'category' => 'reports',
            'shortcut' => 'Ctrl+Shift+R'
        ],
        [
            'id' => 'manage-enrollments',
            'title' => 'Enrollments',
            'description' => 'Manage student enrollments',
            'icon' => 'fas fa-clipboard-list',
            'color' => 'yellow',
            'url' => route('enrollments.index'),
            'permission' => 'manage-enrollments',
            'category' => 'enrollments',
            'shortcut' => 'Ctrl+Shift+E'
        ],
        [
            'id' => 'schedule-exam',
            'title' => 'Schedule Exam',
            'description' => 'Create new exam schedule',
            'icon' => 'fas fa-calendar-plus',
            'color' => 'red',
            'url' => route('exams.create'),
            'permission' => 'create-exams',
            'category' => 'exams',
            'shortcut' => 'Ctrl+Shift+X'
        ],
        [
            'id' => 'system-settings',
            'title' => 'Settings',
            'description' => 'System configuration',
            'icon' => 'fas fa-cog',
            'color' => 'gray',
            'url' => route('settings.index'),
            'permission' => 'manage-system',
            'category' => 'system',
            'shortcut' => 'Ctrl+,'
        ],
        [
            'id' => 'bulk-import',
            'title' => 'Bulk Import',
            'description' => 'Import data from files',
            'icon' => 'fas fa-upload',
            'color' => 'emerald',
            'url' => route('import.index'),
            'permission' => 'import-data',
            'category' => 'data',
            'shortcut' => 'Ctrl+Shift+I'
        ],
    ];

    $allShortcuts = array_merge($defaultShortcuts, $shortcuts);
    
    // Filter shortcuts based on permissions
    $availableShortcuts = collect($allShortcuts)->filter(function ($shortcut) {
        return !isset($shortcut['permission']) || auth()->user()->can($shortcut['permission']);
    })->take(8)->values(); // Limit to 8 shortcuts and ensure it's an array

    $colorClasses = [
        'blue' => 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700',
        'green' => 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700',
        'purple' => 'from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700',
        'indigo' => 'from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700',
        'yellow' => 'from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700',
        'red' => 'from-red-500 to-red-600 hover:from-red-600 hover:to-red-700',
        'gray' => 'from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700',
        'emerald' => 'from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700',
    ];
@endphp

<div class="dashboard-shortcuts" 
     x-data="dashboardShortcuts({{ json_encode($availableShortcuts) }})"
     x-init="init()">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Frequently used actions and shortcuts</p>
        </div>
        
        @if($customizable)
            <div class="flex items-center space-x-2">
                <!-- Customize Button -->
                <button @click="showCustomizeModal = true"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                    <i class="fas fa-edit mr-2"></i>
                    Customize
                </button>
                
                <!-- View Toggle -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button @click="viewMode = 'grid'" 
                            :class="{ 'bg-white dark:bg-gray-600 shadow-sm': viewMode === 'grid' }"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-th"></i>
                    </button>
                    <button @click="viewMode = 'list'" 
                            :class="{ 'bg-white dark:bg-gray-600 shadow-sm': viewMode === 'list' }"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Shortcuts Grid -->
    <div x-show="viewMode === 'grid'" 
         class="grid grid-cols-2 md:grid-cols-{{ $columns }} gap-4">
        <template x-for="shortcut in visibleShortcuts" :key="shortcut.id">
            <div class="group relative">
                <a :href="shortcut.url" 
                   @click="trackShortcutUsage(shortcut.id)"
                   class="block p-6 bg-gradient-to-br text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 hover-lift"
                   :class="getColorClasses(shortcut.color)"
                   :title="`${shortcut.description} (${shortcut.shortcut || 'No shortcut'})`">
                    
                    <!-- Icon -->
                    <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl mb-4 group-hover:bg-white/30 transition-colors duration-200">
                        <i :class="shortcut.icon" class="text-2xl"></i>
                    </div>
                    
                    <!-- Content -->
                    <div>
                        <h3 class="font-semibold text-lg mb-1" x-text="shortcut.title"></h3>
                        <p class="text-sm opacity-90" x-text="shortcut.description"></p>
                        
                        <!-- Keyboard Shortcut -->
                        <div x-show="shortcut.shortcut" class="mt-3">
                            <span class="inline-flex items-center px-2 py-1 bg-white/20 rounded-md text-xs font-mono" 
                                  x-text="shortcut.shortcut"></span>
                        </div>
                    </div>

                    <!-- Usage Count Badge -->
                    <div x-show="shortcut.usage_count > 0" 
                         class="absolute top-2 right-2 bg-white/20 rounded-full px-2 py-1 text-xs font-medium">
                        <span x-text="shortcut.usage_count"></span>
                    </div>
                </a>
            </div>
        </template>
    </div>

    <!-- Shortcuts List -->
    <div x-show="viewMode === 'list'" 
         class="space-y-2">
        <template x-for="shortcut in visibleShortcuts" :key="shortcut.id">
            <a :href="shortcut.url" 
               @click="trackShortcutUsage(shortcut.id)"
               class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 group">
                
                <!-- Icon -->
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mr-4"
                     :class="getIconBgClasses(shortcut.color)">
                    <i :class="shortcut.icon" 
                       class="text-lg"
                       :class="getIconTextClasses(shortcut.color)"></i>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" 
                                x-text="shortcut.title"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="shortcut.description"></p>
                        </div>
                        
                        <!-- Keyboard Shortcut -->
                        <div x-show="shortcut.shortcut" class="flex-shrink-0 ml-4">
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-xs font-mono text-gray-600 dark:text-gray-300" 
                                  x-text="shortcut.shortcut"></span>
                        </div>
                    </div>
                </div>

                <!-- Arrow -->
                <div class="flex-shrink-0 ml-4">
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-200"></i>
                </div>
            </a>
        </template>
    </div>

    <!-- Customize Modal -->
    @if($customizable)
        <div x-show="showCustomizeModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             @click.away="showCustomizeModal = false"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Customize Quick Actions
                        </h3>
                        
                        <div class="space-y-4">
                            <template x-for="shortcut in allShortcuts" :key="shortcut.id">
                                <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               :checked="isShortcutVisible(shortcut.id)"
                                               @change="toggleShortcut(shortcut.id)"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <div class="flex items-center">
                                            <i :class="shortcut.icon" class="text-gray-600 dark:text-gray-400 mr-3"></i>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white" x-text="shortcut.title"></p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="shortcut.description"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <span x-show="shortcut.shortcut" 
                                          class="text-xs font-mono text-gray-500 dark:text-gray-400" 
                                          x-text="shortcut.shortcut"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="saveCustomization()" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Changes
                        </button>
                        <button @click="showCustomizeModal = false" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function dashboardShortcuts(shortcuts) {
    return {
        allShortcuts: shortcuts,
        visibleShortcuts: shortcuts,
        viewMode: localStorage.getItem('shortcuts_view_mode') || 'grid',
        showCustomizeModal: false,
        usageStats: JSON.parse(localStorage.getItem('shortcut_usage') || '{}'),

        init() {
            this.loadCustomization();
            this.setupKeyboardShortcuts();
            this.updateUsageStats();
        },

        loadCustomization() {
            const customization = JSON.parse(localStorage.getItem('shortcuts_customization') || '[]');
            if (customization.length > 0) {
                this.visibleShortcuts = this.allShortcuts.filter(s => customization.includes(s.id));
            }
            if (typeof this.visibleShortcuts === 'object' && !Array.isArray(this.visibleShortcuts)) {
                this.visibleShortcuts = Object.values(this.visibleShortcuts);
            }
        },

        saveCustomization() {
            const visibleIds = this.visibleShortcuts.map(s => s.id);
            localStorage.setItem('shortcuts_customization', JSON.stringify(visibleIds));
            localStorage.setItem('shortcuts_view_mode', this.viewMode);
            this.showCustomizeModal = false;
            window.showSuccessToast('Quick actions customization saved');
        },

        toggleShortcut(shortcutId) {
            const shortcut = this.allShortcuts.find(s => s.id === shortcutId);
            if (this.isShortcutVisible(shortcutId)) {
                this.visibleShortcuts = this.visibleShortcuts.filter(s => s.id !== shortcutId);
            } else {
                this.visibleShortcuts.push(shortcut);
            }
        },

        isShortcutVisible(shortcutId) {
            return this.visibleShortcuts.some(s => s.id === shortcutId);
        },

        trackShortcutUsage(shortcutId) {
            this.usageStats[shortcutId] = (this.usageStats[shortcutId] || 0) + 1;
            localStorage.setItem('shortcut_usage', JSON.stringify(this.usageStats));
            this.updateUsageStats();
        },

        updateUsageStats() {
            this.visibleShortcuts.forEach(shortcut => {
                shortcut.usage_count = this.usageStats[shortcut.id] || 0;
            });
        },

        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Check if any modifier keys are pressed
                if (e.ctrlKey || e.metaKey) {
                    const shortcut = this.visibleShortcuts.find(s => {
                        if (!s.shortcut) return false;
                        
                        const keys = s.shortcut.toLowerCase().split('+').map(k => k.trim());
                        const hasCtrl = keys.includes('ctrl') && (e.ctrlKey || e.metaKey);
                        const hasShift = keys.includes('shift') && e.shiftKey;
                        const hasAlt = keys.includes('alt') && e.altKey;
                        const key = keys[keys.length - 1];
                        
                        return hasCtrl && 
                               (!keys.includes('shift') || hasShift) &&
                               (!keys.includes('alt') || hasAlt) &&
                               e.key.toLowerCase() === key;
                    });

                    if (shortcut) {
                        e.preventDefault();
                        this.trackShortcutUsage(shortcut.id);
                        window.location.href = shortcut.url;
                    }
                }
            });
        },

        getColorClasses(color) {
            const classes = {
                'blue': 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700',
                'green': 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700',
                'purple': 'from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700',
                'indigo': 'from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700',
                'yellow': 'from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700',
                'red': 'from-red-500 to-red-600 hover:from-red-600 hover:to-red-700',
                'gray': 'from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700',
                'emerald': 'from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700',
            };
            return classes[color] || classes.blue;
        },

        getIconBgClasses(color) {
            const classes = {
                'blue': 'bg-blue-100 dark:bg-blue-900/30',
                'green': 'bg-green-100 dark:bg-green-900/30',
                'purple': 'bg-purple-100 dark:bg-purple-900/30',
                'indigo': 'bg-indigo-100 dark:bg-indigo-900/30',
                'yellow': 'bg-yellow-100 dark:bg-yellow-900/30',
                'red': 'bg-red-100 dark:bg-red-900/30',
                'gray': 'bg-gray-100 dark:bg-gray-700',
                'emerald': 'bg-emerald-100 dark:bg-emerald-900/30',
            };
            return classes[color] || classes.blue;
        },

        getIconTextClasses(color) {
            const classes = {
                'blue': 'text-blue-600 dark:text-blue-400',
                'green': 'text-green-600 dark:text-green-400',
                'purple': 'text-purple-600 dark:text-purple-400',
                'indigo': 'text-indigo-600 dark:text-indigo-400',
                'yellow': 'text-yellow-600 dark:text-yellow-400',
                'red': 'text-red-600 dark:text-red-400',
                'gray': 'text-gray-600 dark:text-gray-400',
                'emerald': 'text-emerald-600 dark:text-emerald-400',
            };
            return classes[color] || classes.blue;
        }
    }
}
</script>

<style>
.dashboard-shortcuts .hover-lift:hover {
    transform: translateY(-2px) scale(1.02);
}

@media (prefers-reduced-motion: reduce) {
    .dashboard-shortcuts .hover-lift:hover {
        transform: none;
    }
}
</style>
