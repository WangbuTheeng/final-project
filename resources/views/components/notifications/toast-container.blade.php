@props([
    'position' => 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
])

@php
    $positionClasses = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-center' => 'top-4 left-1/2 transform -translate-x-1/2',
        'bottom-center' => 'bottom-4 left-1/2 transform -translate-x-1/2',
    ];
@endphp

<div id="toast-container" 
     class="fixed {{ $positionClasses[$position] ?? $positionClasses['top-right'] }} z-50 space-y-3 pointer-events-none"
     x-data="toastContainer()"
     x-init="init()">
</div>

<!-- Session-based Toast (if exists) -->
@if(session('toast_notification'))
    @php
        $toast = session('toast_notification');
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.showToast({
                type: '{{ $toast['type'] }}',
                title: '{{ $toast['title'] }}',
                message: '{{ $toast['message'] }}',
                duration: 5000
            });
        });
    </script>
@endif

<!-- Success/Error Flash Messages -->
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.showToast({
                type: 'success',
                title: 'Success',
                message: '{{ session('success') }}',
                duration: 5000
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.showToast({
                type: 'error',
                title: 'Error',
                message: '{{ session('error') }}',
                duration: 7000
            });
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.showToast({
                type: 'warning',
                title: 'Warning',
                message: '{{ session('warning') }}',
                duration: 6000
            });
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.showToast({
                type: 'info',
                title: 'Information',
                message: '{{ session('info') }}',
                duration: 5000
            });
        });
    </script>
@endif

<script>
function toastContainer() {
    return {
        toasts: [],
        maxToasts: 5,

        init() {
            // Make toast functions globally available
            window.showToast = this.showToast.bind(this);
            window.showSuccessToast = this.showSuccessToast.bind(this);
            window.showErrorToast = this.showErrorToast.bind(this);
            window.showWarningToast = this.showWarningToast.bind(this);
            window.showInfoToast = this.showInfoToast.bind(this);
        },

        showToast(options) {
            const toast = {
                id: Date.now() + Math.random(),
                type: options.type || 'info',
                title: options.title || '',
                message: options.message || '',
                duration: options.duration || 5000,
                actionUrl: options.actionUrl || null,
                actionText: options.actionText || null,
                closable: options.closable !== false,
            };

            // Add to toasts array
            this.toasts.push(toast);

            // Limit number of toasts
            if (this.toasts.length > this.maxToasts) {
                this.toasts.shift();
            }

            // Create toast element
            this.createToastElement(toast);

            return toast;
        },

        showSuccessToast(message, title = 'Success', options = {}) {
            return this.showToast({
                type: 'success',
                title: title,
                message: message,
                ...options
            });
        },

        showErrorToast(message, title = 'Error', options = {}) {
            return this.showToast({
                type: 'error',
                title: title,
                message: message,
                duration: 7000,
                ...options
            });
        },

        showWarningToast(message, title = 'Warning', options = {}) {
            return this.showToast({
                type: 'warning',
                title: title,
                message: message,
                duration: 6000,
                ...options
            });
        },

        showInfoToast(message, title = 'Information', options = {}) {
            return this.showToast({
                type: 'info',
                title: title,
                message: message,
                ...options
            });
        },

        createToastElement(toast) {
            const container = document.getElementById('toast-container');
            
            // Create toast wrapper
            const toastWrapper = document.createElement('div');
            toastWrapper.setAttribute('x-data', `toastNotification(${toast.duration}, ${toast.closable})`);
            toastWrapper.setAttribute('x-init', 'init()');
            toastWrapper.className = 'pointer-events-auto';

            // Toast HTML
            const typeConfig = {
                success: {
                    bg: 'bg-green-50 dark:bg-green-900/20',
                    border: 'border-green-200 dark:border-green-800',
                    icon: 'fas fa-check-circle text-green-500',
                    titleColor: 'text-green-800 dark:text-green-200',
                    messageColor: 'text-green-700 dark:text-green-300',
                },
                error: {
                    bg: 'bg-red-50 dark:bg-red-900/20',
                    border: 'border-red-200 dark:border-red-800',
                    icon: 'fas fa-times-circle text-red-500',
                    titleColor: 'text-red-800 dark:text-red-200',
                    messageColor: 'text-red-700 dark:text-red-300',
                },
                warning: {
                    bg: 'bg-yellow-50 dark:bg-yellow-900/20',
                    border: 'border-yellow-200 dark:border-yellow-800',
                    icon: 'fas fa-exclamation-triangle text-yellow-500',
                    titleColor: 'text-yellow-800 dark:text-yellow-200',
                    messageColor: 'text-yellow-700 dark:text-yellow-300',
                },
                info: {
                    bg: 'bg-blue-50 dark:bg-blue-900/20',
                    border: 'border-blue-200 dark:border-blue-800',
                    icon: 'fas fa-info-circle text-blue-500',
                    titleColor: 'text-blue-800 dark:text-blue-200',
                    messageColor: 'text-blue-700 dark:text-blue-300',
                },
            };

            const config = typeConfig[toast.type] || typeConfig.info;

            toastWrapper.innerHTML = `
                <div x-show="visible"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-full"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform translate-x-full"
                     @mouseenter="pause()"
                     @mouseleave="resume()"
                     class="max-w-sm w-full ${config.bg} ${config.border} border rounded-xl shadow-lg overflow-hidden relative"
                     style="display: none;">
                    
                    ${toast.duration > 0 ? `
                        <div class="absolute top-0 left-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-100 ease-linear"
                             :style="\`width: \${progress}%\`"></div>
                    ` : ''}

                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="${config.icon} text-xl"></i>
                            </div>

                            <div class="ml-3 w-0 flex-1">
                                ${toast.title ? `
                                    <p class="text-sm font-semibold ${config.titleColor}">
                                        ${toast.title}
                                    </p>
                                ` : ''}
                                
                                ${toast.message ? `
                                    <p class="text-sm ${config.messageColor} ${toast.title ? 'mt-1' : ''}">
                                        ${toast.message}
                                    </p>
                                ` : ''}

                                ${toast.actionUrl && toast.actionText ? `
                                    <div class="mt-3">
                                        <a href="${toast.actionUrl}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            ${toast.actionText}
                                            <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                ` : ''}
                            </div>

                            ${toast.closable ? `
                                <div class="ml-4 flex-shrink-0 flex">
                                    <button @click="close()"
                                            class="inline-flex text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-gray-600 dark:focus:text-gray-300 transition-colors duration-200">
                                        <span class="sr-only">Close</span>
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(toastWrapper);

            // Initialize Alpine.js for this element
            if (window.Alpine) {
                Alpine.initTree(toastWrapper);
            }
        },

        removeToast(toastId) {
            this.toasts = this.toasts.filter(t => t.id !== toastId);
        }
    }
}

// Toast notification component function (same as in toast.blade.php)
function toastNotification(duration = 5000, closable = true) {
    return {
        visible: false,
        progress: 100,
        timer: null,
        progressTimer: null,

        init() {
            this.show();
        },

        show() {
            this.visible = true;
            
            if (duration > 0) {
                this.startProgress();
                this.timer = setTimeout(() => {
                    this.close();
                }, duration);
            }
        },

        close() {
            this.visible = false;
            this.clearTimers();
            
            setTimeout(() => {
                this.$el.remove();
            }, 200);
        },

        startProgress() {
            const interval = 50;
            const decrement = (100 / duration) * interval;
            
            this.progressTimer = setInterval(() => {
                this.progress -= decrement;
                if (this.progress <= 0) {
                    this.progress = 0;
                    clearInterval(this.progressTimer);
                }
            }, interval);
        },

        clearTimers() {
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            if (this.progressTimer) {
                clearInterval(this.progressTimer);
                this.progressTimer = null;
            }
        },

        pause() {
            this.clearTimers();
        },

        resume() {
            if (this.progress > 0 && duration > 0) {
                const remainingTime = (this.progress / 100) * duration;
                this.timer = setTimeout(() => {
                    this.close();
                }, remainingTime);
                this.startProgress();
            }
        }
    }
}
</script>
