<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user-permissions {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user permissions and roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found!");
                return 1;
            }
        } else {
            // Find first Super Admin user
            $user = User::role('Super Admin')->first();
            if (!$user) {
                $this->error("No Super Admin users found!");
                return 1;
            }
        }

        $this->info("User: {$user->name} ({$user->email})");
        $this->info("Direct Role: {$user->role}");
        $this->info("Spatie Roles: " . $user->roles->pluck('name')->implode(', '));
        
        // Check specific permissions
        $permissions = [
            'view-finances',
            'manage-fees',
            'create-invoices',
            'view-users',
            'manage-exams'
        ];
        
        $this->info("\nPermission Check:");
        foreach ($permissions as $permission) {
            $hasPermission = $user->can($permission);
            $status = $hasPermission ? '✓' : '✗';
            $this->line("  {$status} {$permission}");
        }
        
        $this->info("\nTotal Permissions: " . $user->getAllPermissions()->count());
        
        return 0;
    }
}
