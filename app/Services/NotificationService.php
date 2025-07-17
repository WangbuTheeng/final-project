<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Create a notification for a specific user.
     */
    public function createForUser($userId, array $data)
    {
        $notification = Notification::createForUser($userId, $data);
        
        // Clear cache for user's unread count
        $this->clearUserNotificationCache($userId);
        
        // Broadcast real-time notification if needed
        $this->broadcastNotification($notification);
        
        return $notification;
    }

    /**
     * Create notifications for multiple users.
     */
    public function createForUsers(array $userIds, array $data)
    {
        $notifications = Notification::createForUsers($userIds, $data);
        
        // Clear cache for all affected users
        foreach ($userIds as $userId) {
            $this->clearUserNotificationCache($userId);
        }
        
        return $notifications;
    }

    /**
     * Create notification for users with specific roles.
     */
    public function createForRole($roleName, array $data)
    {
        $userIds = User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->pluck('id')->toArray();

        return $this->createForUsers($userIds, $data);
    }

    /**
     * Create notification for all users.
     */
    public function createForAll(array $data)
    {
        $userIds = User::pluck('id')->toArray();
        return $this->createForUsers($userIds, $data);
    }

    /**
     * Get notifications for a user with pagination.
     */
    public function getUserNotifications($userId, $perPage = 15, $filters = [])
    {
        $query = Notification::where('user_id', $userId)
            ->active()
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['category'])) {
            $query->ofCategory($filters['category']);
        }

        if (isset($filters['priority'])) {
            $query->ofPriority($filters['priority']);
        }

        if (isset($filters['read_status'])) {
            if ($filters['read_status'] === 'unread') {
                $query->unread();
            } elseif ($filters['read_status'] === 'read') {
                $query->read();
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount($userId)
    {
        return Cache::remember("user_notifications_unread_count_{$userId}", 300, function () use ($userId) {
            return Notification::where('user_id', $userId)
                ->unread()
                ->active()
                ->count();
        });
    }

    /**
     * Get recent notifications for a user (for dropdown/preview).
     */
    public function getRecentNotifications($userId, $limit = 5)
    {
        return Notification::where('user_id', $userId)
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($notificationId, $userId = null)
    {
        $query = Notification::where('id', $notificationId);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $notification = $query->first();
        
        if ($notification) {
            $notification->markAsRead();
            $this->clearUserNotificationCache($notification->user_id);
            return true;
        }

        return false;
    }

    /**
     * Mark multiple notifications as read.
     */
    public function markMultipleAsRead(array $notificationIds, $userId = null)
    {
        $query = Notification::whereIn('id', $notificationIds);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $notifications = $query->get();
        $userIds = $notifications->pluck('user_id')->unique();

        $query->update(['read_at' => now()]);

        // Clear cache for affected users
        foreach ($userIds as $userId) {
            $this->clearUserNotificationCache($userId);
        }

        return $notifications->count();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead($userId)
    {
        $count = Notification::where('user_id', $userId)
            ->unread()
            ->update(['read_at' => now()]);

        $this->clearUserNotificationCache($userId);

        return $count;
    }

    /**
     * Delete notification.
     */
    public function deleteNotification($notificationId, $userId = null)
    {
        $query = Notification::where('id', $notificationId);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $notification = $query->first();
        
        if ($notification) {
            $this->clearUserNotificationCache($notification->user_id);
            return $notification->delete();
        }

        return false;
    }

    /**
     * Delete multiple notifications.
     */
    public function deleteMultiple(array $notificationIds, $userId = null)
    {
        $query = Notification::whereIn('id', $notificationIds);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $notifications = $query->get();
        $userIds = $notifications->pluck('user_id')->unique();

        $count = $query->delete();

        // Clear cache for affected users
        foreach ($userIds as $userId) {
            $this->clearUserNotificationCache($userId);
        }

        return $count;
    }

    /**
     * Create toast notification (session-based).
     */
    public function toast($message, $type = 'info', $title = null)
    {
        $toastData = [
            'message' => $message,
            'type' => $type,
            'title' => $title,
            'timestamp' => now()->timestamp,
        ];

        session()->flash('toast_notification', $toastData);

        return $toastData;
    }

    /**
     * Create success toast.
     */
    public function success($message, $title = 'Success')
    {
        return $this->toast($message, 'success', $title);
    }

    /**
     * Create error toast.
     */
    public function error($message, $title = 'Error')
    {
        return $this->toast($message, 'error', $title);
    }

    /**
     * Create warning toast.
     */
    public function warning($message, $title = 'Warning')
    {
        return $this->toast($message, 'warning', $title);
    }

    /**
     * Create info toast.
     */
    public function info($message, $title = 'Information')
    {
        return $this->toast($message, 'info', $title);
    }

    /**
     * Get notification statistics for a user.
     */
    public function getStatistics($userId)
    {
        $baseQuery = Notification::where('user_id', $userId)->active();

        return [
            'total' => $baseQuery->count(),
            'unread' => $baseQuery->unread()->count(),
            'read' => $baseQuery->read()->count(),
            'by_type' => [
                'info' => $baseQuery->ofType(Notification::TYPE_INFO)->count(),
                'success' => $baseQuery->ofType(Notification::TYPE_SUCCESS)->count(),
                'warning' => $baseQuery->ofType(Notification::TYPE_WARNING)->count(),
                'error' => $baseQuery->ofType(Notification::TYPE_ERROR)->count(),
                'system' => $baseQuery->ofType(Notification::TYPE_SYSTEM)->count(),
            ],
            'by_category' => [
                'academic' => $baseQuery->ofCategory(Notification::CATEGORY_ACADEMIC)->count(),
                'financial' => $baseQuery->ofCategory(Notification::CATEGORY_FINANCIAL)->count(),
                'exam' => $baseQuery->ofCategory(Notification::CATEGORY_EXAM)->count(),
                'system' => $baseQuery->ofCategory(Notification::CATEGORY_SYSTEM)->count(),
                'announcement' => $baseQuery->ofCategory(Notification::CATEGORY_ANNOUNCEMENT)->count(),
            ],
            'by_priority' => [
                'low' => $baseQuery->ofPriority(Notification::PRIORITY_LOW)->count(),
                'normal' => $baseQuery->ofPriority(Notification::PRIORITY_NORMAL)->count(),
                'high' => $baseQuery->ofPriority(Notification::PRIORITY_HIGH)->count(),
                'urgent' => $baseQuery->ofPriority(Notification::PRIORITY_URGENT)->count(),
            ],
        ];
    }

    /**
     * Clear notification cache for a user.
     */
    private function clearUserNotificationCache($userId)
    {
        Cache::forget("user_notifications_unread_count_{$userId}");
    }

    /**
     * Broadcast notification for real-time updates.
     */
    private function broadcastNotification($notification)
    {
        // This would integrate with broadcasting (Pusher, WebSockets, etc.)
        // For now, we'll just log it
        \Log::info('Notification created', [
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id,
            'type' => $notification->type,
            'title' => $notification->title,
        ]);
    }

    /**
     * Clean up expired notifications.
     */
    public function cleanupExpired()
    {
        return Notification::where('expires_at', '<', now())->delete();
    }
}
