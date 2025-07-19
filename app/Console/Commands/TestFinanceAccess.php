<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestFinanceAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:finance-access {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test finance access for a user';

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

        // Login as the user
        Auth::login($user);
        
        $this->info("Testing finance access for: {$user->name} ({$user->email})");
        $this->info("User roles: " . $user->roles->pluck('name')->implode(', '));
        
        // Test role checks
        $hasRole = $user->hasRole('Super Admin');
        $this->info("Has Super Admin role: " . ($hasRole ? 'YES' : 'NO'));
        
        // Test permission checks
        $canViewFinances = $user->can('view-finances');
        $this->info("Can view-finances: " . ($canViewFinances ? 'YES' : 'NO'));
        
        // Test the middleware logic
        $hasRoleOrPermission = $user->hasRole('Super Admin') || $user->can('view-finances');
        $this->info("Has role OR permission: " . ($hasRoleOrPermission ? 'YES' : 'NO'));
        
        // Test specific permissions
        $permissions = ['view-finances', 'manage-fees', 'create-invoices', 'manage-invoices'];
        $this->info("\nDetailed permission check:");
        foreach ($permissions as $permission) {
            $has = $user->can($permission);
            $status = $has ? '✓' : '✗';
            $this->line("  {$status} {$permission}");
        }
        
        if ($hasRoleOrPermission) {
            $this->info("\n✅ User should be able to access finance dashboard!");
        } else {
            $this->error("\n❌ User will be blocked from finance dashboard!");
        }
        
        return 0;
    }
}
