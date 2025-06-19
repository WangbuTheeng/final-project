<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UpdateRolePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update role permissions according to new requirements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info('Updating role permissions...');

        // Update Admin role
        $this->updateAdminRole();
        
        // Update Examiner role
        $this->updateExaminerRole();
        
        // Update Accountant role
        $this->updateAccountantRole();

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info('Role permissions updated successfully!');
        return 0;
    }

    private function updateAdminRole()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        if (!$adminRole) {
            $this->error('Admin role not found!');
            return;
        }

        $this->info('Updating Admin role...');

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
                $this->line("Removed permission: {$permissionName}");
            }
        }

        // Add view-subjects permission if not already present
        $viewSubjects = Permission::where('name', 'view-subjects')->first();
        if ($viewSubjects && !$adminRole->hasPermissionTo($viewSubjects)) {
            $adminRole->givePermissionTo($viewSubjects);
            $this->line("Added permission: view-subjects");
        }
    }

    private function updateExaminerRole()
    {
        $examinerRole = Role::where('name', 'Examiner')->first();
        if (!$examinerRole) {
            $this->error('Examiner role not found!');
            return;
        }

        $this->info('Updating Examiner role...');

        // Add view-subjects permission if not already present
        $viewSubjects = Permission::where('name', 'view-subjects')->first();
        if ($viewSubjects && !$examinerRole->hasPermissionTo($viewSubjects)) {
            $examinerRole->givePermissionTo($viewSubjects);
            $this->line("Added permission: view-subjects");
        }
    }

    private function updateAccountantRole()
    {
        $accountantRole = Role::where('name', 'Accountant')->first();
        if (!$accountantRole) {
            $this->error('Accountant role not found!');
            return;
        }

        $this->info('Updating Accountant role...');
        $this->line('Accountant role permissions are already correctly configured.');
    }
}
