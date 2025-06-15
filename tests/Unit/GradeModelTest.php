<?php

namespace Tests\Unit;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class GradeModelTest
 * 
 * Unit tests for the Grade model.
 * 
 * @package Tests\Unit
 */
class GradeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test grade creation with valid data.
     *
     * @return void
     */
    public function test_grade_can_be_created_with_valid_data()
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);
        $exam = Exam::factory()->create();
        $academicYear = AcademicYear::factory()->create();
        $grader = User::factory()->create();

        $grade = Grade::create([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'exam_id' => $exam->id,
            'academic_year_id' => $academicYear->id,
            'semester' => Grade::SEMESTER_FIRST,
            'grade_type' => Grade::TYPE_EXAM,
            'score' => 85.5,
            'max_score' => 100,
            'letter_grade' => Grade::GRADE_A,
            'grade_point' => 5.0,
            'remarks' => 'Excellent performance',
            'graded_by' => $grader->id,
            'graded_at' => now(),
        ]);

        $this->assertInstanceOf(Grade::class, $grade);
        $this->assertEquals(85.5, $grade->score);
        $this->assertEquals(Grade::GRADE_A, $grade->letter_grade);
        $this->assertEquals(5.0, $grade->grade_point);
    }

    /**
     * Test grade relationships.
     *
     * @return void
     */
    public function test_grade_relationships()
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);
        $exam = Exam::factory()->create();
        $academicYear = AcademicYear::factory()->create();
        $grader = User::factory()->create();

        $grade = Grade::factory()->create([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'exam_id' => $exam->id,
            'academic_year_id' => $academicYear->id,
            'graded_by' => $grader->id,
        ]);

        // Test student relationship
        $this->assertInstanceOf(Student::class, $grade->student);
        $this->assertEquals($student->id, $grade->student->id);

        // Test enrollment relationship
        $this->assertInstanceOf(Enrollment::class, $grade->enrollment);
        $this->assertEquals($enrollment->id, $grade->enrollment->id);

        // Test exam relationship
        $this->assertInstanceOf(Exam::class, $grade->exam);
        $this->assertEquals($exam->id, $grade->exam->id);

        // Test academic year relationship
        $this->assertInstanceOf(AcademicYear::class, $grade->academicYear);
        $this->assertEquals($academicYear->id, $grade->academicYear->id);

        // Test grader relationship
        $this->assertInstanceOf(User::class, $grade->grader);
        $this->assertEquals($grader->id, $grade->grader->id);
    }

    /**
     * Test grade scopes.
     *
     * @return void
     */
    public function test_grade_scopes()
    {
        // Create grades with different types and semesters
        $caGrade = Grade::factory()->create(['grade_type' => Grade::TYPE_CA]);
        $examGrade = Grade::factory()->create(['grade_type' => Grade::TYPE_EXAM]);
        $finalGrade = Grade::factory()->create(['grade_type' => Grade::TYPE_FINAL]);
        $firstSemesterGrade = Grade::factory()->create(['semester' => Grade::SEMESTER_FIRST]);
        $secondSemesterGrade = Grade::factory()->create(['semester' => Grade::SEMESTER_SECOND]);

        // Test ofType scope
        $caGrades = Grade::ofType(Grade::TYPE_CA)->get();
        $this->assertCount(1, $caGrades);
        $this->assertEquals($caGrade->id, $caGrades->first()->id);

        $examGrades = Grade::ofType(Grade::TYPE_EXAM)->get();
        $this->assertCount(1, $examGrades);
        $this->assertEquals($examGrade->id, $examGrades->first()->id);

        // Test forSemester scope
        $firstSemesterGrades = Grade::forSemester(Grade::SEMESTER_FIRST)->get();
        $this->assertTrue($firstSemesterGrades->contains($firstSemesterGrade));

        $secondSemesterGrades = Grade::forSemester(Grade::SEMESTER_SECOND)->get();
        $this->assertTrue($secondSemesterGrades->contains($secondSemesterGrade));
    }

    /**
     * Test calculate letter grade method.
     *
     * @return void
     */
    public function test_calculate_letter_grade_method()
    {
        $grade = new Grade();
        $grade->max_score = 100;

        // Test A grade (80-100%)
        $grade->score = 85;
        $grade->calculateLetterGrade();
        $this->assertEquals(Grade::GRADE_A, $grade->letter_grade);
        $this->assertEquals(5.0, $grade->grade_point);

        // Test B grade (70-79%)
        $grade->score = 75;
        $grade->calculateLetterGrade();
        $this->assertEquals(Grade::GRADE_B, $grade->letter_grade);
        $this->assertEquals(4.0, $grade->grade_point);

        // Test C grade (60-69%)
        $grade->score = 65;
        $grade->calculateLetterGrade();
        $this->assertEquals(Grade::GRADE_C, $grade->letter_grade);
        $this->assertEquals(3.0, $grade->grade_point);

        // Test D grade (50-59%)
        $grade->score = 55;
        $grade->calculateLetterGrade();
        $this->assertEquals(Grade::GRADE_D, $grade->letter_grade);
        $this->assertEquals(2.0, $grade->grade_point);

        // Test E grade (40-49%)
        $grade->score = 45;
        $grade->calculateLetterGrade();
        $this->assertEquals(Grade::GRADE_E, $grade->letter_grade);
        $this->assertEquals(1.0, $grade->grade_point);

        // Test F grade (0-39%)
        $grade->score = 35;
        $grade->calculateLetterGrade();
        $this->assertEquals(Grade::GRADE_F, $grade->letter_grade);
        $this->assertEquals(0.0, $grade->grade_point);
    }

    /**
     * Test get percentage method.
     *
     * @return void
     */
    public function test_get_percentage_method()
    {
        $grade = Grade::factory()->create([
            'score' => 85,
            'max_score' => 100
        ]);

        $this->assertEquals(85.0, $grade->getPercentage());

        $grade2 = Grade::factory()->create([
            'score' => 42.5,
            'max_score' => 50
        ]);

        $this->assertEquals(85.0, $grade2->getPercentage());
    }

    /**
     * Test is passing method.
     *
     * @return void
     */
    public function test_is_passing_method()
    {
        $passingGrade = Grade::factory()->create(['letter_grade' => Grade::GRADE_A]);
        $failingGrade = Grade::factory()->create(['letter_grade' => Grade::GRADE_F]);

        $this->assertTrue($passingGrade->isPassing());
        $this->assertFalse($failingGrade->isPassing());
    }

    /**
     * Test get status method.
     *
     * @return void
     */
    public function test_get_status_method()
    {
        $passingGrade = Grade::factory()->create(['letter_grade' => Grade::GRADE_B]);
        $failingGrade = Grade::factory()->create(['letter_grade' => Grade::GRADE_F]);

        $this->assertEquals('Pass', $passingGrade->getStatus());
        $this->assertEquals('Fail', $failingGrade->getStatus());
    }

    /**
     * Test grade types static method.
     *
     * @return void
     */
    public function test_grade_types_static_method()
    {
        $gradeTypes = Grade::getGradeTypes();

        $this->assertIsArray($gradeTypes);
        $this->assertArrayHasKey(Grade::TYPE_CA, $gradeTypes);
        $this->assertArrayHasKey(Grade::TYPE_EXAM, $gradeTypes);
        $this->assertArrayHasKey(Grade::TYPE_FINAL, $gradeTypes);
        $this->assertEquals('Continuous Assessment', $gradeTypes[Grade::TYPE_CA]);
        $this->assertEquals('Exam', $gradeTypes[Grade::TYPE_EXAM]);
        $this->assertEquals('Final Grade', $gradeTypes[Grade::TYPE_FINAL]);
    }

    /**
     * Test letter grades static method.
     *
     * @return void
     */
    public function test_letter_grades_static_method()
    {
        $letterGrades = Grade::getLetterGrades();

        $this->assertIsArray($letterGrades);
        $this->assertArrayHasKey(Grade::GRADE_A, $letterGrades);
        $this->assertArrayHasKey(Grade::GRADE_B, $letterGrades);
        $this->assertArrayHasKey(Grade::GRADE_C, $letterGrades);
        $this->assertArrayHasKey(Grade::GRADE_D, $letterGrades);
        $this->assertArrayHasKey(Grade::GRADE_E, $letterGrades);
        $this->assertArrayHasKey(Grade::GRADE_F, $letterGrades);
    }

    /**
     * Test get grade point static method.
     *
     * @return void
     */
    public function test_get_grade_point_static_method()
    {
        $this->assertEquals(5.0, Grade::getGradePoint(Grade::GRADE_A));
        $this->assertEquals(4.0, Grade::getGradePoint(Grade::GRADE_B));
        $this->assertEquals(3.0, Grade::getGradePoint(Grade::GRADE_C));
        $this->assertEquals(2.0, Grade::getGradePoint(Grade::GRADE_D));
        $this->assertEquals(1.0, Grade::getGradePoint(Grade::GRADE_E));
        $this->assertEquals(0.0, Grade::getGradePoint(Grade::GRADE_F));
        $this->assertEquals(0.0, Grade::getGradePoint('INVALID'));
    }

    /**
     * Test grade casts.
     *
     * @return void
     */
    public function test_grade_casts()
    {
        $grade = Grade::factory()->create([
            'score' => 85.50,
            'max_score' => 100.00,
            'grade_point' => 5.00,
            'graded_at' => '2024-12-25 10:00:00'
        ]);

        $this->assertIsFloat($grade->score);
        $this->assertIsFloat($grade->max_score);
        $this->assertIsFloat($grade->grade_point);
        $this->assertInstanceOf(\Carbon\Carbon::class, $grade->graded_at);
    }

    /**
     * Test grade fillable attributes.
     *
     * @return void
     */
    public function test_grade_fillable_attributes()
    {
        $fillable = [
            'student_id', 'enrollment_id', 'exam_id', 'academic_year_id',
            'semester', 'grade_type', 'score', 'max_score', 'letter_grade',
            'grade_point', 'remarks', 'graded_by', 'graded_at'
        ];

        $grade = new Grade();
        $this->assertEquals($fillable, $grade->getFillable());
    }

    /**
     * Test grade constants.
     *
     * @return void
     */
    public function test_grade_constants()
    {
        // Test grade type constants
        $this->assertEquals('ca', Grade::TYPE_CA);
        $this->assertEquals('exam', Grade::TYPE_EXAM);
        $this->assertEquals('final', Grade::TYPE_FINAL);

        // Test semester constants
        $this->assertEquals('first', Grade::SEMESTER_FIRST);
        $this->assertEquals('second', Grade::SEMESTER_SECOND);

        // Test letter grade constants
        $this->assertEquals('A', Grade::GRADE_A);
        $this->assertEquals('B', Grade::GRADE_B);
        $this->assertEquals('C', Grade::GRADE_C);
        $this->assertEquals('D', Grade::GRADE_D);
        $this->assertEquals('E', Grade::GRADE_E);
        $this->assertEquals('F', Grade::GRADE_F);
    }
}
