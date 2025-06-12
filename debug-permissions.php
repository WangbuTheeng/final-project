<?php

/**
 * Debug Permissions Script
 * 
 * This script will help us understand what's happening with your permissions
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

echo "🔍 DEBUGGING PERMISSION ISSUES\n";
echo "================================\n\n";

// 1. Check if permissions exist
echo "1️⃣ CHECKING PERMISSIONS:\n";
$requiredPermissions = ['view-courses', 'create-courses', 'edit-courses', 'delete-courses'];
foreach ($requiredPermissions as $perm) {
    $exists = Permission::where('name', $perm)->exists();
    echo ($exists ? "✅" : "❌") . " Permission '{$perm}': " . ($exists ? "EXISTS" : "MISSING") . "\n";
}

// 2. Check roles
echo "\n2️⃣ CHECKING ROLES:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "✅ Role: {$role->name} (ID: {$role->id})\n";
}

// 3. Check Super Admin role permissions
echo "\n3️⃣ SUPER ADMIN PERMISSIONS:\n";
$superAdmin = Role::where('name', 'Super Admin')->first();
if ($superAdmin) {
    echo "✅ Super Admin role found (ID: {$superAdmin->id})\n";
    $permissions = $superAdmin->permissions;
    echo "📊 Total permissions: " . $permissions->count() . "\n";
    
    // Check specific permissions
    foreach ($requiredPermissions as $perm) {
        $has = $superAdmin->hasPermissionTo($perm);
        echo ($has ? "✅" : "❌") . " Has '{$perm}': " . ($has ? "YES" : "NO") . "\n";
    }
} else {
    echo "❌ Super Admin role NOT FOUND!\n";
}

// 4. Check your user (assuming you're user ID 1 or email-based)
echo "\n4️⃣ CHECKING YOUR USER:\n";
$user = User::find(1); // Try first user
if (!$user) {
    $user = User::first(); // Get any user
}

if ($user) {
    echo "✅ User found: {$user->first_name} {$user->last_name} (ID: {$user->id})\n";
    echo "📧 Email: {$user->email}\n";
    
    // Check user roles
    $userRoles = $user->roles;
    echo "👤 User roles: " . $userRoles->pluck('name')->implode(', ') . "\n";
    
    // Check if user has Super Admin role
    $hasSuperAdmin = $user->hasRole('Super Admin');
    echo ($hasSuperAdmin ? "✅" : "❌") . " Has Super Admin role: " . ($hasSuperAdmin ? "YES" : "NO") . "\n";
    
    // Check specific permissions
    foreach ($requiredPermissions as $perm) {
        $can = $user->can($perm);
        echo ($can ? "✅" : "❌") . " Can '{$perm}': " . ($can ? "YES" : "NO") . "\n";
    }
} else {
    echo "❌ No users found in database!\n";
}

// 5. Check route middleware
echo "\n5️⃣ CHECKING ROUTE CONFIGURATION:\n";
$routeFile = __DIR__ . '/routes/web.php';
if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    if (strpos($content, "permission:view-courses") !== false) {
        echo "✅ Route uses 'permission:view-courses'\n";
    } else if (strpos($content, "permission:manage-courses") !== false) {
        echo "❌ Route still uses 'permission:manage-courses' (WRONG!)\n";
    } else {
        echo "⚠️  Could not find course route permission\n";
    }
} else {
    echo "❌ routes/web.php not found\n";
}

// 6. Check controller authorization
echo "\n6️⃣ CHECKING CONTROLLER:\n";
$controllerFile = __DIR__ . '/app/Http/Controllers/CourseController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    if (strpos($content, "authorize('view-courses')") !== false) {
        echo "✅ Controller uses 'view-courses' permission\n";
    } else if (strpos($content, "authorize('manage-courses')") !== false) {
        echo "❌ Controller still uses 'manage-courses' (WRONG!)\n";
    } else {
        echo "⚠️  Could not find authorization in controller\n";
    }
} else {
    echo "❌ CourseController.php not found\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 RECOMMENDATIONS:\n";

// Generate recommendations based on findings
if (!Permission::where('name', 'view-courses')->exists()) {
    echo "1. Create missing permissions: php artisan db:seed --class=RolesAndPermissionsSeeder --force\n";
}

if (!$superAdmin) {
    echo "2. Create Super Admin role\n";
} else if ($superAdmin && !$superAdmin->hasPermissionTo('view-courses')) {
    echo "2. Assign permissions to Super Admin role\n";
}

if ($user && !$user->hasRole('Super Admin')) {
    echo "3. Assign Super Admin role to your user\n";
}

echo "4. Clear caches: php artisan permission:cache-reset\n";
echo "5. Try accessing /courses again\n";

echo "\n💡 QUICK FIX COMMAND:\n";
echo "php artisan tinker --execute=\"\n";
echo "use Spatie\Permission\Models\Permission;\n";
echo "use Spatie\Permission\Models\Role;\n";
echo "use App\Models\User;\n";
echo "\n";
echo "// Create permissions\n";
echo "Permission::firstOrCreate(['name' => 'view-courses']);\n";
echo "Permission::firstOrCreate(['name' => 'create-courses']);\n";
echo "Permission::firstOrCreate(['name' => 'edit-courses']);\n";
echo "Permission::firstOrCreate(['name' => 'delete-courses']);\n";
echo "\n";
echo "// Get/Create Super Admin role\n";
echo "\\\$superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);\n";
echo "\\\$superAdmin->givePermissionTo(Permission::all());\n";
echo "\n";
echo "// Assign to your user (change email as needed)\n";
echo "\\\$user = User::where('email', 'your-email@example.com')->first();\n";
echo "if (\\\$user) \\\$user->assignRole('Super Admin');\n";
echo "\n";
echo "echo 'Fixed!';\n";
echo "\"\n";

echo "\n🚀 Run this debug script result and let me know what you see!\n";
