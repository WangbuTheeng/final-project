@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Results Management</h1>
                            <p class="mt-1 text-sm text-gray-500">Generate and manage exam results with comprehensive analytics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Exams</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_exams'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-bar text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Results Generated</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_results'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Students Graded</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_students_graded'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending Results</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_results'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Exams List -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-purple-600 mr-3"></i>
                    Completed Exams with Results
                </h3>
                <p class="mt-1 text-sm text-gray-600">Click on any exam to generate comprehensive results</p>
            </div>

            @if($exams->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Exam Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Course & Class
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Academic Year
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Students
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($exams as $exam)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                                            <div class="text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst($exam->exam_type) }}
                                                </span>
                                                <span class="ml-2">{{ $exam->exam_date->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $exam->class->course->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $exam->class->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $exam->academicYear->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $exam->marks->groupBy('student_id')->count() }} students
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $exam->marks->count() }} total marks
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Results Available
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('results.generate', $exam) }}"
                                               class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-xs font-medium rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200"
                                               title="Generate Results">
                                                <i class="fas fa-chart-line mr-1"></i>
                                                Generate
                                            </a>
                                            <a href="{{ route('results.generate-pdf', $exam) }}"
                                               class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200"
                                               title="Download PDF">
                                                <i class="fas fa-file-pdf mr-1"></i>
                                                PDF
                                            </a>
                                            <a href="{{ route('exams.show', $exam) }}"
                                               class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-xs font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200"
                                               title="View Exam">
                                                <i class="fas fa-eye mr-1"></i>
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($exams->hasPages())
                    <div class="bg-white px-6 py-4 border-t border-gray-200">
                        {{ $exams->links() }}
                    </div>
                @endif
            @else
                <!-- No Exams -->
                <div class="text-center py-12">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                        <i class="fas fa-clipboard-list text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Results Available</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        No completed exams with marks found. Complete some exams and enter marks to generate results.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('exams.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-plus mr-2"></i>
                            View Exams
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Result Generation Features
            </h4>
            <div class="text-sm text-blue-800 space-y-2">
                <p>• <strong>Comprehensive Results:</strong> View detailed results with rankings, statistics, and analytics</p>
                <p>• <strong>TU Format GPA:</strong> Automatic GPA calculation following Tribhuvan University standards</p>
                <p>• <strong>PDF Export:</strong> Generate professional PDF reports for printing and distribution</p>
                <p>• <strong>Individual Marksheets:</strong> Create detailed marksheets for individual students</p>
                <p>• <strong>Class Analytics:</strong> View pass rates, averages, and performance statistics</p>
                <p class="mt-3 text-blue-700">
                    <i class="fas fa-lightbulb mr-1"></i>
                    <strong>Note:</strong> Results are automatically calculated based on entered marks and include theory, practical, and internal assessment components.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
