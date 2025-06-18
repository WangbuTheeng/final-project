@extends('layouts.dashboard')

@section('title', 'Salary Payment Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Salary Payment Details</h1>
            <p class="text-gray-600 mt-2">{{ $salaryPayment->teacher->teacher_name }} - {{ DateTime::createFromFormat('Y-m', $salaryPayment->month)->format('F Y') }}</p>
        </div>
        <div class="flex space-x-3">
            @can('manage-salaries')
                @if($salaryPayment->status == 'pending')
                    <form action="{{ route('finance.salaries.approve', $salaryPayment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200"
                                onclick="return confirm('Are you sure you want to approve this salary payment?')">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approve Payment
                        </button>
                    </form>
                @endif
            @endcan
            <a href="{{ route('finance.salaries.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Salaries
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Salary Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Salary Payment Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">SALARY SLIP</h2>
                        <p class="text-gray-600">{{ DateTime::createFromFormat('Y-m', $salaryPayment->month)->format('F Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($salaryPayment->status == 'paid') bg-green-100 text-green-800
                            @elseif($salaryPayment->status == 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($salaryPayment->status) }}
                        </span>
                    </div>
                </div>

                <!-- Teacher Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Employee Information:</h3>
                        <div class="text-gray-700 space-y-2">
                            <p><span class="font-medium">Name:</span> {{ $salaryPayment->teacher->teacher_name }}</p>
                            <p><span class="font-medium">Employee ID:</span> {{ $salaryPayment->teacher->employee_id }}</p>
                            @if($salaryPayment->teacher->facultyInfo)
                                <p><span class="font-medium">Faculty:</span> {{ $salaryPayment->teacher->facultyInfo->name }}</p>
                            @endif
                            <p><span class="font-medium">Department:</span> {{ $salaryPayment->teacher->department ?? 'N/A' }}</p>
                            @if($salaryPayment->teacher->position)
                                <p><span class="font-medium">Position:</span> {{ $salaryPayment->teacher->position }}</p>
                            @endif
                            @if($salaryPayment->teacher->email)
                                <p><span class="font-medium">Email:</span> {{ $salaryPayment->teacher->email }}</p>
                            @endif
                            @if($salaryPayment->teacher->phone)
                                <p><span class="font-medium">Phone:</span> {{ $salaryPayment->teacher->phone }}</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Details:</h3>
                        <div class="text-gray-700 space-y-2">
                            <p><span class="font-medium">Salary Month:</span> {{ DateTime::createFromFormat('Y-m', $salaryPayment->month)->format('F Y') }}</p>
                            <p><span class="font-medium">Payment Date:</span> {{ $salaryPayment->payment_date ? $salaryPayment->payment_date->format('M d, Y') : 'Not paid yet' }}</p>
                            <p><span class="font-medium">Basic Salary:</span> ₹{{ number_format($salaryPayment->teacher->basic_salary, 2) }}</p>
                            <p><span class="font-medium">Status:</span> 
                                <span class="
                                    @if($salaryPayment->status == 'paid') text-green-600
                                    @elseif($salaryPayment->status == 'pending') text-yellow-600
                                    @else text-red-600
                                    @endif font-medium">
                                    {{ ucfirst($salaryPayment->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Salary Breakdown -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Salary Breakdown</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Basic Salary</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₹{{ number_format($salaryPayment->teacher->basic_salary, 2) }}</td>
                                </tr>
                                
                                @php
                                    $allowances = 0;
                                    $deductions = 0;
                                    
                                    // You can add logic here for allowances and deductions
                                    // For now, we'll show the basic structure
                                @endphp
                                
                                @if($allowances > 0)
                                <tr class="bg-green-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-700 font-medium">Total Allowances</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-700 text-right font-medium">+₹{{ number_format($allowances, 2) }}</td>
                                </tr>
                                @endif
                                
                                @if($deductions > 0)
                                <tr class="bg-red-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700 font-medium">Total Deductions</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700 text-right font-medium">-₹{{ number_format($deductions, 2) }}</td>
                                </tr>
                                @endif
                                
                                <tr class="bg-gray-100 font-semibold">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Net Salary</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₹{{ number_format($salaryPayment->amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Net Amount -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Net Salary Amount</h3>
                        <p class="text-4xl font-bold text-green-600">₹{{ number_format($salaryPayment->amount, 2) }}</p>
                        <p class="text-sm text-gray-600 mt-2">
                            Amount: ₹{{ number_format($salaryPayment->amount, 2) }} Only
                        </p>
                    </div>
                </div>

                @if($salaryPayment->notes)
                <!-- Notes -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes</h3>
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $salaryPayment->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
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
                            @if($salaryPayment->status == 'paid') text-green-600
                            @elseif($salaryPayment->status == 'pending') text-yellow-600
                            @else text-red-600
                            @endif">
                            {{ ucfirst($salaryPayment->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium text-green-600">₹{{ number_format($salaryPayment->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Month:</span>
                        <span class="font-medium">{{ DateTime::createFromFormat('Y-m', $salaryPayment->month)->format('F Y') }}</span>
                    </div>
                    @if($salaryPayment->payment_date)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paid On:</span>
                        <span class="font-medium">{{ $salaryPayment->payment_date->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Teacher Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Teacher Summary</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Employee ID:</span>
                        <p class="font-medium">{{ $salaryPayment->teacher->employee_id }}</p>
                    </div>
                    @if($salaryPayment->teacher->facultyInfo)
                    <div>
                        <span class="text-gray-600">Faculty:</span>
                        <p class="font-medium">{{ $salaryPayment->teacher->facultyInfo->name }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-gray-600">Department:</span>
                        <p class="font-medium">{{ $salaryPayment->teacher->department ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Basic Salary:</span>
                        <p class="font-medium text-green-600">₹{{ number_format($salaryPayment->teacher->basic_salary, 2) }}</p>
                    </div>
                    @if($salaryPayment->teacher->hire_date)
                    <div>
                        <span class="text-gray-600">Hire Date:</span>
                        <p class="font-medium">{{ $salaryPayment->teacher->hire_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Processing Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <p class="text-gray-900">{{ $salaryPayment->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    @if($salaryPayment->updated_at != $salaryPayment->created_at)
                    <div>
                        <span class="text-gray-600">Last Updated:</span>
                        <p class="text-gray-900">{{ $salaryPayment->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    @endif
                    @if($salaryPayment->approved_by)
                    <div>
                        <span class="text-gray-600">Approved By:</span>
                        <p class="font-medium">{{ $salaryPayment->approvedBy->name ?? 'System' }}</p>
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
                        Print Salary Slip
                    </button>
                    
                    @can('view-financial-reports')
                        <a href="{{ route('finance.reports.salary-report', ['teacher_id' => $salaryPayment->teacher_id]) }}" 
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-center block">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Salary History
                        </a>
                    @endcan

                    @can('manage-salaries')
                        <a href="{{ route('finance.salaries.create') }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 text-center block">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Process New Salary
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
