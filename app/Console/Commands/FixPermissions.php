<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class FixPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix orphaned permissions and clean up permission system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing permission system...');

        // Clear cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Check for orphaned role_has_permissions entries
        $this->info('Checking for orphaned role_has_permissions entries...');
        
        $orphanedRolePermissions = DB::table('role_has_permissions')
            ->leftJoin('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->whereNull('permissions.id')
            ->get();

        if ($orphanedRolePermissions->count() > 0) {
            $this->warn("Found {$orphanedRolePermissions->count()} orphaned role_has_permissions entries");
            
            foreach ($orphanedRolePermissions as $orphan) {
                $this->line("Removing orphaned permission ID: {$orphan->permission_id} from role ID: {$orphan->role_id}");
                DB::table('role_has_permissions')
                    ->where('role_id', $orphan->role_id)
                    ->where('permission_id', $orphan->permission_id)
                    ->delete();
            }
        } else {
            $this->info('No orphaned role_has_permissions entries found.');
        }

        // Check for orphaned model_has_permissions entries
        $this->info('Checking for orphaned model_has_permissions entries...');
        
        $orphanedModelPermissions = DB::table('model_has_permissions')
            ->leftJoin('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->whereNull('permissions.id')
            ->get();

        if ($orphanedModelPermissions->count() > 0) {
            $this->warn("Found {$orphanedModelPermissions->count()} orphaned model_has_permissions entries");
            
            foreach ($orphanedModelPermissions as $orphan) {
                $this->line("Removing orphaned permission ID: {$orphan->permission_id} from model ID: {$orphan->model_id}");
                DB::table('model_has_permissions')
                    ->where('model_id', $orphan->model_id)
                    ->where('permission_id', $orphan->permission_id)
                    ->delete();
            }
        } else {
            $this->info('No orphaned model_has_permissions entries found.');
        }

        // Check for orphaned model_has_roles entries
        $this->info('Checking for orphaned model_has_roles entries...');
        
        $orphanedModelRoles = DB::table('model_has_roles')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereNull('roles.id')
            ->get();

        if ($orphanedModelRoles->count() > 0) {
            $this->warn("Found {$orphanedModelRoles->count()} orphaned model_has_roles entries");
            
            foreach ($orphanedModelRoles as $orphan) {
                $this->line("Removing orphaned role ID: {$orphan->role_id} from model ID: {$orphan->model_id}");
                DB::table('model_has_roles')
                    ->where('model_id', $orphan->model_id)
                    ->where('role_id', $orphan->role_id)
                    ->delete();
            }
        } else {
            $this->info('No orphaned model_has_roles entries found.');
        }

        // List all current permissions
        $this->info('Current permissions in system:');
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $this->line("ID: {$permission->id} - Name: {$permission->name}");
        }

        // List all current roles and their permissions
        $this->info('Current roles and their permissions:');
        $roles = Role::with('permissions')->get();
        foreach ($roles as $role) {
            $this->line("Role: {$role->name}");
            foreach ($role->permissions as $permission) {
                $this->line("  - {$permission->name}");
            }
        }

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info('Permission system fixed successfully!');
        return 0;
    }
}
