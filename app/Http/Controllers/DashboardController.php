<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassSection;
use App\Models\Exam;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Fee;
use App\Models\SalaryPayment;
use App\Models\Mark;
use App\Models\Grade;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dynamic dashboard based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(DashboardService $dashboardService)
    {
        $user = Auth::user();

        // Check if user has any roles assigned
        if (!$user->roles()->exists()) {
            // User has no roles, show restricted access view
            return view('dashboard.no-role');
        }

        // Get modern dashboard data using the service
        $dashboardData = $dashboardService->getDashboardData($user);

        // Check if request wants JSON (for API/AJAX calls)
        if (request()->wantsJson()) {
            return response()->json($dashboardData);
        }

        // For backward compatibility, also get legacy data
        $legacyData = $this->getLegacyDashboardData($user);

        // Return the dashboard view with both modern and legacy data
        return view('dashboard', array_merge($legacyData, [
            'dashboardData' => $dashboardData,
            'projectSummary' => $dashboardData['projectSummary'] ?? []
        ]));
    }

    /**
     * Get modern dashboard data as JSON (for Vue components)
     */
    public function data(DashboardService $dashboardService)
    {
        $user = Auth::user();
        $dashboardData = $dashboardService->getDashboardData($user);
        
        return response()->json($dashboardData);
    }

    /**
     * Get legacy dashboard data for backward compatibility
     */
    private function getLegacyDashboardData(User $user): array
    {
        // Determine user's highest role (prioritizing the most important ones)
        $role = 'User'; // Default role
        if ($user->hasRole('Super Admin')) {
            $role = 'Super Admin';
        } elseif ($user->hasRole('Admin')) {
            $role = 'Admin';
        } elseif ($user->hasRole('Examiner')) {
            $role = 'Examiner';
        } elseif ($user->hasRole('Accountant')) {
            $role = 'Accountant';
        } elseif ($user->hasRole('Teacher')) {
            $role = 'Teacher';
        }

        // Get permissions for the view
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        // Get recent data
        $recentStudents = $this->getRecentStudents();
        $upcomingExams = $this->getUpcomingExams();

        // Get finance data
        $financeStats = $this->getFinanceStats();
        $recentPayments = $this->getRecentPayments();
        $overdueInvoices = $this->getOverdueInvoices();

        // Get chart data
        $chartData = $this->getChartData();

        return compact(
            'user',
            'role',
            'permissions',
            'stats',
            'recentStudents',
            'upcomingExams',
            'financeStats',
            'recentPayments',
            'overdueInvoices',
            'chartData'
        );
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $currentAcademicYear = AcademicYear::current();
        $lastMonth = Carbon::now()->subMonth();

        // Total students
        $totalStudents = Student::count();
        $lastMonthStudents = Student::where('created_at', '<=', $lastMonth)->count();
        $studentsGrowth = $lastMonthStudents > 0 ?
            round((($totalStudents - $lastMonthStudents) / $lastMonthStudents) * 100, 1) : 0;

        // Active classes (classes with enrollments in current academic year)
        $activeClasses = ClassSection::whereHas('enrollments', function($query) use ($currentAcademicYear) {
            if ($currentAcademicYear) {
                $query->where('academic_year_id', $currentAcademicYear->id)
                      ->where('status', 'enrolled');
            }
        })->count();

        // Upcoming exams (next 30 days)
        $upcomingExamsCount = Exam::where('exam_date', '>=', Carbon::now())
            ->where('exam_date', '<=', Carbon::now()->addDays(30))
            ->where('status', '!=', 'cancelled')
            ->count();

        // Total users
        $totalUsers = User::count();
        $lastMonthUsers = User::where('created_at', '<=', $lastMonth)->count();
        $usersGrowth = $lastMonthUsers > 0 ?
            round((($totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;

        // Pending results (exams with marks but no grades assigned)
        $pendingResults = Exam::whereHas('marks', function($query) {
            $query->whereNull('grade_letter');
        })->count();

        // Total exams
        $totalExams = Exam::count();

        return [
            'total_students' => $totalStudents,
            'students_growth' => $studentsGrowth,
            'active_classes' => $activeClasses,
            'upcoming_exams' => $upcomingExamsCount,
            'total_users' => $totalUsers,
            'users_growth' => $usersGrowth,
            'pending_results' => $pendingResults,
            'total_exams' => $totalExams,
        ];
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData()
    {
        return [
            'enrollment_trends' => $this->getEnrollmentTrends(),
            'academic_performance' => $this->getAcademicPerformance(),
            'revenue_trends' => $this->getRevenueTrends(),
            'payment_status' => $this->getPaymentStatus(),
        ];
    }

    /**
     * Get enrollment trends data for the last 6 months
     */
    private function getEnrollmentTrends()
    {
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            $enrollmentCount = Enrollment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'enrolled')
                ->count();
            
            $data[] = $enrollmentCount;
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    /**
     * Get academic performance distribution
     */
    private function getAcademicPerformance()
    {
        // Get all grades with letter grades
        $performanceData = Grade::select('letter_grade', DB::raw('COUNT(*) as count'))
            ->whereNotNull('letter_grade')
            ->groupBy('letter_grade')
            ->orderBy('letter_grade', 'asc')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'];

        foreach ($performanceData as $index => $item) {
            $labels[] = 'Grade ' . $item->letter_grade;
            $data[] = $item->count;
        }

        // If no data, provide default values
        if (empty($data)) {
            $labels = ['Grade A', 'Grade B', 'Grade C', 'Grade D', 'Grade F'];
            $data = [25, 35, 30, 8, 2];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels))
        ];
    }

    /**
     * Get revenue trends for the last 6 months
     */
    private function getRevenueTrends()
    {
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            $revenue = Payment::where('status', 'completed')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            
            $data[] = $revenue;
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    /**
     * Get payment status distribution
     */
    private function getPaymentStatus()
    {
        $totalInvoices = Invoice::count();
        
        if ($totalInvoices === 0) {
            return [
                'labels' => ['Paid', 'Pending', 'Overdue'],
                'data' => [0, 0, 0],
                'colors' => ['#10B981', '#F59E0B', '#EF4444']
            ];
        }

        $paidInvoices = Invoice::where('status', 'paid')->count();
        $pendingInvoices = Invoice::whereIn('status', ['sent', 'partially_paid'])->count();
        $overdueInvoices = Invoice::where('status', 'overdue')
            ->orWhere(function($query) {
                $query->whereIn('status', ['sent', 'partially_paid'])
                      ->where('due_date', '<', Carbon::now());
            })->count();

        $labels = ['Paid', 'Pending', 'Overdue'];
        $data = [$paidInvoices, $pendingInvoices, $overdueInvoices];
        $colors = ['#10B981', '#F59E0B', '#EF4444'];

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors
        ];
    }

    /**
     * Get recent students (last 10)
     */
    private function getRecentStudents()
    {
        return Student::with(['user', 'enrollments.class.course'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($student) {
                $latestEnrollment = $student->enrollments->first();
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                    'admission_number' => $student->admission_number,
                    'class' => $latestEnrollment ?
                        $latestEnrollment->class->course->title . ' - ' . $latestEnrollment->class->name :
                        'Not enrolled',
                    'join_date' => $student->created_at->format('M d, Y'),
                ];
            });
    }

    /**
     * Get upcoming exams (next 10)
     */
    private function getUpcomingExams()
    {
        return Exam::with(['class.course', 'academicYear'])
            ->where('exam_date', '>=', Carbon::now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('exam_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'course' => $exam->class->course->title,
                    'class' => $exam->class->name,
                    'date' => $exam->exam_date->format('M d, Y'),
                    'time' => $exam->start_time ? Carbon::parse($exam->start_time)->format('h:i A') : 'TBA',
                ];
            });
    }

    /**
     * Get finance statistics for dashboard
     */
    private function getFinanceStats()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Total revenue (all completed payments)
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $lastMonthRevenue = Payment::where('status', 'completed')
            ->where('payment_date', '<=', $lastMonth)
            ->sum('amount');

        // Outstanding amount (unpaid invoice balances)
        $outstandingAmount = Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->sum('balance');

        // This month's revenue
        $thisMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('payment_date', $currentMonth->month)
            ->whereYear('payment_date', $currentMonth->year)
            ->sum('amount');

        // Last month's revenue for comparison
        $previousMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('payment_date', $lastMonth->month)
            ->whereYear('payment_date', $lastMonth->year)
            ->sum('amount');

        // Calculate growth
        $revenueGrowth = $previousMonthRevenue > 0 ?
            round((($thisMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1) : 0;

        // Total invoices
        $totalInvoices = Invoice::count();
        $pendingInvoices = Invoice::whereIn('status', ['sent', 'partially_paid'])->count();
        $overdueInvoices = Invoice::where('status', 'overdue')
            ->orWhere(function($query) {
                $query->whereIn('status', ['sent', 'partially_paid'])
                      ->where('due_date', '<', Carbon::now());
            })->count();

        // Recent payments count
        $recentPaymentsCount = Payment::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        return [
            'total_revenue' => $totalRevenue,
            'outstanding_amount' => $outstandingAmount,
            'this_month_revenue' => $thisMonthRevenue,
            'revenue_growth' => $revenueGrowth,
            'total_invoices' => $totalInvoices,
            'pending_invoices' => $pendingInvoices,
            'overdue_invoices' => $overdueInvoices,
            'recent_payments_count' => $recentPaymentsCount,
        ];
    }

    /**
     * Get recent payments (last 10)
     */
    private function getRecentPayments()
    {
        return Payment::with(['student.user', 'invoice'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'student_name' => $payment->student->user->full_name,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'payment_date' => $payment->payment_date->format('M d, Y'),
                    'invoice_number' => $payment->invoice ? $payment->invoice->invoice_number : 'N/A',
                ];
            });
    }

    /**
     * Get overdue invoices (next 10)
     */
    private function getOverdueInvoices()
    {
        return Invoice::with(['student.user'])
            ->where(function($query) {
                $query->where('status', 'overdue')
                      ->orWhere(function($subQuery) {
                          $subQuery->whereIn('status', ['sent', 'partially_paid'])
                                   ->where('due_date', '<', Carbon::now());
                      });
            })
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'student_name' => $invoice->student->user->full_name,
                    'amount' => $invoice->balance,
                    'due_date' => $invoice->due_date->format('M d, Y'),
                    'days_overdue' => $invoice->due_date->diffInDays(Carbon::now()),
                ];
            });
    }
}