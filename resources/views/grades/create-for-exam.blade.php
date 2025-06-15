@extends('layouts.app')

@section('title', 'Enter Grades - ' . $exam->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Enter Grades</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $exam->title }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('exams.show', $exam) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Exam
                        </a>
                    </div>
                </div>
            </div>

            <!-- Exam Information -->
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Course</h3>
                        <p class="text-sm text-gray-900">{{ $exam->classSection->course->title }}</p>
                        <p class="text-xs text-gray-500">{{ $exam->classSection->course->code }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Total Marks</h3>
                        <p class="text-sm text-gray-900">{{ $exam->total_marks }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Pass Mark</h3>
                        <p class="text-sm text-gray-900">{{ $exam->pass_mark }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Students</h3>
                        <p class="text-sm text-gray-900">{{ $students->count() }} enrolled</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade Entry Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Student Grades</h2>
                <p class="text-sm text-gray-600 mt-1">Enter scores for each student</p>
            </div>

            @if($students->count() > 0)
                <form method="POST" action="{{ route('grades.store-for-exam', $exam) }}" id="gradesForm">
                    @csrf
                    
                    <div class="px-6 py-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matric Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score (out of {{ $exam->total_marks }})</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $index => $student)
                                        @php
                                            $hasGrade = in_array($student->id, $existingGrades);
                                        @endphp
                                        <tr class="hover:bg-gray-50 {{ $hasGrade ? 'bg-green-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $student->user->first_name }} {{ $student->user->last_name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $student->matric_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($hasGrade)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Already Graded
                                                    </span>
                                                @else
                                                    <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $student->id }}">
                                                    <input type="number" 
                                                           name="grades[{{ $index }}][score]" 
                                                           min="0" 
                                                           max="{{ $exam->total_marks }}" 
                                                           step="0.1"
                                                           class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                           placeholder="0"
                                                           required>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(!$hasGrade)
                                                    <input type="text" 
                                                           name="grades[{{ $index }}][remarks]" 
                                                           maxlength="500"
                                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                           placeholder="Optional remarks">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($hasGrade)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Completed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                @php
                                    $pendingCount = $students->count() - count($existingGrades);
                                @endphp
                                {{ $pendingCount }} student(s) pending grade entry
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" 
                                        onclick="fillAllScores()"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Quick Fill
                                </button>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                                        {{ $pendingCount == 0 ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Save Grades
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No students enrolled</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        No students are enrolled in this class for the current semester.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function fillAllScores() {
    const score = prompt('Enter score to fill for all students (0-{{ $exam->total_marks }}):');
    if (score !== null && !isNaN(score) && score >= 0 && score <= {{ $exam->total_marks }}) {
        const inputs = document.querySelectorAll('input[name*="[score]"]');
        inputs.forEach(input => {
            if (!input.disabled) {
                input.value = score;
            }
        });
    }
}

// Form validation
document.getElementById('gradesForm').addEventListener('submit', function(e) {
    const scoreInputs = document.querySelectorAll('input[name*="[score]"]');
    let hasEmptyScore = false;
    
    scoreInputs.forEach(input => {
        if (!input.disabled && (!input.value || input.value === '')) {
            hasEmptyScore = true;
        }
    });
    
    if (hasEmptyScore) {
        if (!confirm('Some students have empty scores. Do you want to continue?')) {
            e.preventDefault();
        }
    }
});
</script>
@endpush
@endsection
