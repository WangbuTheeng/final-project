<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Super Admin');
    }

    public function index()
    {
        $activityLogs = Activity::latest()->paginate(50);
        return view('activity-logs.index', compact('activityLogs'));
    }
}
