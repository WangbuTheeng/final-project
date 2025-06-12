<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Class StudentControllerTest
 * 
 * Test cases for StudentController functionality.
 * 
 * @package Tests\Feature
 */
class StudentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Department $department;
    protected AcademicYear $academicYear;

    /**
     * Set up test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'view-students']);
        Permission::create(['name' => 'create-students']);
        Permission::create(['name' => 'edit-students']);
        Permission::create(['name' => 'delete-students']);

        // Create role with permissions
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo(['view-students', 'create-students', 'edit-students', 'delete-students']);

        // Create test user
        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        // Create test data
        $faculty = Faculty::factory()->create();
        $this->department = Department::factory()->create(['faculty_id' => $faculty->id]);
        $this->academicYear = AcademicYear::factory()->create(['is_current' => true]);
    }

    /**
     * Test student index page.
     *
     * @return void
     */
    public function test_student_index_displays_students()
    {
        // Create test students
        $students = Student::factory()->count(3)->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id
        ]);

        $response = $this->actingAs($this->user)->get(route('students.index'));

        $response->assertStatus(200);
        $response->assertViewIs('students.index');
        $response->assertViewHas('students');
        
        foreach ($students as $student) {
            $response->assertSee($student->matric_number);
        }
    }

    /**
     * Test student index with filters.
     *
     * @return void
     */
    public function test_student_index_with_filters()
    {
        // Create students with different levels
        $level100Student = Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id,
            'current_level' => 100
        ]);

        $level200Student = Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id,
            'current_level' => 200
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('students.index', ['level' => 100]));

        $response->assertStatus(200);
        $response->assertSee($level100Student->matric_number);
        $response->assertDontSee($level200Student->matric_number);
    }

    /**
     * Test student search functionality.
     *
     * @return void
     */
    public function test_student_search_functionality()
    {
        $student = Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id,
            'matric_number' => 'TEST123456'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('students.index', ['search' => 'TEST123']));

        $response->assertStatus(200);
        $response->assertSee($student->matric_number);
    }

    /**
     * Test student create page.
     *
     * @return void
     */
    public function test_student_create_page_displays()
    {
        $response = $this->actingAs($this->user)->get(route('students.create'));

        $response->assertStatus(200);
        $response->assertViewIs('students.create');
        $response->assertViewHas(['departments', 'academicYears']);
    }

    /**
     * Test student creation.
     *
     * @return void
     */
    public function test_student_can_be_created()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'address' => '123 Test Street'
        ];

        $studentData = [
            'matric_number' => 'STU123456',
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id,
            'current_level' => 100,
            'mode_of_entry' => 'utme',
            'study_mode' => 'full_time',
            'guardian_name' => 'Jane Doe',
            'guardian_phone' => '0987654321',
            'guardian_email' => 'jane.doe@example.com',
            'guardian_relationship' => 'Mother'
        ];

        $formData = array_merge($userData, $studentData);

        $response = $this->actingAs($this->user)
            ->post(route('students.store'), $formData);

        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $this->assertDatabaseHas('students', [
            'matric_number' => 'STU123456',
            'department_id' => $this->department->id
        ]);
    }

    /**
     * Test student creation validation.
     *
     * @return void
     */
    public function test_student_creation_validation()
    {
        $response = $this->actingAs($this->user)
            ->post(route('students.store'), []);

        $response->assertSessionHasErrors([
            'first_name', 'last_name', 'email', 'date_of_birth', 'gender',
            'matric_number', 'department_id', 'academic_year_id', 'current_level',
            'mode_of_entry', 'study_mode'
        ]);
    }

    /**
     * Test duplicate matric number validation.
     *
     * @return void
     */
    public function test_duplicate_matric_number_validation()
    {
        $existingStudent = Student::factory()->create([
            'matric_number' => 'DUPLICATE123',
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id
        ]);

        $formData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'matric_number' => 'DUPLICATE123',
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id,
            'current_level' => 100,
            'mode_of_entry' => 'utme',
            'study_mode' => 'full_time'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('students.store'), $formData);

        $response->assertSessionHasErrors(['matric_number']);
    }

    /**
     * Test student show page.
     *
     * @return void
     */
    public function test_student_show_displays_student_details()
    {
        $student = Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('students.show', $student));

        $response->assertStatus(200);
        $response->assertViewIs('students.show');
        $response->assertViewHas('student');
        $response->assertSee($student->matric_number);
    }

    /**
     * Test student edit page.
     *
     * @return void
     */
    public function test_student_edit_page_displays()
    {
        $student = Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('students.edit', $student));

        $response->assertStatus(200);
        $response->assertViewIs('students.edit');
        $response->assertViewHas('student');
    }

    /**
     * Test student update.
     *
     * @return void
     */
    public function test_student_can_be_updated()
    {
        $student = Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id
        ]);

        $updateData = [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $student->user->email, // Keep same email
            'phone' => '9876543210',
            'date_of_birth' => $student->user->date_of_birth->format('Y-m-d'),
            'gender' => $student->user->gender,
            'address' => 'Updated Address',
            'matric_number' => $student->matric_number, // Keep same matric number
            'department_id' => $this->department->id,
            'current_level' => 200,
            'mode_of_entry' => $student->mode_of_entry,
            'study_mode' => $student->study_mode,
            'status' => 'active'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('students.update', $student), $updateData);

        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->user_id,
            'first_name' => 'Updated',
            'last_name' => 'Name'
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'current_level' => 200
        ]);
    }

    /**
     * Test student deletion.
     *
     * @return void
     */
    public function test_student_can_be_deleted()
    {
        $student = Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id
        ]);

        $userId = $student->user_id;

        $response = $this->actingAs($this->user)
            ->delete(route('students.destroy', $student));

        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('students', ['id' => $student->id]);
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    /**
     * Test unauthorized access.
     *
     * @return void
     */
    public function test_unauthorized_user_cannot_access_students()
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->get(route('students.index'));

        $response->assertStatus(403);
    }

    /**
     * Test student statistics calculation.
     *
     * @return void
     */
    public function test_student_statistics_calculation()
    {
        // Create students with different statuses
        Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id,
            'status' => 'active'
        ]);

        Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id,
            'status' => 'graduated'
        ]);

        Student::factory()->create([
            'department_id' => $this->department->id,
            'academic_year_id' => $this->academicYear->id,
            'status' => 'suspended'
        ]);

        $response = $this->actingAs($this->user)->get(route('students.index'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(1, $stats['active']);
        $this->assertEquals(1, $stats['graduated']);
        $this->assertEquals(1, $stats['suspended']);
    }
}
