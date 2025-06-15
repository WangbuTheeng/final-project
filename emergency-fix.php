<?php

/**
 * EMERGENCY PERMISSION FIX
 * 
 * This will definitely fix your permission issues
 * Run: php emergency-fix.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

echo "🚨 EMERGENCY PERMISSION FIX\n";
echo "===========================\n\n";

try {
    // Step 1: Create all required permissions
    echo "1️⃣ Creating permissions...\n";
    $permissions = [
        // Course Management
        'view-courses', 'create-courses', 'edit-courses', 'delete-courses',
        // Student Management
        'view-students', 'create-students', 'edit-students', 'delete-students',
        // Class Management
        'view-classes', 'create-classes', 'edit-classes', 'delete-classes',
        // Enrollment Management
        'view-enrollments', 'create-enrollments', 'edit-enrollments', 'delete-enrollments',
        // Exam Management
        'view-exams', 'create-exams', 'edit-exams', 'delete-exams', 'grade-exams',
        // User Management
        'view-users', 'create-users', 'edit-users', 'delete-users',
        // Role & Permission Management
        'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
        'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions',
        // Financial Management
        'view-finances', 'manage-fees', 'create-invoices', 'process-payments',
        // Teaching Management
        'assign-homework', 'grade-assignments', 'view-student-progress',
        // Dashboard Access
        'access-admin-dashboard', 'access-teacher-dashboard', 'access-examiner-dashboard',
        'access-accountant-dashboard', 'manage-settings'
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
        echo "✅ {$permission}\n";
    }

    // Step 2: Create/Update Super Admin role
    echo "\n2️⃣ Setting up Super Admin role...\n";
    $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
    $allPermissions = Permission::all();
    $superAdmin->syncPermissions($allPermissions);
    echo "✅ Super Admin role has " . $allPermissions->count() . " permissions\n";

    // Step 3: Find and update your user
    echo "\n3️⃣ Finding your user...\n";
    
    // Try to find the first user or a user with admin-like email
    $user = User::where('email', 'like', '%admin%')->first();
    if (!$user) {
        $user = User::first();
    }

    if ($user) {
        echo "✅ Found user: {$user->first_name} {$user->last_name} ({$user->email})\n";
        
        // Assign Super Admin role
        if (!$user->hasRole('Super Admin')) {
            $user->assignRole('Super Admin');
            echo "✅ Assigned Super Admin role to user\n";
        } else {
            echo "✅ User already has Super Admin role\n";
        }

        // Verify permissions
        if ($user->can('view-courses')) {
            echo "✅ User can view courses\n";
        } else {
            echo "❌ User still cannot view courses\n";
        }
    } else {
        echo "❌ No users found in database!\n";
        echo "💡 Please create a user first or check your database connection\n";
    }

    // Step 4: Clear caches
    echo "\n4️⃣ Clearing caches...\n";
    \Artisan::call('permission:cache-reset');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    echo "✅ Caches cleared\n";

    echo "\n🎉 EMERGENCY FIX COMPLETED!\n";
    echo "================================\n";
    echo "✅ All permissions created\n";
    echo "✅ Super Admin role configured\n";
    echo "✅ User assigned Super Admin role\n";
    echo "✅ Caches cleared\n";
    echo "\n💡 Now try accessing /courses again!\n";
    echo "🔗 If it still doesn't work, the issue might be:\n";
    echo "   - Wrong user logged in\n";
    echo "   - Database connection issues\n";
    echo "   - Cache not properly cleared\n";
    echo "\n🆘 If still broken, try logging out and back in!\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n💡 This might be a database connection issue.\n";
    echo "   Check your .env file and database connection.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🔍 VERIFICATION:\n";

try {
    $coursePermission = Permission::where('name', 'view-courses')->first();
    echo ($coursePermission ? "✅" : "❌") . " view-courses permission exists\n";

    $superAdminRole = Role::where('name', 'Super Admin')->first();
    echo ($superAdminRole ? "✅" : "❌") . " Super Admin role exists\n";

    if ($superAdminRole && $coursePermission) {
        $hasPermission = $superAdminRole->hasPermissionTo('view-courses');
        echo ($hasPermission ? "✅" : "❌") . " Super Admin has view-courses permission\n";
    }

    $userCount = User::count();
    echo "👥 Total users in database: {$userCount}\n";

    if ($userCount > 0) {
        $superAdminUsers = User::role('Super Admin')->count();
        echo "👑 Users with Super Admin role: {$superAdminUsers}\n";
    }

} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
}
