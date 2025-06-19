@extends('layouts.dashboard')

@section('title', 'Finance Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Finance Dashboard</h1>
        <p class="text-gray-600 mt-2">Overview of financial activities and statistics</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Students -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalStudents) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Invoices -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Invoices</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalInvoices) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">NRs {{ number_format($totalRevenue, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Outstanding Amount -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Outstanding</p>
                    <p class="text-2xl font-bold text-gray-900">NRs {{ number_format($outstandingAmount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Salary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Teachers -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Teachers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalTeachers) }}</p>
                </div>
            </div>
        </div>

        <!-- Salaries Paid This Month -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Paid This Month</p>
                    <p class="text-2xl font-bold text-gray-900">NRs {{ number_format($salariesPaidThisMonth, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities and Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
            </div>
            <div class="p-6">
                @if($recentPayments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentPayments as $payment)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $payment->student->user->first_name }} {{ $payment->student->user->last_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $payment->payment_reference }} - {{ $payment->payment_method_display }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">NRs {{ number_format($payment->amount, 2) }}</p>
                                    <p class="text-sm text-gray-600">{{ $payment->payment_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('finance.payments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all payments →</a>
                    </div>
                @else
                    <p class="text-gray-500">No recent payments found.</p>
                @endif
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Overdue Invoices</h3>
            </div>
            <div class="p-6">
                @if($overdueInvoices->count() > 0)
                    <div class="space-y-4">
                        @foreach($overdueInvoices as $invoice)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $invoice->student->user->first_name }} {{ $invoice->student->user->last_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $invoice->invoice_number }} - Due: {{ $invoice->due_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-red-600">NRs {{ number_format($invoice->balance, 2) }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('finance.reports.outstanding-dues') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">View all overdue →</a>
                    </div>
                @else
                    <p class="text-gray-500">No overdue invoices found.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('finance.fees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-center font-medium transition duration-200">
                Create Fee
            </a>
            <a href="{{ route('finance.invoices.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-center font-medium transition duration-200">
                Create Invoice
            </a>
            <a href="{{ route('finance.payments.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-lg text-center font-medium transition duration-200">
                Record Payment
            </a>
            <a href="{{ route('finance.reports.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg text-center font-medium transition duration-200">
                View Reports
            </a>
        </div>
    </div>
</div>
@endsection
