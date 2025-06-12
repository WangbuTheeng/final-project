<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of students with optimized performance.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view-students');

        // Cache filter options for better performance
        $departments = cache()->remember('active_departments', 300, function () {
            return Department::active()->select('id', 'name', 'code')->get();
        });

        $academicYears = cache()->remember('academic_years_desc', 300, function () {
            return AcademicYear::select('id', 'name', 'start_year', 'end_year')
                ->orderBy('name', 'desc')
                ->get();
        });

        // Get filters
        $filters = $request->only([
            'department_id', 'level', 'status', 'academic_year_id', 'search'
        ]);

        // Build optimized query with selective eager loading
        $studentsQuery = Student::select([
                'id', 'user_id', 'matric_number', 'department_id',
                'academic_year_id', 'current_level', 'status', 'cgpa', 'created_at'
            ])
            ->with([
                'user:id,first_name,last_name,email,phone',
                'department:id,name,code',
                'academicYear:id,name,start_year,end_year'
            ]);

        // Apply filters efficiently
        if (!empty($filters['department_id'])) {
            $studentsQuery->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['level'])) {
            $studentsQuery->where('current_level', $filters['level']);
        }

        if (!empty($filters['status'])) {
            $studentsQuery->where('status', $filters['status']);
        }

        if (!empty($filters['academic_year_id'])) {
            $studentsQuery->where('academic_year_id', $filters['academic_year_id']);
        }

        // Optimized search with database indexes
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('matric_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
            });
        }

        // Use index-friendly ordering
        $students = $studentsQuery->orderBy('id', 'desc')->paginate(20);

        // Get statistics with optimized single query
        $stats = $this->getOptimizedStudentStats($filters);

        return view('students.index', compact(
            'students',
            'departments',
            'academicYears',
            'filters',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $this->authorize('create-students');

        $departments = Department::active()->get();
        $academicYears = AcademicYear::active()->get();

        return view('students.create', compact('departments', 'academicYears'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $this->authorize('create-students');

        $request->validate([
            // User fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
            
            // Student fields
            'matric_number' => 'required|string|unique:students,matric_number',
            'department_id' => 'required|exists:departments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'current_level' => 'required|in:100,200,300,400,500',
            'mode_of_entry' => 'required|in:utme,direct_entry,transfer',
            'study_mode' => 'required|in:full_time,part_time,distance',
            
            // Guardian information
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email',
            'guardian_address' => 'nullable|string',
            'guardian_relationship' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'password' => Hash::make('password123'), // Default password
                'role' => 'student',
                'status' => 'active'
            ]);

            // Prepare guardian info
            $guardianInfo = null;
            if ($request->guardian_name) {
                $guardianInfo = [
                    'name' => $request->guardian_name,
                    'phone' => $request->guardian_phone,
                    'email' => $request->guardian_email,
                    'address' => $request->guardian_address,
                    'relationship' => $request->guardian_relationship,
                ];
            }

            // Calculate expected graduation date
            $department = Department::find($request->department_id);
            $academicYear = AcademicYear::find($request->academic_year_id);
            $expectedGraduationDate = $academicYear->end_date->addYears($department->duration_years);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'matric_number' => $request->matric_number,
                'department_id' => $request->department_id,
                'academic_year_id' => $request->academic_year_id,
                'current_level' => $request->current_level,
                'mode_of_entry' => $request->mode_of_entry,
                'study_mode' => $request->study_mode,
                'status' => 'active',
                'expected_graduation_date' => $expectedGraduationDate,
                'guardian_info' => $guardianInfo
            ]);

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully. Default password is "password123".');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating student: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified student with optimized loading.
     *
     * @param Student $student
     * @return \Illuminate\View\View
     */
    public function show(Student $student)
    {
        $this->authorize('view-students');

        // Load relationships efficiently with selective fields
        $student->load([
            'user:id,first_name,last_name,email,phone,date_of_birth,gender,address',
            'department.faculty:id,name,code',
            'academicYear:id,name,start_year,end_year,is_current'
        ]);

        // Get current academic year efficiently
        $currentAcademicYear = cache()->remember('current_academic_year', 300, function () {
            return AcademicYear::current();
        });

        // Optimized current enrollments query
        $currentEnrollments = Enrollment::select([
                'id', 'student_id', 'class_id', 'academic_year_id',
                'semester', 'status', 'enrollment_date', 'total_score', 'final_grade'
            ])
            ->with([
                'class:id,course_id,instructor_id,section,capacity',
                'class.course:id,title,code,credit_units',
                'class.instructor.user:id,first_name,last_name'
            ])
            ->where('student_id', $student->id)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('semester', 'first')
            ->where('status', 'enrolled')
            ->get();

        // Optimized academic history with single query and grouping
        $academicHistory = Enrollment::select([
                'id', 'student_id', 'class_id', 'academic_year_id',
                'semester', 'status', 'enrollment_date', 'total_score', 'final_grade'
            ])
            ->with([
                'class:id,course_id,section',
                'class.course:id,title,code,credit_units',
                'academicYear:id,name,start_year,end_year'
            ])
            ->where('student_id', $student->id)
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('semester', 'desc')
            ->get()
            ->groupBy(['academic_year_id', 'semester']);

        // Calculate performance metrics
        $performanceMetrics = $this->calculateStudentPerformanceMetrics($student);

        return view('students.show', compact(
            'student',
            'currentEnrollments',
            'academicHistory',
            'performanceMetrics'
        ));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(Student $student)
    {
        $this->authorize('edit-students');

        $student->load('user');
        $departments = Department::active()->get();
        $academicYears = AcademicYear::active()->get();

        return view('students.edit', compact('student', 'departments', 'academicYears'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student)
    {
        $this->authorize('edit-students');

        $request->validate([
            // User fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($student->user_id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'address' => 'nullable|string',
            
            // Student fields
            'matric_number' => ['required', 'string', Rule::unique('students')->ignore($student->id)],
            'department_id' => 'required|exists:departments,id',
            'current_level' => 'required|in:100,200,300,400,500',
            'mode_of_entry' => 'required|in:utme,direct_entry,transfer',
            'study_mode' => 'required|in:full_time,part_time,distance',
            'status' => 'required|in:active,graduated,suspended,withdrawn,deferred',
            
            // Guardian information
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email',
            'guardian_address' => 'nullable|string',
            'guardian_relationship' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Update user account
            $student->user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
            ]);

            // Prepare guardian info
            $guardianInfo = null;
            if ($request->guardian_name) {
                $guardianInfo = [
                    'name' => $request->guardian_name,
                    'phone' => $request->guardian_phone,
                    'email' => $request->guardian_email,
                    'address' => $request->guardian_address,
                    'relationship' => $request->guardian_relationship,
                ];
            }

            // Update student record
            $student->update([
                'matric_number' => $request->matric_number,
                'department_id' => $request->department_id,
                'current_level' => $request->current_level,
                'mode_of_entry' => $request->mode_of_entry,
                'study_mode' => $request->study_mode,
                'status' => $request->status,
                'guardian_info' => $guardianInfo
            ]);

            DB::commit();

            return redirect()->route('students.show', $student)
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating student: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete-students');

        try {
            DB::beginTransaction();

            // Check if student has active enrollments
            $activeEnrollments = $student->enrollments()->where('status', 'enrolled')->count();
            if ($activeEnrollments > 0) {
                return back()->with('error', 'Cannot delete student with active enrollments.');
            }

            $student->delete();
            $student->user->delete();

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Get optimized student statistics with single query.
     *
     * @param array $filters
     * @return array
     */
    private function getOptimizedStudentStats(array $filters = []): array
    {
        $query = Student::query();

        // Apply same filters as main query
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['level'])) {
            $query->where('current_level', $filters['level']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('matric_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Single query to get all statistics
        $stats = $query->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = "graduated" THEN 1 ELSE 0 END) as graduated,
            SUM(CASE WHEN status = "suspended" THEN 1 ELSE 0 END) as suspended,
            SUM(CASE WHEN status = "withdrawn" THEN 1 ELSE 0 END) as withdrawn,
            SUM(CASE WHEN status = "deferred" THEN 1 ELSE 0 END) as deferred,
            AVG(cgpa) as average_cgpa
        ')->first();

        return [
            'total' => $stats->total ?? 0,
            'active' => $stats->active ?? 0,
            'graduated' => $stats->graduated ?? 0,
            'suspended' => $stats->suspended ?? 0,
            'withdrawn' => $stats->withdrawn ?? 0,
            'deferred' => $stats->deferred ?? 0,
            'average_cgpa' => round($stats->average_cgpa ?? 0, 2),
        ];
    }

    /**
     * Calculate student performance metrics efficiently.
     *
     * @param Student $student
     * @return array
     */
    private function calculateStudentPerformanceMetrics(Student $student): array
    {
        // Use single query to get all enrollment statistics
        $enrollmentStats = Enrollment::where('student_id', $student->id)
            ->selectRaw('
                COUNT(*) as total_enrollments,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_courses,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_courses,
                SUM(CASE WHEN status = "enrolled" THEN 1 ELSE 0 END) as current_enrollments,
                AVG(CASE WHEN total_score IS NOT NULL THEN total_score ELSE NULL END) as average_score
            ')
            ->first();

        // Calculate completion rate
        $completionRate = $enrollmentStats->total_enrollments > 0
            ? round(($enrollmentStats->completed_courses / $enrollmentStats->total_enrollments) * 100, 2)
            : 0;

        return [
            'total_enrollments' => $enrollmentStats->total_enrollments ?? 0,
            'completed_courses' => $enrollmentStats->completed_courses ?? 0,
            'failed_courses' => $enrollmentStats->failed_courses ?? 0,
            'current_enrollments' => $enrollmentStats->current_enrollments ?? 0,
            'average_score' => round($enrollmentStats->average_score ?? 0, 2),
            'completion_rate' => $completionRate,
            'cgpa' => $student->cgpa ?? 0,
            'total_credits' => $student->total_credits_earned ?? 0,
        ];
    }
}
