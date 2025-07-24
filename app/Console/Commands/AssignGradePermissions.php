<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignGradePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grades:assign-permissions {email? : Email of the user to assign permissions to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign grade management permissions to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            $email = $this->ask('Enter the email address of the user');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        // Create permissions if they don't exist
        $permissions = [
            'manage-grades',
            'view-grades',
            'create-grades',
            'edit-grades',
            'delete-grades'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to user
        $user->givePermissionTo($permissions);

        // Also assign Admin role if user doesn't have sufficient permissions
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin') && !$user->hasRole('Teacher') && !$user->hasRole('Examiner')) {
            $adminRole = Role::firstOrCreate(['name' => 'Admin']);
            $user->assignRole($adminRole);
            $this->info("Assigned Admin role to {$user->name}");
        }

        $this->info("Successfully assigned grade permissions to {$user->name} ({$user->email})");
        $this->info("User can now access:");
        $this->line("- Grades Index: /grades");
        $this->line("- Create Grade: /grades/create");
        $this->line("- Bulk Grade Entry: /grades/bulk-entry");
        
        return 0;
    }
}
