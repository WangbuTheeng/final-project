@extends('layouts.dashboard')

@section('title', 'Create Enrollment')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Create New Enrollment</h1>
        <a href="{{ route('enrollments.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4 sm:mt-0">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Enrollments
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Hierarchical Selection -->
        <div class="lg:col-span-1 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Selection Filters</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Academic Year -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year <span class="text-red-600">*</span></label>
                    <div class="mt-1">
                        <select name="academic_year_id" id="academic_year_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}"
                                        {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <!-- Faculty -->
                <div>
                    <label for="faculty_id" class="block text-sm font-medium text-gray-700">Faculty <span class="text-red-600">*</span></label>
                    <div class="mt-1">
                        <select name="faculty_id" id="faculty_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required disabled>
                            <option value="">Select Faculty</option>
                        </select>
                    </div>
                    <div id="faculty_loading" class="hidden mt-2">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading faculties...
                        </div>
                    </div>
                </div>

                <!-- Course -->
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700">Course <span class="text-red-600">*</span></label>
                    <div class="mt-1">
                        <select name="course_id" id="course_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required disabled>
                            <option value="">Select Course</option>
                        </select>
                    </div>
                    <div id="course_loading" class="hidden mt-2">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading courses...
                        </div>
                    </div>
                </div>

                <!-- Class -->
                <div>
                    <label for="class_selection" class="block text-sm font-medium text-gray-700">Class <span class="text-red-600">*</span></label>
                    <div class="mt-1">
                        <select name="class_selection" id="class_selection"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required disabled>
                            <option value="">Select Class</option>
                        </select>
                    </div>
                    <div id="class_loading" class="hidden mt-2">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading classes...
                        </div>
                    </div>
                </div>

                <!-- Selection Summary -->
                <div id="selection_summary" class="hidden mt-4 p-3 bg-blue-50 rounded-md border border-blue-200">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Selection Summary</h4>
                    <div class="text-xs text-blue-700 space-y-1">
                        <div id="summary_academic_year"></div>
                        <div id="summary_semester"></div>
                        <div id="summary_faculty"></div>
                        <div id="summary_course"></div>
                        <div id="summary_class"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Course Information -->
            <div id="course_info_section" class="hidden bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Course Information</h3>
                </div>
                <div class="p-6">
                    <div id="course_details" class="space-y-3">
                        <!-- Course details will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Available Classes -->
            <div id="classes_section" class="hidden bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Available Classes</h3>
                </div>
                <div class="p-6">
                    <div id="classes_list" class="space-y-3">
                        <!-- Classes will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Enrollment Form -->
            <div id="enrollment_form_section" class="hidden bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Enroll Student</h3>
                </div>
                <div class="p-6">
                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700 font-medium">Please fix the following errors:</p>
                                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('enrollments.store') }}" id="enrollment_form" class="space-y-4" onsubmit="console.log('Form submitted via onsubmit')">
                        @csrf
                        <input type="hidden" name="academic_year_id" id="form_academic_year_id">
                        <input type="hidden" name="class_id" id="form_class_id">

                        @error('academic_year_id')
                            <div class="text-sm text-red-600 mb-2">{{ $message }}</div>
                        @enderror
                        @error('class_id')
                            <div class="text-sm text-red-600 mb-2">{{ $message }}</div>
                        @enderror

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="student_id" class="block text-sm font-medium text-gray-700">Student <span class="text-red-600">*</span></label>
                                <div class="mt-1">
                                    <select name="student_id" id="student_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                        <option value="">Select Student</option>
                                    </select>
                                    @error('student_id')
                                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div id="student_loading" class="hidden mt-2">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Loading students...
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="selected_class_display" class="block text-sm font-medium text-gray-700">Selected Class</label>
                                <div class="mt-1">
                                    <input type="text" id="selected_class_display"
                                           class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm"
                                           readonly placeholder="No class selected">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="enrollment_date" class="block text-sm font-medium text-gray-700">Enrollment Date <span class="text-red-600">*</span></label>
                            <div class="mt-1">
                                <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                       id="enrollment_date" name="enrollment_date"
                                       value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                                @error('enrollment_date')
                                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('enrollments.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" id="submit_btn"
                                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Create Enrollment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Initial State Message -->
            <div id="initial_message" class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 text-center py-12 space-y-3">
                    <i class="fas fa-graduation-cap fa-3x text-gray-400 mb-3"></i>
                    <h5 class="text-lg font-medium text-gray-900">Start Enrollment Process</h5>
                    <p class="text-gray-500">Please select Academic Year and Semester to begin the enrollment process.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up CSRF token for all AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const academicYearSelect = document.getElementById('academic_year_id');
    const facultySelect = document.getElementById('faculty_id');
    const courseSelect = document.getElementById('course_id');
    const classSelect = document.getElementById('class_selection');
    const studentSelect = document.getElementById('student_id');

    const facultyLoading = document.getElementById('faculty_loading');
    const courseLoading = document.getElementById('course_loading');
    const classLoading = document.getElementById('class_loading');
    const studentLoading = document.getElementById('student_loading');

    const selectionSummary = document.getElementById('selection_summary');
    const courseInfoSection = document.getElementById('course_info_section');
    const classesSection = document.getElementById('classes_section');
    const enrollmentFormSection = document.getElementById('enrollment_form_section');
    const initialMessage = document.getElementById('initial_message');

    let selectedCourse = null;
    let selectedClass = null;
    let availableClasses = [];

    // Initialize based on current selections
    if (academicYearSelect.value) {
        loadFaculties();
    }

    // Academic Year change handler
    academicYearSelect.addEventListener('change', function() {
        resetSelections(['faculty', 'course', 'class', 'student']);
        if (this.value) {
            loadFaculties();
        } else {
            hideAllSections();
        }
        updateSummary();
    });

    // Faculty change handler
    facultySelect.addEventListener('change', function() {
        resetSelections(['course', 'class', 'student']);
        if (this.value) {
            loadCourses();
        } else {
            hideCourseSections();
        }
        updateSummary();
    });

    // Course change handler
    courseSelect.addEventListener('change', function() {
        resetSelections(['class', 'student']);
        if (this.value) {
            loadClasses();
        } else {
            hideClassSections();
        }
        updateSummary();
    });

    // Class selection handler
    classSelect.addEventListener('change', function() {
        if (this.value) {
            selectedClass = availableClasses.find(c => c.id == this.value);
            showEnrollmentForm();
            loadStudents();
        } else {
            selectedClass = null;
            hideEnrollmentForm();
        }
        updateSummary();
    });

    // Add form submission debugging
    const enrollmentForm = document.getElementById('enrollment_form');
    enrollmentForm.addEventListener('submit', function(e) {
        console.log('Form submission started');
        console.log('Academic Year ID:', document.getElementById('form_academic_year_id').value);
        console.log('Class ID:', document.getElementById('form_class_id').value);
        console.log('Student ID:', document.getElementById('student_id').value);
        console.log('Enrollment Date:', document.getElementById('enrollment_date').value);

        // Check if required fields are filled
        if (!document.getElementById('form_academic_year_id').value) {
            e.preventDefault();
            alert('Academic Year is required');
            return false;
        }
        if (!document.getElementById('form_class_id').value) {
            e.preventDefault();
            alert('Class is required');
            return false;
        }
        if (!document.getElementById('student_id').value) {
            e.preventDefault();
            alert('Student is required');
            return false;
        }

        console.log('Form validation passed, submitting...');
    });

    function resetSelections(types) {
        if (types.includes('faculty')) {
            facultySelect.innerHTML = '<option value="">Select Faculty</option>';
            facultySelect.disabled = true;
        }
        if (types.includes('course')) {
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            courseSelect.disabled = true;
            selectedCourse = null;
        }
        if (types.includes('class')) {
            classSelect.innerHTML = '<option value="">Select Class</option>';
            classSelect.disabled = true;
            selectedClass = null;
            availableClasses = [];
        }
        if (types.includes('student')) {
            studentSelect.innerHTML = '<option value="">Select Student</option>';
        }
    }

    function hideAllSections() {
        initialMessage.classList.remove('hidden');
        courseInfoSection.classList.add('hidden');
        classesSection.classList.add('hidden');
        enrollmentFormSection.classList.add('hidden');
        selectionSummary.classList.add('hidden');
    }

    function hideCourseSections() {
        courseInfoSection.classList.add('hidden');
        classesSection.classList.add('hidden');
        enrollmentFormSection.classList.add('hidden');
    }

    function hideClassSections() {
        classesSection.classList.add('hidden');
        enrollmentFormSection.classList.add('hidden');
    }

    function hideEnrollmentForm() {
        enrollmentFormSection.classList.add('hidden');
    }

    function showEnrollmentForm() {
        initialMessage.classList.add('hidden');
        enrollmentFormSection.classList.remove('hidden');

        // Update form fields
        document.getElementById('form_academic_year_id').value = academicYearSelect.value;
        document.getElementById('form_class_id').value = classSelect.value;
        document.getElementById('selected_class_display').value = selectedClass ? selectedClass.name : '';
    }

    async function loadFaculties() {
        if (!academicYearSelect.value) return;

        facultyLoading.classList.remove('hidden');
        facultySelect.disabled = true;

        try {
            const response = await fetch(`/ajax/faculties?academic_year_id=${academicYearSelect.value}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            facultySelect.innerHTML = '<option value="">Select Faculty</option>';
            data.faculties.forEach(faculty => {
                const option = document.createElement('option');
                option.value = faculty.id;
                option.textContent = faculty.name;
                facultySelect.appendChild(option);
            });

            facultySelect.disabled = false;
        } catch (error) {
            console.error('Error loading faculties:', error);
            alert('Error loading faculties. Please try again.');
        } finally {
            facultyLoading.classList.add('hidden');
        }
    }

    async function loadCourses() {
        if (!facultySelect.value || !academicYearSelect.value) return;

        courseLoading.classList.remove('hidden');
        courseSelect.disabled = true;

        try {
            const response = await fetch(`/ajax/courses/by-faculty?faculty_id=${facultySelect.value}&academic_year_id=${academicYearSelect.value}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            courseSelect.innerHTML = '<option value="">Select Course</option>';
            data.courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.id;
                option.textContent = `${course.code} - ${course.title}`;
                option.dataset.course = JSON.stringify(course);
                courseSelect.appendChild(option);
            });

            courseSelect.disabled = false;
        } catch (error) {
            console.error('Error loading courses:', error);
            alert('Error loading courses. Please try again.');
        } finally {
            courseLoading.classList.add('hidden');
        }
    }

    async function loadClasses() {
        if (!courseSelect.value || !academicYearSelect.value) return;

        classLoading.classList.remove('hidden');
        classSelect.disabled = true;

        // Get selected course data
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        selectedCourse = JSON.parse(selectedOption.dataset.course);

        try {
            const response = await fetch(`/ajax/classes/by-course?course_id=${courseSelect.value}&academic_year_id=${academicYearSelect.value}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            availableClasses = data.classes;

            // Show course information
            showCourseInfo();

            // Show classes section
            showClassesSection();

            // Populate class select
            classSelect.innerHTML = '<option value="">Select Class</option>';
            data.classes.forEach(classItem => {
                const option = document.createElement('option');
                option.value = classItem.id;
                option.textContent = `${classItem.name} (${classItem.current_enrollment}/${classItem.capacity} enrolled)`;
                classSelect.appendChild(option);
            });

            classSelect.disabled = false;
        } catch (error) {
            console.error('Error loading classes:', error);
            alert('Error loading classes. Please try again.');
        } finally {
            classLoading.classList.add('hidden');
        }
    }

    function showCourseInfo() {
        if (!selectedCourse) return;

        const courseDetails = document.getElementById('course_details');
        courseDetails.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Course Code:</span>
                    <span class="ml-2 text-sm text-gray-900">${selectedCourse.code}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Credit Units:</span>
                    <span class="ml-2 text-sm text-gray-900">${selectedCourse.credit_units}</span>
                </div>
                <div class="md:col-span-2">
                    <span class="text-sm font-medium text-gray-500">Course Title:</span>
                    <span class="ml-2 text-sm text-gray-900">${selectedCourse.title}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Course Type:</span>
                    <span class="ml-2 text-sm text-gray-900 capitalize">${selectedCourse.course_type}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Organization:</span>
                    <span class="ml-2 text-sm text-gray-900">${selectedCourse.period_display}</span>
                </div>
            </div>
        `;

        initialMessage.classList.add('hidden');
        courseInfoSection.classList.remove('hidden');
    }

    function showClassesSection() {
        if (!availableClasses.length) return;

        const classesList = document.getElementById('classes_list');
        classesList.innerHTML = availableClasses.map(classItem => `
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer class-item" data-class-id="${classItem.id}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">${classItem.name}</h4>
                        <div class="mt-1 text-xs text-gray-500 space-y-1">
                            <div>Instructor: ${classItem.instructor ? classItem.instructor.name : 'Not assigned'}</div>
                            <div>Room: ${classItem.room || 'TBA'}</div>
                            <div>Capacity: ${classItem.current_enrollment}/${classItem.capacity} students</div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="w-16 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: ${classItem.enrollment_percentage}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 text-center">${classItem.enrollment_percentage}%</div>
                    </div>
                </div>
            </div>
        `).join('');

        // Add click handlers to class items
        classesList.querySelectorAll('.class-item').forEach(item => {
            item.addEventListener('click', function() {
                const classId = this.dataset.classId;
                classSelect.value = classId;
                classSelect.dispatchEvent(new Event('change'));

                // Visual feedback
                classesList.querySelectorAll('.class-item').forEach(i => i.classList.remove('ring-2', 'ring-blue-500'));
                this.classList.add('ring-2', 'ring-blue-500');
            });
        });

        classesSection.classList.remove('hidden');
    }

    async function loadStudents() {
        if (!selectedClass || !selectedCourse) return;

        studentLoading.classList.remove('hidden');

        try {
            // Build query parameters for student loading
            let queryParams = new URLSearchParams();
            queryParams.append('status', 'active');

            console.log('Selected course:', selectedCourse);
            console.log('Faculty select value:', facultySelect.value);

            // Try to use department first, then fall back to faculty
            if (selectedCourse.department && selectedCourse.department.id) {
                queryParams.append('department_id', selectedCourse.department.id);
                console.log('Using department_id:', selectedCourse.department.id);
            } else if (facultySelect.value) {
                queryParams.append('faculty_id', facultySelect.value);
                console.log('Using faculty_id:', facultySelect.value);
            } else {
                console.log('No department or faculty available for filtering');
            }

            const url = `/ajax/students?${queryParams.toString()}`;
            console.log('Loading students from:', url);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Response error:', errorText);
                throw new Error(`HTTP ${response.status}: ${response.statusText} - ${errorText}`);
            }

            const data = await response.json();
            console.log('Students data:', data);

            studentSelect.innerHTML = '<option value="">Select Student</option>';
            if (data.students && data.students.length > 0) {
                data.students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = `${student.admission_number} - ${student.user.full_name}`;
                    studentSelect.appendChild(option);
                });
                console.log(`Loaded ${data.students.length} students`);
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No students available';
                option.disabled = true;
                studentSelect.appendChild(option);
                console.log('No students found');
            }
        } catch (error) {
            console.error('Error loading students:', error);
            studentSelect.innerHTML = '<option value="">Error loading students</option>';
        } finally {
            studentLoading.classList.add('hidden');
        }
    }

    function updateSummary() {
        const academicYearText = academicYearSelect.options[academicYearSelect.selectedIndex]?.text || '';
        const facultyText = facultySelect.options[facultySelect.selectedIndex]?.text || '';
        const courseText = courseSelect.options[courseSelect.selectedIndex]?.text || '';
        const classText = classSelect.options[classSelect.selectedIndex]?.text || '';

        document.getElementById('summary_academic_year').textContent = academicYearText ? `Academic Year: ${academicYearText}` : '';
        document.getElementById('summary_faculty').textContent = facultyText ? `Faculty: ${facultyText}` : '';
        document.getElementById('summary_course').textContent = courseText ? `Course: ${courseText}` : '';
        document.getElementById('summary_class').textContent = classText ? `Class: ${classText}` : '';

        // Hide semester summary since we don't have a semester selector
        document.getElementById('summary_semester').textContent = '';

        if (academicYearText || facultyText || courseText || classText) {
            selectionSummary.classList.remove('hidden');
        } else {
            selectionSummary.classList.add('hidden');
        }
    }
});
</script>
@endsection
