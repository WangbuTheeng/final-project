<?php

/**
 * Script to refresh permissions and ensure Super Admin has all permissions
 * 
 * This script will:
 * 1. Create any missing permissions
 * 2. Ensure Super Admin role has all permissions
 * 3. Display current permission status
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "🔧 Refreshing Permissions...\n\n";

// Define all required permissions
$requiredPermissions = [
    // User Management
    'view-users', 'create-users', 'edit-users', 'delete-users',
    
    // Role & Permission Management
    'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
    'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions',
    
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
    
    // Financial Management
    'view-finances', 'manage-fees', 'create-invoices', 'process-payments',
    
    // Teaching Management
    'assign-homework', 'grade-assignments', 'view-student-progress',
    
    // Dashboard Access
    'access-admin-dashboard', 'access-teacher-dashboard', 'access-examiner-dashboard', 
    'access-accountant-dashboard', 'manage-settings',
];

echo "📋 Creating missing permissions...\n";

$createdCount = 0;
foreach ($requiredPermissions as $permissionName) {
    $permission = Permission::firstOrCreate(['name' => $permissionName]);
    if ($permission->wasRecentlyCreated) {
        echo "✅ Created permission: {$permissionName}\n";
        $createdCount++;
    }
}

if ($createdCount === 0) {
    echo "✅ All permissions already exist\n";
} else {
    echo "✅ Created {$createdCount} new permissions\n";
}

echo "\n👑 Ensuring Super Admin has all permissions...\n";

// Get or create Super Admin role
$superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

// Get all permissions
$allPermissions = Permission::all();

// Assign all permissions to Super Admin
$superAdminRole->syncPermissions($allPermissions);

echo "✅ Super Admin role now has " . $allPermissions->count() . " permissions\n";

echo "\n📊 Current Permission Summary:\n";
echo "Total Permissions: " . Permission::count() . "\n";
echo "Total Roles: " . Role::count() . "\n";

echo "\n🔍 Role Permission Counts:\n";
$roles = Role::with('permissions')->get();
foreach ($roles as $role) {
    echo "- {$role->name}: " . $role->permissions->count() . " permissions\n";
}

echo "\n✅ Permission refresh completed!\n";

// Clear permission cache
if (function_exists('app')) {
    try {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        echo "✅ Permission cache cleared\n";
    } catch (Exception $e) {
        echo "⚠️  Could not clear permission cache: " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 All done! You can now access the courses page.\n";
