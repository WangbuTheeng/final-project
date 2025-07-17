@props([
    'position' => 'right', // left, right, center
])

<div x-data="notificationCenter()" 
     x-init="init()"
     class="relative">
    
    <!-- Notification Bell Button -->
    <button
        @click="toggleCenter()"
        class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-colors duration-200"
        :class="{ 'text-blue-600 dark:text-blue-400': isOpen }"
    >
        <i class="fas fa-bell text-xl"></i>
        
        <!-- Unread Count Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 transform scale-0"
              x-transition:enter-end="opacity-100 transform scale-100"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[1.25rem] h-5">
        </span>
        
        <!-- Pulse Animation for New Notifications -->
        <span x-show="hasNewNotifications" 
              class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-ping">
        </span>
    </button>

    <!-- Notification Center Dropdown -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.away="closeCenter()"
         class="absolute z-50 mt-2 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden {{ $position === 'left' ? 'right-0' : 'left-0' }}"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Notifications
                </h3>
                <div class="flex items-center space-x-2">
                    <!-- Mark All Read Button -->
                    <button @click="markAllAsRead()"
                            x-show="unreadCount > 0"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                        Mark all read
                    </button>
                    
                    <!-- Settings Button -->
                    <button @click="openSettings()"
                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg">
                        <i class="fas fa-cog text-sm"></i>
                    </button>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-300">
                <span x-text="`${unreadCount} unread`"></span>
                <span x-text="`${notifications.length} total`"></span>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="p-6 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent mx-auto"></div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Loading notifications...</p>
        </div>

        <!-- Notifications List -->
        <div x-show="!loading" class="max-h-96 overflow-y-auto">
            <!-- Empty State -->
            <div x-show="notifications.length === 0" class="p-8 text-center">
                <i class="fas fa-bell-slash text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No notifications</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">You're all caught up!</p>
            </div>

            <!-- Notification Items -->
            <template x-for="notification in notifications" :key="notification.id">
                <div class="border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                     :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.is_read }">
                    
                    <div class="p-4 flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                 :class="{
                                     'bg-blue-100 dark:bg-blue-900/30': notification.type === 'info',
                                     'bg-green-100 dark:bg-green-900/30': notification.type === 'success',
                                     'bg-yellow-100 dark:bg-yellow-900/30': notification.type === 'warning',
                                     'bg-red-100 dark:bg-red-900/30': notification.type === 'error',
                                     'bg-gray-100 dark:bg-gray-700': notification.type === 'system'
                                 }">
                                <i :class="notification.icon"
                                   class="text-sm"
                                   :class="{
                                       'text-blue-600 dark:text-blue-400': notification.type === 'info',
                                       'text-green-600 dark:text-green-400': notification.type === 'success',
                                       'text-yellow-600 dark:text-yellow-400': notification.type === 'warning',
                                       'text-red-600 dark:text-red-400': notification.type === 'error',
                                       'text-gray-600 dark:text-gray-400': notification.type === 'system'
                                   }"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"
                                       x-text="notification.title"></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 line-clamp-2"
                                       x-text="notification.message"></p>
                                    
                                    <!-- Action Button -->
                                    <div x-show="notification.action_url" class="mt-2">
                                        <a :href="notification.action_url"
                                           @click="markAsRead(notification.id)"
                                           class="inline-flex items-center text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            <span x-text="notification.action_text || 'View'"></span>
                                            <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-1 ml-2">
                                    <!-- Mark as Read/Unread -->
                                    <button @click="toggleRead(notification)"
                                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded"
                                            :title="notification.is_read ? 'Mark as unread' : 'Mark as read'">
                                        <i :class="notification.is_read ? 'fas fa-envelope' : 'fas fa-envelope-open'"
                                           class="text-xs"></i>
                                    </button>
                                    
                                    <!-- Delete -->
                                    <button @click="deleteNotification(notification.id)"
                                            class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded"
                                            title="Delete notification">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Timestamp and Priority -->
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400"
                                      x-text="notification.time_ago"></span>
                                
                                <!-- Priority Badge -->
                                <span x-show="notification.priority !== 'normal'"
                                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': notification.priority === 'urgent',
                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300': notification.priority === 'high',
                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': notification.priority === 'low'
                                      }"
                                      x-text="notification.priority.toUpperCase()">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div x-show="!loading && notifications.length > 0" 
             class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <a href="{{ route('notifications.index') }}" 
               class="block text-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationCenter() {
    return {
        isOpen: false,
        loading: false,
        notifications: [],
        unreadCount: 0,
        hasNewNotifications: false,
        pollInterval: null,

        init() {
            this.loadNotifications();
            this.startPolling();
        },

        async loadNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/notifications/recent', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response');
                }

                const data = await response.json();

                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
                this.hasNewNotifications = false;
            } catch (error) {
                console.error('Failed to load notifications:', error);
                // Set fallback data
                this.notifications = [];
                this.unreadCount = 0;
                this.hasNewNotifications = false;

                // Only show error toast if it's not a network/auth issue
                if (error.message !== 'Failed to fetch' && !error.message.includes('401')) {
                    if (window.showErrorToast) {
                        window.showErrorToast('Unable to load notifications');
                    }
                }
            } finally {
                this.loading = false;
            }
        },

        toggleCenter() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },

        closeCenter() {
            this.isOpen = false;
        },

        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification && !notification.is_read) {
                        notification.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },

        async toggleRead(notification) {
            if (notification.is_read) {
                // Mark as unread (if you implement this endpoint)
                notification.is_read = false;
                this.unreadCount++;
            } else {
                await this.markAsRead(notification.id);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        async deleteNotification(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    const index = this.notifications.findIndex(n => n.id === notificationId);
                    if (index !== -1) {
                        const notification = this.notifications[index];
                        if (!notification.is_read) {
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        }
                        this.notifications.splice(index, 1);
                    }
                }
            } catch (error) {
                console.error('Failed to delete notification:', error);
            }
        },

        openSettings() {
            // Navigate to notification settings (placeholder for now)
            alert('Notification settings feature coming soon!');
            // TODO: Implement notification settings page
            // window.location.href = '/settings';
        },

        startPolling() {
            // Poll for new notifications every 60 seconds (reduced frequency to prevent spam)
            this.pollInterval = setInterval(async () => {
                if (!this.isOpen && document.visibilityState === 'visible') {
                    try {
                        const oldCount = this.unreadCount;
                        await this.loadNotifications();
                        if (this.unreadCount > oldCount) {
                            this.hasNewNotifications = true;
                            setTimeout(() => {
                                this.hasNewNotifications = false;
                            }, 3000);
                        }
                    } catch (error) {
                        // Silently fail during polling to prevent console spam
                        console.debug('Notification polling failed:', error);
                    }
                }
            }, 60000);
        },

        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
            }
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
