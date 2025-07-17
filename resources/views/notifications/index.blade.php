@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6" x-data="notificationManager()">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Notifications</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Manage your notifications and preferences</p>
        </div>
        
        <div class="mt-4 lg:mt-0 flex items-center space-x-3">
            <!-- Test Notification Button (only in local) -->
            @if(app()->environment('local'))
                <button @click="createTestNotification()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                    <i class="fas fa-flask mr-2"></i>
                    Test Notification
                </button>
            @endif
            
            <!-- Mark All Read -->
            <button @click="markAllAsRead()"
                    x-show="statistics.unread > 0"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                <i class="fas fa-check-double mr-2"></i>
                Mark All Read
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Notifications -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bell text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Unread Notifications -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-envelope text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Unread</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['unread'] }}</p>
                </div>
            </div>
        </div>

        <!-- High Priority -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">High Priority</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['by_priority']['high'] + $statistics['by_priority']['urgent'] }}</p>
                </div>
            </div>
        </div>

        <!-- This Week -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-week text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">This Week</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $notifications->where('created_at', '>=', now()->startOfWeek())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-4">
                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select name="type" 
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="info" {{ request('type') === 'info' ? 'selected' : '' }}>Info</option>
                        <option value="success" {{ request('type') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="warning" {{ request('type') === 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="error" {{ request('type') === 'error' ? 'selected' : '' }}>Error</option>
                        <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>System</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select name="category" 
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        <option value="academic" {{ request('category') === 'academic' ? 'selected' : '' }}>Academic</option>
                        <option value="financial" {{ request('category') === 'financial' ? 'selected' : '' }}>Financial</option>
                        <option value="exam" {{ request('category') === 'exam' ? 'selected' : '' }}>Exam</option>
                        <option value="system" {{ request('category') === 'system' ? 'selected' : '' }}>System</option>
                        <option value="announcement" {{ request('category') === 'announcement' ? 'selected' : '' }}>Announcement</option>
                    </select>
                </div>

                <!-- Read Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="read_status" 
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="unread" {{ request('read_status') === 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('read_status') === 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>

                <!-- Apply Filters Button -->
                <div class="flex items-end">
                    <button onclick="applyFilters()" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                        Apply Filters
                    </button>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div x-show="selectedNotifications.length > 0" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="flex items-center space-x-3">
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    <span x-text="selectedNotifications.length"></span> selected
                </span>
                <button @click="markSelectedAsRead()"
                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg">
                    Mark as Read
                </button>
                <button @click="deleteSelected()"
                        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg">
                    Delete
                </button>
                <button @click="clearSelection()"
                        class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 {{ $notification->isUnread() ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                         x-data="{ selected: false }"
                         :class="{ 'ring-2 ring-blue-500': selected }">
                        
                        <div class="flex items-start space-x-4">
                            <!-- Checkbox -->
                            <div class="flex items-center h-6">
                                <input type="checkbox" 
                                       x-model="selected"
                                       @change="toggleNotificationSelection({{ $notification->id }}, selected)"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </div>

                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                    {{ $notification->type === 'success' ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                    {{ $notification->type === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                    {{ $notification->type === 'error' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                                    {{ $notification->type === 'info' ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                    {{ $notification->type === 'system' ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                                    <i class="{{ $notification->icon }} text-lg
                                        {{ $notification->type === 'success' ? 'text-green-600 dark:text-green-400' : '' }}
                                        {{ $notification->type === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                        {{ $notification->type === 'error' ? 'text-red-600 dark:text-red-400' : '' }}
                                        {{ $notification->type === 'info' ? 'text-blue-600 dark:text-blue-400' : '' }}
                                        {{ $notification->type === 'system' ? 'text-gray-600 dark:text-gray-400' : '' }}"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $notification->title }}
                                            @if($notification->isUnread())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 ml-2">
                                                    New
                                                </span>
                                            @endif
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $notification->message }}</p>
                                        
                                        <!-- Action Button -->
                                        @if($notification->action_url)
                                            <div class="mt-3">
                                                <a href="{{ $notification->action_url }}" 
                                                   onclick="markAsRead({{ $notification->id }})"
                                                   class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                    {{ $notification->action_text ?? 'View Details' }}
                                                    <i class="fas fa-arrow-right ml-1"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        <!-- Priority Badge -->
                                        @if($notification->priority !== 'normal')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                {{ $notification->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                                {{ $notification->priority === 'high' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                                {{ $notification->priority === 'low' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                                {{ strtoupper($notification->priority) }}
                                            </span>
                                        @endif

                                        <!-- Mark as Read/Unread -->
                                        <button onclick="toggleRead({{ $notification->id }}, {{ $notification->isRead() ? 'false' : 'true' }})"
                                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg"
                                                title="{{ $notification->isRead() ? 'Mark as unread' : 'Mark as read' }}">
                                            <i class="{{ $notification->isRead() ? 'fas fa-envelope' : 'fas fa-envelope-open' }} text-sm"></i>
                                        </button>

                                        <!-- Delete -->
                                        <button onclick="deleteNotification({{ $notification->id }})"
                                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg"
                                                title="Delete notification">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Metadata -->
                                <div class="flex items-center justify-between mt-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center space-x-4">
                                        <span>{{ $notification->time_ago }}</span>
                                        <span class="capitalize">{{ $notification->category }}</span>
                                    </div>
                                    <span>{{ $notification->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <i class="fas fa-bell-slash text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No notifications found</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['type', 'category', 'read_status']))
                        Try adjusting your filters to see more notifications.
                    @else
                        You're all caught up! No notifications to display.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<script>
function notificationManager() {
    return {
        selectedNotifications: [],
        statistics: @json($statistics),

        toggleNotificationSelection(notificationId, selected) {
            if (selected) {
                this.selectedNotifications.push(notificationId);
            } else {
                this.selectedNotifications = this.selectedNotifications.filter(id => id !== notificationId);
            }
        },

        clearSelection() {
            this.selectedNotifications = [];
            // Uncheck all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        },

        async markSelectedAsRead() {
            if (this.selectedNotifications.length === 0) return;

            try {
                const response = await fetch('/notifications/mark-multiple-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        notification_ids: this.selectedNotifications
                    })
                });

                if (response.ok) {
                    window.showSuccessToast('Selected notifications marked as read');
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (error) {
                window.showErrorToast('Failed to mark notifications as read');
            }
        },

        async deleteSelected() {
            if (this.selectedNotifications.length === 0) return;

            if (!confirm('Are you sure you want to delete the selected notifications?')) return;

            try {
                const response = await fetch('/notifications/bulk-delete', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        notification_ids: this.selectedNotifications
                    })
                });

                if (response.ok) {
                    window.showSuccessToast('Selected notifications deleted');
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (error) {
                window.showErrorToast('Failed to delete notifications');
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
                    window.showSuccessToast('All notifications marked as read');
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (error) {
                window.showErrorToast('Failed to mark all as read');
            }
        },

        async createTestNotification() {
            try {
                const response = await fetch('/notifications/test', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    window.showSuccessToast('Test notification created');
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (error) {
                window.showErrorToast('Failed to create test notification');
            }
        }
    }
}

// Global functions for individual notification actions
async function markAsRead(notificationId) {
    try {
        await fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
}

async function toggleRead(notificationId, markAsRead) {
    try {
        if (markAsRead) {
            await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });
        }
        window.location.reload();
    } catch (error) {
        window.showErrorToast('Failed to update notification');
    }
}

async function deleteNotification(notificationId) {
    if (!confirm('Are you sure you want to delete this notification?')) return;

    try {
        const response = await fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        if (response.ok) {
            window.showSuccessToast('Notification deleted');
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        window.showErrorToast('Failed to delete notification');
    }
}

function applyFilters() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = window.location.pathname;

    const params = ['type', 'category', 'read_status'];
    params.forEach(param => {
        const select = document.querySelector(`select[name="${param}"]`);
        if (select && select.value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = param;
            input.value = select.value;
            form.appendChild(input);
        }
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
