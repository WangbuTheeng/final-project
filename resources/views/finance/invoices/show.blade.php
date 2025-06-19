@extends('layouts.dashboard')

@section('title', 'Invoice Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header (No Print) -->
    <div class="flex justify-between items-center mb-6 no-print">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Invoice Details</h1>
            <p class="text-gray-600 mt-2">Invoice #{{ $invoice->invoice_number }}</p>
        </div>
        <div class="flex space-x-3">
            @can('manage-invoices')
                <form action="{{ route('finance.invoices.update-status', $invoice) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="{{ $invoice->status == 'draft' ? 'sent' : 'draft' }}">
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        {{ $invoice->status == 'draft' ? 'Send Invoice' : 'Mark as Draft' }}
                    </button>
                </form>
            @endcan
            <a href="{{ route('finance.invoices.print', $invoice) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Invoice
            </a>
            <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Quick Print
            </button>
            <a href="{{ route('finance.invoices.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Invoices
            </a>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Side - Invoice Details (2/3 width) -->
        <div class="lg:col-span-2 order-1 lg:order-1">
            <div class="bg-white rounded-lg shadow-md p-8 print:shadow-none print:p-0">
        <!-- College Header -->
        <div class="text-center border-b-2 border-gray-300 pb-6 mb-8">
            <div class="flex items-center justify-center mb-4">
                @if($collegeSettings->logo_path)
                    <img src="{{ asset('storage/' . $collegeSettings->logo_path) }}" alt="College Logo" class="h-16 w-16 mr-4">
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $collegeSettings->college_name }}</h1>
                    <p class="text-lg text-gray-600">{{ $collegeSettings->college_address }}</p>
                    @if($collegeSettings->college_phone || $collegeSettings->college_email)
                        <div class="text-sm text-gray-500 mt-1">
                            @if($collegeSettings->college_phone)
                                <span>Phone: {{ $collegeSettings->college_phone }}</span>
                            @endif
                            @if($collegeSettings->college_phone && $collegeSettings->college_email)
                                <span class="mx-2">|</span>
                            @endif
                            @if($collegeSettings->college_email)
                                <span>Email: {{ $collegeSettings->college_email }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">INVOICE</h2>
                <p class="text-xl text-gray-600 mt-1">#{{ $invoice->invoice_number }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium print:bg-gray-200 print:text-gray-800
                    @if($invoice->status == 'paid') bg-green-100 text-green-800
                    @elseif($invoice->status == 'partial') bg-yellow-100 text-yellow-800
                    @elseif($invoice->status == 'overdue') bg-red-100 text-red-800
                    @elseif($invoice->status == 'sent') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>
        </div>

        <!-- Student and Invoice Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Student Information -->
            <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Student Information</h3>
                <div class="text-gray-700 space-y-2">
                    <p><span class="font-medium">Name:</span> {{ $invoice->student->user->name }}</p>
                    <p><span class="font-medium">Student ID:</span> {{ $invoice->student->student_id }}</p>
                    <p><span class="font-medium">Admission Number:</span> {{ $invoice->student->admission_number ?? 'N/A' }}</p>
                    @if($invoice->student->user->email)
                        <p><span class="font-medium">Email:</span> {{ $invoice->student->user->email }}</p>
                    @endif
                    @if($invoice->student->user->phone)
                        <p><span class="font-medium">Phone:</span> {{ $invoice->student->user->phone }}</p>
                    @endif
                    @if($invoice->student->faculty)
                        <p><span class="font-medium">Faculty:</span> {{ $invoice->student->faculty->name }}</p>
                    @endif
                    @if($invoice->student->department)
                        <p><span class="font-medium">Department:</span> {{ $invoice->student->department->name }}</p>
                    @endif
                </div>
            </div>

            <!-- Invoice Details -->
            <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Invoice Details</h3>
                <div class="text-gray-700 space-y-2">
                    <p><span class="font-medium">Issue Date:</span> {{ $invoice->issue_date->format('M d, Y') }}</p>
                    <p><span class="font-medium">Due Date:</span> {{ $invoice->due_date->format('M d, Y') }}</p>
                    <p><span class="font-medium">Academic Year:</span> {{ $invoice->academicYear->name }}</p>
                    @if($invoice->paid_date)
                        <p><span class="font-medium">Paid Date:</span> {{ $invoice->paid_date->format('M d, Y') }}</p>
                    @endif
                    <p><span class="font-medium">Created By:</span> {{ $invoice->creator->name ?? 'System' }}</p>
                </div>
            </div>
        </div>

        <!-- Enrolled Classes Information -->
        @if($studentClasses->count() > 0)
        <!-- <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Enrolled Classes</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($studentClasses as $enrollment)
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <h4 class="font-semibold text-gray-900">{{ $enrollment->class->course->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $enrollment->class->course->code }}</p>
                        <p class="text-sm text-gray-600">Class: {{ $enrollment->class->name }}</p>
                        <p class="text-sm text-gray-600">Semester: {{ $enrollment->class->semester }}</p>
                        @if($enrollment->class->instructor)
                            <p class="text-sm text-gray-600">Instructor: {{ $enrollment->class->instructor->name }}</p>
                        @endif
                        <p class="text-sm text-gray-600">Faculty: {{ $enrollment->class->course->faculty->name }}</p>
                    </div>
                @endforeach
            </div>
        </div> -->
        @endif

        <!-- Fee Items -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Fee Details</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->line_items as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $item['fee_name'] }}</div>
                                @if(isset($item['description']) && !empty($item['description']))
                                    <div class="text-xs text-gray-600 mt-1 italic">{{ $item['description'] }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ $item['fee_code'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-center">{{ $item['quantity'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">NRs {{ number_format($item['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="border-t-2 border-gray-300 pt-6">
            <div class="flex justify-end">
                <div class="w-80 space-y-3">
                    <div class="flex justify-between text-lg">
                        <span class="text-gray-700 font-medium">Subtotal:</span>
                        <span class="text-gray-900 font-medium">NRs {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    @if($invoice->discount > 0)
                    <div class="flex justify-between text-lg">
                        <span class="text-gray-700 font-medium">Discount:</span>
                        <span class="text-red-600 font-medium">-NRs {{ number_format($invoice->discount, 2) }}</span>
                    </div>
                    @endif
                    @if($invoice->tax > 0)
                    <div class="flex justify-between text-lg">
                        <span class="text-gray-700 font-medium">Tax:</span>
                        <span class="text-gray-900 font-medium">NRs {{ number_format($invoice->tax, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-2xl font-bold border-t-2 border-gray-300 pt-3">
                        <span class="text-gray-900">Total Amount:</span>
                        <span class="text-gray-900">NRs {{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    @if($invoice->amount_paid > 0)
                    <div class="flex justify-between text-lg">
                        <span class="text-gray-700 font-medium">Amount Paid:</span>
                        <span class="text-green-600 font-medium">NRs {{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    @endif
                    @if($invoice->balance > 0)
                    <div class="flex justify-between text-xl font-bold text-red-600 border-t border-gray-200 pt-2">
                        <span>Balance Due:</span>
                        <span>NRs {{ number_format($invoice->balance, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($invoice->notes)
        <!-- Notes -->
        <div class="border-t border-gray-200 pt-6 mt-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Notes</h3>
            <p class="text-gray-700 text-lg">{{ $invoice->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="border-t-2 border-gray-300 pt-6 mt-8 text-center">
            <p class="text-sm text-gray-500">
                This invoice was generated on {{ now()->format('M d, Y \a\t g:i A') }}
            </p>
            <p class="text-sm text-gray-500 mt-1">
                For any queries, please contact the finance office at {{ $collegeSettings->college_email ?? $collegeSettings->college_phone }}
            </p>
        </div>
            </div>
        </div>

        <!-- Right Side - Sidebar Information (1/3 width) -->
        <div class="lg:col-span-1 order-2 lg:order-2 no-print">
            <div class="space-y-6">
            <!-- Quick Payment Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium">NRs {{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount Paid:</span>
                        <span class="font-medium text-green-600">NRs {{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <span class="text-gray-900 font-semibold">Balance Due:</span>
                        <span class="font-semibold {{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            NRs {{ number_format($invoice->balance, 2) }}
                        </span>
                    </div>
                </div>

                @if($invoice->balance > 0)
                <div class="mt-4 pt-4 border-t">
                    @can('create-payments')
                        <a href="{{ route('finance.payments.create', ['invoice_id' => $invoice->id]) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-center block">
                            Record Payment
                        </a>
                    @endcan
                </div>
                @endif
            </div>

            <!-- Payment History -->
            @if($invoice->payments->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
                <div class="space-y-3">
                    @foreach($invoice->payments as $payment)
                    <div class="border-l-4 border-green-400 pl-4 py-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">NRs {{ number_format($payment->amount, 2) }}</p>
                                <p class="text-sm text-gray-600">{{ $payment->payment_date->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($payment->status == 'completed') bg-green-100 text-green-800
                                @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                        @if($payment->description)
                            <p class="text-sm text-gray-600 mt-1">{{ $payment->description }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Student Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $invoice->student->user->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $invoice->student->student_id }}</p>
                        </div>
                    </div>

                    <div class="border-t pt-3 space-y-2 text-sm">
                        @if($invoice->student->admission_number)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Admission No:</span>
                                <span class="font-medium">{{ $invoice->student->admission_number }}</span>
                            </div>
                        @endif
                        @if($invoice->student->user->email)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium text-xs">{{ $invoice->student->user->email }}</span>
                            </div>
                        @endif
                        @if($invoice->student->user->phone)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-medium">{{ $invoice->student->user->phone }}</span>
                            </div>
                        @endif
                        @if($invoice->student->faculty)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Faculty:</span>
                                <span class="font-medium">{{ $invoice->student->faculty->name }}</span>
                            </div>
                        @endif
                        @if($invoice->student->department)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Department:</span>
                                <span class="font-medium">{{ $invoice->student->department->name }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Academic Year:</span>
                            <span class="font-medium">{{ $invoice->academicYear->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @can('view-financial-reports')
                        <a href="{{ route('finance.reports.student-statement', ['student_id' => $invoice->student_id, 'academic_year_id' => $invoice->academic_year_id]) }}"
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-center block">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Full Statement
                        </a>
                    @endcan

                    @can('create-invoices')
                        <a href="{{ route('finance.invoices.create', ['student_id' => $invoice->student_id, 'academic_year_id' => $invoice->academic_year_id]) }}"
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-center block">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create New Invoice
                        </a>
                    @endcan

                    <a href="{{ route('finance.invoices.index', ['search' => $invoice->student->user->name]) }}"
                       class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-center block">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        View All Invoices
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }

    body {
        font-size: 14px;
        line-height: 1.4;
        color: #000;
        background: white;
    }

    .container {
        max-width: none;
        padding: 0;
        margin: 0;
    }

    /* Print layout adjustments */
    .grid {
        display: block !important;
    }

    .lg\\:col-span-2,
    .lg\\:col-span-1 {
        width: 100% !important;
    }

    .bg-white {
        background: white !important;
    }

    .shadow-md {
        box-shadow: none !important;
    }

    .rounded-lg {
        border-radius: 0 !important;
    }

    .border-gray-200,
    .border-gray-300 {
        border-color: #000 !important;
    }

    .text-gray-900 {
        color: #000 !important;
    }

    .text-gray-700 {
        color: #333 !important;
    }

    .text-gray-600 {
        color: #555 !important;
    }

    .text-gray-500 {
        color: #666 !important;
    }

    .bg-gray-50 {
        background-color: #f8f9fa !important;
    }

    /* Ensure proper page breaks */
    .page-break {
        page-break-before: always;
    }

    /* Table styling for print */
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #000;
        padding: 8px;
    }

    th {
        background-color: #f0f0f0 !important;
        font-weight: bold;
    }

    /* Hide background colors in print */
    .bg-green-100,
    .bg-yellow-100,
    .bg-red-100,
    .bg-blue-100,
    .bg-gray-100 {
        background-color: #f0f0f0 !important;
        color: #000 !important;
    }
}

/* Screen styles */
@media screen {
    .print-only {
        display: none;
    }
}
</style>
@endsection
