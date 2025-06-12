<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\ClassSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class GradeService
 * 
 * Service class for handling grade-related business logic.
 * 
 * @package App\Services
 */
class GradeService
{
    /**
     * Get paginated grades with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedGrades(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Grade::with([
            'student.user',
            'exam',
            'enrollment.class.course',
            'academicYear',
            'grader'
        ]);

        // Apply filters
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['grade_type'])) {
            $query->where('grade_type', $filters['grade_type']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->whereHas('enrollment', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('student.user', function ($userQuery) use ($filters) {
                    $userQuery->where('first_name', 'like', '%' . $filters['search'] . '%')
                             ->orWhere('last_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('student', function ($studentQuery) use ($filters) {
                    $studentQuery->where('matric_number', 'like', '%' . $filters['search'] . '%');
                });
            });
        }

        return $query->orderBy('graded_at', 'desc')->paginate($perPage);
    }

    /**
     * Get students enrolled in an exam's class.
     *
     * @param Exam $exam
     * @return Collection
     */
    public function getExamStudents(Exam $exam): Collection
    {
        return Enrollment::with(['student.user'])
            ->where('class_id', $exam->class_id)
            ->where('academic_year_id', $exam->academic_year_id)
            ->where('semester', $exam->semester)
            ->where('status', 'enrolled')
            ->get()
            ->pluck('student');
    }

    /**
     * Create grades for an exam.
     *
     * @param Exam $exam
     * @param array $gradesData
     * @return Collection
     */
    public function createGradesForExam(Exam $exam, array $gradesData): Collection
    {
        return DB::transaction(function () use ($exam, $gradesData) {
            $grades = collect();

            foreach ($gradesData as $gradeData) {
                // Find the enrollment
                $enrollment = Enrollment::where('student_id', $gradeData['student_id'])
                    ->where('class_id', $exam->class_id)
                    ->where('academic_year_id', $exam->academic_year_id)
                    ->where('semester', $exam->semester)
                    ->first();

                if (!$enrollment) {
                    throw new \Exception("Student not enrolled in this class.");
                }

                // Create grade
                $grade = Grade::create(array_merge($gradeData, [
                    'enrollment_id' => $enrollment->id,
                ]));

                // Calculate letter grade and grade point
                $grade->calculateLetterGrade();
                $grade->save();

                $grades->push($grade);

                // Log the grade creation
                activity()
                    ->performedOn($grade)
                    ->causedBy(auth()->user())
                    ->log("Grade entered for {$grade->student->user->full_name} in {$exam->title}");
            }

            return $grades;
        });
    }

    /**
     * Get student results for a specific period.
     *
     * @param Student $student
     * @param int|null $academicYearId
     * @param string|null $semester
     * @return Collection
     */
    public function getStudentResults(Student $student, ?int $academicYearId = null, ?string $semester = null): Collection
    {
        $query = Grade::with([
            'exam',
            'enrollment.class.course',
            'academicYear'
        ])->where('student_id', $student->id);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->orderBy('academic_year_id', 'desc')
                    ->orderBy('semester', 'desc')
                    ->orderBy('graded_at', 'desc')
                    ->get();
    }

    /**
     * Get exam results with student information.
     *
     * @param Exam $exam
     * @return Collection
     */
    public function getExamResults(Exam $exam): Collection
    {
        return Grade::with(['student.user'])
            ->where('exam_id', $exam->id)
            ->orderBy('score', 'desc')
            ->get();
    }

    /**
     * Get exam statistics.
     *
     * @param Exam $exam
     * @return array
     */
    public function getExamStatistics(Exam $exam): array
    {
        $grades = Grade::where('exam_id', $exam->id);

        $stats = $grades->selectRaw('
            COUNT(*) as total_students,
            AVG(score) as average_score,
            MAX(score) as highest_score,
            MIN(score) as lowest_score,
            SUM(CASE WHEN letter_grade != "F" THEN 1 ELSE 0 END) as passed_count
        ')->first();

        $gradeDistribution = $grades->selectRaw('
            letter_grade,
            COUNT(*) as count
        ')->groupBy('letter_grade')->pluck('count', 'letter_grade')->toArray();

        return [
            'total_students' => $stats->total_students ?? 0,
            'average_score' => round($stats->average_score ?? 0, 2),
            'highest_score' => $stats->highest_score ?? 0,
            'lowest_score' => $stats->lowest_score ?? 0,
            'passed_count' => $stats->passed_count ?? 0,
            'failed_count' => ($stats->total_students ?? 0) - ($stats->passed_count ?? 0),
            'pass_rate' => $stats->total_students > 0 ? round(($stats->passed_count / $stats->total_students) * 100, 2) : 0,
            'grade_distribution' => $gradeDistribution,
        ];
    }

    /**
     * Calculate GPA for a student.
     *
     * @param Student $student
     * @param int|null $academicYearId
     * @param string|null $semester
     * @return float
     */
    public function calculateGPA(Student $student, ?int $academicYearId = null, ?string $semester = null): float
    {
        $query = Grade::join('enrollments', 'grades.enrollment_id', '=', 'enrollments.id')
            ->join('classes', 'enrollments.class_id', '=', 'classes.id')
            ->join('courses', 'classes.course_id', '=', 'courses.id')
            ->where('grades.student_id', $student->id)
            ->where('grades.grade_type', Grade::TYPE_FINAL);

        if ($academicYearId) {
            $query->where('grades.academic_year_id', $academicYearId);
        }

        if ($semester) {
            $query->where('grades.semester', $semester);
        }

        $results = $query->selectRaw('
            SUM(grades.grade_point * courses.credit_units) as total_grade_points,
            SUM(courses.credit_units) as total_credit_units
        ')->first();

        if (!$results || $results->total_credit_units == 0) {
            return 0.0;
        }

        return round($results->total_grade_points / $results->total_credit_units, 2);
    }

    /**
     * Update a grade.
     *
     * @param Grade $grade
     * @param array $data
     * @return Grade
     */
    public function updateGrade(Grade $grade, array $data): Grade
    {
        return DB::transaction(function () use ($grade, $data) {
            $oldScore = $grade->score;
            
            $grade->update($data);
            
            // Recalculate letter grade and grade point
            $grade->calculateLetterGrade();
            $grade->save();

            // Log the grade update
            activity()
                ->performedOn($grade)
                ->causedBy(auth()->user())
                ->log("Grade updated from {$oldScore} to {$grade->score}");

            return $grade;
        });
    }

    /**
     * Delete a grade.
     *
     * @param Grade $grade
     * @return bool
     */
    public function deleteGrade(Grade $grade): bool
    {
        return DB::transaction(function () use ($grade) {
            $studentName = $grade->student->user->full_name;
            $examTitle = $grade->exam ? $grade->exam->title : 'N/A';
            
            $deleted = $grade->delete();
            
            if ($deleted) {
                // Log the grade deletion
                activity()
                    ->causedBy(auth()->user())
                    ->log("Grade deleted for {$studentName} in {$examTitle}");
            }

            return $deleted;
        });
    }

    /**
     * Generate result sheet for a class.
     *
     * @param int $classId
     * @param int $academicYearId
     * @param string $semester
     * @return array
     */
    public function generateResultSheet(int $classId, int $academicYearId, string $semester): array
    {
        $class = ClassSection::with(['course', 'instructor.user'])->findOrFail($classId);
        
        $enrollments = Enrollment::with(['student.user'])
            ->where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->where('status', 'enrolled')
            ->get();

        $results = [];
        
        foreach ($enrollments as $enrollment) {
            $grades = Grade::where('enrollment_id', $enrollment->id)
                ->where('academic_year_id', $academicYearId)
                ->where('semester', $semester)
                ->get();

            $caScore = $grades->where('grade_type', Grade::TYPE_CA)->sum('score');
            $examScore = $grades->where('grade_type', Grade::TYPE_EXAM)->sum('score');
            $finalGrade = $grades->where('grade_type', Grade::TYPE_FINAL)->first();

            $results[] = [
                'student' => $enrollment->student,
                'ca_score' => $caScore,
                'exam_score' => $examScore,
                'total_score' => $caScore + $examScore,
                'final_grade' => $finalGrade,
                'grades' => $grades,
            ];
        }

        // Sort by total score descending
        usort($results, function ($a, $b) {
            return $b['total_score'] <=> $a['total_score'];
        });

        return [
            'class' => $class,
            'academic_year_id' => $academicYearId,
            'semester' => $semester,
            'results' => $results,
            'statistics' => $this->calculateClassStatistics($results),
        ];
    }

    /**
     * Calculate final grades for enrollments.
     *
     * @param array $enrollmentIds
     * @return int
     */
    public function calculateFinalGrades(array $enrollmentIds): int
    {
        return DB::transaction(function () use ($enrollmentIds) {
            $updated = 0;

            foreach ($enrollmentIds as $enrollmentId) {
                $enrollment = Enrollment::findOrFail($enrollmentId);
                
                // Get CA and Exam scores
                $caScore = Grade::where('enrollment_id', $enrollmentId)
                    ->where('grade_type', Grade::TYPE_CA)
                    ->sum('score');
                
                $examScore = Grade::where('enrollment_id', $enrollmentId)
                    ->where('grade_type', Grade::TYPE_EXAM)
                    ->sum('score');

                $totalScore = $caScore + $examScore;

                // Create or update final grade
                $finalGrade = Grade::updateOrCreate(
                    [
                        'enrollment_id' => $enrollmentId,
                        'grade_type' => Grade::TYPE_FINAL,
                    ],
                    [
                        'student_id' => $enrollment->student_id,
                        'academic_year_id' => $enrollment->academic_year_id,
                        'semester' => $enrollment->semester,
                        'score' => $totalScore,
                        'max_score' => 100, // Assuming 100 is the maximum
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                    ]
                );

                // Calculate letter grade
                $finalGrade->calculateLetterGrade();
                $finalGrade->save();

                // Update enrollment with final grade
                $enrollment->update([
                    'total_score' => $totalScore,
                    'final_grade' => $finalGrade->letter_grade,
                ]);

                $updated++;
            }

            return $updated;
        });
    }

    /**
     * Calculate class statistics.
     *
     * @param array $results
     * @return array
     */
    private function calculateClassStatistics(array $results): array
    {
        if (empty($results)) {
            return [
                'total_students' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0,
            ];
        }

        $totalStudents = count($results);
        $totalScores = array_column($results, 'total_score');
        $passedCount = count(array_filter($results, function ($result) {
            return $result['final_grade'] && $result['final_grade']->letter_grade !== 'F';
        }));

        return [
            'total_students' => $totalStudents,
            'average_score' => round(array_sum($totalScores) / $totalStudents, 2),
            'highest_score' => max($totalScores),
            'lowest_score' => min($totalScores),
            'pass_rate' => round(($passedCount / $totalStudents) * 100, 2),
        ];
    }
}
