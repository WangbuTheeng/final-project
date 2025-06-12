<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dynamic dashboard based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $role = null;
        
        // Determine user's highest role (prioritizing the most important ones)
        if ($user->hasRole('Super Admin')) {
            $role = 'Super Admin';
        } elseif ($user->hasRole('Admin')) {
            $role = 'Admin';
        } elseif ($user->hasRole('Examiner')) {
            $role = 'Examiner';
        } elseif ($user->hasRole('Accountant')) {
            $role = 'Accountant';
        } elseif ($user->hasRole('Teacher')) {
            $role = 'Teacher';
        } else {
            $role = 'User'; // Default role
        }

        // Get permissions for the view
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        // Return the dashboard view (using the renamed file)
        return view('dashboard', compact('user', 'role', 'permissions'));
    }
} 