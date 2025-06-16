@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Student Marksheet</h1>
                            <p class="mt-1 text-sm text-gray-500">Individual detailed marksheet with subject-wise performance</p>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <a href="{{ route('results.student-marksheet-pdf', [$exam, $student]) }}"
                           class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Download PDF
                        </a>
                        <a href="{{ route('results.generate', $exam) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Results
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Information</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-16 w-16">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-xl font-medium text-white">
                                    {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="text-lg font-medium text-gray-900">
                                {{ $student->user->first_name }} {{ $student->user->last_name }}
                            </div>
                            <div class="text-sm text-gray-500">Student ID: {{ $student->student_id }}</div>
                            <div class="text-sm text-gray-500">{{ $student->user->email }}</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Course:</span>
                            <span class="text-sm text-gray-900">{{ $exam->class->course->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Class:</span>
                            <span class="text-sm text-gray-900">{{ $exam->class->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Academic Year:</span>
                            <span class="text-sm text-gray-900">{{ $exam->academicYear->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Exam Date:</span>
                            <span class="text-sm text-gray-900">{{ $exam->exam_date->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ $exam->title }}</h3>
                <p class="text-sm text-gray-500">{{ ucfirst($exam->exam_type) }} Examination</p>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalObtained, 1) }}</div>
                        <div class="text-sm text-gray-500">Total Obtained</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($totalMaximum, 1) }}</div>
                        <div class="text-sm text-gray-500">Total Maximum</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($overallPercentage, 2) }}%</div>
                        <div class="text-sm text-gray-500">Percentage</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($gpa, 2) }}</div>
                        <div class="text-sm text-gray-500">GPA</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject-wise Marks -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Subject-wise Performance</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Theory
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Practical
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Internal
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Percentage
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grade
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($marks as $mark)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $mark->subject->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $mark->subject->code }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($mark->theory_marks > 0)
                                        <div class="text-sm text-gray-900">{{ number_format($mark->theory_marks, 1) }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($mark->practical_marks > 0)
                                        <div class="text-sm text-gray-900">{{ number_format($mark->practical_marks, 1) }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($mark->internal_marks > 0)
                                        <div class="text-sm text-gray-900">{{ number_format($mark->internal_marks, 1) }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($mark->obtained_marks, 1) }}/{{ number_format($mark->total_marks, 1) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ number_format($mark->percentage, 2) }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $mark->grade_letter == 'A' ? 'bg-green-100 text-green-800' : 
                                           ($mark->grade_letter == 'B+' || $mark->grade_letter == 'B' ? 'bg-blue-100 text-blue-800' : 
                                           ($mark->grade_letter == 'C+' || $mark->grade_letter == 'C' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($mark->grade_letter == 'D' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))) }}">
                                        {{ $mark->grade_letter }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $mark->percentage >= 40 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $mark->percentage >= 40 ? 'Pass' : 'Fail' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Overall Result -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Overall Result</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-3xl font-bold text-indigo-600">{{ $overallGrade }}</div>
                        <div class="text-sm text-gray-500">Overall Grade</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600">{{ number_format($gpa, 2) }}</div>
                        <div class="text-sm text-gray-500">Grade Point Average</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-3xl font-bold {{ $resultStatus == 'Pass' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $resultStatus }}
                        </div>
                        <div class="text-sm text-gray-500">Final Result</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grading Scale Reference -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Tribhuvan University Grading Scale
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-blue-800">
                <div class="space-y-1">
                    <div class="font-semibold">Grade A (4.0)</div>
                    <div>80-100%</div>
                </div>
                <div class="space-y-1">
                    <div class="font-semibold">Grade B+ (3.6)</div>
                    <div>70-79%</div>
                </div>
                <div class="space-y-1">
                    <div class="font-semibold">Grade B (3.2)</div>
                    <div>60-69%</div>
                </div>
                <div class="space-y-1">
                    <div class="font-semibold">Grade C+ (2.8)</div>
                    <div>50-59%</div>
                </div>
                <div class="space-y-1">
                    <div class="font-semibold">Grade C (2.4)</div>
                    <div>45-49%</div>
                </div>
                <div class="space-y-1">
                    <div class="font-semibold">Grade D (2.0)</div>
                    <div>40-44%</div>
                </div>
                <div class="space-y-1">
                    <div class="font-semibold">Grade F (0.0)</div>
                    <div>Below 40%</div>
                </div>
                <div class="space-y-1">
                    <div class="font-semibold">Pass Requirement</div>
                    <div>40% minimum in each subject</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
