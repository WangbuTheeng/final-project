<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UpdateTeacherPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update-teacher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Teacher role permissions to allow viewing data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Get the Teacher role
        $teacherRole = Role::where('name', 'Teacher')->first();

        if (!$teacherRole) {
            $this->error('Teacher role not found!');
            return 1;
        }

        // Add the missing permissions for teachers to view data
        $newPermissions = [
            'view-students',
            'view-exams', 
            'view-grades',
            'view-reports',
            'manage-settings', // For viewing faculties
        ];

        $addedPermissions = [];

        // Check if permissions exist and assign them
        foreach ($newPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                if (!$teacherRole->hasPermissionTo($permission)) {
                    $teacherRole->givePermissionTo($permission);
                    $addedPermissions[] = $permissionName;
                    $this->info("Added permission: {$permissionName}");
                } else {
                    $this->line("Permission already exists: {$permissionName}");
                }
            } else {
                $this->warn("Permission not found: {$permissionName}");
            }
        }

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (count($addedPermissions) > 0) {
            $this->info('Teacher permissions updated successfully!');
            $this->info('Added permissions: ' . implode(', ', $addedPermissions));
        } else {
            $this->info('No new permissions were added. Teacher role already has all required permissions.');
        }

        return 0;
    }
}
