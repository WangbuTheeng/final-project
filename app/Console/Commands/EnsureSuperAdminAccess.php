<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class EnsureSuperAdminAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ensure:super-admin-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure Super Admin has full access to all features including Academic Years';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Ensuring Super Admin has full access...');

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get or create Super Admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->info('✅ Super Admin role exists');

        // Get all permissions
        $allPermissions = Permission::all();
        $this->info("📋 Found {$allPermissions->count()} total permissions");

        // Ensure Super Admin has ALL permissions
        $superAdminRole->syncPermissions($allPermissions);
        $this->info('✅ Super Admin now has all permissions');

        // Verify specific permissions
        $keyPermissions = [
            'manage-settings',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-permissions',
            'view-courses',
            'manage-courses',
            'view-students',
            'manage-students',
            'view-exams',
            'manage-exams',
            'view-finances',
            'manage-fees',
        ];

        $this->info('🔍 Verifying key permissions:');
        foreach ($keyPermissions as $permission) {
            $hasPermission = $superAdminRole->hasPermissionTo($permission);
            $status = $hasPermission ? '✅' : '❌';
            $this->line("  {$status} {$permission}");
        }

        // Get Super Admin users
        $superAdminUsers = User::role('Super Admin')->get();
        $this->info("👥 Found {$superAdminUsers->count()} Super Admin users:");

        foreach ($superAdminUsers as $user) {
            $userPermissions = $user->getAllPermissions()->count();
            $this->line("  • {$user->name} ({$user->email}) - {$userPermissions} permissions");
            
            // Refresh user permissions
            $user->load('roles', 'permissions');
        }

        // Test Academic Years access
        $this->info('🎓 Testing Academic Years access:');
        foreach ($superAdminUsers as $user) {
            $canAccessAcademicYears = $user->hasRole('Super Admin') || $user->hasRole('Admin');
            $hasManageSettings = $user->can('manage-settings');
            
            $status1 = $canAccessAcademicYears ? '✅' : '❌';
            $status2 = $hasManageSettings ? '✅' : '❌';
            
            $this->line("  • {$user->name}:");
            $this->line("    {$status1} Role-based access (Super Admin/Admin)");
            $this->line("    {$status2} manage-settings permission");
        }

        $this->info('🎉 Super Admin access verification complete!');
        
        return 0;
    }
}
