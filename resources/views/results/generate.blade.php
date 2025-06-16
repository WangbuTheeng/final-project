@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }} - Results</h1>
                            <p class="mt-1 text-sm text-gray-500">Comprehensive exam results with rankings and analytics</p>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    {{ $exam->class->course->title }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $exam->class->name }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $exam->academicYear->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <a href="{{ route('results.generate-pdf', $exam) }}"
                           class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Download PDF
                        </a>
                        <a href="{{ route('results.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Results
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $classStats['total_students'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Passed</p>
                            <p class="text-2xl font-bold text-green-600">{{ $classStats['passed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-times-circle text-red-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Failed</p>
                            <p class="text-2xl font-bold text-red-600">{{ $classStats['failed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-percentage text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pass Rate</p>
                            <p class="text-2xl font-bold text-purple-600">{{ number_format($classStats['pass_rate'], 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-bar text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Average</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ number_format($classStats['average_percentage'], 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-trophy text-indigo-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Highest</p>
                            <p class="text-2xl font-bold text-indigo-600">{{ number_format($classStats['highest_percentage'], 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-table text-purple-600 mr-3"></i>
                    Detailed Results
                </h3>
                <p class="mt-1 text-sm text-gray-600">Complete results with rankings and individual subject performance</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rank
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Marks
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Percentage
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grade
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                GPA
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Result
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sortedResults as $result)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($result['rank'] <= 3)
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center
                                                {{ $result['rank'] == 1 ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($result['rank'] == 2 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800') }}">
                                                <i class="fas fa-trophy text-sm"></i>
                                            </div>
                                        @endif
                                        <span class="ml-2 text-sm font-medium text-gray-900">{{ $result['rank'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ substr($result['student']->user->first_name, 0, 1) }}{{ substr($result['student']->user->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $result['student']->user->first_name }} {{ $result['student']->user->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: {{ $result['student']->student_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($result['total_obtained'], 1) }}/{{ number_format($result['total_maximum'], 1) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ number_format($result['percentage'], 2) }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $result['letter_grade'] == 'A' ? 'bg-green-100 text-green-800' : 
                                           ($result['letter_grade'] == 'B+' || $result['letter_grade'] == 'B' ? 'bg-blue-100 text-blue-800' : 
                                           ($result['letter_grade'] == 'C+' || $result['letter_grade'] == 'C' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($result['letter_grade'] == 'D' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))) }}">
                                        {{ $result['letter_grade'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($result['gpa'], 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $result['result_status'] == 'Pass' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas {{ $result['result_status'] == 'Pass' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                        {{ $result['result_status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('results.student-marksheet', [$exam, $result['student']]) }}"
                                       class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
                                       title="View Marksheet">
                                        <i class="fas fa-file-alt mr-1"></i>
                                        Marksheet
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Grading Information
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h5 class="text-sm font-semibold text-blue-800 mb-2">TU Grading Scale</h5>
                    <div class="text-sm text-blue-700 space-y-1">
                        <div class="flex justify-between">
                            <span>A (80-100%)</span>
                            <span>4.0 GPA</span>
                        </div>
                        <div class="flex justify-between">
                            <span>B+ (70-79%)</span>
                            <span>3.6 GPA</span>
                        </div>
                        <div class="flex justify-between">
                            <span>B (60-69%)</span>
                            <span>3.2 GPA</span>
                        </div>
                        <div class="flex justify-between">
                            <span>C+ (50-59%)</span>
                            <span>2.8 GPA</span>
                        </div>
                        <div class="flex justify-between">
                            <span>C (45-49%)</span>
                            <span>2.4 GPA</span>
                        </div>
                        <div class="flex justify-between">
                            <span>D (40-44%)</span>
                            <span>2.0 GPA</span>
                        </div>
                        <div class="flex justify-between">
                            <span>F (Below 40%)</span>
                            <span>0.0 GPA</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h5 class="text-sm font-semibold text-blue-800 mb-2">Result Calculation</h5>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p>• Results are calculated based on theory, practical, and internal marks</p>
                        <p>• Students must pass each subject individually (40% minimum)</p>
                        <p>• Overall percentage must be 40% or above to pass</p>
                        <p>• Rankings are based on overall percentage</p>
                        <p>• GPA follows Tribhuvan University standards</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
