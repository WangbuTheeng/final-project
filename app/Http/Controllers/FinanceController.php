<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
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
     * Display a listing of the fees.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexFees()
    {
        // Check if user has permission to view finances
        $this->authorize('view-finances');
        
        $fees = Fee::with('academicYear')->get();
        return view('finance.fees.index', compact('fees'));
    }
    
    /**
     * Show the form for creating a new fee.
     *
     * @return \Illuminate\Http\Response
     */
    public function createFee()
    {
        // Check if user has permission to manage fees
        $this->authorize('manage-fees');
        
        $academicYears = AcademicYear::all();
        return view('finance.fees.create', compact('academicYears'));
    }
    
    /**
     * Store a newly created fee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFee(Request $request)
    {
        // Check if user has permission to manage fees
        $this->authorize('manage-fees');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'description' => ['nullable', 'string'],
        ]);
        
        Fee::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'academic_year_id' => $request->academic_year_id,
            'description' => $request->description,
        ]);
        
        return redirect()->route('finance.fees.index')
            ->with('success', 'Fee created successfully.');
    }
    
    /**
     * Display the specified fee.
     *
     * @param  \App\Models\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function showFee(Fee $fee)
    {
        // Check if user has permission to view finances
        $this->authorize('view-finances');
        
        $fee->load('academicYear');
        return view('finance.fees.show', compact('fee'));
    }
    
    /**
     * Show the form for editing the specified fee.
     *
     * @param  \App\Models\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function editFee(Fee $fee)
    {
        // Check if user has permission to manage fees
        $this->authorize('manage-fees');
        
        $academicYears = AcademicYear::all();
        return view('finance.fees.edit', compact('fee', 'academicYears'));
    }
    
    /**
     * Update the specified fee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function updateFee(Request $request, Fee $fee)
    {
        // Check if user has permission to manage fees
        $this->authorize('manage-fees');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'description' => ['nullable', 'string'],
        ]);
        
        $fee->name = $request->name;
        $fee->amount = $request->amount;
        $fee->academic_year_id = $request->academic_year_id;
        $fee->description = $request->description;
        $fee->save();
        
        return redirect()->route('finance.fees.index')
            ->with('success', 'Fee updated successfully.');
    }
    
    /**
     * Remove the specified fee from storage.
     *
     * @param  \App\Models\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function destroyFee(Fee $fee)
    {
        // Check if user has permission to manage fees
        $this->authorize('manage-fees');
        
        // Check if there are any invoices associated with this fee
        // This should be implemented based on your specific relationships
        
        $fee->delete();
        
        return redirect()->route('finance.fees.index')
            ->with('success', 'Fee deleted successfully.');
    }
    
    /**
     * Display a listing of invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexInvoices()
    {
        // Check if user has permission to view finances
        $this->authorize('view-finances');
        
        $invoices = Invoice::with(['student', 'fee'])->get();
        return view('finance.invoices.index', compact('invoices'));
    }
    
    /**
     * Show the form for creating a new invoice.
     *
     * @return \Illuminate\Http\Response
     */
    public function createInvoice()
    {
        // Check if user has permission to create invoices
        $this->authorize('create-invoices');
        
        $students = Student::all();
        $fees = Fee::all();
        return view('finance.invoices.create', compact('students', 'fees'));
    }
    
    /**
     * Store a newly created invoice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeInvoice(Request $request)
    {
        // Check if user has permission to create invoices
        $this->authorize('create-invoices');
        
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'fee_id' => ['required', 'exists:fees,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);
        
        $invoice = Invoice::create([
            'student_id' => $request->student_id,
            'fee_id' => $request->fee_id,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'status' => 'unpaid', // Default status
        ]);
        
        return redirect()->route('finance.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }
    
    /**
     * Display the specified invoice.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function showInvoice(Invoice $invoice)
    {
        // Check if user has permission to view finances
        $this->authorize('view-finances');
        
        $invoice->load(['student', 'fee', 'payments']);
        
        // Calculate total paid amount
        $totalPaid = $invoice->payments->sum('amount');
        $remainingDue = $invoice->amount - $totalPaid;
        
        return view('finance.invoices.show', compact('invoice', 'totalPaid', 'remainingDue'));
    }
    
    /**
     * Process payment for an invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        // Check if user has permission to process payments
        $this->authorize('process-payments');
        
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string'],
            'reference_number' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
        
        // Start a transaction to ensure data integrity
        DB::beginTransaction();
        
        try {
            // Create the payment
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);
            
            // Calculate total paid amount
            $totalPaid = $invoice->payments()->sum('amount') + $request->amount;
            
            // Update invoice status
            if ($totalPaid >= $invoice->amount) {
                $invoice->status = 'paid';
            } else if ($totalPaid > 0) {
                $invoice->status = 'partial';
            }
            
            $invoice->save();
            
            DB::commit();
            
            return redirect()->route('finance.invoices.show', $invoice)
                ->with('success', 'Payment processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error processing payment: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display student fee statement.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function studentFeeStatement(Student $student)
    {
        // Check if user has permission to view finances
        $this->authorize('view-finances');
        
        $invoices = Invoice::with(['fee', 'payments'])
            ->where('student_id', $student->id)
            ->get();
        
        // Calculate totals
        $totalBilled = $invoices->sum('amount');
        $totalPaid = 0;
        
        foreach ($invoices as $invoice) {
            $invoice->paid_amount = $invoice->payments->sum('amount');
            $invoice->due_amount = $invoice->amount - $invoice->paid_amount;
            $totalPaid += $invoice->paid_amount;
        }
        
        $totalDue = $totalBilled - $totalPaid;
        
        return view('finance.statements.student', compact('student', 'invoices', 'totalBilled', 'totalPaid', 'totalDue'));
    }
    
    /**
     * Generate PDF fee statement for a student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function generateFeeStatementPDF(Student $student)
    {
        // Check if user has permission to view finances
        $this->authorize('view-finances');
        
        // This should be implemented using a PDF library like Dompdf
        // For now, we'll just return a view
        
        return redirect()->route('finance.statements.student', $student)
            ->with('info', 'PDF generation would happen here in the full implementation.');
    }
}
