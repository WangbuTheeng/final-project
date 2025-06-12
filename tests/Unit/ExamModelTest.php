<?php

namespace Tests\Unit;

use App\Models\Exam;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ExamModelTest
 * 
 * Unit tests for the Exam model.
 * 
 * @package Tests\Unit
 */
class ExamModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test exam creation with valid data.
     *
     * @return void
     */
    public function test_exam_can_be_created_with_valid_data()
    {
        $classSection = ClassSection::factory()->create();
        $academicYear = AcademicYear::factory()->create();
        $user = User::factory()->create();

        $exam = Exam::create([
            'title' => 'Test Exam',
            'class_id' => $classSection->id,
            'academic_year_id' => $academicYear->id,
            'exam_type' => Exam::TYPE_TEST,
            'semester' => Exam::SEMESTER_FIRST,
            'exam_date' => now()->addDays(7),
            'duration_minutes' => 120,
            'total_marks' => 100,
            'pass_mark' => 40,
            'venue' => 'Room 101',
            'instructions' => 'Test instructions',
            'status' => Exam::STATUS_SCHEDULED,
            'created_by' => $user->id,
        ]);

        $this->assertInstanceOf(Exam::class, $exam);
        $this->assertEquals('Test Exam', $exam->title);
        $this->assertEquals(Exam::TYPE_TEST, $exam->exam_type);
        $this->assertEquals(Exam::STATUS_SCHEDULED, $exam->status);
    }

    /**
     * Test exam relationships.
     *
     * @return void
     */
    public function test_exam_relationships()
    {
        $classSection = ClassSection::factory()->create();
        $academicYear = AcademicYear::factory()->create();
        $user = User::factory()->create();

        $exam = Exam::factory()->create([
            'class_id' => $classSection->id,
            'academic_year_id' => $academicYear->id,
            'created_by' => $user->id,
        ]);

        // Test class section relationship
        $this->assertInstanceOf(ClassSection::class, $exam->classSection);
        $this->assertEquals($classSection->id, $exam->classSection->id);

        // Test academic year relationship
        $this->assertInstanceOf(AcademicYear::class, $exam->academicYear);
        $this->assertEquals($academicYear->id, $exam->academicYear->id);

        // Test creator relationship
        $this->assertInstanceOf(User::class, $exam->creator);
        $this->assertEquals($user->id, $exam->creator->id);
    }

    /**
     * Test exam grades relationship.
     *
     * @return void
     */
    public function test_exam_grades_relationship()
    {
        $exam = Exam::factory()->create();
        $grades = Grade::factory()->count(3)->create(['exam_id' => $exam->id]);

        $this->assertCount(3, $exam->grades);
        $this->assertInstanceOf(Grade::class, $exam->grades->first());
    }

    /**
     * Test exam scopes.
     *
     * @return void
     */
    public function test_exam_scopes()
    {
        // Create exams with different types and statuses
        $quizExam = Exam::factory()->create(['exam_type' => Exam::TYPE_QUIZ]);
        $testExam = Exam::factory()->create(['exam_type' => Exam::TYPE_TEST]);
        $scheduledExam = Exam::factory()->create(['status' => Exam::STATUS_SCHEDULED]);
        $completedExam = Exam::factory()->create(['status' => Exam::STATUS_COMPLETED]);
        $firstSemesterExam = Exam::factory()->create(['semester' => Exam::SEMESTER_FIRST]);
        $secondSemesterExam = Exam::factory()->create(['semester' => Exam::SEMESTER_SECOND]);

        // Test ofType scope
        $quizExams = Exam::ofType(Exam::TYPE_QUIZ)->get();
        $this->assertCount(1, $quizExams);
        $this->assertEquals($quizExam->id, $quizExams->first()->id);

        // Test withStatus scope
        $scheduledExams = Exam::withStatus(Exam::STATUS_SCHEDULED)->get();
        $this->assertTrue($scheduledExams->contains($scheduledExam));

        // Test forSemester scope
        $firstSemesterExams = Exam::forSemester(Exam::SEMESTER_FIRST)->get();
        $this->assertTrue($firstSemesterExams->contains($firstSemesterExam));
    }

    /**
     * Test upcoming exams scope.
     *
     * @return void
     */
    public function test_upcoming_exams_scope()
    {
        // Create past and future exams
        $pastExam = Exam::factory()->create([
            'exam_date' => now()->subDays(7),
            'status' => Exam::STATUS_COMPLETED
        ]);

        $futureExam = Exam::factory()->create([
            'exam_date' => now()->addDays(7),
            'status' => Exam::STATUS_SCHEDULED
        ]);

        $upcomingExams = Exam::upcoming()->get();

        $this->assertCount(1, $upcomingExams);
        $this->assertEquals($futureExam->id, $upcomingExams->first()->id);
    }

    /**
     * Test exam status methods.
     *
     * @return void
     */
    public function test_exam_status_methods()
    {
        $scheduledExam = Exam::factory()->create(['status' => Exam::STATUS_SCHEDULED]);
        $ongoingExam = Exam::factory()->create(['status' => Exam::STATUS_ONGOING]);
        $completedExam = Exam::factory()->create(['status' => Exam::STATUS_COMPLETED]);
        $cancelledExam = Exam::factory()->create(['status' => Exam::STATUS_CANCELLED]);

        // Test isScheduled method
        $this->assertTrue($scheduledExam->isScheduled());
        $this->assertFalse($ongoingExam->isScheduled());

        // Test isOngoing method
        $this->assertTrue($ongoingExam->isOngoing());
        $this->assertFalse($scheduledExam->isOngoing());

        // Test isCompleted method
        $this->assertTrue($completedExam->isCompleted());
        $this->assertFalse($scheduledExam->isCompleted());

        // Test isCancelled method
        $this->assertTrue($cancelledExam->isCancelled());
        $this->assertFalse($scheduledExam->isCancelled());
    }

    /**
     * Test formatted duration method.
     *
     * @return void
     */
    public function test_formatted_duration_method()
    {
        $exam1 = Exam::factory()->create(['duration_minutes' => 60]);
        $exam2 = Exam::factory()->create(['duration_minutes' => 90]);
        $exam3 = Exam::factory()->create(['duration_minutes' => 30]);

        $this->assertEquals('1h 0m', $exam1->getFormattedDuration());
        $this->assertEquals('1h 30m', $exam2->getFormattedDuration());
        $this->assertEquals('30m', $exam3->getFormattedDuration());
    }

    /**
     * Test pass percentage method.
     *
     * @return void
     */
    public function test_pass_percentage_method()
    {
        $exam = Exam::factory()->create([
            'total_marks' => 100,
            'pass_mark' => 40
        ]);

        $this->assertEquals(40.0, $exam->getPassPercentage());
    }

    /**
     * Test exam types static method.
     *
     * @return void
     */
    public function test_exam_types_static_method()
    {
        $examTypes = Exam::getExamTypes();

        $this->assertIsArray($examTypes);
        $this->assertArrayHasKey(Exam::TYPE_QUIZ, $examTypes);
        $this->assertArrayHasKey(Exam::TYPE_TEST, $examTypes);
        $this->assertArrayHasKey(Exam::TYPE_MIDTERM, $examTypes);
        $this->assertArrayHasKey(Exam::TYPE_FINAL, $examTypes);
        $this->assertArrayHasKey(Exam::TYPE_PRACTICAL, $examTypes);
        $this->assertArrayHasKey(Exam::TYPE_ASSIGNMENT, $examTypes);
    }

    /**
     * Test exam statuses static method.
     *
     * @return void
     */
    public function test_exam_statuses_static_method()
    {
        $statuses = Exam::getStatuses();

        $this->assertIsArray($statuses);
        $this->assertArrayHasKey(Exam::STATUS_SCHEDULED, $statuses);
        $this->assertArrayHasKey(Exam::STATUS_ONGOING, $statuses);
        $this->assertArrayHasKey(Exam::STATUS_COMPLETED, $statuses);
        $this->assertArrayHasKey(Exam::STATUS_CANCELLED, $statuses);
    }

    /**
     * Test exam semesters static method.
     *
     * @return void
     */
    public function test_exam_semesters_static_method()
    {
        $semesters = Exam::getSemesters();

        $this->assertIsArray($semesters);
        $this->assertArrayHasKey(Exam::SEMESTER_FIRST, $semesters);
        $this->assertArrayHasKey(Exam::SEMESTER_SECOND, $semesters);
    }

    /**
     * Test exam casts.
     *
     * @return void
     */
    public function test_exam_casts()
    {
        $exam = Exam::factory()->create([
            'exam_date' => '2024-12-25 10:00:00',
            'total_marks' => 100.50,
            'pass_mark' => 40.25,
            'duration_minutes' => 120
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $exam->exam_date);
        $this->assertIsFloat($exam->total_marks);
        $this->assertIsFloat($exam->pass_mark);
        $this->assertIsInt($exam->duration_minutes);
    }

    /**
     * Test exam fillable attributes.
     *
     * @return void
     */
    public function test_exam_fillable_attributes()
    {
        $fillable = [
            'title', 'class_id', 'academic_year_id', 'exam_type', 'semester',
            'exam_date', 'duration_minutes', 'total_marks', 'pass_mark',
            'venue', 'instructions', 'status', 'created_by'
        ];

        $exam = new Exam();
        $this->assertEquals($fillable, $exam->getFillable());
    }
}
