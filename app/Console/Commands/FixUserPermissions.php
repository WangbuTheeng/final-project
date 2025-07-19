<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class FixUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:user-permissions {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix user permissions by assigning them directly';

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

        $this->info("Fixing permissions for: {$user->name} ({$user->email})");
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Get all permissions
        $allPermissions = Permission::all();
        
        // Assign all permissions directly to the user
        $user->givePermissionTo($allPermissions);
        
        $this->info("Assigned {$allPermissions->count()} permissions directly to user");
        
        // Test key permissions
        $keyPermissions = ['view-finances', 'manage-fees', 'create-invoices'];
        $this->info("\nTesting key permissions:");
        foreach ($keyPermissions as $permission) {
            $has = $user->can($permission);
            $status = $has ? '✓' : '✗';
            $this->line("  {$status} {$permission}");
        }
        
        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->info("\nUser permissions have been fixed!");
        
        return 0;
    }
}
