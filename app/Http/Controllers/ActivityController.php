<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    protected $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Display activity feed page.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'user_id', 'action', 'event_type', 'severity', 
            'date_from', 'date_to', 'days', 'tags'
        ]);

        $activities = $this->activityService->getActivities(
            $filters, 
            $request->get('per_page', 20)
        );

        $statistics = $this->activityService->getStatistics(
            $request->get('stats_days', 30)
        );

        return view('activities.index', compact('activities', 'statistics', 'filters'));
    }

    /**
     * Get recent activities for AJAX requests.
     */
    public function getRecent(Request $request)
    {
        $limit = $request->get('limit', 10);
        $activities = $this->activityService->getRecentActivities($limit);

        return response()->json([
            'activities' => $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'action' => $activity->action,
                    'event_type' => $activity->event_type,
                    'severity' => $activity->severity,
                    'icon' => $activity->icon,
                    'color' => $activity->color,
                    'time_ago' => $activity->time_ago,
                    'user' => $activity->user ? [
                        'id' => $activity->user->id,
                        'name' => $activity->user->name,
                        'avatar' => $activity->user->avatar ?? null,
                    ] : null,
                    'subject' => $activity->subject ? [
                        'type' => class_basename($activity->subject_type),
                        'id' => $activity->subject_id,
                        'name' => $this->getSubjectName($activity->subject),
                    ] : null,
                    'created_at' => $activity->created_at->toISOString(),
                ];
            }),
            'has_more' => $activities->count() >= $limit,
        ]);
    }

    /**
     * Get activity statistics.
     */
    public function getStatistics(Request $request)
    {
        $days = $request->get('days', 30);
        $statistics = $this->activityService->getStatistics($days);

        return response()->json($statistics);
    }

    /**
     * Get user activity statistics.
     */
    public function getUserStatistics(Request $request, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $days = $request->get('days', 30);
        
        $statistics = $this->activityService->getUserStatistics($userId, $days);

        return response()->json($statistics);
    }

    /**
     * Get activities by user.
     */
    public function getUserActivities(Request $request, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        $filters = array_merge(
            $request->only(['action', 'event_type', 'severity', 'date_from', 'date_to', 'days']),
            ['user_id' => $userId]
        );

        $activities = $this->activityService->getActivities(
            $filters,
            $request->get('per_page', 20)
        );

        if ($request->expectsJson()) {
            return response()->json([
                'activities' => $activities->items(),
                'pagination' => [
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'per_page' => $activities->perPage(),
                    'total' => $activities->total(),
                ],
            ]);
        }

        return view('activities.user', compact('activities', 'userId'));
    }

    /**
     * Export activities.
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $filters = $request->only([
            'user_id', 'action', 'event_type', 'severity', 
            'date_from', 'date_to', 'days'
        ]);

        // Get all activities without pagination for export
        $activities = $this->activityService->getActivities($filters, 10000);

        // Log the export activity
        $this->activityService->logExport('activities', 'Activity log exported', [
            'format' => $request->format,
            'record_count' => $activities->count(),
            'filters' => $filters,
        ]);

        $filename = 'activities_' . now()->format('Y-m-d_H-i-s');

        switch ($request->format) {
            case 'csv':
                return $this->exportToCsv($activities, $filename);
            case 'excel':
                return $this->exportToExcel($activities, $filename);
            case 'pdf':
                return $this->exportToPdf($activities, $filename);
        }
    }

    /**
     * Clean up old activities.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365',
        ]);

        $deletedCount = $this->activityService->cleanup($request->days);

        $this->activityService->logSystemActivity(
            'cleanup',
            "Cleaned up {$deletedCount} old activities (older than {$request->days} days)",
            ['deleted_count' => $deletedCount, 'days' => $request->days]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} old activities",
                'deleted_count' => $deletedCount,
            ]);
        }

        return back()->with('success', "Cleaned up {$deletedCount} old activities");
    }

    /**
     * Get activity timeline data.
     */
    public function getTimeline(Request $request)
    {
        $days = $request->get('days', 7);
        $userId = $request->get('user_id');

        $filters = ['days' => $days];
        if ($userId) {
            $filters['user_id'] = $userId;
        }

        $activities = $this->activityService->getActivities($filters, 1000);

        // Group activities by date
        $timeline = $activities->groupBy(function ($activity) {
            return $activity->created_at->format('Y-m-d');
        })->map(function ($dayActivities, $date) {
            return [
                'date' => $date,
                'formatted_date' => \Carbon\Carbon::parse($date)->format('M j, Y'),
                'day_name' => \Carbon\Carbon::parse($date)->format('l'),
                'activities' => $dayActivities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'description' => $activity->description,
                        'action' => $activity->action,
                        'event_type' => $activity->event_type,
                        'severity' => $activity->severity,
                        'icon' => $activity->icon,
                        'color' => $activity->color,
                        'time' => $activity->created_at->format('H:i'),
                        'time_ago' => $activity->time_ago,
                        'user' => $activity->user ? [
                            'id' => $activity->user->id,
                            'name' => $activity->user->name,
                        ] : null,
                    ];
                })->values(),
                'count' => $dayActivities->count(),
            ];
        })->values();

        return response()->json([
            'timeline' => $timeline,
            'total_activities' => $activities->count(),
            'date_range' => [
                'from' => now()->subDays($days)->format('Y-m-d'),
                'to' => now()->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Get subject name for display.
     */
    private function getSubjectName($subject)
    {
        if (!$subject) return null;

        return match(class_basename($subject)) {
            'User' => $subject->name,
            'Student' => $subject->user->name ?? 'Unknown Student',
            'Teacher' => $subject->user->name ?? 'Unknown Teacher',
            'Course' => $subject->name,
            'Enrollment' => "Enrollment #{$subject->id}",
            default => "#{$subject->id}",
        };
    }

    /**
     * Export activities to CSV.
     */
    private function exportToCsv($activities, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($activities) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'User', 'Action', 'Description', 'Event Type', 
                'Severity', 'Subject Type', 'Subject ID', 'IP Address', 
                'Created At'
            ]);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->user->name ?? 'System',
                    $activity->action,
                    $activity->description,
                    $activity->event_type,
                    $activity->severity,
                    $activity->subject_type ? class_basename($activity->subject_type) : '',
                    $activity->subject_id,
                    $activity->ip_address,
                    $activity->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export activities to Excel (simplified CSV for now).
     */
    private function exportToExcel($activities, $filename)
    {
        // For now, return CSV with Excel headers
        // In a real implementation, you'd use Laravel Excel package
        return $this->exportToCsv($activities, $filename);
    }

    /**
     * Export activities to PDF (simplified for now).
     */
    private function exportToPdf($activities, $filename)
    {
        // For now, return a simple text response
        // In a real implementation, you'd use a PDF library like DomPDF
        $content = "Activity Report - Generated on " . now()->format('Y-m-d H:i:s') . "\n\n";
        
        foreach ($activities as $activity) {
            $content .= sprintf(
                "[%s] %s - %s (%s)\n",
                $activity->created_at->format('Y-m-d H:i:s'),
                $activity->user->name ?? 'System',
                $activity->description,
                $activity->action
            );
        }

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"{$filename}.txt\"",
        ]);
    }
}
