@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Grades Management</h1>
            <p class="mt-1 text-sm text-gray-500">View and manage student grades across all subjects and exams</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('grades.bulk-entry') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i>
                Bulk Grade Entry
            </a>
            <a href="{{ route('grades.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Grade
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Grades</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalGrades) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-chart-line text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Average Score</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($averageScore, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-trophy text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pass Rate</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($passRate, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Recent Grades</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $recentGrades }}</p>
                        <p class="text-xs text-gray-400">Last 7 days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('grades.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by student name or admission number..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Academic Year Filter -->
                <div>
                    <select name="academic_year_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Grade Type Filter -->
                <div>
                    <select name="grade_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Grade Types</option>
                        @foreach($gradeTypes as $type)
                            <option value="{{ $type }}" {{ request('grade_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Letter Grade Filter -->
                <div>
                    <select name="letter_grade"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Letter Grades</option>
                        @foreach($letterGrades as $grade)
                            <option value="{{ $grade }}" {{ request('letter_grade') == $grade ? 'selected' : '' }}>
                                {{ $grade }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex space-x-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <i class="fas fa-search mr-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('grades.index') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        @if($grades->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject/Exam
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Scores
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grade
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Academic Info
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Graded By
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($grades as $grade)
                            <tr class="table-row-hover transition-all duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-primary-600">
                                                    {{ strtoupper(substr($grade->student->user->first_name, 0, 1) . substr($grade->student->user->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $grade->student->user->first_name }} {{ $grade->student->user->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $grade->student->admission_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        @if($grade->subject)
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $grade->subject->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $grade->subject->code }}
                                            </div>
                                        @elseif($grade->exam)
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $grade->exam->title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $grade->exam->getExamTypeLabel() }}
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500">General Grade</div>
                                        @endif
                                        <div class="text-xs text-blue-600 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($grade->grade_type) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($grade->theory_score !== null && $grade->practical_score !== null)
                                            <div class="space-y-1">
                                                <div class="flex justify-between">
                                                    <span class="text-xs text-gray-500">Theory:</span>
                                                    <span class="font-medium">{{ $grade->theory_score }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-xs text-gray-500">Practical:</span>
                                                    <span class="font-medium">{{ $grade->practical_score }}</span>
                                                </div>
                                                <div class="border-t pt-1 flex justify-between">
                                                    <span class="text-xs text-gray-500">Total:</span>
                                                    <span class="font-bold">{{ $grade->score }}/{{ $grade->max_score }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="font-medium">{{ $grade->score }}/{{ $grade->max_score }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ number_format(($grade->score / $grade->max_score) * 100, 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $percentage = ($grade->score / $grade->max_score) * 100;
                                        $letterGrade = $grade->letter_grade ?? 'N/A';
                                        $isPassing = $grade->score >= ($grade->max_score * 0.4); // Assuming 40% pass mark
                                        $gradeColor = $isPassing ? 'green' : 'red';
                                        if ($percentage >= 80) $gradeColor = 'green';
                                        elseif ($percentage >= 70) $gradeColor = 'blue';
                                        elseif ($percentage >= 60) $gradeColor = 'yellow';
                                        elseif ($percentage >= 40) $gradeColor = 'orange';
                                        else $gradeColor = 'red';
                                    @endphp
                                    <div class="flex flex-col items-start">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $gradeColor }}-100 text-{{ $gradeColor }}-800">
                                            {{ $letterGrade }}
                                        </span>
                                        @if($isPassing)
                                            <span class="text-xs text-green-600 mt-1">
                                                <i class="fas fa-check mr-1"></i>Pass
                                            </span>
                                        @else
                                            <span class="text-xs text-red-600 mt-1">
                                                <i class="fas fa-times mr-1"></i>Fail
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $grade->academicYear->name }}
                                    </div>
                                    @if($grade->semester)
                                        <div class="text-sm text-gray-500">
                                            Semester {{ $grade->semester }}
                                        </div>
                                    @elseif($grade->year)
                                        <div class="text-sm text-gray-500">
                                            Year {{ $grade->year }}
                                        </div>
                                    @endif
                                    <div class="text-xs text-gray-400">
                                        {{ $grade->graded_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($grade->grader)
                                        <div class="text-sm text-gray-900">
                                            {{ $grade->grader->first_name }} {{ $grade->grader->last_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $grade->graded_at->format('g:i A') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">System</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('grades.show', $grade) }}"
                                           class="action-btn text-primary-600 hover:text-primary-900 p-1 rounded-md hover:bg-primary-50"
                                           title="View Grade Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($grade->student)
                                            <a href="{{ route('grades.student-report', $grade->student) }}"
                                               class="action-btn text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-50"
                                               title="Student Report">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($grades->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $grades->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-graduation-cap text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No grades found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'academic_year_id', 'grade_type', 'letter_grade']))
                        Try adjusting your search criteria or filters.
                    @else
                        Get started by entering grades for students.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'academic_year_id', 'grade_type', 'letter_grade']))
                        <a href="{{ route('grades.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('grades.bulk-entry') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i>
                        Start Grade Entry
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Custom hover effects for table rows */
.table-row-hover:hover {
    background-color: #f9fafb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Smooth transitions for action buttons */
.action-btn {
    transition: all 0.2s ease-in-out;
}

.action-btn:hover {
    transform: scale(1.05);
}

/* Grade badge animations */
.grade-badge {
    animation: fadeIn 0.3s ease-in-out;
}

/* Statistics card hover effects */
.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush
@endsection
