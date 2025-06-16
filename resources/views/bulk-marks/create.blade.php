@extends('layouts.dashboard')

@section('title', 'Bulk Marks Entry - ' . $exam->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bulk Marks Entry</h1>
            <p class="text-gray-600 mt-1">{{ $exam->title }} - {{ $exam->class->name }}</p>
        </div>
        <a href="{{ route('bulk-marks.index', ['exam_id' => $exam->id]) }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            Back to Exam Selection
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Exam Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Exam Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Exam</label>
                <p class="text-gray-900">{{ $exam->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Class</label>
                <p class="text-gray-900">{{ $exam->class->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Total Students</label>
                <p class="text-gray-900">{{ $enrollments->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Marks Entry Form -->
    @if($subjects->count() > 0)
    <form method="POST" action="{{ route('bulk-marks.store') }}" id="bulkMarksForm">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Student Marks</h2>
                <p class="text-sm text-gray-600 mt-1">Enter marks for each student and subject</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            @foreach($subjects as $subject)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $subject->name }}
                                    <div class="text-xs text-gray-400 normal-case">
                                        @php
                                            // Check if this subject has pivot data (from exam-subject relationship)
                                            $subjectPivot = $subject->pivot ?? null;
                                            $theoryMarks = $subjectPivot ? $subjectPivot->theory_marks : ($subject->full_marks_theory ?? 0);
                                            $practicalMarks = $subjectPivot ? $subjectPivot->practical_marks : ($subject->full_marks_practical ?? 0);
                                        @endphp
                                        @if($theoryMarks > 0)
                                            Theory: {{ $theoryMarks }}
                                        @endif
                                        @if($practicalMarks > 0)
                                            @if($theoryMarks > 0) | @endif
                                            Practical: {{ $practicalMarks }}
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $index => $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $enrollment->student->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: {{ $enrollment->student->student_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($subjects as $subject)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $existingMark = $existingMarks[$enrollment->student_id][$subject->id]->first() ?? null;
                                            // Check if this subject has pivot data (from exam-subject relationship)
                                            $subjectPivot = $subject->pivot ?? null;
                                            $theoryMarks = $subjectPivot ? $subjectPivot->theory_marks : ($subject->full_marks_theory ?? 0);
                                            $practicalMarks = $subjectPivot ? $subjectPivot->practical_marks : ($subject->full_marks_practical ?? 0);
                                        @endphp

                                        <input type="hidden" name="marks[{{ $index }}_{{ $subject->id }}][student_id]" value="{{ $enrollment->student_id }}">
                                        <input type="hidden" name="marks[{{ $index }}_{{ $subject->id }}][subject_id]" value="{{ $subject->id }}">

                                        <div class="space-y-2">
                                            @if($theoryMarks > 0)
                                                <div>
                                                    <label class="block text-xs text-gray-600">Theory ({{ $theoryMarks }})</label>
                                                    <input type="number"
                                                           name="marks[{{ $index }}_{{ $subject->id }}][theory_marks]"
                                                           value="{{ old('marks.' . $index . '_' . $subject->id . '.theory_marks', $existingMark ? $existingMark->theory_marks : '') }}"
                                                           min="0"
                                                           max="{{ $theoryMarks }}"
                                                           step="0.01"
                                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                           placeholder="0">
                                                </div>
                                            @endif

                                            @if($practicalMarks > 0)
                                                <div>
                                                    <label class="block text-xs text-gray-600">Practical ({{ $practicalMarks }})</label>
                                                    <input type="number"
                                                           name="marks[{{ $index }}_{{ $subject->id }}][practical_marks]"
                                                           value="{{ old('marks.' . $index . '_' . $subject->id . '.practical_marks', $existingMark ? $existingMark->practical_marks : '') }}"
                                                           min="0"
                                                           max="{{ $practicalMarks }}"
                                                           step="0.01"
                                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                           placeholder="0">
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Total Students: {{ $enrollments->count() }} | Subjects: {{ $subjects->count() }}
                </div>
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="clearAllMarks()" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Clear All
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Save Marks
                    </button>
                </div>
            </div>
        </div>
    </form>
    @else
    <!-- No Subjects Available -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No Subjects Available</h3>
            <p class="mt-1 text-sm text-gray-500">
                This exam doesn't have any subjects configured. Please configure subjects for this exam first.
            </p>
            <div class="mt-6">
                <a href="{{ route('exams.edit', $exam) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Configure Exam
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function clearAllMarks() {
    if (confirm('Are you sure you want to clear all marks? This action cannot be undone.')) {
        const inputs = document.querySelectorAll('input[type="number"]');
        inputs.forEach(input => {
            input.value = '';
        });
    }
}

// Auto-save functionality (optional)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bulkMarksForm');
    if (form) {
        const inputs = form.querySelectorAll('input[type="number"]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                // You can add auto-save functionality here if needed
            });
        });
    }
});
</script>
@endsection
