<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class FixFinancePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:finance-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix finance permissions for Super Admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing finance permissions...');

        // Clear permission cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        
        if (!$superAdminRole) {
            $this->error('Super Admin role not found!');
            return 1;
        }

        // Get all permissions
        $allPermissions = Permission::all();
        
        // Sync all permissions to Super Admin role
        $superAdminRole->syncPermissions($allPermissions);
        
        $this->info("Super Admin role now has {$allPermissions->count()} permissions");
        
        // Get Super Admin users and refresh their permissions
        $superAdminUsers = User::role('Super Admin')->get();
        
        foreach ($superAdminUsers as $user) {
            // Force refresh user permissions
            $user->load('roles.permissions', 'permissions');
            
            $this->info("Checking user: {$user->name} ({$user->email})");
            
            // Test key permissions
            $keyPermissions = ['view-finances', 'manage-fees', 'create-invoices'];
            foreach ($keyPermissions as $permission) {
                $hasPermission = $user->can($permission);
                $status = $hasPermission ? '✓' : '✗';
                $this->line("  {$status} {$permission}");
            }
        }
        
        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->info('Finance permissions have been fixed!');
        
        return 0;
    }
}
