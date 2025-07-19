<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SalaryPayment;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if user can bypass authorization (Super Admin)
     */
    private function canBypassAuth(): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    /**
     * Authorize with Super Admin bypass
     */
    private function authorizeWithBypass(string $permission): void
    {
        if (!$this->canBypassAuth()) {
            $this->authorize($permission);
        }
    }

    // ==================== DASHBOARD ====================

    /**
     * Display the finance dashboard.
     */
    public function dashboard()
    {
        $this->authorizeWithBypass('view-finances');

        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Student Finance Statistics
        $totalStudents = Student::count();
        $totalInvoices = Invoice::count();
        $totalRevenue = Payment::completed()->sum('amount');
        $outstandingAmount = Invoice::sum('balance');

        // Monthly Revenue (last 12 months)
        $monthlyRevenue = Payment::completed()
            ->selectRaw('MONTH(payment_date) as month, YEAR(payment_date) as year, SUM(amount) as total')
            ->where('payment_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Recent Payments
        $recentPayments = Payment::with(['student.user', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Overdue Invoices
        $overdueInvoices = Invoice::overdue()
            ->with(['student.user'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        // Teacher Salary Statistics
        $totalTeachers = Teacher::active()->count();
        $salariesPaidThisMonth = SalaryPayment::where('month', now()->format('Y-m'))
            ->where('status', 'paid')
            ->sum('amount');

        return view('finance.dashboard', compact(
            'totalStudents', 'totalInvoices', 'totalRevenue', 'outstandingAmount',
            'monthlyRevenue', 'recentPayments', 'overdueInvoices',
            'totalTeachers', 'salariesPaidThisMonth'
        ));
    }

    // ==================== FEE MANAGEMENT ====================

    /**
     * Display a listing of the fees.
     */
    public function indexFees(Request $request)
    {
        $this->authorize('view-finances');

        $query = Fee::with(['academicYear', 'course', 'department']);

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('fee_type')) {
            $query->where('fee_type', $request->fee_type);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        $fees = $query->orderBy('created_at', 'desc')->paginate(15);
        $fees->appends($request->query());

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();

        return view('finance.fees.index', compact('fees', 'academicYears', 'departments'));
    }

    /**
     * Show the form for creating a new fee.
     */
    public function createFee()
    {
        $this->authorize('manage-fees');

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();
        $courses = Course::with('faculty')->where('is_active', true)->orderBy('title')->get();

        return view('finance.fees.create', compact('academicYears', 'departments', 'courses'));
    }

    /**
     * Store a newly created fee in storage.
     */
    public function storeFee(Request $request)
    {
        $this->authorize('manage-fees');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', 'unique:fees'],
            'description' => ['nullable', 'string'],
            'fee_type' => ['required', 'in:tuition,library,laboratory,sports,medical,accommodation,registration,examination,other'],
            'amount' => ['required', 'numeric', 'min:0'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'is_mandatory' => ['boolean'],
            'is_active' => ['boolean'],
            'due_date' => ['nullable', 'date']
        ]);

        try {
            $code = $request->code ?: Fee::generateFeeCode($request->fee_type, $request->academic_year_id);

            Fee::create([
                'name' => $request->name,
                'code' => strtoupper($code),
                'description' => $request->description,
                'fee_type' => $request->fee_type,
                'amount' => $request->amount,
                'course_id' => $request->course_id,
                'department_id' => $request->department_id,
                'academic_year_id' => $request->academic_year_id,
                'is_mandatory' => $request->has('is_mandatory'),
                'is_active' => $request->has('is_active') ? $request->is_active : true,
                'due_date' => $request->due_date
            ]);

            return redirect()->route('finance.fees.index')
                ->with('success', 'Fee created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating fee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified fee.
     */
    public function showFee(Fee $fee)
    {
        $this->authorize('view-finances');

        $fee->load(['academicYear', 'course', 'department']);

        return view('finance.fees.show', compact('fee'));
    }

    /**
     * Show the form for editing the specified fee.
     */
    public function editFee(Fee $fee)
    {
        $this->authorize('manage-fees');

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $departments = Department::orderBy('name')->get();
        $courses = Course::with('faculty')->where('is_active', true)->orderBy('title')->get();

        return view('finance.fees.edit', compact('fee', 'academicYears', 'departments', 'courses'));
    }

    /**
     * Update the specified fee in storage.
     */
    public function updateFee(Request $request, Fee $fee)
    {
        $this->authorize('manage-fees');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:fees,code,' . $fee->id],
            'description' => ['nullable', 'string'],
            'fee_type' => ['required', 'in:tuition,library,laboratory,sports,medical,accommodation,registration,examination,other'],
            'amount' => ['required', 'numeric', 'min:0'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'is_mandatory' => ['boolean'],
            'is_active' => ['boolean'],
            'due_date' => ['nullable', 'date']
        ]);

        try {
            $fee->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'fee_type' => $request->fee_type,
                'amount' => $request->amount,
                'course_id' => $request->course_id,
                'department_id' => $request->department_id,
                'academic_year_id' => $request->academic_year_id,
                'is_mandatory' => $request->has('is_mandatory'),
                'is_active' => $request->has('is_active') ? $request->is_active : true,
                'due_date' => $request->due_date
            ]);

            return redirect()->route('finance.fees.index')
                ->with('success', 'Fee updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating fee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified fee from storage.
     */
    public function destroyFee(Fee $fee)
    {
        $this->authorize('manage-fees');

        try {
            $fee->delete();

            return redirect()->route('finance.fees.index')
                ->with('success', 'Fee deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting fee: ' . $e->getMessage());
        }
    }

    // ==================== INVOICE MANAGEMENT ====================

    /**
     * Display a listing of invoices.
     */
    public function indexInvoices(Request $request)
    {
        $this->authorize('view-finances');

        $query = Invoice::with(['student.user', 'academicYear']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('student.user', function ($sq) use ($searchTerm) {
                      $sq->where('first_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('last_name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);
        $invoices->appends($request->query());

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('finance.invoices.index', compact('invoices', 'academicYears'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function createInvoice(Request $request)
    {
        $this->authorize('create-invoices');

        $students = Student::with('user')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        // Pre-selected student and academic year (from invoice show page)
        $preSelectedStudent = null;
        $preSelectedAcademicYear = null;

        if ($request->filled('student_id')) {
            $preSelectedStudent = Student::with(['user', 'invoices' => function($query) {
                $query->whereIn('status', ['sent', 'partially_paid', 'overdue'])
                      ->where('balance', '>', 0)
                      ->orderBy('due_date', 'asc');
            }])->find($request->student_id);
        }

        if ($request->filled('academic_year_id')) {
            $preSelectedAcademicYear = AcademicYear::find($request->academic_year_id);
        }

        return view('finance.invoices.create', compact('students', 'academicYears', 'preSelectedStudent', 'preSelectedAcademicYear'));
    }

    /**
     * Get student's courses and classes for a specific academic year via AJAX.
     */
    public function getStudentCoursesAndClasses(Request $request)
    {
        $this->authorize('create-invoices');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $student = Student::with(['enrollments.class.course'])->find($request->student_id);

        // Get student's enrollments for the specified academic year
        $enrollments = $student->enrollments()
            ->with(['class.course.faculty'])
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'enrolled')
            ->get();

        $courses = [];
        $classes = [];

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->class->course;
            $class = $enrollment->class;

            // Add course if not already added
            if (!isset($courses[$course->id])) {
                $courses[$course->id] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'code' => $course->code,
                    'faculty_name' => $course->faculty->name,
                    'credit_units' => $course->credit_units
                ];
            }

            // Add class
            $classes[] = [
                'id' => $class->id,
                'name' => $class->name,
                'course_id' => $course->id,
                'course_title' => $course->title,
                'semester' => $class->semester,
                'year' => $class->year,
                'instructor' => $class->instructor ? $class->instructor->first_name . ' ' . $class->instructor->last_name : 'TBA',
                'room' => $class->room
            ];
        }

        return response()->json([
            'success' => true,
            'courses' => array_values($courses),
            'classes' => $classes
        ]);
    }

    /**
     * Get applicable fees for a student via AJAX.
     */
    public function getApplicableFees(Request $request)
    {
        $this->authorize('create-invoices');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $student = Student::find($request->student_id);
        $fees = Fee::getApplicableFeesForStudent($student, $request->academic_year_id);

        return response()->json([
            'success' => true,
            'fees' => $fees->map(function ($fee) {
                return [
                    'id' => $fee->id,
                    'name' => $fee->name,
                    'code' => $fee->code,
                    'amount' => $fee->amount,
                    'fee_type_display' => $fee->fee_type_display,
                    'is_mandatory' => $fee->is_mandatory
                ];
            })
        ]);
    }

    /**
     * Get student invoices via AJAX.
     */
    public function getStudentInvoices(Request $request)
    {
        $this->authorize('create-payments');

        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $invoices = Invoice::where('student_id', $request->student_id)
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->where('balance', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'total_amount' => $invoice->total_amount,
                    'amount_paid' => $invoice->amount_paid,
                    'balance' => $invoice->balance,
                    'due_date' => $invoice->due_date->format('Y-m-d'),
                    'status' => $invoice->status
                ];
            });

        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    }

    /**
     * Get student outstanding balance via AJAX.
     */
    public function getStudentOutstandingBalance(Request $request)
    {
        $this->authorize('create-invoices');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'nullable|exists:academic_years,id'
        ]);

        $student = Student::find($request->student_id);

        if ($request->filled('academic_year_id')) {
            $outstandingBalance = $student->getOutstandingBalanceForYear($request->academic_year_id);
            $unpaidInvoices = $student->invoices()
                ->where('academic_year_id', $request->academic_year_id)
                ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
                ->where('balance', '>', 0)
                ->orderBy('due_date', 'asc')
                ->get();
        } else {
            $outstandingBalance = $student->outstanding_balance;
            $unpaidInvoices = $student->getUnpaidInvoices();
        }

        return response()->json([
            'success' => true,
            'outstanding_balance' => $outstandingBalance,
            'unpaid_invoices' => $unpaidInvoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'balance' => $invoice->balance,
                    'due_date' => $invoice->due_date->format('Y-m-d'),
                    'status' => $invoice->status,
                    'academic_year' => $invoice->academicYear->name
                ];
            })
        ]);
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function storeInvoice(Request $request)
    {
        $this->authorize('create-invoices');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'fee_ids' => 'nullable|array',
            'fee_ids.*' => 'exists:fees,id',
            'due_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
            'include_outstanding' => 'nullable|boolean',
            'fee_descriptions' => 'nullable|array',
            'fee_descriptions.*' => 'nullable|string|max:255',
            'custom_expenses' => 'nullable|array',
            'custom_expenses.*.description' => 'required_with:custom_expenses|string|max:255',
            'custom_expenses.*.amount' => 'required_with:custom_expenses|numeric|min:0.01'
        ]);

        // Validate that at least one fee or custom expense is provided
        if (empty($request->fee_ids) && empty($request->custom_expenses)) {
            return redirect()->back()
                ->with('error', 'Please select at least one fee or add a custom expense.')
                ->withInput();
        }

        try {
            $student = Student::find($request->student_id);
            $includeOutstanding = $request->boolean('include_outstanding');
            $feeDescriptions = $request->input('fee_descriptions', []);
            $customExpenses = $request->input('custom_expenses', []);

            $invoice = Invoice::createFromFees(
                $student,
                $request->academic_year_id,
                $request->fee_ids ?? [],
                $request->due_date,
                $request->notes,
                $includeOutstanding,
                $feeDescriptions,
                $customExpenses
            );

            $successMessage = 'Invoice created successfully.';
            if ($includeOutstanding) {
                $outstandingBalance = $student->getOutstandingBalanceForYear($request->academic_year_id);
                if ($outstandingBalance > 0) {
                    $successMessage .= ' Outstanding balance of NRs ' . number_format($outstandingBalance, 2) . ' has been included.';
                }
            }

            return redirect()->route('finance.invoices.show', $invoice)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified invoice.
     */
    public function showInvoice(Invoice $invoice)
    {
        $this->authorize('view-finances');

        $invoice->load([
            'student.user',
            'student.department.faculty',
            'student.faculty',
            'academicYear',
            'payments'
        ]);

        // Get college settings
        $collegeSettings = \App\Models\CollegeSetting::getSettings();

        // Get student's enrolled classes for this academic year
        $studentClasses = \App\Models\Enrollment::with(['class.course.faculty', 'class.instructor'])
            ->where('student_id', $invoice->student_id)
            ->where('academic_year_id', $invoice->academic_year_id)
            ->where('status', 'enrolled')
            ->get();

        return view('finance.invoices.show', compact('invoice', 'collegeSettings', 'studentClasses'));
    }

    /**
     * Display the invoice in print-friendly format.
     */
    public function printInvoice(Invoice $invoice)
    {
        $this->authorize('view-finances');

        $invoice->load([
            'student.user',
            'student.department.faculty',
            'student.faculty',
            'academicYear',
            'payments'
        ]);

        // Get college settings
        $collegeSettings = \App\Models\CollegeSetting::getSettings();

        // Get student's enrolled classes for this academic year
        $studentClasses = \App\Models\Enrollment::with(['class.course.faculty', 'class.instructor'])
            ->where('student_id', $invoice->student_id)
            ->where('academic_year_id', $invoice->academic_year_id)
            ->where('status', 'enrolled')
            ->get();

        return view('finance.invoices.print', compact('invoice', 'collegeSettings', 'studentClasses'));
    }

    /**
     * Update invoice status.
     */
    public function updateInvoiceStatus(Request $request, Invoice $invoice)
    {
        $this->authorize('manage-invoices');

        $request->validate([
            'status' => 'required|in:draft,sent,paid,partially_paid,overdue,cancelled'
        ]);

        try {
            $invoice->update(['status' => $request->status]);

            return redirect()->back()
                ->with('success', 'Invoice status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating invoice status: ' . $e->getMessage());
        }
    }

    // ==================== PAYMENT MANAGEMENT ====================

    /**
     * Display a listing of payments.
     */
    public function indexPayments(Request $request)
    {
        $this->authorize('view-finances');

        $query = Payment::with(['student.user', 'invoice', 'receivedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('payment_reference', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('transaction_id', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('student.user', function ($sq) use ($searchTerm) {
                      $sq->where('first_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('last_name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        $payments->appends($request->query());

        return view('finance.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function createPayment(Request $request)
    {
        $this->authorize('create-payments');

        $invoice = null;
        $preSelectedStudent = null;

        if ($request->filled('invoice_id')) {
            $invoice = Invoice::with(['student.user', 'academicYear'])->find($request->invoice_id);
            if ($invoice) {
                $preSelectedStudent = $invoice->student;
            }
        }

        $students = Student::with('user')->orderBy('admission_number')->get();

        return view('finance.payments.create', compact('invoice', 'preSelectedStudent', 'students'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function storePayment(Request $request)
    {
        $this->authorize('create-payments');

        // Custom validation for invoice payments
        if ($request->filled('invoice_id')) {
            $invoice = Invoice::find($request->invoice_id);
            if ($invoice && $request->amount > $invoice->balance) {
                return redirect()->back()
                    ->with('error', 'Payment amount (NRs ' . number_format($request->amount, 2) . ') cannot exceed remaining balance of NRs ' . number_format($invoice->balance, 2))
                    ->withInput();
            }
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_money,cheque,online',
            'transaction_id' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:completed,pending,failed'
        ]);

        try {
            $student = Student::find($request->student_id);

            if ($request->filled('invoice_id')) {
                // Payment for specific invoice
                $invoice = Invoice::find($request->invoice_id);

                $payment = Payment::create([
                    'payment_reference' => Payment::generatePaymentReference(),
                    'student_id' => $student->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $request->transaction_id,
                    'payment_date' => $request->payment_date,
                    'description' => $request->description,
                    'notes' => $request->notes,
                    'status' => $request->status,
                    'received_by' => auth()->id()
                ]);

                // Invoice status will be updated automatically by the Payment model events
            } else {
                // General payment (not tied to specific invoice)
                $payment = Payment::create([
                    'payment_reference' => Payment::generatePaymentReference(),
                    'student_id' => $student->id,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $request->transaction_id,
                    'payment_date' => $request->payment_date,
                    'description' => $request->description,
                    'notes' => $request->notes,
                    'status' => $request->status,
                    'received_by' => auth()->id()
                ]);
            }

            return redirect()->route('finance.payments.show', $payment)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error recording payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified payment.
     */
    public function showPayment(Payment $payment)
    {
        $this->authorize('view-finances');

        $payment->load(['student.user', 'invoice', 'receivedBy', 'verifiedBy']);

        return view('finance.payments.show', compact('payment'));
    }

    /**
     * Verify a payment.
     */
    public function verifyPayment(Payment $payment)
    {
        $this->authorize('verify-payments');

        try {
            if (!$payment->canBeVerified()) {
                return redirect()->back()
                    ->with('error', 'Payment cannot be verified.');
            }

            $payment->verify();

            return redirect()->back()
                ->with('success', 'Payment verified successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a payment.
     */
    public function cancelPayment(Request $request, Payment $payment)
    {
        $this->authorize('manage-payments');

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            if (!$payment->canBeCancelled()) {
                return redirect()->back()
                    ->with('error', 'Payment cannot be cancelled.');
            }

            $payment->cancel($request->reason);

            return redirect()->back()
                ->with('success', 'Payment cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error cancelling payment: ' . $e->getMessage());
        }
    }

    // ==================== TEACHER SALARY MANAGEMENT ====================

    /**
     * Display a listing of teachers for salary management.
     */
    public function indexTeachers(Request $request)
    {
        $this->authorize('manage-salaries');

        $query = Teacher::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('teacher_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('employee_id', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        $teachers = $query->orderBy('teacher_name')->paginate(15);
        $teachers->appends($request->query());

        $departments = Teacher::distinct()->pluck('department')->filter();

        // Load department and faculty information for each teacher
        $teachers->getCollection()->transform(function ($teacher) {
            $teacher->departmentModel = Department::with('faculty')->where('name', $teacher->department)->first();
            return $teacher;
        });

        return view('finance.teachers.index', compact('teachers', 'departments'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function createTeacher()
    {
        $this->authorize('manage-salaries');

        $faculties = Faculty::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('finance.teachers.create', compact('faculties', 'departments'));
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function storeTeacher(Request $request)
    {
        $this->authorize('manage-salaries');

        $request->validate([
            'teacher_name' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:50|unique:teachers',
            'email' => 'required|email|unique:teachers',
            'phone' => 'nullable|string|max:20',
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave,terminated',
            'bank_account' => 'nullable|string|max:255',
            'address' => 'nullable|string'
        ]);

        try {
            // Get department name for employee ID generation
            $department = $request->department_id ? Department::find($request->department_id) : null;
            $departmentName = $department ? $department->name : null;
            $employeeId = $request->employee_id ?: Teacher::generateEmployeeId($departmentName);

            Teacher::create([
                'teacher_name' => $request->teacher_name,
                'employee_id' => $employeeId,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $departmentName, // Store department name for backward compatibility
                'position' => $request->position,
                'hire_date' => $request->hire_date,
                'basic_salary' => $request->basic_salary,
                'status' => $request->status,
                'bank_account' => $request->bank_account,
                'address' => $request->address
            ]);

            return redirect()->route('finance.teachers.index')
                ->with('success', 'Teacher created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating teacher: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified teacher.
     */
    public function showTeacher(Teacher $teacher)
    {
        $this->authorize('manage-salaries');

        $teacher->load('salaryPayments');
        $currentYear = now()->year;
        $salaryHistory = $teacher->getSalaryHistory($currentYear);
        $unpaidMonths = $teacher->getUnpaidMonthsThisYear();

        // Find the department and faculty for this teacher
        $department = Department::with('faculty')->where('name', $teacher->department)->first();

        return view('finance.teachers.show', compact('teacher', 'salaryHistory', 'unpaidMonths', 'department'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function editTeacher(Teacher $teacher)
    {
        $this->authorize('manage-salaries');

        $faculties = Faculty::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('finance.teachers.edit', compact('teacher', 'faculties', 'departments'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function updateTeacher(Request $request, Teacher $teacher)
    {
        $this->authorize('manage-salaries');

        $request->validate([
            'teacher_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:50|unique:teachers,employee_id,' . $teacher->id,
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave,terminated',
            'bank_account' => 'nullable|string|max:255',
            'address' => 'nullable|string'
        ]);

        try {
            // Get department name for storage
            $department = $request->department_id ? Department::find($request->department_id) : null;
            $departmentName = $department ? $department->name : null;

            $teacher->update([
                'teacher_name' => $request->teacher_name,
                'employee_id' => $request->employee_id,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $departmentName, // Store department name for backward compatibility
                'position' => $request->position,
                'hire_date' => $request->hire_date,
                'basic_salary' => $request->basic_salary,
                'status' => $request->status,
                'bank_account' => $request->bank_account,
                'address' => $request->address
            ]);

            return redirect()->route('finance.teachers.index')
                ->with('success', 'Teacher updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating teacher: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ==================== SALARY PAYMENT MANAGEMENT ====================

    /**
     * Display salary payments.
     */
    public function indexSalaryPayments(Request $request)
    {
        $this->authorize('manage-salaries');

        $query = SalaryPayment::with(['teacher']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('faculty_id')) {
            $faculty = Faculty::with('departments')->find($request->faculty_id);
            if ($faculty) {
                $departmentNames = $faculty->departments->pluck('name')->toArray();
                $query->whereHas('teacher', function ($q) use ($departmentNames) {
                    $q->whereIn('department', $departmentNames);
                });
            }
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('teacher', function ($q) use ($searchTerm) {
                $q->where('teacher_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('employee_id', 'LIKE', "%{$searchTerm}%");
            });
        }

        $salaryPayments = $query->orderBy('created_at', 'desc')->paginate(15);
        $salaryPayments->appends($request->query());

        // Statistics
        $totalTeachers = Teacher::active()->count();
        $thisMonthPaid = SalaryPayment::where('month', now()->format('Y-m'))
            ->where('status', 'paid')
            ->sum('amount');

        $faculties = Faculty::orderBy('name')->get();

        // Add faculty information to each salary payment
        $salaryPayments->getCollection()->transform(function ($payment) {
            if ($payment->teacher->department) {
                $department = Department::with('faculty')->where('name', $payment->teacher->department)->first();
                $payment->teacher->facultyInfo = $department ? $department->faculty : null;
            } else {
                $payment->teacher->facultyInfo = null;
            }
            return $payment;
        });

        return view('finance.salaries.index', compact(
            'salaryPayments', 'totalTeachers', 'thisMonthPaid', 'faculties'
        ));
    }

    /**
     * Show the form for creating salary payments.
     */
    public function createSalaryPayment()
    {
        $this->authorize('manage-salaries');

        $teachers = Teacher::orderBy('teacher_name')->get();
        $faculties = Faculty::orderBy('name')->get();
        $recentPayments = SalaryPayment::with(['teacher'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Add faculty information to teachers
        $teachers->each(function ($teacher) {
            if ($teacher->department) {
                $department = Department::with('faculty')->where('name', $teacher->department)->first();
                $teacher->facultyInfo = $department ? $department->faculty : null;
            } else {
                $teacher->facultyInfo = null;
            }
        });

        return view('finance.salaries.create', compact('teachers', 'faculties', 'recentPayments'));
    }

    /**
     * Process salary payment for a teacher.
     */
    public function storeSalaryPayment(Request $request)
    {
        $this->authorize('manage-salaries');

        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'month' => 'required|date_format:Y-m',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date'
        ]);

        try {
            $teacher = Teacher::find($request->teacher_id);
            $year = (int) substr($request->month, 0, 4);
            $month = (int) substr($request->month, -2);

            // Check if teacher can receive salary for this month
            [$canReceive, $message] = $teacher->canReceiveSalaryForMonth($year, $month);

            if (!$canReceive) {
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            SalaryPayment::create([
                'teacher_id' => $request->teacher_id,
                'month' => $request->month,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'status' => 'paid'
            ]);

            return redirect()->route('finance.salaries.index')
                ->with('success', 'Salary payment processed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing salary payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the bulk salary processing form.
     */
    public function bulkProcessSalaries()
    {
        $this->authorize('manage-salaries');

        $teachers = Teacher::active()->orderBy('teacher_name')->get();
        $faculties = Faculty::orderBy('name')->get();

        // Add faculty information to teachers
        $teachers->each(function ($teacher) {
            if ($teacher->department) {
                $department = Department::with('faculty')->where('name', $teacher->department)->first();
                $teacher->facultyInfo = $department ? $department->faculty : null;
            } else {
                $teacher->facultyInfo = null;
            }
        });

        // Get current month for default selection
        $currentMonth = now()->format('Y-m');

        // Get teachers who haven't received salary for current month
        $unpaidTeachers = $teachers->filter(function ($teacher) use ($currentMonth) {
            $year = (int) substr($currentMonth, 0, 4);
            $month = (int) substr($currentMonth, -2);
            [$canReceive, $message] = $teacher->canReceiveSalaryForMonth($year, $month);
            return $canReceive;
        });

        return view('finance.salaries.bulk-process', compact('teachers', 'faculties', 'unpaidTeachers', 'currentMonth'));
    }

    /**
     * Process bulk salary payments.
     */
    public function bulkSalaryPayment(Request $request)
    {
        $this->authorize('manage-salaries');

        $request->validate([
            'month' => 'required|date_format:Y-m',
            'teacher_ids' => 'required|array|min:1',
            'teacher_ids.*' => 'exists:teachers,id',
            'payment_date' => 'required|date'
        ]);

        try {
            $year = (int) substr($request->month, 0, 4);
            $month = (int) substr($request->month, -2);
            $successCount = 0;
            $errors = [];

            DB::transaction(function () use ($request, $year, $month, &$successCount, &$errors) {
                foreach ($request->teacher_ids as $teacherId) {
                    $teacher = Teacher::find($teacherId);

                    [$canReceive, $message] = $teacher->canReceiveSalaryForMonth($year, $month);

                    if ($canReceive) {
                        SalaryPayment::create([
                            'teacher_id' => $teacherId,
                            'month' => $request->month,
                            'amount' => $teacher->basic_salary,
                            'payment_date' => $request->payment_date,
                            'status' => 'paid'
                        ]);
                        $successCount++;
                    } else {
                        $errors[] = "{$teacher->teacher_name}: {$message}";
                    }
                }
            });

            $message = "Successfully processed {$successCount} salary payments.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->route('finance.salaries.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing bulk salary payments: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified salary payment.
     */
    public function showSalaryPayment(SalaryPayment $salaryPayment)
    {
        $this->authorize('manage-salaries');

        $salaryPayment->load(['teacher']);

        // Add faculty information to teacher
        if ($salaryPayment->teacher->department) {
            $department = Department::with('faculty')->where('name', $salaryPayment->teacher->department)->first();
            $salaryPayment->teacher->facultyInfo = $department ? $department->faculty : null;
        } else {
            $salaryPayment->teacher->facultyInfo = null;
        }

        return view('finance.salaries.show', compact('salaryPayment'));
    }

    /**
     * Approve a salary payment.
     */
    public function approveSalaryPayment(SalaryPayment $salaryPayment)
    {
        $this->authorize('manage-salaries');

        try {
            if ($salaryPayment->status !== 'pending') {
                return redirect()->back()
                    ->with('error', 'Only pending salary payments can be approved.');
            }

            $salaryPayment->update([
                'status' => 'paid',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            return redirect()->back()
                ->with('success', 'Salary payment approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error approving salary payment: ' . $e->getMessage());
        }
    }

    // ==================== FINANCIAL REPORTS ====================

    /**
     * Display financial reports index.
     */
    public function indexReports()
    {
        $this->authorize('view-financial-reports');

        $students = Student::with('user')->orderBy('admission_number')->get();
        $academicYears = AcademicYear::orderBy('name')->get();

        return view('finance.reports.index', compact('students', 'academicYears'));
    }

    /**
     * Display financial reports.
     */
    public function reports()
    {
        $this->authorize('view-financial-reports');

        return view('finance.reports.index');
    }

    /**
     * Generate student fee statement.
     */
    public function studentFeeStatement(Request $request)
    {
        $this->authorize('view-financial-reports');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'nullable|exists:academic_years,id'
        ]);

        $student = Student::with('user')->find($request->student_id);
        $academicYearId = $request->academic_year_id;

        $invoicesQuery = $student->invoices()->with(['academicYear', 'payments']);

        if ($academicYearId) {
            $invoicesQuery->where('academic_year_id', $academicYearId);
        }

        $invoices = $invoicesQuery->orderBy('created_at', 'desc')->get();
        $totalBilled = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('amount_paid');
        $outstandingBalance = $totalBilled - $totalPaid;

        return view('finance.reports.student-statement', compact(
            'student', 'invoices', 'totalBilled', 'totalPaid', 'outstandingBalance'
        ));
    }

    /**
     * Generate payment report.
     */
    public function paymentReport(Request $request)
    {
        $this->authorize('view-financial-reports');

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'payment_method' => 'nullable|in:cash,bank_transfer,card,mobile_money,cheque,online'
        ]);

        $query = Payment::completed()
            ->with(['student.user', 'invoice'])
            ->whereBetween('payment_date', [$request->date_from, $request->date_to]);

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();
        $totalAmount = $payments->sum('amount');
        $paymentSummary = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount')
            ];
        });

        return view('finance.reports.payment-report', compact(
            'payments', 'totalAmount', 'paymentSummary', 'request'
        ));
    }

    /**
     * Generate outstanding dues report.
     */
    public function outstandingDuesReport(Request $request)
    {
        $this->authorize('view-financial-reports');

        $query = Invoice::with(['student.user', 'academicYear'])
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->where('balance', '>', 0);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('overdue_only')) {
            $query->where('due_date', '<', now());
        }

        $invoices = $query->orderBy('due_date')->get();
        $totalOutstanding = $invoices->sum('balance');
        $overdueAmount = $invoices->where('due_date', '<', now())->sum('balance');

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('finance.reports.outstanding-dues', compact(
            'invoices', 'totalOutstanding', 'overdueAmount', 'academicYears'
        ));
    }

    /**
     * Generate salary report.
     */
    public function salaryReport(Request $request)
    {
        $this->authorize('view-financial-reports');

        // If no parameters provided, show the form
        if (!$request->hasAny(['year', 'month', 'department'])) {
            $departments = Teacher::distinct()->pluck('department')->filter()->sort();
            return view('finance.reports.salary-report', compact('departments'));
        }

        // Validate the form data
        $request->validate([
            'year' => 'required|integer|min:2020|max:' . (now()->year + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'department' => 'nullable|string'
        ]);

        $query = SalaryPayment::with('teacher')
            ->where('status', 'paid')
            ->whereYear('payment_date', $request->year);

        if ($request->filled('month')) {
            $query->whereMonth('payment_date', $request->month);
        }

        if ($request->filled('department')) {
            $query->whereHas('teacher', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $salaryPayments = $query->orderBy('payment_date', 'desc')->get();
        $totalAmount = $salaryPayments->sum('amount');
        $departmentSummary = $salaryPayments->groupBy('teacher.department')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount')
            ];
        });

        return view('finance.reports.salary-report', compact(
            'salaryPayments', 'totalAmount', 'departmentSummary', 'request'
        ));
    }

    /**
     * Export salary data as CSV.
     */
    public function exportSalaries(Request $request)
    {
        $this->authorize('view-financial-reports');

        $request->validate([
            'year' => 'nullable|integer|min:2020|max:' . (now()->year + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'faculty_id' => 'nullable|exists:faculties,id',
            'status' => 'nullable|in:pending,paid,cancelled'
        ]);

        $query = SalaryPayment::with(['teacher']);

        // Apply filters
        if ($request->filled('year')) {
            $query->whereYear('payment_date', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('payment_date', $request->month);
        }

        if ($request->filled('faculty_id')) {
            $faculty = Faculty::with('departments')->find($request->faculty_id);
            if ($faculty) {
                $departmentNames = $faculty->departments->pluck('name')->toArray();
                $query->whereHas('teacher', function ($q) use ($departmentNames) {
                    $q->whereIn('department', $departmentNames);
                });
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $salaryPayments = $query->orderBy('payment_date', 'desc')->get();

        $filename = 'salary-export-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($salaryPayments) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Employee ID',
                'Teacher Name',
                'Faculty',
                'Department',
                'Month',
                'Amount',
                'Payment Date',
                'Status',
                'Created At'
            ]);

            // Add data rows
            foreach ($salaryPayments as $payment) {
                // Get faculty information
                $facultyName = 'N/A';
                if ($payment->teacher->department) {
                    $department = Department::with('faculty')->where('name', $payment->teacher->department)->first();
                    $facultyName = $department && $department->faculty ? $department->faculty->name : 'N/A';
                }

                fputcsv($file, [
                    $payment->teacher->employee_id ?? 'N/A',
                    $payment->teacher->teacher_name,
                    $facultyName,
                    $payment->teacher->department ?? 'N/A',
                    $payment->month,
                    number_format($payment->amount, 2),
                    $payment->payment_date->format('Y-m-d'),
                    ucfirst($payment->status),
                    $payment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export student fee statement as PDF.
     */
    public function exportStudentStatement(Request $request)
    {
        $this->authorize('view-financial-reports');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'nullable|exists:academic_years,id'
        ]);

        $student = Student::with('user')->find($request->student_id);
        $academicYearId = $request->academic_year_id;

        $invoicesQuery = $student->invoices()->with(['academicYear', 'payments']);

        if ($academicYearId) {
            $invoicesQuery->where('academic_year_id', $academicYearId);
        }

        $invoices = $invoicesQuery->orderBy('created_at', 'desc')->get();
        $totalBilled = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('amount_paid');
        $outstandingBalance = $totalBilled - $totalPaid;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('finance.reports.student-statement-pdf', compact(
            'student', 'invoices', 'totalBilled', 'totalPaid', 'outstandingBalance'
        ));

        return $pdf->download("fee-statement-{$student->admission_number}.pdf");
    }





    // ==================== EXPENSE MANAGEMENT ====================

    /**
     * Display a listing of expenses.
     */
    public function indexExpenses(Request $request)
    {
        $this->authorize('view-finances');

        $query = Expense::with(['department', 'createdBy', 'approvedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('expense_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('vendor_name', 'LIKE', "%{$searchTerm}%");
            });
        }

        $expenses = $query->orderBy('created_at', 'desc')->paginate(15);
        $expenses->appends($request->query());

        $departments = Department::orderBy('name')->get();
        $categories = [
            'utilities', 'maintenance', 'supplies', 'equipment', 'travel',
            'training', 'marketing', 'insurance', 'rent', 'food',
            'transportation', 'communication', 'professional_services', 'other'
        ];

        return view('finance.expenses.index', compact('expenses', 'departments', 'categories'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function createExpense()
    {
        $this->authorize('manage-expenses');

        $departments = Department::orderBy('name')->get();
        $categories = [
            'utilities' => 'Utilities',
            'maintenance' => 'Maintenance',
            'supplies' => 'Supplies',
            'equipment' => 'Equipment',
            'travel' => 'Travel',
            'training' => 'Training',
            'marketing' => 'Marketing',
            'insurance' => 'Insurance',
            'rent' => 'Rent',
            'food' => 'Food',
            'transportation' => 'Transportation',
            'communication' => 'Communication',
            'professional_services' => 'Professional Services',
            'other' => 'Other'
        ];

        return view('finance.expenses.create', compact('departments', 'categories'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function storeExpense(Request $request)
    {
        $this->authorize('manage-expenses');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:utilities,maintenance,supplies,equipment,travel,training,marketing,insurance,rent,food,transportation,communication,professional_services,other',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,card,online,other',
            'vendor_name' => 'nullable|string|max:255',
            'vendor_contact' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string'
        ]);

        try {
            $expenseNumber = Expense::generateExpenseNumber();

            Expense::create([
                'expense_number' => $expenseNumber,
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'payment_method' => $request->payment_method,
                'vendor_name' => $request->vendor_name,
                'vendor_contact' => $request->vendor_contact,
                'receipt_number' => $request->receipt_number,
                'department_id' => $request->department_id,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => auth()->id()
            ]);

            return redirect()->route('finance.expenses.index')
                ->with('success', 'Expense created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating expense: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified expense.
     */
    public function showExpense(Expense $expense)
    {
        $this->authorize('view-finances');

        $expense->load(['department', 'createdBy', 'approvedBy']);

        return view('finance.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function editExpense(Expense $expense)
    {
        $this->authorize('manage-expenses');

        if (!$expense->canBeEdited()) {
            return redirect()->route('finance.expenses.show', $expense)
                ->with('error', 'This expense cannot be edited.');
        }

        $departments = Department::orderBy('name')->get();
        $categories = [
            'utilities' => 'Utilities',
            'maintenance' => 'Maintenance',
            'supplies' => 'Supplies',
            'equipment' => 'Equipment',
            'travel' => 'Travel',
            'training' => 'Training',
            'marketing' => 'Marketing',
            'insurance' => 'Insurance',
            'rent' => 'Rent',
            'food' => 'Food',
            'transportation' => 'Transportation',
            'communication' => 'Communication',
            'professional_services' => 'Professional Services',
            'other' => 'Other'
        ];

        return view('finance.expenses.edit', compact('expense', 'departments', 'categories'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function updateExpense(Request $request, Expense $expense)
    {
        $this->authorize('manage-expenses');

        if (!$expense->canBeEdited()) {
            return redirect()->route('finance.expenses.show', $expense)
                ->with('error', 'This expense cannot be edited.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:utilities,maintenance,supplies,equipment,travel,training,marketing,insurance,rent,food,transportation,communication,professional_services,other',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,card,online,other',
            'vendor_name' => 'nullable|string|max:255',
            'vendor_contact' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string'
        ]);

        try {
            $expense->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'payment_method' => $request->payment_method,
                'vendor_name' => $request->vendor_name,
                'vendor_contact' => $request->vendor_contact,
                'receipt_number' => $request->receipt_number,
                'department_id' => $request->department_id,
                'notes' => $request->notes
            ]);

            return redirect()->route('finance.expenses.index')
                ->with('success', 'Expense updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating expense: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Approve an expense.
     */
    public function approveExpense(Expense $expense)
    {
        $this->authorize('approve-expenses');

        if (!$expense->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'This expense cannot be approved.');
        }

        try {
            $expense->approve(auth()->id());

            return redirect()->back()
                ->with('success', 'Expense approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error approving expense: ' . $e->getMessage());
        }
    }

    /**
     * Reject an expense.
     */
    public function rejectExpense(Request $request, Expense $expense)
    {
        $this->authorize('approve-expenses');

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if (!$expense->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'This expense cannot be rejected.');
        }

        try {
            $expense->reject(auth()->id(), $request->reason);

            return redirect()->back()
                ->with('success', 'Expense rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error rejecting expense: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroyExpense(Expense $expense)
    {
        $this->authorize('manage-expenses');

        if (!$expense->canBeEdited()) {
            return redirect()->route('finance.expenses.index')
                ->with('error', 'This expense cannot be deleted.');
        }

        try {
            $expense->delete();

            return redirect()->route('finance.expenses.index')
                ->with('success', 'Expense deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting expense: ' . $e->getMessage());
        }
    }

    /**
     * Get expense analytics data.
     */
    public function expenseAnalytics(Request $request)
    {
        $this->authorize('view-financial-reports');

        $year = $request->get('year', now()->year);
        $month = $request->get('month');

        $query = Expense::approved();

        if ($year) {
            $query->whereYear('expense_date', $year);
        }

        if ($month) {
            $query->whereMonth('expense_date', $month);
        }

        $totalExpenses = $query->sum('amount');
        $expensesByCategory = $query->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $monthlyExpenses = Expense::approved()
            ->whereYear('expense_date', $year)
            ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        return response()->json([
            'total_expenses' => $totalExpenses,
            'expenses_by_category' => $expensesByCategory,
            'monthly_expenses' => $monthlyExpenses
        ]);
    }
}
