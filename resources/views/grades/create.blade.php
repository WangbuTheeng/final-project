@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add Grade</h1>
            <p class="mt-1 text-sm text-gray-500">Create a new grade entry for a student</p>
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

    <!-- Create Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Grade Information</h3>
            <p class="mt-1 text-sm text-gray-500">Fill in the grade details below</p>
        </div>

        <form method="POST" action="{{ route('grades.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Academic Year -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Academic Year <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_year_id"
                            id="academic_year_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('academic_year_id') border-red-300 @enderror"
                            required>
                        <option value="">Select academic year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select name="class_id"
                            id="class_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('class_id') border-red-300 @enderror"
                            required>
                        <option value="">Select class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}"
                                    {{ old('class_id', $selectedClass?->id) == $class->id ? 'selected' : '' }}
                                    data-organization-type="{{ $class->course->organization_type }}">
                                {{ $class->name }} - {{ $class->course->title }}
                                ({{ $class->academicYear->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Student -->
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id"
                            id="student_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('student_id') border-red-300 @enderror"
                            required>
                        <option value="">Select student</option>
                    </select>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Select a class first to load students</p>
                </div>

                <!-- Subject -->
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject <span class="text-gray-400">(Optional)</span>
                    </label>
                    <select name="subject_id"
                            id="subject_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('subject_id') border-red-300 @enderror">
                        <option value="">Select subject</option>
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Leave empty for general grade</p>
                </div>

                <!-- Grade Type -->
                <div>
                    <label for="grade_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Grade Type <span class="text-red-500">*</span>
                    </label>
                    <select name="grade_type"
                            id="grade_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('grade_type') border-red-300 @enderror"
                            required>
                        <option value="">Select grade type</option>
                        <option value="ca" {{ old('grade_type') == 'ca' ? 'selected' : '' }}>Continuous Assessment (CA)</option>
                        <option value="exam" {{ old('grade_type') == 'exam' ? 'selected' : '' }}>Exam</option>
                        <option value="final" {{ old('grade_type') == 'final' ? 'selected' : '' }}>Final Grade</option>
                    </select>
                    @error('grade_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exam (Optional) -->
                <div>
                    <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam <span class="text-gray-400">(Optional)</span>
                    </label>
                    <select name="exam_id"
                            id="exam_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('exam_id') border-red-300 @enderror">
                        <option value="">Select exam</option>
                    </select>
                    @error('exam_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Link to a specific exam if applicable</p>
                </div>

                <!-- Semester (for semester-based courses) -->
                <div id="semester-group" style="display: none;">
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester <span class="text-red-500">*</span>
                    </label>
                    <select name="semester"
                            id="semester"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('semester') border-red-300 @enderror">
                        <option value="">Select semester</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('semester')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year (for year-based courses) -->
                <div id="year-group" style="display: none;">
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        Year <span class="text-red-500">*</span>
                    </label>
                    <select name="year"
                            id="year"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('year') border-red-300 @enderror">
                        <option value="">Select year</option>
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>
                                {{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }} Year
                            </option>
                        @endfor
                    </select>
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Scores Section -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Score Information</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Score -->
                    <div>
                        <label for="score" class="block text-sm font-medium text-gray-700 mb-2">
                            Total Score <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="score"
                               id="score"
                               value="{{ old('score') }}"
                               min="0"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('score') border-red-300 @enderror"
                               required>
                        @error('score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Maximum Score -->
                    <div>
                        <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Score <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="max_score"
                               id="max_score"
                               value="{{ old('max_score', 100) }}"
                               min="1"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('max_score') border-red-300 @enderror"
                               required>
                        @error('max_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Theory Score (Optional) -->
                    <div>
                        <label for="theory_score" class="block text-sm font-medium text-gray-700 mb-2">
                            Theory Score <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input type="number"
                               name="theory_score"
                               id="theory_score"
                               value="{{ old('theory_score') }}"
                               min="0"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('theory_score') border-red-300 @enderror">
                        @error('theory_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty if no theory component</p>
                    </div>

                    <!-- Practical Score (Optional) -->
                    <div>
                        <label for="practical_score" class="block text-sm font-medium text-gray-700 mb-2">
                            Practical Score <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input type="number"
                               name="practical_score"
                               id="practical_score"
                               value="{{ old('practical_score') }}"
                               min="0"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('practical_score') border-red-300 @enderror">
                        @error('practical_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty if no practical component</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h4>

                <!-- Remarks -->
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                        Remarks <span class="text-gray-400">(Optional)</span>
                    </label>
                    <textarea name="remarks"
                              id="remarks"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('remarks') border-red-300 @enderror"
                              placeholder="Enter any additional remarks or comments about this grade...">{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('grades.index') }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>
                    Create Grade
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const academicYearSelect = document.getElementById('academic_year_id');
    const classSelect = document.getElementById('class_id');
    const studentSelect = document.getElementById('student_id');
    const subjectSelect = document.getElementById('subject_id');
    const examSelect = document.getElementById('exam_id');
    const semesterGroup = document.getElementById('semester-group');
    const yearGroup = document.getElementById('year-group');
    const semesterSelect = document.getElementById('semester');
    const yearSelect = document.getElementById('year');
    const scoreInput = document.getElementById('score');
    const maxScoreInput = document.getElementById('max_score');
    const theoryScoreInput = document.getElementById('theory_score');
    const practicalScoreInput = document.getElementById('practical_score');

    // Handle class selection
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const organizationType = selectedOption.getAttribute('data-organization-type');

        // Clear dependent dropdowns
        studentSelect.innerHTML = '<option value="">Select student</option>';
        subjectSelect.innerHTML = '<option value="">Select subject</option>';
        examSelect.innerHTML = '<option value="">Select exam</option>';

        // Show/hide semester or year fields
        if (organizationType === 'semester') {
            semesterGroup.style.display = 'block';
            yearGroup.style.display = 'none';
            semesterSelect.setAttribute('required', 'required');
            yearSelect.removeAttribute('required');
        } else if (organizationType === 'yearly') {
            semesterGroup.style.display = 'none';
            yearGroup.style.display = 'block';
            yearSelect.setAttribute('required', 'required');
            semesterSelect.removeAttribute('required');
        } else {
            semesterGroup.style.display = 'none';
            yearGroup.style.display = 'none';
            semesterSelect.removeAttribute('required');
            yearSelect.removeAttribute('required');
        }

        // Load students and subjects for the selected class
        if (classId && academicYearSelect.value) {
            loadStudents(classId, academicYearSelect.value);
            loadSubjects(classId);
            loadExams(classId);
        }
    });

    // Handle academic year selection
    academicYearSelect.addEventListener('change', function() {
        const academicYearId = this.value;
        const classId = classSelect.value;

        // Clear students
        studentSelect.innerHTML = '<option value="">Select student</option>';

        // Load students if both class and academic year are selected
        if (classId && academicYearId) {
            loadStudents(classId, academicYearId);
        }
    });

    // Load students function
    function loadStudents(classId, academicYearId) {
        fetch(`/ajax/students?class_id=${classId}&academic_year_id=${academicYearId}`)
            .then(response => response.json())
            .then(students => {
                students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = `${student.user.first_name} ${student.user.last_name} (${student.admission_number})`;
                    studentSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading students:', error);
            });
    }

    // Load subjects function
    function loadSubjects(classId) {
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

    // Load exams function
    function loadExams(classId) {
        fetch(`/ajax/exams?class_id=${classId}`)
            .then(response => response.json())
            .then(exams => {
                exams.forEach(exam => {
                    const option = document.createElement('option');
                    option.value = exam.id;
                    option.textContent = `${exam.title} (${exam.exam_type})`;
                    examSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading exams:', error);
            });
    }

    // Calculate total score when theory and practical scores change
    function calculateTotalScore() {
        const theoryScore = parseFloat(theoryScoreInput.value) || 0;
        const practicalScore = parseFloat(practicalScoreInput.value) || 0;

        if (theoryScore > 0 || practicalScore > 0) {
            scoreInput.value = theoryScore + practicalScore;
        }
    }

    // Add event listeners for score calculation
    theoryScoreInput.addEventListener('input', calculateTotalScore);
    practicalScoreInput.addEventListener('input', calculateTotalScore);

    // Validate that score doesn't exceed max score
    function validateScore() {
        const score = parseFloat(scoreInput.value) || 0;
        const maxScore = parseFloat(maxScoreInput.value) || 0;

        if (score > maxScore && maxScore > 0) {
            scoreInput.setCustomValidity('Score cannot exceed maximum score');
        } else {
            scoreInput.setCustomValidity('');
        }
    }

    scoreInput.addEventListener('input', validateScore);
    maxScoreInput.addEventListener('input', validateScore);

    // Initialize on page load
    if (classSelect.value) {
        classSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
