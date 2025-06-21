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

        // Update Admin role - remove user management and financial permissions
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            // Remove permissions that Admin should not have
            $permissionsToRemove = [
                'view-users', 'create-users', 'edit-users', 'delete-users',
                'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
                'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions',
                'assign-roles-to-users', 'assign-permissions-to-roles',
                'view-finances', 'manage-fees', 'create-invoices', 'manage-invoices',
                'create-payments', 'verify-payments', 'manage-payments', 'view-financial-reports',
                'manage-expenses', 'approve-expenses', 'manage-salaries',
            ];

            foreach ($permissionsToRemove as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && $adminRole->hasPermissionTo($permission)) {
                    $adminRole->revokePermissionTo($permission);
                }
            }

            // Add view-subjects permission if not already present
            $viewSubjects = Permission::where('name', 'view-subjects')->first();
            if ($viewSubjects && !$adminRole->hasPermissionTo($viewSubjects)) {
                $adminRole->givePermissionTo($viewSubjects);
            }
        }

        // Update Examiner role - add view-subjects permission
        $examinerRole = Role::where('name', 'Examiner')->first();
        if ($examinerRole) {
            $viewSubjects = Permission::where('name', 'view-subjects')->first();
            if ($viewSubjects && !$examinerRole->hasPermissionTo($viewSubjects)) {
                $examinerRole->givePermissionTo($viewSubjects);
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

        // Restore Admin role permissions
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $permissionsToRestore = [
                'view-users', 'create-users', 'edit-users', 'delete-users',
                'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
                'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions',
                'assign-roles-to-users', 'assign-permissions-to-roles',
                'view-finances', 'manage-fees', 'create-invoices', 'manage-invoices',
                'create-payments', 'verify-payments', 'manage-payments', 'view-financial-reports',
                'manage-expenses', 'approve-expenses', 'manage-salaries',
            ];

            foreach ($permissionsToRestore as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$adminRole->hasPermissionTo($permission)) {
                    $adminRole->givePermissionTo($permission);
                }
            }
        }

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
