@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bulk Grade Entry</h1>
            <p class="mt-1 text-sm text-gray-500">Enter grades for multiple students at once</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('grades.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Grades
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

    @if(!isset($class))
        <!-- Filter Form -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Select Class and Subject</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('grades.bulk-entry') }}" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Academic Year -->
                        <div>
                            <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Academic Year <span class="text-red-500">*</span>
                            </label>
                            <select name="academic_year_id"
                                    id="academic_year_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Class -->
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Class <span class="text-red-500">*</span>
                            </label>
                            <select name="class_id"
                                    id="class_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} - {{ $class->course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Subject <span class="text-gray-400">(Optional)</span>
                            </label>
                            <select name="subject_id"
                                    id="subject_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Grade Type -->
                        <div>
                            <label for="grade_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Grade Type <span class="text-red-500">*</span>
                            </label>
                            <select name="grade_type"
                                    id="grade_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    required>
                                <option value="">Select Grade Type</option>
                                <option value="ca" {{ request('grade_type') == 'ca' ? 'selected' : '' }}>Continuous Assessment (CA)</option>
                                <option value="exam" {{ request('grade_type') == 'exam' ? 'selected' : '' }}>Examination</option>
                                <option value="final" {{ request('grade_type') == 'final' ? 'selected' : '' }}>Final Grade</option>
                            </select>
                        </div>

                        <!-- Semester/Year -->
                        <div id="semester-group" style="display: none;">
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                                Semester
                            </label>
                            <select name="semester"
                                    id="semester"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Semester</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>
                                        Semester {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div id="year-group" style="display: none;">
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                Year
                            </label>
                            <select name="year"
                                    id="year"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Year</option>
                                @for($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                        Year {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <i class="fas fa-search mr-2"></i>
                            Load Students
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- Grade Entry Form -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Grade Entry</h3>
                        <p class="text-sm text-gray-500">
                            {{ $class->name }} - {{ $class->course->title }}
                            @if($subject)
                                | {{ $subject->name }}
                            @endif
                            | {{ ucfirst($request->grade_type) }}
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $enrollments->count() }} students
                    </div>
                </div>
            </div>

            @if($enrollments->count() > 0)
                <form method="POST" action="{{ route('grades.bulk-store') }}" class="p-6">
                    @csrf

                    <!-- Hidden fields to maintain context -->
                    <input type="hidden" name="class_id" value="{{ $request->class_id }}">
                    <input type="hidden" name="academic_year_id" value="{{ $request->academic_year_id }}">
                    <input type="hidden" name="subject_id" value="{{ $request->subject_id }}">
                    <input type="hidden" name="grade_type" value="{{ $request->grade_type }}">
                    <input type="hidden" name="semester" value="{{ $request->semester }}">
                    <input type="hidden" name="year" value="{{ $request->year }}">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student
                                    </th>
                                    @if($subject && $subject->hasTheoryComponent())
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Theory Score
                                            <div class="text-xs text-gray-400 normal-case">(Max: {{ $subject->full_marks_theory }})</div>
                                        </th>
                                    @endif
                                    @if($subject && $subject->hasPracticalComponent())
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Practical Score
                                            <div class="text-xs text-gray-400 normal-case">(Max: {{ $subject->full_marks_practical }})</div>
                                        </th>
                                    @endif
                                    @if(!$subject || (!$subject->hasTheoryComponent() && !$subject->hasPracticalComponent()))
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total Score
                                            <div class="text-xs text-gray-400 normal-case">(Max: 100)</div>
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
                                            <input type="hidden" name="grades[{{ $index }}][enrollment_id]" value="{{ $enrollment->id }}">
                                        </td>

                                        @if($subject && $subject->hasTheoryComponent())
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number"
                                                       name="grades[{{ $index }}][theory_score]"
                                                       value="{{ old("grades.{$index}.theory_score", $existingGrade?->theory_score) }}"
                                                       min="0"
                                                       max="{{ $subject->full_marks_theory }}"
                                                       step="0.01"
                                                       class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                       placeholder="0">
                                            </td>
                                        @endif

                                        @if($subject && $subject->hasPracticalComponent())
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number"
                                                       name="grades[{{ $index }}][practical_score]"
                                                       value="{{ old("grades.{$index}.practical_score", $existingGrade?->practical_score) }}"
                                                       min="0"
                                                       max="{{ $subject->full_marks_practical }}"
                                                       step="0.01"
                                                       class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                       placeholder="0">
                                            </td>
                                        @endif

                                        @if(!$subject || (!$subject->hasTheoryComponent() && !$subject->hasPracticalComponent()))
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number"
                                                       name="grades[{{ $index }}][score]"
                                                       value="{{ old("grades.{$index}.score", $existingGrade?->score) }}"
                                                       min="0"
                                                       max="100"
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
                            @if($subject && $subject->hasTheoryComponent() && $subject->hasPracticalComponent())
                                <p>Total score will be calculated as Theory + Practical scores.</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('grades.bulk-entry') }}"
                               class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Back to Selection
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
    @endif
</div>

@push('styles')
<style>
/* Custom gradient for avatars */
.avatar-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Input focus styles */
input[type="number"]:focus,
input[type="text"]:focus,
select:focus {
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
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const semesterGroup = document.getElementById('semester-group');
    const yearGroup = document.getElementById('year-group');

    // Handle class selection to load subjects
    if (classSelect) {
        classSelect.addEventListener('change', function() {
            const classId = this.value;

            // Clear subjects
            subjectSelect.innerHTML = '<option value="">All Subjects</option>';

            if (classId) {
                fetch(`{{ route('grades.subjects.by-class') }}?class_id=${classId}`)
                    .then(response => response.json())
                    .then(subjects => {
                        subjects.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = `${subject.name} (${subject.code})`;
                            subjectSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading subjects:', error);
                    });
            }
        });
    }

    // Show/hide semester/year based on course type
    // This would need to be enhanced based on the course type logic

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
