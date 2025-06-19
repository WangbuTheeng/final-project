<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions relevant to a college CMS
        $permissions = [
            // User Management
            'view-users', 'create-users', 'edit-users', 'delete-users',
            // Role & Permission Management
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
            'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions',
            'assign-roles-to-users', 'assign-permissions-to-roles',
            // Academic Management
            'view-courses', 'create-courses', 'edit-courses', 'delete-courses', 'manage-courses',
            'view-students', 'create-students', 'edit-students', 'delete-students', 'manage-students',
            'manage-classes',
            // Enrollment Management
            'view-enrollments', 'manage-enrollments', 'create-enrollments', 'drop-enrollments',
            // Examination Management
            'view-exams', 'create-exams', 'edit-exams', 'delete-exams', 'manage-exams',
            // Grade Management
            'view-grades', 'create-grades', 'edit-grades', 'delete-grades', 'manage-grades',
            // Financial Management
            'view-finances', 'manage-fees', 'create-invoices', 'manage-invoices',
            'create-payments', 'verify-payments', 'manage-payments', 'manage-salaries',
            'view-financial-reports', 'manage-expenses', 'approve-expenses',
            // Teaching Management
            'view-classes', 'create-classes', 'edit-classes', 'assign-homework',
            'grade-assignments', 'view-student-progress',
            // Reports
            'view-reports', 'generate-reports', 'export-reports',
            // Settings/Dashboard
            'access-admin-dashboard', 'access-teacher-dashboard', 'access-examiner-dashboard',
            'access-accountant-dashboard', 'manage-settings',
            // Faculty and Department Management
            'view-faculties', 'manage-faculties',
            // New permission for subjects
            'view-subjects',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Ensure permissions are reloaded after creation
        $allPermissions = Permission::all();

        // 2. Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $examinerRole = Role::firstOrCreate(['name' => 'Examiner']);
        $accountantRole = Role::firstOrCreate(['name' => 'Accountant']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);

        // 3. Assign Permissions to Roles

        // Super Admin gets all permissions
        $superAdminRole->givePermissionTo($allPermissions);

        // Admin gets most permissions except user management, activity logs, and financial management
        $adminPermissions = [
            // Academic Management
            'view-courses', 'create-courses', 'edit-courses', 'delete-courses', 'manage-courses',
            'view-students', 'create-students', 'edit-students', 'delete-students', 'manage-students',
            'view-enrollments', 'manage-enrollments', 'create-enrollments', 'drop-enrollments',
            'view-exams', 'create-exams', 'edit-exams', 'delete-exams', 'manage-exams',
            'view-grades', 'create-grades', 'edit-grades', 'delete-grades', 'manage-grades',
            'view-classes', 'create-classes', 'edit-classes', 'manage-classes',
            'view-subjects',
            // Faculty and Department Management
            'view-faculties', 'manage-faculties',
            // Reports and Settings
            'view-reports', 'generate-reports', 'export-reports',
            'access-admin-dashboard', 'manage-settings',
        ];
        $adminRole->givePermissionTo($adminPermissions);

        // Examiner role permissions - Only exam management + limited dashboard
        $examinerRole->givePermissionTo([
            'view-exams', 'create-exams', 'edit-exams', 'delete-exams', 'manage-exams',
            'view-grades', 'create-grades', 'edit-grades', 'delete-grades', 'manage-grades',
            'view-students', 'view-courses', 'view-subjects', // Needed to see students/courses for exams
            'view-reports', 'generate-reports', // For exam reports
            'access-examiner-dashboard',
        ]);

        // Accountant role permissions - Only financial management + limited dashboard
        $accountantRole->givePermissionTo([
            'view-finances', 'manage-fees', 'create-invoices', 'manage-invoices',
            'create-payments', 'verify-payments', 'manage-payments', 'manage-salaries',
            'view-financial-reports', 'manage-expenses', 'approve-expenses',
            'view-students', // Needed for financial operations
            'access-accountant-dashboard',
        ]);

        // Teacher role permissions
        $teacherRole->givePermissionTo([
            'view-courses',
            'view-classes',
            'view-subjects',
            'view-students',
            'view-exams',
            'view-grades',
            'view-reports',
            'view-faculties', // For viewing faculties only
            'access-teacher-dashboard', // Keep dashboard access
        ]);

        // 4. Create test users for each role
        $this->createTestUsers();
    }

    private function createTestUsers(): void
    {
        // Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        // Examiner user
        $examiner = User::firstOrCreate(
            ['email' => 'examiner@example.com'],
            [
                'name' => 'Examiner User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $examiner->assignRole('Examiner');

        // Accountant user
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@example.com'],
            [
                'name' => 'Accountant User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $accountant->assignRole('Accountant');

        // Teacher user
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Teacher User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $teacher->assignRole('Teacher');
    }
}
