<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display notifications page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['type', 'category', 'priority', 'read_status']);
        
        $notifications = $this->notificationService->getUserNotifications(
            $user->id, 
            $request->get('per_page', 15),
            $filters
        );

        $statistics = $this->notificationService->getStatistics($user->id);

        return view('notifications.index', compact('notifications', 'statistics', 'filters'));
    }

    /**
     * Get notifications for dropdown/preview (AJAX).
     */
    public function getRecent(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 5);
        
        $notifications = $this->notificationService->getRecentNotifications($user->id, $limit);
        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'category' => $notification->category,
                    'priority' => $notification->priority,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text,
                    'is_read' => $notification->isRead(),
                    'time_ago' => $notification->time_ago,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            }),
            'unread_count' => $unreadCount,
            'has_more' => $notifications->count() >= $limit,
        ]);
    }

    /**
     * Get unread count (AJAX).
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user->id);

        return response()->json(['count' => $count]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $success = $this->notificationService->markAsRead($id, $user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Notification marked as read' : 'Notification not found'
            ]);
        }

        if ($success) {
            return back()->with('success', 'Notification marked as read');
        }

        return back()->with('error', 'Notification not found');
    }

    /**
     * Mark multiple notifications as read.
     */
    public function markMultipleAsRead(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'integer|exists:notifications,id'
        ]);

        $user = Auth::user();
        $count = $this->notificationService->markMultipleAsRead(
            $request->notification_ids, 
            $user->id
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} notifications marked as read",
                'count' => $count
            ]);
        }

        return back()->with('success', "{$count} notifications marked as read");
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $count > 0 ? "All notifications marked as read" : "No unread notifications",
                'count' => $count
            ]);
        }

        return back()->with('success', 
            $count > 0 ? "All notifications marked as read" : "No unread notifications"
        );
    }

    /**
     * Delete notification.
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $success = $this->notificationService->deleteNotification($id, $user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Notification deleted' : 'Notification not found'
            ]);
        }

        if ($success) {
            return back()->with('success', 'Notification deleted');
        }

        return back()->with('error', 'Notification not found');
    }

    /**
     * Delete multiple notifications.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'integer|exists:notifications,id'
        ]);

        $user = Auth::user();
        $count = $this->notificationService->deleteMultiple(
            $request->notification_ids, 
            $user->id
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} notifications deleted",
                'count' => $count
            ]);
        }

        return back()->with('success', "{$count} notifications deleted");
    }

    /**
     * Get notification statistics.
     */
    public function getStatistics()
    {
        $user = Auth::user();
        $statistics = $this->notificationService->getStatistics($user->id);

        return response()->json($statistics);
    }

    /**
     * Test notification creation (for development).
     */
    public function test(Request $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $user = Auth::user();
        
        $types = ['info', 'success', 'warning', 'error', 'system'];
        $categories = ['academic', 'financial', 'exam', 'system', 'announcement'];
        $priorities = ['low', 'normal', 'high', 'urgent'];

        $notification = $this->notificationService->createForUser($user->id, [
            'type' => $types[array_rand($types)],
            'category' => $categories[array_rand($categories)],
            'priority' => $priorities[array_rand($priorities)],
            'title' => 'Test Notification',
            'message' => 'This is a test notification created at ' . now()->format('Y-m-d H:i:s'),
            'action_url' => route('notifications.index'),
            'action_text' => 'View All',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Test notification created',
                'notification' => $notification
            ]);
        }

        return back()->with('success', 'Test notification created');
    }
}
