<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new faculty permissions
        Permission::firstOrCreate(['name' => 'view-faculties']);
        Permission::firstOrCreate(['name' => 'manage-faculties']);

        // Get roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $teacherRole = Role::where('name', 'Teacher')->first();

        if ($superAdminRole) {
            // Super Admin gets all permissions
            $superAdminRole->givePermissionTo(['view-faculties', 'manage-faculties']);
        }

        if ($adminRole) {
            // Admin gets faculty management permissions
            $adminRole->givePermissionTo(['view-faculties', 'manage-faculties']);
        }

        if ($teacherRole) {
            // Teacher gets only view permission and remove manage-settings
            $teacherRole->givePermissionTo('view-faculties');
            $teacherRole->revokePermissionTo('manage-settings');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get roles
        $teacherRole = Role::where('name', 'Teacher')->first();

        if ($teacherRole) {
            // Restore manage-settings to teacher
            $teacherRole->givePermissionTo('manage-settings');
            $teacherRole->revokePermissionTo('view-faculties');
        }

        // Remove faculty permissions from all roles
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->revokePermissionTo(['view-faculties', 'manage-faculties']);
        }

        // Delete the permissions
        Permission::where('name', 'view-faculties')->delete();
        Permission::where('name', 'manage-faculties')->delete();
    }
};
