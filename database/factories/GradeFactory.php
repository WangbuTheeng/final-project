<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Grade::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gradeTypes = array_keys(Grade::getGradeTypes());
        $semesters = [Grade::SEMESTER_FIRST, Grade::SEMESTER_SECOND];
        $letterGrades = array_keys(Grade::getLetterGrades());
        
        $score = $this->faker->numberBetween(0, 100);
        $maxScore = 100;
        $letterGrade = $this->getLetterGradeFromScore($score);
        $gradePoint = Grade::getGradePoint($letterGrade);

        return [
            'student_id' => Student::factory(),
            'enrollment_id' => Enrollment::factory(),
            'exam_id' => Exam::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester' => $this->faker->randomElement($semesters),
            'grade_type' => $this->faker->randomElement($gradeTypes),
            'score' => $score,
            'max_score' => $maxScore,
            'letter_grade' => $letterGrade,
            'grade_point' => $gradePoint,
            'remarks' => $this->faker->optional(0.3)->sentence(),
            'graded_by' => User::factory(),
            'graded_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Get letter grade based on score percentage.
     *
     * @param float $score
     * @param float $maxScore
     * @return string
     */
    private function getLetterGradeFromScore(float $score, float $maxScore = 100): string
    {
        $percentage = ($score / $maxScore) * 100;
        
        if ($percentage >= 80) {
            return Grade::GRADE_A;
        } elseif ($percentage >= 70) {
            return Grade::GRADE_B;
        } elseif ($percentage >= 60) {
            return Grade::GRADE_C;
        } elseif ($percentage >= 50) {
            return Grade::GRADE_D;
        } elseif ($percentage >= 40) {
            return Grade::GRADE_E;
        } else {
            return Grade::GRADE_F;
        }
    }

    /**
     * Indicate that the grade is for continuous assessment.
     *
     * @return static
     */
    public function continuousAssessment(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'grade_type' => Grade::TYPE_CA,
                'exam_id' => null, // CA grades might not be tied to specific exams
                'max_score' => $this->faker->randomElement([30, 40, 50]),
            ];
        });
    }

    /**
     * Indicate that the grade is for an exam.
     *
     * @return static
     */
    public function examGrade(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'grade_type' => Grade::TYPE_EXAM,
                'max_score' => $this->faker->randomElement([60, 70, 100]),
            ];
        });
    }

    /**
     * Indicate that the grade is a final grade.
     *
     * @return static
     */
    public function finalGrade(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'grade_type' => Grade::TYPE_FINAL,
                'exam_id' => null, // Final grades are calculated, not from specific exams
                'max_score' => 100,
            ];
        });
    }

    /**
     * Indicate that the grade is excellent (A grade).
     *
     * @return static
     */
    public function excellent(): static
    {
        return $this->state(function (array $attributes) {
            $score = $this->faker->numberBetween(80, 100);
            return [
                'score' => $score,
                'letter_grade' => Grade::GRADE_A,
                'grade_point' => 5.0,
                'remarks' => $this->faker->randomElement([
                    'Excellent performance',
                    'Outstanding work',
                    'Exceptional understanding',
                    'Superb effort'
                ]),
            ];
        });
    }

    /**
     * Indicate that the grade is good (B grade).
     *
     * @return static
     */
    public function good(): static
    {
        return $this->state(function (array $attributes) {
            $score = $this->faker->numberBetween(70, 79);
            return [
                'score' => $score,
                'letter_grade' => Grade::GRADE_B,
                'grade_point' => 4.0,
                'remarks' => $this->faker->randomElement([
                    'Good performance',
                    'Well done',
                    'Good understanding',
                    'Solid work'
                ]),
            ];
        });
    }

    /**
     * Indicate that the grade is average (C grade).
     *
     * @return static
     */
    public function average(): static
    {
        return $this->state(function (array $attributes) {
            $score = $this->faker->numberBetween(60, 69);
            return [
                'score' => $score,
                'letter_grade' => Grade::GRADE_C,
                'grade_point' => 3.0,
                'remarks' => $this->faker->randomElement([
                    'Average performance',
                    'Satisfactory work',
                    'Adequate understanding',
                    'Room for improvement'
                ]),
            ];
        });
    }

    /**
     * Indicate that the grade is below average (D grade).
     *
     * @return static
     */
    public function belowAverage(): static
    {
        return $this->state(function (array $attributes) {
            $score = $this->faker->numberBetween(50, 59);
            return [
                'score' => $score,
                'letter_grade' => Grade::GRADE_D,
                'grade_point' => 2.0,
                'remarks' => $this->faker->randomElement([
                    'Below average performance',
                    'Needs improvement',
                    'Requires more effort',
                    'Study harder'
                ]),
            ];
        });
    }

    /**
     * Indicate that the grade is poor (E grade).
     *
     * @return static
     */
    public function poor(): static
    {
        return $this->state(function (array $attributes) {
            $score = $this->faker->numberBetween(40, 49);
            return [
                'score' => $score,
                'letter_grade' => Grade::GRADE_E,
                'grade_point' => 1.0,
                'remarks' => $this->faker->randomElement([
                    'Poor performance',
                    'Significant improvement needed',
                    'Barely passing',
                    'Extra help required'
                ]),
            ];
        });
    }

    /**
     * Indicate that the grade is failing (F grade).
     *
     * @return static
     */
    public function failing(): static
    {
        return $this->state(function (array $attributes) {
            $score = $this->faker->numberBetween(0, 39);
            return [
                'score' => $score,
                'letter_grade' => Grade::GRADE_F,
                'grade_point' => 0.0,
                'remarks' => $this->faker->randomElement([
                    'Failed',
                    'Unsatisfactory performance',
                    'Must retake',
                    'Serious improvement needed'
                ]),
            ];
        });
    }

    /**
     * Indicate that the grade is for first semester.
     *
     * @return static
     */
    public function firstSemester(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'semester' => Grade::SEMESTER_FIRST,
            ];
        });
    }

    /**
     * Indicate that the grade is for second semester.
     *
     * @return static
     */
    public function secondSemester(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'semester' => Grade::SEMESTER_SECOND,
            ];
        });
    }

    /**
     * Indicate that the grade has detailed remarks.
     *
     * @return static
     */
    public function withRemarks(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'remarks' => $this->faker->paragraph(),
            ];
        });
    }

    /**
     * Indicate that the grade is recent.
     *
     * @return static
     */
    public function recent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'graded_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }

    /**
     * Create a grade with a specific score.
     *
     * @param float $score
     * @param float $maxScore
     * @return static
     */
    public function withScore(float $score, float $maxScore = 100): static
    {
        return $this->state(function (array $attributes) use ($score, $maxScore) {
            $letterGrade = $this->getLetterGradeFromScore($score, $maxScore);
            $gradePoint = Grade::getGradePoint($letterGrade);
            
            return [
                'score' => $score,
                'max_score' => $maxScore,
                'letter_grade' => $letterGrade,
                'grade_point' => $gradePoint,
            ];
        });
    }
}
