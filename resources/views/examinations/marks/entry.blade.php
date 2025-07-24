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
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $examination->title }}</h1>
                            <p class="mt-1 text-sm text-gray-500">Marks Entry</p>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $examination->exam_date->format('M d, Y') }}</span>
                                <span><i class="fas fa-users mr-1"></i>{{ $enrollments->count() }} Students</span>
                                <span><i class="fas fa-book mr-1"></i>{{ $subjects->count() }} Subject(s)</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <a href="{{ route('examinations.show', $examination) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Exam
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Class</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->class->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Assessment Type</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($examination->assessment_type === 'internal')
                                <span class="text-purple-600">Internal Assessment ({{ $examination->weightage_percentage }}%)</span>
                            @elseif($examination->assessment_type === 'final')
                                <span class="text-blue-600">Final Exam ({{ $examination->weightage_percentage }}%)</span>
                            @else
                                {{ ucfirst($examination->assessment_type ?? $examination->exam_type ?? 'N/A') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Marks</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->total_marks }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Exam Date</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $examination->exam_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marks Entry Form -->
        @if($subjects->count() > 0 && $enrollments->count() > 0)
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Validation Errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('examinations.marks.store', $examination) }}" class="space-y-6">
            @csrf

            <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-table text-blue-600 mr-2"></i>
                        Marks Entry Table
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">Enter marks for each student and subject</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                                    Student
                                </th>
                                @foreach($subjects as $subject)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l border-gray-200">
                                    <div class="space-y-1">
                                        <div class="font-semibold">{{ $subject->name }}</div>
                                        <div class="text-xs text-gray-400">({{ $subject->code }})</div>
                                        @if($examination->is_multi_subject)
                                            @php
                                                $subjectPivot = $examination->subjects()->where('subject_id', $subject->id)->first();
                                                $totalMarks = ($subjectPivot->pivot->theory_marks ?? 0) + ($subjectPivot->pivot->practical_marks ?? 0);
                                            @endphp
                                            <div class="text-xs text-gray-400">Total: {{ $totalMarks }}</div>
                                        @endif
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                            <tr class="bg-gray-100">
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-100 z-10">
                                    Mark Components
                                </th>
                                @foreach($subjects as $subject)
                                @php
                                    $theoryMarks = 0;
                                    $practicalMarks = 0;

                                    if($examination->is_multi_subject) {
                                        $subjectPivot = $examination->subjects()->where('subject_id', $subject->id)->first();
                                        $theoryMarks = $subjectPivot->pivot->theory_marks ?? 0;
                                        $practicalMarks = $subjectPivot->pivot->practical_marks ?? 0;
                                    } else {
                                        $theoryMarks = $subject->full_marks_theory ?? 0;
                                        $practicalMarks = $subject->full_marks_practical ?? 0;
                                    }
                                @endphp
                                <th class="px-2 py-2 text-center border-l border-gray-200">
                                    <div class="grid grid-cols-2 gap-1 text-xs">
                                        <div class="text-blue-600 font-medium">
                                            Theory
                                            @if($theoryMarks > 0)
                                                <div class="text-gray-500 font-normal">({{ $theoryMarks }})</div>
                                            @endif
                                        </div>
                                        <div class="text-green-600 font-medium">
                                            Practical
                                            @if($practicalMarks > 0)
                                                <div class="text-gray-500 font-normal">({{ $practicalMarks }})</div>
                                            @endif
                                        </div>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($enrollments as $index => $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ substr($enrollment->student->user->name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $enrollment->student->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $enrollment->student->admission_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($subjects as $subject)
                                @php
                                    $existingMark = $existingMarks->get($enrollment->student_id . '_' . $subject->id)?->first();

                                    // Get the correct theory and practical marks limits
                                    $theoryMarks = 0;
                                    $practicalMarks = 0;

                                    if($examination->is_multi_subject) {
                                        $subjectPivot = $examination->subjects()->where('subject_id', $subject->id)->first();
                                        $theoryMarks = $subjectPivot->pivot->theory_marks ?? 0;
                                        $practicalMarks = $subjectPivot->pivot->practical_marks ?? 0;
                                    } else {
                                        $theoryMarks = $subject->full_marks_theory ?? 0;
                                        $practicalMarks = $subject->full_marks_practical ?? 0;
                                    }
                                @endphp
                                <td class="px-2 py-4 text-center border-l border-gray-200">
                                    <!-- Theory + Practical Marks Only -->
                                    <div class="grid grid-cols-2 gap-1">
                                        <!-- Theory Marks -->
                                        @if($theoryMarks > 0)
                                        <div>
                                            <label class="text-xs text-gray-500">Theory</label>
                                            <input type="number"
                                                   name="marks[{{ $enrollment->student_id }}_{{ $subject->id }}][theory_marks]"
                                                   value="{{ old('marks.' . $enrollment->student_id . '_' . $subject->id . '.theory_marks', $existingMark->theory_marks ?? '') }}"
                                                   class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="0"
                                                   min="0"
                                                   max="{{ $theoryMarks }}"
                                                   step="0.01">
                                        </div>
                                        @else
                                        <div class="flex items-center justify-center text-xs text-gray-400">
                                            No Theory
                                        </div>
                                        @endif

                                        <!-- Practical Marks -->
                                        @if($practicalMarks > 0)
                                        <div>
                                            <label class="text-xs text-gray-500">Practical</label>
                                            <input type="number"
                                                   name="marks[{{ $enrollment->student_id }}_{{ $subject->id }}][practical_marks]"
                                                   value="{{ old('marks.' . $enrollment->student_id . '_' . $subject->id . '.practical_marks', $existingMark->practical_marks ?? '') }}"
                                                   class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-green-500 focus:border-green-500"
                                                   placeholder="0"
                                                   min="0"
                                                   max="{{ $practicalMarks }}"
                                                   step="0.01">
                                        </div>
                                        @else
                                        <div class="flex items-center justify-center text-xs text-gray-400">
                                            No Practical
                                        </div>
                                        @endif
                                    </div>
                                    <!-- Hidden fields -->
                                    <input type="hidden" name="marks[{{ $enrollment->student_id }}_{{ $subject->id }}][student_id]" value="{{ $enrollment->student_id }}">
                                    <input type="hidden" name="marks[{{ $enrollment->student_id }}_{{ $subject->id }}][subject_id]" value="{{ $subject->id }}">
                                    <input type="hidden" name="marks[{{ $enrollment->student_id }}_{{ $subject->id }}][enrollment_id]" value="{{ $enrollment->id }}">
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Submit Section -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Enter marks for each component. Leave blank if not applicable.
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" onclick="clearAllMarks()" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-eraser mr-2"></i>
                                Clear All
                            </button>
                            <button type="submit" onclick="debugFormSubmission(event)"
                                    class="inline-flex items-center px-8 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                                <i class="fas fa-save mr-2"></i>
                                Save All Marks
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @else
        <!-- No Data Available -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Data Available</h3>
                <p class="text-gray-600">
                    @if($subjects->count() == 0)
                        No subjects found for this examination.
                    @elseif($enrollments->count() == 0)
                        No students enrolled for this class.
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function clearAllMarks() {
    if (confirm('Are you sure you want to clear all marks? This action cannot be undone.')) {
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.value = '';
        });
    }
}

function debugFormSubmission(event) {
    // Get all form data
    const form = event.target.closest('form');
    const formData = new FormData(form);

    console.log('Form submission debug:');
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);

    // Log all form fields
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    // Count non-empty marks
    let nonEmptyCount = 0;
    const inputs = form.querySelectorAll('input[type="number"]');
    inputs.forEach(input => {
        if (input.value && input.value.trim() !== '') {
            nonEmptyCount++;
            console.log(`Non-empty field: ${input.name} = ${input.value}`);
        }
    });

    console.log(`Total non-empty mark fields: ${nonEmptyCount}`);

    if (nonEmptyCount === 0) {
        alert('Please enter at least one mark before submitting.');
        event.preventDefault();
        return false;
    }

    return true;
}

// Validate marks against maximum limits
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="number"]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const maxValue = parseFloat(this.getAttribute('max'));
            const currentValue = parseFloat(this.value);

            // Remove any existing error styling
            this.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

            // Remove any existing error message
            const existingError = this.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }

            if (currentValue > maxValue) {
                // Add error styling
                this.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

                // Add error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message text-xs text-red-600 mt-1';
                errorDiv.textContent = `Max: ${maxValue}`;
                this.parentNode.appendChild(errorDiv);
            }
        });

        // Also validate on blur
        input.addEventListener('blur', function() {
            const maxValue = parseFloat(this.getAttribute('max'));
            const currentValue = parseFloat(this.value);

            if (currentValue > maxValue) {
                this.value = maxValue; // Auto-correct to maximum value

                // Remove error styling since we corrected it
                this.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                const existingError = this.parentNode.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
            }
        });
    });
});
</script>
@endsection
