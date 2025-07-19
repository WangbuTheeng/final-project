<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixSuperAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:super-admin-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure Super Admin has all permissions including finance access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing Super Admin permissions...');

        // Get Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        
        if (!$superAdminRole) {
            $this->error('Super Admin role not found!');
            return 1;
        }

        // Get all permissions
        $allPermissions = Permission::all();
        
        $this->info("Found {$allPermissions->count()} total permissions");
        
        // Give Super Admin all permissions
        $superAdminRole->givePermissionTo($allPermissions);
        
        // Refresh the role to get updated permissions count
        $superAdminRole->refresh();
        $superAdminRole->load('permissions');
        
        $this->info("Super Admin now has {$superAdminRole->permissions->count()} permissions");
        
        // List some key permissions to verify
        $keyPermissions = [
            'view-finances',
            'manage-fees', 
            'create-invoices',
            'manage-invoices',
            'view-users',
            'create-users',
            'manage-exams',
            'view-exams'
        ];
        
        $this->info('Checking key permissions:');
        foreach ($keyPermissions as $permission) {
            $hasPermission = $superAdminRole->hasPermissionTo($permission);
            $status = $hasPermission ? '✓' : '✗';
            $this->line("  {$status} {$permission}");
        }
        
        $this->info('Super Admin permissions have been updated successfully!');
        
        return 0;
    }
}
