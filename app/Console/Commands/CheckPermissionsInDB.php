<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CheckPermissionsInDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:permissions-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check permissions in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking permissions in database...');

        // Check if finance permissions exist
        $financePermissions = ['view-finances', 'manage-fees', 'create-invoices', 'manage-invoices', 'create-payments'];
        
        $this->info('Finance permissions in database:');
        foreach ($financePermissions as $permName) {
            $permission = Permission::where('name', $permName)->first();
            if ($permission) {
                $this->line("  ✓ {$permName} (ID: {$permission->id})");
            } else {
                $this->line("  ✗ {$permName} - NOT FOUND");
            }
        }

        // Check Super Admin role permissions
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $this->info("\nSuper Admin role permissions: {$superAdminRole->permissions->count()}");
            
            $hasFinancePerms = $superAdminRole->permissions->whereIn('name', $financePermissions);
            $this->info("Finance permissions assigned to Super Admin role: {$hasFinancePerms->count()}");
            
            foreach ($financePermissions as $permName) {
                $hasIt = $superAdminRole->hasPermissionTo($permName);
                $status = $hasIt ? '✓' : '✗';
                $this->line("  {$status} {$permName}");
            }
        }

        // Check user permissions
        $user = User::role('Super Admin')->first();
        if ($user) {
            $this->info("\nUser: {$user->name}");
            $this->info("User roles: " . $user->roles->pluck('name')->implode(', '));
            $this->info("Direct permissions: {$user->permissions->count()}");
            $this->info("All permissions (via roles): {$user->getAllPermissions()->count()}");
            
            // Test specific permission
            $testPerm = 'view-finances';
            $canDo = $user->can($testPerm);
            $this->info("Can '{$testPerm}': " . ($canDo ? 'YES' : 'NO'));
            
            // Check via different methods
            $hasPermissionTo = $user->hasPermissionTo($testPerm);
            $this->info("hasPermissionTo '{$testPerm}': " . ($hasPermissionTo ? 'YES' : 'NO'));
        }

        return 0;
    }
}
