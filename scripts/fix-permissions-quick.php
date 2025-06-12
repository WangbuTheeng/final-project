<?php

/**
 * Quick Permission Fix Script
 * 
 * Run this script to immediately fix the permission issues
 * Usage: php scripts/fix-permissions-quick.php
 */

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "❌ Error: Please run this script from the Laravel project root directory.\n";
    echo "Usage: php scripts/fix-permissions-quick.php\n";
    exit(1);
}

echo "🚀 Quick Permission Fix Starting...\n\n";

// Run the permission refresh script
echo "1️⃣ Refreshing permissions...\n";
$output = shell_exec('php artisan db:seed --class=RolesAndPermissionsSeeder --force 2>&1');
echo $output;

// Clear all caches
echo "\n2️⃣ Clearing caches...\n";
$commands = [
    'php artisan permission:cache-reset',
    'php artisan config:clear',
    'php artisan route:clear',
    'php artisan view:clear',
    'php artisan cache:clear'
];

foreach ($commands as $command) {
    echo "Running: {$command}\n";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo $output;
    }
}

echo "\n3️⃣ Verifying Super Admin permissions...\n";

// Create a simple verification script
$verifyScript = '
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$superAdmin = Role::where("name", "Super Admin")->first();
if ($superAdmin) {
    $allPermissions = Permission::all();
    $superAdmin->syncPermissions($allPermissions);
    echo "✅ Super Admin now has " . $allPermissions->count() . " permissions\n";
    
    // Check specific permissions
    $requiredPerms = ["view-courses", "create-courses", "edit-courses", "delete-courses"];
    foreach ($requiredPerms as $perm) {
        if ($superAdmin->hasPermissionTo($perm)) {
            echo "✅ Has permission: " . $perm . "\n";
        } else {
            echo "❌ Missing permission: " . $perm . "\n";
        }
    }
} else {
    echo "❌ Super Admin role not found\n";
}
';

$output = shell_exec('php artisan tinker --execute="' . $verifyScript . '" 2>&1');
echo $output;

echo "\n🎉 Quick fix completed!\n";
echo "\n📝 What was fixed:\n";
echo "✅ Updated route permissions to match existing permission names\n";
echo "✅ Fixed controller authorization methods\n";
echo "✅ Added missing permissions to seeder\n";
echo "✅ Ensured Super Admin has all permissions\n";
echo "✅ Cleared all caches\n";

echo "\n🔗 You should now be able to access:\n";
echo "- Courses page (/courses)\n";
echo "- Classes page (/classes)\n";
echo "- Students page (/students)\n";
echo "- Enrollments page (/enrollments)\n";
echo "- Exams page (/exams)\n";

echo "\n💡 If you still have issues, try:\n";
echo "1. Log out and log back in\n";
echo "2. Check your user has the Super Admin role\n";
echo "3. Run: php artisan permission:cache-reset\n";

echo "\n✨ Happy coding!\n";
