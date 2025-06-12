<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\User;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Class ExamControllerTest
 * 
 * Test cases for ExamController functionality.
 * 
 * @package Tests\Feature
 */
class ExamControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected AcademicYear $academicYear;
    protected ClassSection $classSection;

    /**
     * Set up test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'view-exams']);
        Permission::create(['name' => 'create-exams']);
        Permission::create(['name' => 'edit-exams']);
        Permission::create(['name' => 'delete-exams']);

        // Create role with permissions
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo(['view-exams', 'create-exams', 'edit-exams', 'delete-exams']);

        // Create test user
        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        // Create test data
        $faculty = Faculty::factory()->create();
        $department = Department::factory()->create(['faculty_id' => $faculty->id]);
        $course = Course::factory()->create([
            'faculty_id' => $faculty->id,
            'department_id' => $department->id
        ]);
        $this->academicYear = AcademicYear::factory()->create(['is_current' => true]);
        $this->classSection = ClassSection::factory()->create([
            'course_id' => $course->id,
            'academic_year_id' => $this->academicYear->id
        ]);
    }

    /**
     * Test exam index page.
     *
     * @return void
     */
    public function test_exam_index_displays_exams()
    {
        // Create test exams
        $exams = Exam::factory()->count(3)->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)->get(route('exams.index'));

        $response->assertStatus(200);
        $response->assertViewIs('exams.index');
        $response->assertViewHas('exams');
        
        foreach ($exams as $exam) {
            $response->assertSee($exam->title);
        }
    }

    /**
     * Test exam index with filters.
     *
     * @return void
     */
    public function test_exam_index_with_filters()
    {
        // Create exams with different types
        $quizExam = Exam::factory()->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'exam_type' => 'quiz',
            'created_by' => $this->user->id
        ]);

        $finalExam = Exam::factory()->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'exam_type' => 'final',
            'created_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('exams.index', ['exam_type' => 'quiz']));

        $response->assertStatus(200);
        $response->assertSee($quizExam->title);
        $response->assertDontSee($finalExam->title);
    }

    /**
     * Test exam create page.
     *
     * @return void
     */
    public function test_exam_create_page_displays()
    {
        $response = $this->actingAs($this->user)->get(route('exams.create'));

        $response->assertStatus(200);
        $response->assertViewIs('exams.create');
        $response->assertViewHas(['examTypes', 'semesters', 'academicYears', 'classes']);
    }

    /**
     * Test exam creation.
     *
     * @return void
     */
    public function test_exam_can_be_created()
    {
        $examData = [
            'title' => 'Test Exam',
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'exam_type' => 'test',
            'semester' => 'first',
            'exam_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'duration_minutes' => 120,
            'total_marks' => 100,
            'pass_mark' => 40,
            'venue' => 'Room 101',
            'instructions' => 'Test instructions'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('exams.store'), $examData);

        $response->assertRedirect(route('exams.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('exams', [
            'title' => 'Test Exam',
            'class_id' => $this->classSection->id,
            'created_by' => $this->user->id
        ]);
    }

    /**
     * Test exam creation validation.
     *
     * @return void
     */
    public function test_exam_creation_validation()
    {
        $response = $this->actingAs($this->user)
            ->post(route('exams.store'), []);

        $response->assertSessionHasErrors([
            'title', 'class_id', 'academic_year_id', 'exam_type', 
            'semester', 'exam_date', 'duration_minutes', 'total_marks', 'pass_mark'
        ]);
    }

    /**
     * Test exam show page.
     *
     * @return void
     */
    public function test_exam_show_displays_exam_details()
    {
        $exam = Exam::factory()->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('exams.show', $exam));

        $response->assertStatus(200);
        $response->assertViewIs('exams.show');
        $response->assertViewHas('exam');
        $response->assertSee($exam->title);
    }

    /**
     * Test exam edit page.
     *
     * @return void
     */
    public function test_exam_edit_page_displays()
    {
        $exam = Exam::factory()->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('exams.edit', $exam));

        $response->assertStatus(200);
        $response->assertViewIs('exams.edit');
        $response->assertViewHas('exam');
    }

    /**
     * Test exam update.
     *
     * @return void
     */
    public function test_exam_can_be_updated()
    {
        $exam = Exam::factory()->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'created_by' => $this->user->id
        ]);

        $updateData = [
            'title' => 'Updated Exam Title',
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'exam_type' => 'final',
            'semester' => 'second',
            'exam_date' => now()->addDays(14)->format('Y-m-d H:i:s'),
            'duration_minutes' => 180,
            'total_marks' => 150,
            'pass_mark' => 60,
            'venue' => 'Updated Venue',
            'instructions' => 'Updated instructions',
            'status' => 'scheduled'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('exams.update', $exam), $updateData);

        $response->assertRedirect(route('exams.show', $exam));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'title' => 'Updated Exam Title',
            'exam_type' => 'final'
        ]);
    }

    /**
     * Test exam deletion.
     *
     * @return void
     */
    public function test_exam_can_be_deleted()
    {
        $exam = Exam::factory()->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('exams.destroy', $exam));

        $response->assertRedirect(route('exams.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('exams', ['id' => $exam->id]);
    }

    /**
     * Test unauthorized access.
     *
     * @return void
     */
    public function test_unauthorized_user_cannot_access_exams()
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->get(route('exams.index'));

        $response->assertStatus(403);
    }

    /**
     * Test exam start functionality.
     *
     * @return void
     */
    public function test_exam_can_be_started()
    {
        $exam = Exam::factory()->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'status' => 'scheduled',
            'created_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('exams.start', $exam));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'status' => 'ongoing'
        ]);
    }

    /**
     * Test exam completion functionality.
     *
     * @return void
     */
    public function test_exam_can_be_completed()
    {
        $exam = Exam::factory()->create([
            'class_id' => $this->classSection->id,
            'academic_year_id' => $this->academicYear->id,
            'status' => 'ongoing',
            'created_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('exams.complete', $exam));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'status' => 'completed'
        ]);
    }
}
