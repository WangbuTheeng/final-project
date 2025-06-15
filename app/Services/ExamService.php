<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\ClassSection;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class ExamService
 * 
 * Service class for handling exam-related business logic.
 * 
 * @package App\Services
 */
class ExamService
{
    /**
     * Get paginated exams with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedExams(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Exam::with(['classSection.course', 'academicYear', 'creator']);

        // Apply filters
        if (!empty($filters['exam_type'])) {
            $query->where('exam_type', $filters['exam_type']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('venue', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('exam_date', 'desc')->paginate($perPage);
    }

    /**
     * Create a new exam.
     *
     * @param array $data
     * @return Exam
     */
    public function createExam(array $data): Exam
    {
        return DB::transaction(function () use ($data) {
            $exam = Exam::create($data);
            
            // Log the exam creation
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->log('Exam created: ' . $exam->title);

            return $exam;
        });
    }

    /**
     * Update an existing exam.
     *
     * @param Exam $exam
     * @param array $data
     * @return Exam
     */
    public function updateExam(Exam $exam, array $data): Exam
    {
        return DB::transaction(function () use ($exam, $data) {
            $oldTitle = $exam->title;
            $exam->update($data);
            
            // Log the exam update
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->log("Exam updated: {$oldTitle} -> {$exam->title}");

            return $exam;
        });
    }

    /**
     * Delete an exam if it has no associated grades.
     *
     * @param Exam $exam
     * @return bool
     * @throws \Exception
     */
    public function deleteExam(Exam $exam): bool
    {
        return DB::transaction(function () use ($exam) {
            // Check if there are any grades associated with this exam
            $gradesCount = $exam->grades()->count();
            
            if ($gradesCount > 0) {
                throw new \Exception("Cannot delete exam with {$gradesCount} associated grades.");
            }

            $title = $exam->title;
            $deleted = $exam->delete();
            
            if ($deleted) {
                // Log the exam deletion
                activity()
                    ->causedBy(auth()->user())
                    ->log("Exam deleted: {$title}");
            }

            return $deleted;
        });
    }

    /**
     * Get upcoming exams for a specific class or all classes.
     *
     * @param int|null $classId
     * @param int $limit
     * @return Collection
     */
    public function getUpcomingExams(?int $classId = null, int $limit = 10): Collection
    {
        $query = Exam::with(['classSection.course', 'academicYear'])
            ->upcoming()
            ->orderBy('exam_date', 'asc');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get exam statistics.
     *
     * @param array $filters
     * @return array
     */
    public function getExamStatistics(array $filters = []): array
    {
        $query = Exam::query();

        // Apply filters
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        $total = $query->count();
        $scheduled = (clone $query)->where('status', Exam::STATUS_SCHEDULED)->count();
        $ongoing = (clone $query)->where('status', Exam::STATUS_ONGOING)->count();
        $completed = (clone $query)->where('status', Exam::STATUS_COMPLETED)->count();
        $cancelled = (clone $query)->where('status', Exam::STATUS_CANCELLED)->count();

        return [
            'total' => $total,
            'scheduled' => $scheduled,
            'ongoing' => $ongoing,
            'completed' => $completed,
            'cancelled' => $cancelled,
        ];
    }

    /**
     * Start an exam (change status to ongoing).
     *
     * @param Exam $exam
     * @return bool
     * @throws \Exception
     */
    public function startExam(Exam $exam): bool
    {
        if (!$exam->isScheduled()) {
            throw new \Exception('Only scheduled exams can be started.');
        }

        $updated = $exam->update(['status' => Exam::STATUS_ONGOING]);
        
        if ($updated) {
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->log("Exam started: {$exam->title}");
        }

        return $updated;
    }

    /**
     * Complete an exam (change status to completed).
     *
     * @param Exam $exam
     * @return bool
     * @throws \Exception
     */
    public function completeExam(Exam $exam): bool
    {
        if (!$exam->isOngoing()) {
            throw new \Exception('Only ongoing exams can be completed.');
        }

        $updated = $exam->update(['status' => Exam::STATUS_COMPLETED]);
        
        if ($updated) {
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->log("Exam completed: {$exam->title}");
        }

        return $updated;
    }

    /**
     * Cancel an exam.
     *
     * @param Exam $exam
     * @return bool
     * @throws \Exception
     */
    public function cancelExam(Exam $exam): bool
    {
        if ($exam->isCompleted()) {
            throw new \Exception('Completed exams cannot be cancelled.');
        }

        $updated = $exam->update(['status' => Exam::STATUS_CANCELLED]);
        
        if ($updated) {
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->log("Exam cancelled: {$exam->title}");
        }

        return $updated;
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
     * Check if an exam can be edited.
     *
     * @param Exam $exam
     * @return bool
     */
    public function canEditExam(Exam $exam): bool
    {
        // Cannot edit completed or cancelled exams
        if ($exam->isCompleted() || $exam->isCancelled()) {
            return false;
        }

        // Cannot edit if exam has grades
        return $exam->grades()->count() === 0;
    }

    /**
     * Check if an exam can be deleted.
     *
     * @param Exam $exam
     * @return bool
     */
    public function canDeleteExam(Exam $exam): bool
    {
        // Cannot delete if exam has grades
        return $exam->grades()->count() === 0;
    }
}
