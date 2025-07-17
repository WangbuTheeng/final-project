@props([
    'type' => 'info',
    'title' => '',
    'message' => '',
    'duration' => 5000,
    'closable' => true,
    'actionUrl' => null,
    'actionText' => null,
])

@php
    $typeConfig = [
        'success' => [
            'bg' => 'bg-green-50 dark:bg-green-900/20',
            'border' => 'border-green-200 dark:border-green-800',
            'icon' => 'fas fa-check-circle text-green-500',
            'title_color' => 'text-green-800 dark:text-green-200',
            'message_color' => 'text-green-700 dark:text-green-300',
        ],
        'error' => [
            'bg' => 'bg-red-50 dark:bg-red-900/20',
            'border' => 'border-red-200 dark:border-red-800',
            'icon' => 'fas fa-times-circle text-red-500',
            'title_color' => 'text-red-800 dark:text-red-200',
            'message_color' => 'text-red-700 dark:text-red-300',
        ],
        'warning' => [
            'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
            'border' => 'border-yellow-200 dark:border-yellow-800',
            'icon' => 'fas fa-exclamation-triangle text-yellow-500',
            'title_color' => 'text-yellow-800 dark:text-yellow-200',
            'message_color' => 'text-yellow-700 dark:text-yellow-300',
        ],
        'info' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/20',
            'border' => 'border-blue-200 dark:border-blue-800',
            'icon' => 'fas fa-info-circle text-blue-500',
            'title_color' => 'text-blue-800 dark:text-blue-200',
            'message_color' => 'text-blue-700 dark:text-blue-300',
        ],
    ];

    $config = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div 
    x-data="toastNotification({{ $duration }}, {{ $closable ? 'true' : 'false' }})"
    x-show="visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-full"
    class="toast-notification max-w-sm w-full {{ $config['bg'] }} {{ $config['border'] }} border rounded-xl shadow-lg pointer-events-auto overflow-hidden"
    style="display: none;"
>
    <!-- Progress Bar -->
    @if($duration > 0)
        <div class="absolute top-0 left-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-100 ease-linear"
             :style="`width: ${progress}%`"></div>
    @endif

    <div class="p-4">
        <div class="flex items-start">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <i class="{{ $config['icon'] }} text-xl"></i>
            </div>

            <!-- Content -->
            <div class="ml-3 w-0 flex-1">
                @if($title)
                    <p class="text-sm font-semibold {{ $config['title_color'] }}">
                        {{ $title }}
                    </p>
                @endif
                
                @if($message)
                    <p class="text-sm {{ $config['message_color'] }} {{ $title ? 'mt-1' : '' }}">
                        {{ $message }}
                    </p>
                @endif

                <!-- Action Button -->
                @if($actionUrl && $actionText)
                    <div class="mt-3">
                        <a href="{{ $actionUrl }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            {{ $actionText }}
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Close Button -->
            @if($closable)
                <div class="ml-4 flex-shrink-0 flex">
                    <button 
                        @click="close()"
                        class="inline-flex text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-gray-600 dark:focus:text-gray-300 transition-colors duration-200"
                    >
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
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
            
            // Remove element after animation
            setTimeout(() => {
                this.$el.remove();
            }, 200);
        },

        startProgress() {
            const interval = 50; // Update every 50ms
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

        // Pause on hover
        pause() {
            this.clearTimers();
        },

        // Resume on mouse leave
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

<style>
.toast-notification {
    position: relative;
}

.toast-notification:hover .progress-bar {
    animation-play-state: paused;
}
</style>
