#!/bin/bash

# Quick Permission Fix Script for Laravel College Management System
# This script fixes the 403 permission errors

echo "🚀 Fixing Permission Issues..."
echo "================================"

# Step 1: Re-run the permissions seeder
echo "1️⃣ Refreshing permissions and roles..."
php artisan db:seed --class=RolesAndPermissionsSeeder --force

# Step 2: Clear permission cache
echo "2️⃣ Clearing permission cache..."
php artisan permission:cache-reset

# Step 3: Clear other caches
echo "3️⃣ Clearing application caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Step 4: Verify Super Admin permissions
echo "4️⃣ Ensuring Super Admin has all permissions..."
php artisan tinker --execute="
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

\$superAdmin = Role::where('name', 'Super Admin')->first();
if (\$superAdmin) {
    \$allPermissions = Permission::all();
    \$superAdmin->syncPermissions(\$allPermissions);
    echo '✅ Super Admin now has ' . \$allPermissions->count() . ' permissions' . PHP_EOL;
} else {
    echo '❌ Super Admin role not found' . PHP_EOL;
}
"

echo ""
echo "✅ Permission fix completed!"
echo ""
echo "🎉 You should now be able to access:"
echo "   - Courses (/courses)"
echo "   - Classes (/classes)" 
echo "   - Students (/students)"
echo "   - Enrollments (/enrollments)"
echo "   - Exams (/exams)"
echo ""
echo "💡 If you still have issues:"
echo "   1. Log out and log back in"
echo "   2. Verify you have the Super Admin role"
echo "   3. Run: php artisan permission:cache-reset"
echo ""
echo "🔗 For more help, check the pull request: https://github.com/WangbuTheeng/final-project/pull/1"
