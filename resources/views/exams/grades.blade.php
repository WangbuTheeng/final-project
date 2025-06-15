@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Grade Entry</h1>
            <p class="mt-1 text-sm text-gray-500">Enter grades for {{ $exam->title }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('exams.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Exams
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

    <!-- Exam Information -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Exam Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Exam Details</h4>
                    <div class="mt-2">
                        <p class="text-lg font-semibold text-gray-900">{{ $exam->title }}</p>
                        <p class="text-sm text-gray-600">{{ $exam->getExamTypeLabel() }}</p>
                        <p class="text-sm text-gray-500">{{ $exam->getFormattedExamDate() }}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Class & Subject</h4>
                    <div class="mt-2">
                        <p class="text-lg font-semibold text-gray-900">{{ $exam->class->name }}</p>
                        <p class="text-sm text-gray-600">{{ $exam->class->course->title }}</p>
                        @if($exam->subject)
                            <p class="text-sm text-blue-600">{{ $exam->subject->name }}</p>
                        @endif
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Marks Distribution</h4>
                    <div class="mt-2">
                        <p class="text-lg font-semibold text-gray-900">{{ $exam->total_marks }} Total</p>
                        @if($exam->hasTheory() || $exam->hasPractical())
                            <div class="text-sm text-gray-600">
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
                        <p class="text-sm text-green-600">Pass: {{ $exam->pass_mark }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Entry Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Student Grades</h3>
                <div class="text-sm text-gray-500">
                    {{ $enrollments->count() }} students enrolled
                </div>
            </div>
        </div>

        @if($enrollments->count() > 0)
            <form method="POST" action="{{ route('exams.grades.store', $exam) }}" class="p-6">
                @csrf

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student
                                </th>
                                @if($exam->hasTheory())
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Theory Score
                                        <div class="text-xs text-gray-400 normal-case">(Max: {{ $exam->theory_marks }})</div>
                                    </th>
                                @endif
                                @if($exam->hasPractical())
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Practical Score
                                        <div class="text-xs text-gray-400 normal-case">(Max: {{ $exam->practical_marks }})</div>
                                    </th>
                                @endif
                                @if(!$exam->hasTheory() && !$exam->hasPractical())
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Score
                                        <div class="text-xs text-gray-400 normal-case">(Max: {{ $exam->total_marks }})</div>
                                    </th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Remarks
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Grade
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($enrollments as $index => $enrollment)
                                @php
                                    $student = $enrollment->student;
                                    $existingGrade = $existingGrades->get($student->id);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full avatar-gradient flex items-center justify-center shadow-lg">
                                                    <span class="text-white font-bold text-sm">
                                                        {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $student->user->first_name }} {{ $student->user->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $student->admission_number }}
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>

                                    @if($exam->hasTheory())
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" 
                                                   name="grades[{{ $index }}][theory_score]" 
                                                   value="{{ old("grades.{$index}.theory_score", $existingGrade?->theory_score) }}"
                                                   min="0" 
                                                   max="{{ $exam->theory_marks }}"
                                                   step="0.01"
                                                   class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                   placeholder="0">
                                        </td>
                                    @endif

                                    @if($exam->hasPractical())
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" 
                                                   name="grades[{{ $index }}][practical_score]" 
                                                   value="{{ old("grades.{$index}.practical_score", $existingGrade?->practical_score) }}"
                                                   min="0" 
                                                   max="{{ $exam->practical_marks }}"
                                                   step="0.01"
                                                   class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                   placeholder="0">
                                        </td>
                                    @endif

                                    @if(!$exam->hasTheory() && !$exam->hasPractical())
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" 
                                                   name="grades[{{ $index }}][theory_score]" 
                                                   value="{{ old("grades.{$index}.theory_score", $existingGrade?->score) }}"
                                                   min="0" 
                                                   max="{{ $exam->total_marks }}"
                                                   step="0.01"
                                                   class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                   placeholder="0">
                                        </td>
                                    @endif

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" 
                                               name="grades[{{ $index }}][remarks]" 
                                               value="{{ old("grades.{$index}.remarks", $existingGrade?->remarks) }}"
                                               class="w-32 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="Optional">
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($existingGrade)
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $existingGrade->getGradeBgColor() }} {{ $existingGrade->getGradeColor() }}">
                                                    {{ $existingGrade->letter_grade }}
                                                </span>
                                                <span class="text-sm text-gray-600">
                                                    {{ $existingGrade->getFormattedScore() }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $existingGrade->getStatus() }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">Not graded</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <p><strong>Note:</strong> Leave scores empty to skip grading for that student.</p>
                        @if($exam->hasTheory() && $exam->hasPractical())
                            <p>Total score will be calculated as Theory + Practical scores.</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('exams.index') }}"
                           class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            Save Grades
                        </button>
                    </div>
                </div>
            </form>
        @else
            <!-- No Students -->
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-users text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No students enrolled</h3>
                <p class="mt-1 text-sm text-gray-500">There are no students enrolled in this class for the selected period.</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Custom gradient for avatars */
.avatar-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Input focus styles */
input[type="number"]:focus,
input[type="text"]:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Table hover effects */
tbody tr:hover {
    background-color: #f9fafb;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate total scores if both theory and practical are present
    const hasTheory = {{ $exam->hasTheory() ? 'true' : 'false' }};
    const hasPractical = {{ $exam->hasPractical() ? 'true' : 'false' }};
    const maxTheory = {{ $exam->theory_marks ?? 0 }};
    const maxPractical = {{ $exam->practical_marks ?? 0 }};
    const maxTotal = {{ $exam->total_marks }};

    // Add validation for score inputs
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            const max = parseFloat(this.getAttribute('max'));
            
            if (value > max) {
                this.setCustomValidity(`Score cannot exceed ${max}`);
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Keyboard navigation for table inputs
    document.querySelectorAll('input').forEach((input, index, inputs) => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                const nextIndex = index + 1;
                if (nextIndex < inputs.length) {
                    inputs[nextIndex].focus();
                }
            }
        });
    });
});
</script>
@endpush
@endsection
