<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Mark;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\ClassSection;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        // Check if user has any report permissions
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') &&
            !auth()->user()->can('view-reports') &&
            !auth()->user()->can('view-financial-reports')) {
            abort(403, 'Unauthorized access to reports.');
        }

        // Get report statistics
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_exams' => Exam::count(),
            'total_courses' => Course::count(),
            'active_academic_year' => AcademicYear::current()?->name ?? 'Not Set',
        ];

        return view('reports.index', compact('stats'));
    }

    /**
     * Student Reports
     */
    public function studentReports(Request $request)
    {
        // Check if user has permission to view reports
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') &&
            !auth()->user()->can('view-reports')) {
            abort(403, 'Unauthorized access to student reports.');
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $faculties = Faculty::orderBy('name')->get();
        $courses = Course::orderBy('title')->get();
        $classes = ClassSection::with('course')->orderBy('name')->get();

        $query = Student::with(['user', 'enrollments.class.course.faculty', 'enrollments.academicYear']);

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $query->whereHas('enrollments', function($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->filled('faculty_id')) {
            $query->whereHas('enrollments.class.course', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        if ($request->filled('course_id')) {
            $query->whereHas('enrollments.class', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('class_id')) {
            $query->whereHas('enrollments', function($q) use ($request) {
                $q->where('class_section_id', $request->class_id);
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('enrollments', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $students = $query->paginate(20);

        // Statistics
        $totalStudents = $query->count();
        $enrolledStudents = Student::whereHas('enrollments', function($q) {
            $q->where('status', 'enrolled');
        })->count();

        $stats = [
            'total' => $totalStudents,
            'enrolled' => $enrolledStudents,
            'graduated' => Student::whereHas('enrollments', function($q) {
                $q->where('status', 'graduated');
            })->count(),
            'dropped' => Student::whereHas('enrollments', function($q) {
                $q->where('status', 'dropped');
            })->count(),
        ];

        return view('reports.students', compact(
            'students', 'academicYears', 'faculties', 'courses', 'classes', 'stats'
        ));
    }

    /**
     * Academic Performance Reports
     */
    public function academicReports(Request $request)
    {
        // Check if user has permission to view reports
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') &&
            !auth()->user()->can('view-reports')) {
            abort(403, 'Unauthorized access to academic reports.');
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $exams = Exam::with('class.course')->orderBy('exam_date', 'desc')->get();
        $courses = Course::orderBy('title')->get();

        $query = Grade::with(['student.user', 'exam', 'subject', 'academicYear']);

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('course_id')) {
            $query->whereHas('exam.class', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $grades = $query->paginate(20);

        // Performance Statistics
        $stats = [
            'total_grades' => Grade::count(),
            'average_gpa' => Grade::avg('grade_point') ?? 0,
            'highest_gpa' => Grade::max('grade_point') ?? 0,
            'pass_rate' => $this->calculatePassRate(),
        ];

        return view('reports.academic', compact(
            'grades', 'academicYears', 'exams', 'courses', 'stats'
        ));
    }

    /**
     * Teacher Reports
     */
    public function teacherReports(Request $request)
    {
        // Check if user has permission to view reports
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') &&
            !auth()->user()->can('view-reports')) {
            abort(403, 'Unauthorized access to teacher reports.');
        }

        $departments = Teacher::distinct()->pluck('department')->filter()->sort();

        $query = Teacher::query();

        // Apply filters
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $teachers = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => Teacher::count(),
            'active' => Teacher::where('status', 'active')->count(),
            'inactive' => Teacher::where('status', 'inactive')->count(),
            'on_leave' => Teacher::where('status', 'on_leave')->count(),
        ];

        return view('reports.teachers', compact(
            'teachers', 'departments', 'stats'
        ));
    }

    /**
     * Enrollment Reports
     */
    public function enrollmentReports(Request $request)
    {
        // Check if user has permission to view reports
        if (!auth()->user()->hasRole(['Super Admin', 'Admin']) &&
            !auth()->user()->can('view-reports')) {
            abort(403, 'Unauthorized access to enrollment reports.');
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $faculties = Faculty::orderBy('name')->get();
        $courses = Course::orderBy('title')->get();

        // Enrollment trends by academic year
        $enrollmentTrends = Enrollment::select(
                'academic_year_id',
                DB::raw('COUNT(*) as total_enrollments'),
                DB::raw('COUNT(CASE WHEN status = "enrolled" THEN 1 END) as active_enrollments')
            )
            ->with('academicYear')
            ->groupBy('academic_year_id')
            ->orderBy('academic_year_id', 'desc')
            ->get();

        // Enrollment by faculty
        $enrollmentByFaculty = Enrollment::select(
                'faculties.name as faculty_name',
                DB::raw('COUNT(*) as total_enrollments')
            )
            ->join('class_sections', 'enrollments.class_section_id', '=', 'class_sections.id')
            ->join('courses', 'class_sections.course_id', '=', 'courses.id')
            ->join('faculties', 'courses.faculty_id', '=', 'faculties.id')
            ->where('enrollments.status', 'enrolled')
            ->groupBy('faculties.id', 'faculties.name')
            ->orderBy('total_enrollments', 'desc')
            ->get();

        // Recent enrollments
        $recentEnrollments = Enrollment::with(['student.user', 'class.course', 'academicYear'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total_enrollments' => Enrollment::count(),
            'active_enrollments' => Enrollment::where('status', 'enrolled')->count(),
            'this_year_enrollments' => Enrollment::whereHas('academicYear', function($q) {
                $q->where('is_current', true);
            })->count(),
        ];

        return view('reports.enrollments', compact(
            'enrollmentTrends', 'enrollmentByFaculty', 'recentEnrollments',
            'academicYears', 'faculties', 'courses', 'stats'
        ));
    }

    /**
     * System Reports
     */
    public function systemReports()
    {
        // Only Super Admin can access system reports
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access to system reports.');
        }

        // User activity statistics
        $userStats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'recent_logins' => User::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', Carbon::now()->subDays(7))
                ->count(),
        ];

        // System usage statistics
        $systemStats = [
            'total_academic_years' => AcademicYear::count(),
            'total_faculties' => Faculty::count(),
            'total_courses' => Course::count(),
            'total_classes' => ClassSection::count(),
            'total_subjects' => Subject::count(),
        ];

        // Recent activity
        $recentUsers = User::orderBy('last_login_at', 'desc')
            ->limit(10)
            ->get();

        return view('reports.system', compact(
            'userStats', 'systemStats', 'recentUsers'
        ));
    }

    /**
     * Export student report to CSV
     */
    public function exportStudents(Request $request)
    {
        // Check if user has permission to view reports
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') &&
            !auth()->user()->can('view-reports')) {
            abort(403, 'Unauthorized access to export reports.');
        }

        // Implementation for CSV export
        // This would generate and download a CSV file
        return response()->json(['message' => 'Export functionality coming soon']);
    }

    /**
     * Calculate pass rate
     */
    private function calculatePassRate()
    {
        $totalGrades = Grade::count();
        if ($totalGrades === 0) return 0;

        $passingGrades = Grade::where('grade_point', '>=', 2.0)->count(); // Assuming 2.0 is passing
        return round(($passingGrades / $totalGrades) * 100, 2);
    }
}
