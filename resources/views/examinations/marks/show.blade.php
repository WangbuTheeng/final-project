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
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-bar text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $examination->title }}</h1>
                            <p class="mt-1 text-sm text-gray-500">Examination Results</p>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $examination->exam_date->format('M d, Y') }}</span>
                                <span><i class="fas fa-users mr-1"></i>{{ $marks->count() }} Students</span>
                                <span><i class="fas fa-book mr-1"></i>{{ $examination->subjects->count() }} Subject(s)</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <a href="{{ route('examinations.marks.entry', $examination) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Marks
                        </a>
                        <a href="{{ route('examinations.show', $examination) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Exam
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            @php
                $totalStudents = $marks->count();
                $passedStudents = $marks->filter(function($studentMarks) {
                    return $studentMarks->every(function($mark) {
                        return $mark->percentage >= 40; // Assuming 40% is pass percentage
                    });
                })->count();
                $averagePercentage = $marks->flatten()->avg('percentage');
                $highestPercentage = $marks->flatten()->max('percentage');
            @endphp
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalStudents }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Passed Students</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $passedStudents }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-percentage text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Average %</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($averagePercentage, 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-trophy text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Highest %</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($highestPercentage, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-table text-green-600 mr-2"></i>
                    Examination Results
                </h3>
                <p class="mt-1 text-sm text-gray-600">Detailed marks for each student and subject</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                                Student
                            </th>
                            @foreach($examination->subjects as $subject)
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l border-gray-200">
                                <div class="space-y-1">
                                    <div class="font-semibold">{{ $subject->name }}</div>
                                    <div class="text-xs text-gray-400">({{ $subject->code }})</div>
                                </div>
                            </th>
                            @endforeach
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l border-gray-200">
                                Overall
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($marks as $studentId => $studentMarks)
                        @php
                            $firstMark = $studentMarks->first();
                            $student = $firstMark->student;
                            $totalObtained = $studentMarks->sum('obtained_marks');
                            $totalMarks = $studentMarks->sum('total_marks');
                            $overallPercentage = $totalMarks > 0 ? ($totalObtained / $totalMarks) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($student->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $student->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $student->admission_number }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @foreach($examination->subjects as $subject)
                            @php
                                $subjectMark = $studentMarks->where('subject_id', $subject->id)->first();
                            @endphp
                            <td class="px-4 py-4 text-center border-l border-gray-200">
                                @if($subjectMark)
                                <div class="space-y-1">
                                    <div class="text-lg font-semibold text-gray-900">
                                        {{ $subjectMark->obtained_marks }}/{{ $subjectMark->total_marks }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ number_format($subjectMark->percentage, 1) }}%
                                    </div>
                                    @if($subjectMark->grade_letter)
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($subjectMark->percentage >= 80) bg-green-100 text-green-800
                                        @elseif($subjectMark->percentage >= 60) bg-blue-100 text-blue-800
                                        @elseif($subjectMark->percentage >= 40) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $subjectMark->grade_letter }}
                                    </div>
                                    @endif
                                </div>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="px-4 py-4 text-center border-l border-gray-200 bg-gray-50">
                                <div class="space-y-1">
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ $totalObtained }}/{{ $totalMarks }}
                                    </div>
                                    <div class="text-sm font-semibold text-gray-600">
                                        {{ number_format($overallPercentage, 1) }}%
                                    </div>
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($overallPercentage >= 80) bg-green-100 text-green-800
                                        @elseif($overallPercentage >= 60) bg-blue-100 text-blue-800
                                        @elseif($overallPercentage >= 40) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($overallPercentage >= 40) PASS @else FAIL @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($marks->isEmpty())
        <!-- No Results -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <i class="fas fa-chart-bar text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Results Available</h3>
                <p class="text-gray-600 mb-4">No marks have been entered for this examination yet.</p>
                <a href="{{ route('examinations.marks.entry', $examination) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    Enter Marks
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
