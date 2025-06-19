<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get the Teacher role
        $teacherRole = Role::where('name', 'Teacher')->first();

        if ($teacherRole) {
            // Add the missing permissions for teachers to view data
            $newPermissions = [
                'view-students',
                'view-exams', 
                'view-grades',
                'view-reports',
                'manage-settings', // For viewing faculties
            ];

            // Check if permissions exist and assign them
            foreach ($newPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$teacherRole->hasPermissionTo($permission)) {
                    $teacherRole->givePermissionTo($permission);
                }
            }
        }

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get the Teacher role
        $teacherRole = Role::where('name', 'Teacher')->first();

        if ($teacherRole) {
            // Remove the permissions that were added
            $permissionsToRemove = [
                'view-students',
                'view-exams',
                'view-grades', 
                'view-reports',
                'manage-settings',
            ];

            foreach ($permissionsToRemove as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && $teacherRole->hasPermissionTo($permission)) {
                    $teacherRole->revokePermissionTo($permission);
                }
            }
        }

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
