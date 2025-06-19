<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the Teacher role
        $teacherRole = Role::where('name', 'Teacher')->first();

        if ($teacherRole) {
            // Remove all exam management permissions from Teacher role
            $examManagementPermissions = [
                'create-exams',
                'edit-exams',
                'delete-exams',
                'manage-exams'
            ];

            foreach ($examManagementPermissions as $permission) {
                if ($teacherRole->hasPermissionTo($permission)) {
                    $teacherRole->revokePermissionTo($permission);
                }
            }

            // Ensure Teacher has view-exams permission
            if (!$teacherRole->hasPermissionTo('view-exams')) {
                $teacherRole->givePermissionTo('view-exams');
            }

            echo "Teacher exam permissions fixed: removed management permissions, kept view permission\n";
        } else {
            echo "Teacher role not found\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the Teacher role
        $teacherRole = Role::where('name', 'Teacher')->first();

        if ($teacherRole) {
            // Restore exam management permissions to Teacher role
            $examManagementPermissions = [
                'create-exams',
                'edit-exams',
                'delete-exams',
                'manage-exams'
            ];

            $teacherRole->givePermissionTo($examManagementPermissions);
            echo "Teacher exam management permissions restored\n";
        }
    }
};
