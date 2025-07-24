@extends('layouts.dashboard')

@push('styles')
<style>
.table-compact {
    font-size: 0.875rem;
    border-collapse: collapse;
}
.table-compact th,
.table-compact td {
    padding: 0.5rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
}
.table-compact th {
    background-color: #f9fafb;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
}
.table-compact tbody tr:hover {
    background-color: #f9fafb;
}
.table-compact .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-weight: 500;
}
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $examination->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $examination->examType->name ?? 'N/A' }} - {{ $examination->exam_date->format('M d, Y') }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="{{ route('examinations.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Examinations
            </a>
            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                @if($examination->status === 'scheduled')
                    <a href="{{ route('examinations.edit', $examination) }}"
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Exam
                    </a>
                @endif
                <a href="{{ route('examinations.marks.entry', $examination) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i>
                    Enter Marks
                </a>
                <a href="{{ route('examinations.marks.show', $examination) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-chart-bar mr-2"></i>
                    View Results
                </a>
            @endif
        </div>
    </div>

    <!-- Exam Details Card -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Examination Details</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Basic Information -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Basic Information</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Title</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Education Level</dt>
                            <dd class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $examination->education_level)) }}</dd>
                        </div>
                        @if($examination->stream)
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Stream</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->stream }}</dd>
                        </div>
                        @endif
                        @if($examination->program_code)
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Program</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->program_code }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Class</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->class->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Academic Year</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->academicYear->name ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Exam Schedule -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Schedule</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Date</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->exam_date->format('F d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Start Time</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->start_time ? \Carbon\Carbon::parse($examination->start_time)->format('g:i A') : 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">End Time</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->end_time ? \Carbon\Carbon::parse($examination->end_time)->format('g:i A') : 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Duration</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->duration_minutes }} minutes</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Venue</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->venue ?? 'Not specified' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Marks & Status -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Marks & Status</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Total Marks</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->total_marks }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Pass Mark</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->pass_mark }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Status</dt>
                            <dd class="text-sm">
                                @php
                                    $statusConfig = [
                                        'scheduled' => ['bg-blue-100 text-blue-800', 'Scheduled'],
                                        'ongoing' => ['bg-yellow-100 text-yellow-800', 'Ongoing'],
                                        'completed' => ['bg-green-100 text-green-800', 'Completed'],
                                        'cancelled' => ['bg-red-100 text-red-800', 'Cancelled'],
                                        'published' => ['bg-green-100 text-green-800', 'Published'],
                                    ];
                                    $config = $statusConfig[$examination->status] ?? ['bg-gray-100 text-gray-800', ucfirst($examination->status)];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }}">
                                    {{ $config[1] }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Max Students</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->max_students ?? 'No limit' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Multi-Subject</dt>
                            <dd class="text-sm text-gray-600">{{ $examination->is_multi_subject ? 'Yes' : 'No' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-900">Grading System</dt>
                            <dd class="text-sm text-gray-600">
                                @if($examination->gradingSystem)
                                    <div class="flex items-center">
                                        <i class="fas fa-graduation-cap text-blue-500 mr-2"></i>
                                        <span class="font-medium">{{ $examination->gradingSystem->name }}</span>
                                    </div>
                                    @if($examination->gradingSystem->description)
                                        <div class="text-xs text-gray-500 mt-1">{{ $examination->gradingSystem->description }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400 italic">Default grading system</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Grading Scale Information -->
    @if($examination->gradingSystem && $examination->gradingSystem->gradeScales->count() > 0)
    <div class="bg-white shadow rounded-lg mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                Grading Scale - {{ $examination->gradingSystem->name }}
            </h3>
        </div>
        <div class="px-6 py-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade Point</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Percentage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Percentage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($examination->gradingSystem->gradeScales as $gradeScale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $gradeScale->grade_letter }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($gradeScale->grade_point, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($gradeScale->min_percentage, 2) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($gradeScale->max_percentage, 2) }}%</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $gradeScale->description ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <p><strong>Pass Percentage:</strong> {{ \App\Models\CollegeSetting::getPassPercentage() }}%</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Subjects Section -->
    @if($examination->is_multi_subject && $examination->subjects->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Subjects</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-compact">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Theory</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Practical</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pass</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($examination->subjects as $subject)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-3 py-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900 truncate">{{ $subject->name }}</span>
                                <span class="text-xs text-gray-500 font-mono">{{ $subject->code }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="badge bg-blue-100 text-blue-800">
                                {{ $subject->pivot->theory_marks ?? 0 }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="badge bg-green-100 text-green-800">
                                {{ $subject->pivot->practical_marks ?? 0 }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="badge bg-purple-100 text-purple-800 font-semibold">
                                {{ ($subject->pivot->theory_marks ?? 0) + ($subject->pivot->practical_marks ?? 0) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="badge bg-yellow-100 text-yellow-800">
                                {{ ($subject->pivot->pass_marks_theory ?? 0) + ($subject->pivot->pass_marks_practical ?? 0) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($examination->subject)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Subject</h3>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-medium text-gray-900">{{ $examination->subject->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $examination->subject->code }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $examination->total_marks }}</div>
                    <div class="text-sm text-gray-500">Total Marks</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Instructions -->
    @if($examination->instructions)
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Instructions</h3>
        </div>
        <div class="px-6 py-4">
            <div class="prose max-w-none">
                {!! nl2br(e($examination->instructions)) !!}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
