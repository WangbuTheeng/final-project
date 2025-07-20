@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exams</h1>
            <p class="mt-1 text-sm text-gray-500">Manage exams, types, and grade entry</p>
        </div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('exams.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Create Exam
            </a>
        </div>
        @endif
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
                            <i class="fas fa-file-alt text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Exams</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalExams }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Upcoming</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $upcomingExams }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-play text-orange-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Ongoing</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $ongoingExams }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Completed</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $completedExams }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('exams.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search exams..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Exam Type Filter -->
                <div>
                    <select name="exam_type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Types</option>
                        @foreach($examTypes as $type)
                            <option value="{{ $type }}" {{ request('exam_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Academic Year Filter -->
                <div>
                    <select name="academic_year_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
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
                    <a href="{{ route('exams.index') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        @if($exams->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Exam Details
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Class & Subject
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Marks Distribution
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Schedule
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($exams as $exam)
                            <tr class="table-row-hover transition-all duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $exam->title }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $exam->getExamTypeLabel() }}
                                            </span>
                                        </div>
                                        @if($exam->venue)
                                            <div class="text-xs text-gray-400 mt-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $exam->venue }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $exam->class->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $exam->class->course->title }}
                                        </div>
                                        @if($exam->subject)
                                            <div class="text-xs text-blue-600 mt-1">
                                                {{ $exam->subject->name }}
                                            </div>
                                        @endif
                                        <div class="text-xs text-gray-400">
                                            {{ $exam->academicYear->name }}
                                            @if($exam->semester)
                                                - Semester {{ $exam->semester }}
                                            @elseif($exam->year)
                                                - Year {{ $exam->year }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="font-medium">Total: {{ $exam->total_marks }} marks</div>
                                        @if($exam->hasTheory() || $exam->hasPractical())
                                            <div class="text-xs text-gray-500 mt-1">
                                                @if($exam->hasTheory())
                                                    Theory: {{ $exam->theory_marks }}
                                                @endif
                                                @if($exam->hasTheory() && $exam->hasPractical())
                                                    |
                                                @endif
                                                @if($exam->hasPractical())
                                                    Practical: {{ $exam->practical_marks }}
                                                @endif
                                            </div>
                                        @endif
                                        <div class="text-xs text-green-600">
                                            Pass: {{ $exam->pass_mark }} ({{ number_format($exam->getPassPercentage(), 1) }}%)
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $exam->exam_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $exam->exam_date->format('g:i A') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $exam->getFormattedDuration() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $statusInfo = $exam->getStatusLabel(); @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusInfo['color'] }}-100 text-{{ $statusInfo['color'] }}-800 status-badge">
                                        @switch($exam->status)
                                            @case('scheduled')
                                                <i class="fas fa-calendar mr-1"></i>
                                                @break
                                            @case('ongoing')
                                                <i class="fas fa-play mr-1"></i>
                                                @break
                                            @case('completed')
                                                <i class="fas fa-check mr-1"></i>
                                                @break
                                            @case('cancelled')
                                                <i class="fas fa-times mr-1"></i>
                                                @break
                                        @endswitch
                                        {{ $statusInfo['label'] }}
                                    </span>
                                    @if($exam->status === 'completed')
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $exam->getGradedStudentsCount() }}/{{ $exam->getEnrolledStudentsCount() }} graded
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('exams.show', $exam) }}"
                                           class="action-btn text-primary-600 hover:text-primary-900 p-1 rounded-md hover:bg-primary-50"
                                           title="View Exam">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('exams.grades', $exam) }}"
                                           class="action-btn text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-50"
                                           title="Enter Grades">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('exams.edit', $exam) }}"
                                           class="action-btn text-yellow-600 hover:text-yellow-900 p-1 rounded-md hover:bg-yellow-50"
                                           title="Edit Exam">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        @if($exam->grades()->count() === 0)
                                            <form method="POST" action="{{ route('exams.destroy', $exam) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="action-btn text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50"
                                                        title="Delete Exam"
                                                        onclick="return confirm('Are you sure you want to delete this exam?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($exams->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $exams->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-file-alt text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No exams found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                        Get started by creating your first exam.
                    @else
                        No exams are available to view.
                    @endif
                </p>
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                <div class="mt-6">
                    <a href="{{ route('exams.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Exam
                    </a>
                </div>
                @endif
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

/* Status badge animations */
.status-badge {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush
@endsection
