@extends('layouts.dashboard')

@section('title', 'Payment Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Payment Details</h1>
            <p class="text-gray-600 mt-2">Payment Reference: {{ $payment->payment_reference }}</p>
        </div>
        <div class="flex space-x-3">
            @can('verify-payments')
                @if($payment->status == 'pending')
                    <form action="{{ route('finance.payments.verify', $payment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verify Payment
                        </button>
                    </form>
                @endif
            @endcan
            <a href="{{ route('finance.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Payments
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Payment Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">PAYMENT RECEIPT</h2>
                        <p class="text-gray-600">{{ $payment->payment_reference }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($payment->status == 'completed') bg-green-100 text-green-800
                            @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status == 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>

                <!-- Student and Payment Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Student Information:</h3>
                        <div class="text-gray-700">
                            <p class="font-medium">{{ $payment->student->user->name }}</p>
                            <p>Student ID: {{ $payment->student->student_id }}</p>
                            @if($payment->student->user->email)
                                <p>{{ $payment->student->user->email }}</p>
                            @endif
                            @if($payment->student->user->phone)
                                <p>{{ $payment->student->user->phone }}</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Details:</h3>
                        <div class="text-gray-700 space-y-1">
                            <p><span class="font-medium">Payment Date:</span> {{ $payment->payment_date->format('M d, Y \a\t g:i A') }}</p>
                            <p><span class="font-medium">Payment Method:</span> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            @if($payment->transaction_id)
                                <p><span class="font-medium">Transaction ID:</span> {{ $payment->transaction_id }}</p>
                            @endif
                            @if($payment->verified_at)
                                <p><span class="font-medium">Verified At:</span> {{ $payment->verified_at->format('M d, Y \a\t g:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Amount Information -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment Amount</h3>
                        <p class="text-4xl font-bold text-green-600">₹{{ number_format($payment->amount, 2) }}</p>
                    </div>
                </div>

                @if($payment->description)
                <!-- Description -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-700">{{ $payment->description }}</p>
                </div>
                @endif

                @if($payment->payment_details && count($payment->payment_details) > 0)
                <!-- Payment Details -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Details</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($payment->payment_details as $key => $value)
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</dt>
                                    <dd class="text-sm text-gray-900">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
                @endif

                @if($payment->notes)
                <!-- Internal Notes -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Internal Notes</h3>
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $payment->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Related Invoice -->
            @if($payment->invoice)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Invoice</h3>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-medium text-gray-900">Invoice #{{ $payment->invoice->invoice_number }}</h4>
                            <p class="text-sm text-gray-600">{{ $payment->invoice->academicYear->name }} - {{ ucfirst($payment->invoice->semester) }} Semester</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @if($payment->invoice->status == 'paid') bg-green-100 text-green-800
                            @elseif($payment->invoice->status == 'partial') bg-yellow-100 text-yellow-800
                            @elseif($payment->invoice->status == 'overdue') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($payment->invoice->status) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Total:</span>
                            <span class="font-medium">₹{{ number_format($payment->invoice->total_amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Paid:</span>
                            <span class="font-medium text-green-600">₹{{ number_format($payment->invoice->amount_paid, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Balance:</span>
                            <span class="font-medium {{ $payment->invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                ₹{{ number_format($payment->invoice->balance, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('finance.invoices.show', $payment->invoice) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Full Invoice →
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium
                            @if($payment->status == 'completed') text-green-600
                            @elseif($payment->status == 'pending') text-yellow-600
                            @elseif($payment->status == 'failed') text-red-600
                            @else text-gray-600
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium text-green-600">₹{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Method:</span>
                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                    </div>
                    @if($payment->transaction_id)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transaction:</span>
                        <span class="font-mono text-sm">{{ $payment->transaction_id }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Processing Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Received By:</span>
                        <p class="font-medium">{{ $payment->receivedBy->name ?? 'System' }}</p>
                    </div>
                    @if($payment->verified_by)
                    <div>
                        <span class="text-gray-600">Verified By:</span>
                        <p class="font-medium">{{ $payment->verifiedBy->name }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->verified_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <p class="text-gray-900">{{ $payment->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    @if($payment->updated_at != $payment->created_at)
                    <div>
                        <span class="text-gray-600">Last Updated:</span>
                        <p class="text-gray-900">{{ $payment->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <button onclick="window.print()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Receipt
                    </button>
                    
                    @if($payment->invoice)
                        <a href="{{ route('finance.invoices.show', $payment->invoice) }}" 
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-center block">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Invoice
                        </a>
                    @endif

                    @can('view-financial-reports')
                        <a href="{{ route('finance.reports.student-statement', ['student_id' => $payment->student_id]) }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-center block">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Student Statement
                        </a>
                    @endcan
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
        font-size: 12px;
    }
    
    .container {
        max-width: none;
        padding: 0;
    }
    
    .grid {
        display: block;
    }
    
    .lg\:col-span-2 {
        width: 100%;
    }
    
    .space-y-6 > * + * {
        margin-top: 1rem;
    }
}
</style>
@endsection
