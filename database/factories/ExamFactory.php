<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $examTypes = array_keys(Exam::getExamTypes());
        $semesters = array_keys(Exam::getSemesters());
        $statuses = array_keys(Exam::getStatuses());

        return [
            'title' => $this->faker->sentence(3),
            'class_id' => ClassSection::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'exam_type' => $this->faker->randomElement($examTypes),
            'semester' => $this->faker->randomElement($semesters),
            'exam_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'duration_minutes' => $this->faker->randomElement([60, 90, 120, 150, 180]),
            'total_marks' => $this->faker->randomElement([50, 75, 100, 150, 200]),
            'pass_mark' => function (array $attributes) {
                return $attributes['total_marks'] * 0.4; // 40% pass mark
            },
            'venue' => $this->faker->optional()->randomElement([
                'Room 101', 'Room 102', 'Hall A', 'Hall B', 'Lab 1', 'Lab 2'
            ]),
            'instructions' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the exam is scheduled.
     *
     * @return static
     */
    public function scheduled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Exam::STATUS_SCHEDULED,
                'exam_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            ];
        });
    }

    /**
     * Indicate that the exam is ongoing.
     *
     * @return static
     */
    public function ongoing(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Exam::STATUS_ONGOING,
                'exam_date' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            ];
        });
    }

    /**
     * Indicate that the exam is completed.
     *
     * @return static
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Exam::STATUS_COMPLETED,
                'exam_date' => $this->faker->dateTimeBetween('-3 months', '-1 week'),
            ];
        });
    }

    /**
     * Indicate that the exam is cancelled.
     *
     * @return static
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Exam::STATUS_CANCELLED,
            ];
        });
    }

    /**
     * Indicate that the exam is a quiz.
     *
     * @return static
     */
    public function quiz(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'exam_type' => Exam::TYPE_QUIZ,
                'duration_minutes' => $this->faker->randomElement([30, 45, 60]),
                'total_marks' => $this->faker->randomElement([20, 25, 30, 50]),
            ];
        });
    }

    /**
     * Indicate that the exam is a test.
     *
     * @return static
     */
    public function test(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'exam_type' => Exam::TYPE_TEST,
                'duration_minutes' => $this->faker->randomElement([60, 90, 120]),
                'total_marks' => $this->faker->randomElement([50, 75, 100]),
            ];
        });
    }

    /**
     * Indicate that the exam is a midterm.
     *
     * @return static
     */
    public function midterm(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'exam_type' => Exam::TYPE_MIDTERM,
                'duration_minutes' => $this->faker->randomElement([120, 150, 180]),
                'total_marks' => $this->faker->randomElement([100, 150, 200]),
            ];
        });
    }

    /**
     * Indicate that the exam is a final exam.
     *
     * @return static
     */
    public function final(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'exam_type' => Exam::TYPE_FINAL,
                'duration_minutes' => $this->faker->randomElement([180, 210, 240]),
                'total_marks' => $this->faker->randomElement([150, 200, 250]),
            ];
        });
    }

    /**
     * Indicate that the exam is a practical.
     *
     * @return static
     */
    public function practical(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'exam_type' => Exam::TYPE_PRACTICAL,
                'duration_minutes' => $this->faker->randomElement([120, 180, 240]),
                'total_marks' => $this->faker->randomElement([50, 75, 100]),
                'venue' => $this->faker->randomElement(['Lab 1', 'Lab 2', 'Computer Lab', 'Science Lab']),
            ];
        });
    }

    /**
     * Indicate that the exam is an assignment.
     *
     * @return static
     */
    public function assignment(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'exam_type' => Exam::TYPE_ASSIGNMENT,
                'duration_minutes' => null, // Assignments don't have time limits
                'total_marks' => $this->faker->randomElement([25, 50, 75, 100]),
                'venue' => null, // Assignments can be done anywhere
            ];
        });
    }

    /**
     * Indicate that the exam is for first semester.
     *
     * @return static
     */
    public function firstSemester(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'semester' => Exam::SEMESTER_FIRST,
            ];
        });
    }

    /**
     * Indicate that the exam is for second semester.
     *
     * @return static
     */
    public function secondSemester(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'semester' => Exam::SEMESTER_SECOND,
            ];
        });
    }

    /**
     * Indicate that the exam is upcoming.
     *
     * @return static
     */
    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Exam::STATUS_SCHEDULED,
                'exam_date' => $this->faker->dateTimeBetween('+1 day', '+2 weeks'),
            ];
        });
    }

    /**
     * Indicate that the exam has high marks.
     *
     * @return static
     */
    public function highMarks(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'total_marks' => $this->faker->randomElement([200, 250, 300]),
                'pass_mark' => function (array $attributes) {
                    return $attributes['total_marks'] * 0.5; // 50% pass mark for high marks exams
                },
            ];
        });
    }

    /**
     * Indicate that the exam has detailed instructions.
     *
     * @return static
     */
    public function withInstructions(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'instructions' => $this->faker->paragraphs(3, true),
            ];
        });
    }
}
