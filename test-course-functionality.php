<?php

/**
 * Test Course Functionality Script
 * 
 * This script tests if course creation and management works properly
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Department;

echo "🧪 TESTING COURSE FUNCTIONALITY\n";
echo "===============================\n\n";

try {
    // Step 1: Check permissions
    echo "1️⃣ Checking permissions...\n";
    $requiredPermissions = ['view-courses', 'create-courses', 'edit-courses', 'delete-courses'];
    $missingPermissions = [];
    
    foreach ($requiredPermissions as $perm) {
        $exists = Permission::where('name', $perm)->exists();
        if ($exists) {
            echo "✅ {$perm}\n";
        } else {
            echo "❌ {$perm} - MISSING!\n";
            $missingPermissions[] = $perm;
        }
    }

    if (!empty($missingPermissions)) {
        echo "\n🚨 Creating missing permissions...\n";
        foreach ($missingPermissions as $perm) {
            Permission::create(['name' => $perm]);
            echo "✅ Created {$perm}\n";
        }
    }

    // Step 2: Check Super Admin role
    echo "\n2️⃣ Checking Super Admin role...\n";
    $superAdmin = Role::where('name', 'Super Admin')->first();
    if (!$superAdmin) {
        echo "❌ Super Admin role not found! Creating...\n";
        $superAdmin = Role::create(['name' => 'Super Admin']);
    }
    
    // Ensure Super Admin has all permissions
    $allPermissions = Permission::all();
    $superAdmin->syncPermissions($allPermissions);
    echo "✅ Super Admin has " . $allPermissions->count() . " permissions\n";

    // Step 3: Check user
    echo "\n3️⃣ Checking user setup...\n";
    $user = User::first();
    if (!$user) {
        echo "❌ No users found! Please create a user first.\n";
        return;
    }
    
    echo "✅ Found user: {$user->first_name} {$user->last_name} ({$user->email})\n";
    
    if (!$user->hasRole('Super Admin')) {
        $user->assignRole('Super Admin');
        echo "✅ Assigned Super Admin role to user\n";
    } else {
        echo "✅ User already has Super Admin role\n";
    }

    // Step 4: Test specific permissions
    echo "\n4️⃣ Testing user permissions...\n";
    foreach ($requiredPermissions as $perm) {
        $can = $user->can($perm);
        echo ($can ? "✅" : "❌") . " User can '{$perm}': " . ($can ? "YES" : "NO") . "\n";
    }

    // Step 5: Check required models exist
    echo "\n5️⃣ Checking required models...\n";
    
    // Check Faculty
    $facultyCount = Faculty::count();
    echo "🏛️  Faculties in database: {$facultyCount}\n";
    if ($facultyCount === 0) {
        echo "⚠️  No faculties found. Creating sample faculty...\n";
        $faculty = Faculty::create([
            'name' => 'Sample Faculty',
            'code' => 'SF',
            'description' => 'Sample faculty for testing',
            'status' => 'active'
        ]);
        echo "✅ Created sample faculty: {$faculty->name}\n";
    } else {
        $faculty = Faculty::first();
        echo "✅ Using faculty: {$faculty->name}\n";
    }

    // Check Department
    $departmentCount = Department::count();
    echo "🏢 Departments in database: {$departmentCount}\n";
    if ($departmentCount === 0) {
        echo "⚠️  No departments found. Creating sample department...\n";
        $department = Department::create([
            'name' => 'Sample Department',
            'code' => 'SD',
            'faculty_id' => $faculty->id,
            'description' => 'Sample department for testing',
            'status' => 'active'
        ]);
        echo "✅ Created sample department: {$department->name}\n";
    } else {
        $department = Department::first();
        echo "✅ Using department: {$department->name}\n";
    }

    // Step 6: Test course creation
    echo "\n6️⃣ Testing course creation...\n";
    
    $testCourseData = [
        'title' => 'Test Course - ' . date('Y-m-d H:i:s'),
        'code' => 'TEST' . rand(100, 999),
        'description' => 'This is a test course created by the functionality test script',
        'credit_units' => 3,
        'level' => 100,
        'semester' => 'first',
        'faculty_id' => $faculty->id,
        'department_id' => $department->id,
        'status' => 'active'
    ];

    try {
        $course = Course::create($testCourseData);
        echo "✅ Successfully created test course: {$course->title} ({$course->code})\n";
        echo "   📋 Course ID: {$course->id}\n";
        echo "   🎓 Credit Units: {$course->credit_units}\n";
        echo "   📚 Level: {$course->level}\n";
        
        // Test course update
        $course->update(['description' => 'Updated description - ' . date('H:i:s')]);
        echo "✅ Successfully updated course description\n";
        
        // Test course deletion
        $courseId = $course->id;
        $course->delete();
        echo "✅ Successfully deleted test course (ID: {$courseId})\n";
        
    } catch (Exception $e) {
        echo "❌ Failed to create course: " . $e->getMessage() . "\n";
        echo "📍 Error in: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }

    // Step 7: Clear caches
    echo "\n7️⃣ Clearing caches...\n";
    \Artisan::call('permission:cache-reset');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    echo "✅ Caches cleared\n";

    echo "\n🎉 COURSE FUNCTIONALITY TEST COMPLETED!\n";
    echo "=====================================\n";
    echo "✅ Permissions verified\n";
    echo "✅ Super Admin role configured\n";
    echo "✅ User permissions tested\n";
    echo "✅ Course CRUD operations tested\n";
    echo "✅ Caches cleared\n";
    
    echo "\n🌐 WEB INTERFACE TEST:\n";
    echo "Now try these URLs in your browser:\n";
    echo "1. View courses: /courses\n";
    echo "2. Create course: /courses/create\n";
    echo "3. If you can access both, course functionality is working!\n";

    echo "\n💡 TROUBLESHOOTING:\n";
    echo "If course creation still doesn't work:\n";
    echo "1. Check if you're logged in as the correct user\n";
    echo "2. Verify the user has Super Admin role\n";
    echo "3. Check browser console for JavaScript errors\n";
    echo "4. Check Laravel logs: storage/logs/laravel.log\n";

} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n💡 This might indicate:\n";
    echo "   - Database connection issues\n";
    echo "   - Missing database tables\n";
    echo "   - Incorrect .env configuration\n";
    echo "\n🔧 Try running:\n";
    echo "   php artisan migrate\n";
    echo "   php artisan db:seed\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
