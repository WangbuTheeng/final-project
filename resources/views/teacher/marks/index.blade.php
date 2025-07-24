@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mark Entry Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">
                Enter marks for your assigned subjects and exams
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <form method="GET" action="{{ route('teacher.marks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="education_level" class="block text-sm font-medium text-gray-700">Education Level</label>
                    <select name="education_level" id="education_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Levels</option>
                        <option value="plus_two" {{ request('education_level') === 'plus_two' ? 'selected' : '' }}>+2 Level</option>
                        <option value="bachelors" {{ request('education_level') === 'bachelors' ? 'selected' : '' }}>Bachelor's Level</option>
                    </select>
                </div>

                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <select name="academic_year_id" id="academic_year_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams List -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Your Assigned Exams</h3>
        </div>

        @if($exams->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class/Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Your Subjects</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($exams as $exam)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $exam->examType ? $exam->examType->name : $exam->getExamTypeLabel() }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $exam->exam_date->format('M d, Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $exam->class->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $exam->class->course->title ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-400">{{ $exam->academicYear->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        @foreach($exam->subjects->where('instructor_id', Auth::id()) as $subject)
                                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $subject->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusInfo = $exam->getStatusLabel();
                                        $statusColors = [
                                            'scheduled' => 'bg-yellow-100 text-yellow-800',
                                            'ongoing' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$exam->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @foreach($exam->subjects->where('instructor_id', Auth::id()) as $subject)
                                            <a href="{{ route('teacher.marks.create', ['exam_id' => $exam->id, 'subject_id' => $subject->id]) }}"
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Enter Marks
                                            </a>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $exams->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Exams Assigned</h3>
                    <p class="text-sm">You don't have any exams assigned for mark entry at the moment.</p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterSelects = document.querySelectorAll('#status, #education_level, #academic_year_id');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush
@endsection
