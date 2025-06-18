<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
use Carbon\Carbon;

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
    public function index()
    {
        $user = Auth::user();
        $role = null;

        // Determine user's highest role (prioritizing the most important ones)
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
        } else {
            $role = 'User'; // Default role
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

        // Return the dashboard view
        return view('dashboard', compact(
            'user',
            'role',
            'permissions',
            'stats',
            'recentStudents',
            'upcomingExams',
            'financeStats',
            'recentPayments',
            'overdueInvoices'
        ));
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

        return [
            'total_students' => $totalStudents,
            'students_growth' => $studentsGrowth,
            'active_classes' => $activeClasses,
            'upcoming_exams' => $upcomingExamsCount,
            'total_users' => $totalUsers,
            'users_growth' => $usersGrowth,
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