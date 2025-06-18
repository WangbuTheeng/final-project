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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create new permissions
        $newPermissions = [
            'view-reports',
            'generate-reports', 
            'export-reports',
        ];

        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $examinerRole = Role::where('name', 'Examiner')->first();
        $teacherRole = Role::where('name', 'Teacher')->first();

        // Assign permissions to roles
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($newPermissions);
        }

        if ($adminRole) {
            $adminRole->givePermissionTo($newPermissions);
        }

        if ($examinerRole) {
            $examinerRole->givePermissionTo(['view-reports', 'generate-reports']);
        }

        if ($teacherRole) {
            $teacherRole->givePermissionTo(['view-reports']);
        }

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permissions
        $permissions = ['view-reports', 'generate-reports', 'export-reports'];
        
        foreach ($permissions as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $perm->delete();
            }
        }

        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
