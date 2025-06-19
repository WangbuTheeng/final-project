<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Super Admin');
    }

    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by log name
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by subject type (model)
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Filter by IP address
        if ($request->filled('ip_address')) {
            $query->whereJsonContains('properties->ip_address', $request->ip_address);
        }

        $activityLogs = $query->latest()->paginate(50);

        // Get filter options
        $users = User::select('id', 'name')->orderBy('name')->get();
        $logNames = Activity::distinct()->pluck('log_name')->filter()->sort();
        $events = Activity::distinct()->pluck('event')->filter()->sort();
        $subjectTypes = Activity::distinct()->pluck('subject_type')->filter()->map(function($type) {
            return class_basename($type);
        })->sort();

        return view('activity-logs.index', compact('activityLogs', 'users', 'logNames', 'events', 'subjectTypes'));
    }

    public function show($id)
    {
        $activityLog = Activity::with(['causer', 'subject'])->findOrFail($id);
        return view('activity-logs.show', compact('activityLog'));
    }

    public function export(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // Apply same filters as index
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('ip_address')) {
            $query->whereJsonContains('properties->ip_address', $request->ip_address);
        }

        $activityLogs = $query->latest()->get();

        $filename = 'activity_logs_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activityLogs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Log Name',
                'Description',
                'Event',
                'Subject Type',
                'Subject ID',
                'User',
                'User ID',
                'IP Address',
                'User Agent',
                'URL',
                'Properties',
                'Created At'
            ]);

            foreach ($activityLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->log_name,
                    $log->description,
                    $log->event,
                    $log->subject_type ? class_basename($log->subject_type) : '',
                    $log->subject_id,
                    $log->causer ? $log->causer->name : '',
                    $log->causer_id,
                    $log->properties['ip_address'] ?? '',
                    $log->properties['user_agent'] ?? '',
                    $log->properties['url'] ?? '',
                    json_encode($log->properties),
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
