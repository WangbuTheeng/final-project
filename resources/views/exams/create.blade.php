@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Exam</h1>
            <p class="mt-1 text-sm text-gray-500">Create a new exam with theory and practical components</p>
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

    <!-- Create Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Exam Information</h3>
            <p class="mt-1 text-sm text-gray-500">Fill in the exam details below</p>
        </div>

        <form method="POST" action="{{ route('exams.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Exam Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-300 @enderror"
                           placeholder="e.g., Midterm Examination - Mathematics"
                           required>
                    @error('title')
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
                    <div class="mt-1 flex items-center justify-between">
                        <p class="text-sm text-gray-500">Leave empty for general class exam</p>
                        <button type="button"
                                id="load-marks-btn"
                                class="text-sm text-primary-600 hover:text-primary-800 disabled:text-gray-400"
                                disabled>
                            <i class="fas fa-download mr-1"></i>
                            Load Marks from Subject
                        </button>
                    </div>
                </div>

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

                <!-- Exam Type -->
                <div>
                    <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Type <span class="text-red-500">*</span>
                    </label>
                    <select name="exam_type" 
                            id="exam_type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('exam_type') border-red-300 @enderror"
                            required>
                        <option value="">Select exam type</option>
                        @foreach($examTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('exam_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('exam_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

                <!-- Exam Date -->
                <div>
                    <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local"
                           name="exam_date"
                           id="exam_date"
                           value="{{ old('exam_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('exam_date') border-red-300 @enderror"
                           required>
                    @error('exam_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exam Period Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Period Start <span class="text-gray-400">(Optional)</span>
                    </label>
                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ old('start_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('start_date') border-red-300 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">For exam periods spanning multiple days</p>
                </div>

                <!-- Exam Period End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Period End <span class="text-gray-400">(Optional)</span>
                    </label>
                    <input type="date"
                           name="end_date"
                           id="end_date"
                           value="{{ old('end_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('end_date') border-red-300 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Must be after or equal to start date</p>
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Duration (minutes) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="duration_minutes" 
                           id="duration_minutes" 
                           value="{{ old('duration_minutes', 120) }}"
                           min="15" 
                           max="480"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('duration_minutes') border-red-300 @enderror"
                           required>
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Duration in minutes (15-480)</p>
                </div>
            </div>

            <!-- Marks Section -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Marks Distribution</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Marks -->
                    <div>
                        <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            Total Marks <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="total_marks" 
                               id="total_marks" 
                               value="{{ old('total_marks', 100) }}"
                               min="1" 
                               max="1000"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('total_marks') border-red-300 @enderror"
                               required>
                        @error('total_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Theory Marks -->
                    <div>
                        <label for="theory_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            Theory Marks <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input type="number" 
                               name="theory_marks" 
                               id="theory_marks" 
                               value="{{ old('theory_marks') }}"
                               min="0" 
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('theory_marks') border-red-300 @enderror">
                        @error('theory_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty if no theory component</p>
                    </div>

                    <!-- Practical Marks -->
                    <div>
                        <label for="practical_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            Practical Marks <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input type="number" 
                               name="practical_marks" 
                               id="practical_marks" 
                               value="{{ old('practical_marks') }}"
                               min="0" 
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('practical_marks') border-red-300 @enderror">
                        @error('practical_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty if no practical component</p>
                    </div>
                </div>

                <!-- Pass Mark -->
                <div class="mt-6">
                    <label for="pass_mark" class="block text-sm font-medium text-gray-700 mb-2">
                        Pass Mark <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="pass_mark" 
                           id="pass_mark" 
                           value="{{ old('pass_mark', 40) }}"
                           min="0" 
                           step="0.01"
                           class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('pass_mark') border-red-300 @enderror"
                           required>
                    @error('pass_mark')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Minimum marks required to pass</p>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Venue -->
                    <div>
                        <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">
                            Venue <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input type="text" 
                               name="venue" 
                               id="venue" 
                               value="{{ old('venue') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('venue') border-red-300 @enderror"
                               placeholder="e.g., Main Hall, Room 101">
                        @error('venue')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-6">
                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                        Instructions <span class="text-gray-400">(Optional)</span>
                    </label>
                    <textarea name="instructions" 
                              id="instructions" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('instructions') border-red-300 @enderror"
                              placeholder="Enter exam instructions, rules, or special notes...">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('exams.index') }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>
                    Create Exam
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const semesterGroup = document.getElementById('semester-group');
    const yearGroup = document.getElementById('year-group');
    const semesterSelect = document.getElementById('semester');
    const yearSelect = document.getElementById('year');
    const totalMarksInput = document.getElementById('total_marks');
    const theoryMarksInput = document.getElementById('theory_marks');
    const practicalMarksInput = document.getElementById('practical_marks');
    const passMarkInput = document.getElementById('pass_mark');
    const loadMarksBtn = document.getElementById('load-marks-btn');

    // Handle class selection
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const organizationType = selectedOption.getAttribute('data-organization-type');

        // Clear subjects
        subjectSelect.innerHTML = '<option value="">Select subject</option>';

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

        // Load subjects for the selected class
        if (classId) {
            fetch(`{{ route('exams.subjects.by-class') }}?class_id=${classId}`)
                .then(response => response.json())
                .then(subjects => {
                    subjects.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = `${subject.name} (${subject.code})`;
                        option.dataset.theoryMarks = subject.full_marks_theory || '';
                        option.dataset.practicalMarks = subject.full_marks_practical || '';
                        option.dataset.passMarksTheory = subject.pass_marks_theory || '';
                        option.dataset.passMarksPractical = subject.pass_marks_practical || '';
                        option.dataset.isPractical = subject.is_practical || false;
                        subjectSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                });
        }
    });

    // Handle subject selection
    subjectSelect.addEventListener('change', function() {
        const subjectId = this.value;
        loadMarksBtn.disabled = !subjectId;
    });

    // Handle load marks button
    loadMarksBtn.addEventListener('click', function() {
        const subjectId = subjectSelect.value;
        if (!subjectId) return;

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...';

        fetch(`{{ route('exams.subject-marks') }}?subject_id=${subjectId}`)
            .then(response => response.json())
            .then(data => {
                // Load marks from subject
                if (data.theory_marks) {
                    theoryMarksInput.value = data.theory_marks;
                }
                if (data.practical_marks) {
                    practicalMarksInput.value = data.practical_marks;
                }
                if (data.total_marks) {
                    totalMarksInput.value = data.total_marks;
                }
                if (data.total_pass_marks) {
                    passMarkInput.value = data.total_pass_marks;
                }

                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'mt-2 p-2 bg-green-50 border border-green-200 text-green-700 rounded text-sm';
                successMsg.innerHTML = '<i class="fas fa-check mr-1"></i>Marks loaded successfully from subject!';
                this.parentNode.appendChild(successMsg);

                setTimeout(() => {
                    successMsg.remove();
                }, 3000);

                this.disabled = false;
                this.innerHTML = '<i class="fas fa-download mr-1"></i>Load Marks from Subject';
            })
            .catch(error => {
                console.error('Error loading marks:', error);
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-download mr-1"></i>Load Marks from Subject';
            });
    });

    // Validate marks distribution
    function validateMarks() {
        const totalMarks = parseFloat(totalMarksInput.value) || 0;
        const theoryMarks = parseFloat(theoryMarksInput.value) || 0;
        const practicalMarks = parseFloat(practicalMarksInput.value) || 0;

        if (theoryMarks > 0 && practicalMarks > 0) {
            const sum = theoryMarks + practicalMarks;
            if (Math.abs(sum - totalMarks) > 0.01) {
                theoryMarksInput.setCustomValidity('Theory + Practical marks must equal Total marks');
                practicalMarksInput.setCustomValidity('Theory + Practical marks must equal Total marks');
            } else {
                theoryMarksInput.setCustomValidity('');
                practicalMarksInput.setCustomValidity('');
            }
        } else {
            theoryMarksInput.setCustomValidity('');
            practicalMarksInput.setCustomValidity('');
        }
    }

    // Add event listeners for marks validation
    [totalMarksInput, theoryMarksInput, practicalMarksInput].forEach(input => {
        input.addEventListener('input', validateMarks);
    });

    // Initialize on page load
    if (classSelect.value) {
        classSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
