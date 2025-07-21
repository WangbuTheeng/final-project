<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignEnrollmentPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enrollment:fix-permissions {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix enrollment permissions for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'superadmin@example.com';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get or create Super Admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Get all permissions
        $allPermissions = Permission::all();

        // Assign all permissions to Super Admin role
        $superAdminRole->syncPermissions($allPermissions);

        // Assign Super Admin role to user
        $user->assignRole($superAdminRole);

        // Also give direct permissions to user (backup)
        $user->givePermissionTo($allPermissions);

        $this->info("✅ Fixed permissions for user: {$user->name} ({$user->email})");
        $this->info("✅ Assigned Super Admin role");
        $this->info("✅ Assigned all " . $allPermissions->count() . " permissions");

        // Test specific permission
        if ($user->hasPermissionTo('manage-enrollments')) {
            $this->info("✅ User can access enrollments");
        } else {
            $this->error("❌ User still cannot access enrollments");
        }

        return 0;
    }
}
