@extends('layouts.dashboard')

@section('title', 'Teacher Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Teacher Details</h1>
            <p class="text-gray-600 mt-2">View teacher information and salary history</p>
        </div>
        <div class="flex space-x-3">
            @can('manage-salaries')
                <a href="{{ route('finance.teachers.edit', $teacher) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Teacher
                </a>
            @endcan
            <a href="{{ route('finance.teachers.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Teachers
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Teacher Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Teacher Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Teacher Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->teacher_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Employee ID</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->employee_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->phone ?: 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Faculty</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department && $department->faculty ? $department->faculty->name : 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Department</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->department ?: 'Not Assigned' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Position</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->position }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Hire Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->hire_date->format('M d, Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Basic Salary</label>
                        <p class="mt-1 text-sm text-gray-900">₦{{ number_format($teacher->basic_salary, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($teacher->status == 'active') bg-green-100 text-green-800
                            @elseif($teacher->status == 'inactive') bg-gray-100 text-gray-800
                            @elseif($teacher->status == 'on_leave') bg-yellow-100 text-yellow-800
                            @elseif($teacher->status == 'terminated') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $teacher->status)) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Bank Account</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->bank_account ?: 'N/A' }}</p>
                    </div>
                </div>

                @if($teacher->address)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500">Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $teacher->address }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Salary Summary -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Salary Summary</h2>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-blue-600">Monthly Salary</div>
                        <div class="text-2xl font-bold text-blue-900">₦{{ number_format($teacher->basic_salary, 2) }}</div>
                    </div>

                    @if(isset($unpaidMonths) && count($unpaidMonths) > 0)
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-red-600">Unpaid Months ({{ now()->year }})</div>
                            <div class="text-2xl font-bold text-red-900">{{ count($unpaidMonths) }}</div>
                            <div class="text-xs text-red-600 mt-1">
                                {{ implode(', ', array_map(function($month) {
                                    return date('M', mktime(0, 0, 0, $month, 1));
                                }, $unpaidMonths)) }}
                            </div>
                        </div>
                    @endif

                    @if(isset($salaryHistory))
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-600">Paid This Year</div>
                            <div class="text-2xl font-bold text-green-900">{{ count($salaryHistory) }}</div>
                            <div class="text-xs text-green-600 mt-1">months</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Salary History -->
    @if(isset($salaryHistory) && count($salaryHistory) > 0)
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Salary History ({{ now()->year }})</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salaryHistory as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ date('F Y', strtotime($payment->month . '-01')) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₦{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->payment_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($payment->status == 'paid') bg-green-100 text-green-800
                                            @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($payment->status == 'cancelled') bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
