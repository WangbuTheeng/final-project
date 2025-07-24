@extends('layouts.dashboard')

@section('title', 'Course Enrollment - Nepal University System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header with Nepal University Branding -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full p-4 mr-4">
                    <i class="fas fa-graduation-cap text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Course Enrollment System</h1>
                    <p class="text-lg text-gray-600">Nepal University - Semester Registration</p>
                </div>
            </div>
            <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                <span class="flex items-center"><i class="fas fa-calendar mr-1"></i> Academic Year 2024-2025</span>
                <span class="flex items-center"><i class="fas fa-clock mr-1"></i> Enrollment Period: Open</span>
                <span class="flex items-center"><i class="fas fa-users mr-1"></i> Tribhuvan University System</span>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mb-6">
            <a href="{{ route('enrollments.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Enrollments
            </a>
        </div>

        <!-- Enhanced Form Container with Nepal University Design -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center">
                    <div class="bg-white/20 rounded-full p-3 mr-4">
                        <i class="fas fa-user-graduate text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Course Enrollment Form</h2>
                        <p class="text-blue-100 mt-1">Complete course registration for Nepal university system</p>
                    </div>
                </div>
            </div>

            <!-- Enrollment Steps Indicator -->
            <div class="bg-gray-50 px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 bg-blue-600 rounded-full text-white text-sm font-medium">1</div>
                            <span class="ml-2 text-sm font-medium text-gray-900">Student Selection</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-0.5 bg-gray-300"></div>
                            <div class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded-full text-gray-500 text-sm font-medium">2</div>
                            <span class="ml-2 text-sm text-gray-500">Course Selection</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-0.5 bg-gray-300"></div>
                            <div class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded-full text-gray-500 text-sm font-medium">3</div>
                            <span class="ml-2 text-sm text-gray-500">Enrollment Details</span>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('enrollments.store') }}" class="space-y-8 p-8">
                @csrf

                <!-- Step 1: Academic Year & Student Selection -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-calendar-alt mr-3 text-blue-500"></i>
                            Academic Information
                        </h3>
                        <p class="text-gray-600 mt-1">Select academic year and student for enrollment</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Academic Year -->
                        <div>
                            <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Academic Year <span class="text-red-500">*</span>
                            </label>
                            <select name="academic_year_id" id="academic_year_id" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} ({{ $year->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Semester (Only for Semester System) -->
                        <div id="semester_field" style="display: none;">
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                                Semester <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Semester System Only)</span>
                            </label>
                            <select name="semester" id="semester"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Semester</option>
                                <option value="first">First Semester (Shrawan - Poush)</option>
                                <option value="second">Second Semester (Magh - Ashar)</option>
                                <option value="summer">Summer Semester (Jestha - Ashar)</option>
                            </select>
                            @error('semester')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Examination System Info -->
                        <div id="examination_system_info" class="hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900" id="system_type_text">System Type</p>
                                        <p class="text-xs text-blue-700" id="system_description">Description</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Student Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Faculty -->
                        <div>
                            <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Faculty <span class="text-red-500">*</span>
                            </label>
                            <select name="faculty_id" id="faculty_id" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Faculty</option>
                                @foreach($faculties ?? [] as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                            @error('faculty_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Student -->
                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Student <span class="text-red-500">*</span>
                            </label>
                            <select name="student_id" id="student_id" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Student</option>
                            </select>
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Student Information Display -->
                    <div id="student_info" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">Selected Student Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Name:</span>
                                <span id="student_name" class="text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Admission Number:</span>
                                <span id="student_admission" class="text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Current CGPA:</span>
                                <span id="student_cgpa" class="text-gray-900"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Course Selection -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-book mr-3 text-green-500"></i>
                            Course Selection
                        </h3>
                        <p class="text-gray-600 mt-1">Select course and class section for enrollment</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Course -->
                        <div>
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Course <span class="text-red-500">*</span>
                            </label>
                            <select name="course_id" id="course_id" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Course</option>
                            </select>
                            @error('course_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Class Section -->
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Class Section <span class="text-red-500">*</span>
                            </label>
                            <select name="class_id" id="class_id" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select Class Section</option>
                            </select>
                            @error('class_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Course Information Display -->
                    <div id="course_info" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 mb-2">Course Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Course Code:</span>
                                <span id="course_code" class="text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Credit Hours:</span>
                                <span id="course_credits" class="text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Available Seats:</span>
                                <span id="class_capacity" class="text-gray-900"></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Prerequisites:</span>
                                <span id="course_prerequisites" class="text-gray-900"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Nepal-Specific Enrollment Details -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-file-alt mr-3 text-purple-500"></i>
                            Enrollment Details (Nepal University System)
                        </h3>
                        <p class="text-gray-600 mt-1">Complete enrollment information as per Nepal university requirements</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Enrollment Date -->
                        <div>
                            <label for="enrollment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Enrollment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="enrollment_date" id="enrollment_date" required
                                   value="{{ date('Y-m-d') }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('enrollment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Enrollment Type -->
                        <div>
                            <label for="enrollment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Enrollment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="enrollment_type" id="enrollment_type" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="regular">Regular Enrollment</option>
                                <option value="late">Late Enrollment (with penalty)</option>
                                <option value="makeup">Makeup Enrollment</option>
                                <option value="readmission">Readmission</option>
                            </select>
                            @error('enrollment_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Status <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_status" id="payment_status" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="pending">Payment Pending</option>
                                <option value="paid">Paid</option>
                                <option value="partial">Partially Paid</option>
                                <option value="waived">Fee Waived (Scholarship)</option>
                            </select>
                            @error('payment_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Fee Information -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-900 mb-2 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Fee Structure (NPR)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Base Fee:</span>
                                <span class="text-gray-900">NPR 2,000</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Per Credit:</span>
                                <span class="text-gray-900">NPR 150</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Late Penalty:</span>
                                <span class="text-gray-900">NPR 500</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Total Fee:</span>
                                <span id="total_fee" class="text-gray-900 font-semibold">NPR 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Nepal University Requirements -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Attendance Requirement -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="attendance_required" id="attendance_required" checked
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    Minimum 75% attendance required (Nepal University Standard)
                                </span>
                            </label>
                        </div>

                        <!-- Prerequisites Check -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="prerequisites_met" id="prerequisites_met" checked
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    All course prerequisites have been met
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Enrollment Notes -->
                    <div>
                        <label for="enrollment_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Enrollment Notes (Optional)
                        </label>
                        <textarea name="enrollment_notes" id="enrollment_notes" rows="3"
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Any special notes or remarks for this enrollment..."></textarea>
                        @error('enrollment_notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        All fields marked with <span class="text-red-500">*</span> are required
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('enrollments.index') }}"
                           class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Enroll Student
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Nepal-specific enrollment form JavaScript
    const academicYearSelect = document.getElementById('academic_year_id');
    const semesterSelect = document.getElementById('semester');
    const facultySelect = document.getElementById('faculty_id');
    const studentSelect = document.getElementById('student_id');
    const courseSelect = document.getElementById('course_id');
    const classSelect = document.getElementById('class_id');
    const enrollmentTypeSelect = document.getElementById('enrollment_type');
    const totalFeeSpan = document.getElementById('total_fee');

    // Student info display elements
    const studentInfo = document.getElementById('student_info');
    const studentName = document.getElementById('student_name');
    const studentAdmission = document.getElementById('student_admission');
    const studentCgpa = document.getElementById('student_cgpa');

    // Course info display elements
    const courseInfo = document.getElementById('course_info');
    const courseCode = document.getElementById('course_code');
    const courseCredits = document.getElementById('course_credits');
    const classCapacity = document.getElementById('class_capacity');
    const coursePrerequisites = document.getElementById('course_prerequisites');

    // Load faculties when academic year changes
    academicYearSelect.addEventListener('change', function() {
        loadFaculties();
        resetSelections(['faculty_id', 'student_id', 'course_id', 'class_id']);
    });

    // Load students when faculty changes
    facultySelect.addEventListener('change', function() {
        loadStudents();
        loadCourses();
        resetSelections(['student_id', 'course_id', 'class_id']);
    });

    // Load courses when semester changes
    semesterSelect.addEventListener('change', function() {
        if (facultySelect.value) {
            loadCourses();
        }
        resetSelections(['course_id', 'class_id']);
    });

    // Show student info when student is selected
    studentSelect.addEventListener('change', function() {
        if (this.value) {
            showStudentInfo();
        } else {
            hideStudentInfo();
        }
    });

    // Load classes when course changes and handle examination system
    courseSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const examinationSystem = selectedOption.dataset.system;

            // Show/hide semester field based on examination system
            const semesterField = document.getElementById('semester_field');
            const systemInfo = document.getElementById('examination_system_info');
            const systemTypeText = document.getElementById('system_type_text');
            const systemDescription = document.getElementById('system_description');

            if (examinationSystem === 'semester') {
                semesterField.style.display = 'block';
                semesterSelect.required = true;
                systemTypeText.textContent = 'Semester System';
                systemDescription.textContent = 'This course uses semester-based enrollment and examination.';
            } else {
                semesterField.style.display = 'none';
                semesterSelect.required = false;
                semesterSelect.value = ''; // Clear semester selection
                systemTypeText.textContent = 'Annual System';
                systemDescription.textContent = 'This course uses annual enrollment and yearly examination.';
            }

            systemInfo.classList.remove('hidden');
            loadClasses();
        } else {
            document.getElementById('semester_field').style.display = 'none';
            document.getElementById('examination_system_info').classList.add('hidden');
            semesterSelect.required = false;
        }
        resetSelections(['class_id']);
    });

    // Show course info when class is selected
    classSelect.addEventListener('change', function() {
        if (this.value) {
            showCourseInfo();
            calculateFee();
        } else {
            hideCourseInfo();
        }
    });

    // Recalculate fee when enrollment type changes
    enrollmentTypeSelect.addEventListener('change', function() {
        calculateFee();
    });

    function loadFaculties() {
        if (!academicYearSelect.value) return;

        fetch(`/ajax/faculties?academic_year_id=${academicYearSelect.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                facultySelect.innerHTML = '<option value="">Select Faculty</option>';
                if (data.faculties) {
                    data.faculties.forEach(faculty => {
                        facultySelect.innerHTML += `<option value="${faculty.id}">${faculty.name}</option>`;
                    });
                } else if (Array.isArray(data)) {
                    data.forEach(faculty => {
                        facultySelect.innerHTML += `<option value="${faculty.id}">${faculty.name}</option>`;
                    });
                }
                facultySelect.disabled = false;
            })
            .catch(error => console.error('Error loading faculties:', error));
    }

    function loadStudents() {
        if (!facultySelect.value) return;

        fetch(`/ajax/students?faculty_id=${facultySelect.value}&academic_year_id=${academicYearSelect.value}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                studentSelect.innerHTML = '<option value="">Select Student</option>';
                const students = data.students || data;
                if (Array.isArray(students)) {
                    students.forEach(student => {
                        const firstName = student.user?.first_name || student.user?.name?.split(' ')[0] || '';
                        const lastName = student.user?.last_name || student.user?.name?.split(' ').slice(1).join(' ') || '';
                        const fullName = student.user?.name || `${firstName} ${lastName}`.trim();
                        studentSelect.innerHTML += `<option value="${student.id}" data-name="${fullName}" data-admission="${student.admission_number}" data-cgpa="${student.cgpa || 'N/A'}">${fullName} (${student.admission_number})</option>`;
                    });
                }
                studentSelect.disabled = false;
            })
            .catch(error => console.error('Error loading students:', error));
    }

    function loadCourses() {
        if (!facultySelect.value) return;

        // Build query parameters
        let params = `faculty_id=${facultySelect.value}&academic_year_id=${academicYearSelect.value}`;
        if (semesterSelect.value && document.getElementById('semester_field').style.display !== 'none') {
            params += `&semester=${semesterSelect.value}`;
        }

        fetch(`/ajax/courses/by-faculty?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                courseSelect.innerHTML = '<option value="">Select Course</option>';
                const courses = data.courses || data;
                if (Array.isArray(courses)) {
                    courses.forEach(course => {
                        const systemBadge = course.examination_system === 'annual' ?
                            '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Annual</span>' :
                            '<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Semester</span>';

                        courseSelect.innerHTML += `<option value="${course.id}"
                            data-code="${course.code}"
                            data-credits="${course.credit_units || course.credit_hours}"
                            data-system="${course.examination_system}"
                            data-prerequisites="${course.prerequisites || 'None'}">
                            ${course.code} - ${course.title || course.name} (${(course.examination_system || 'semester').toUpperCase()})
                        </option>`;
                    });
                }
                courseSelect.disabled = false;
            })
            .catch(error => console.error('Error loading courses:', error));
    }

    function loadClasses() {
        if (!courseSelect.value) return;

        // Build query parameters
        let params = `course_id=${courseSelect.value}&academic_year_id=${academicYearSelect.value}`;

        // Only add semester if it's required (semester system)
        const selectedCourse = courseSelect.options[courseSelect.selectedIndex];
        if (selectedCourse && selectedCourse.dataset.system === 'semester' && semesterSelect.value) {
            params += `&semester=${semesterSelect.value}`;
        }

        fetch(`/ajax/classes/by-course?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                classSelect.innerHTML = '<option value="">Select Class Section</option>';
                const classes = data.classes || data;
                if (Array.isArray(classes)) {
                    classes.forEach(classSection => {
                        const available = classSection.available_slots || (classSection.capacity - (classSection.enrolled_count || classSection.current_enrollment || 0));
                        const total = classSection.capacity;
                        classSelect.innerHTML += `<option value="${classSection.id}" data-capacity="${available}" data-total="${total}">${classSection.name} (${available}/${total} available)</option>`;
                    });
                }
                classSelect.disabled = false;
            })
            .catch(error => console.error('Error loading classes:', error));
    }

    function showStudentInfo() {
        const selectedOption = studentSelect.options[studentSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            studentName.textContent = selectedOption.dataset.name;
            studentAdmission.textContent = selectedOption.dataset.admission;
            studentCgpa.textContent = selectedOption.dataset.cgpa;
            studentInfo.classList.remove('hidden');
        }
    }

    function hideStudentInfo() {
        studentInfo.classList.add('hidden');
    }

    function showCourseInfo() {
        const courseOption = courseSelect.options[courseSelect.selectedIndex];
        const classOption = classSelect.options[classSelect.selectedIndex];

        if (courseOption && classOption && courseOption.value && classOption.value) {
            courseCode.textContent = courseOption.dataset.code;
            courseCredits.textContent = courseOption.dataset.credits + ' credits';
            classCapacity.textContent = classOption.dataset.capacity + '/' + classOption.dataset.total;
            coursePrerequisites.textContent = courseOption.dataset.prerequisites;
            courseInfo.classList.remove('hidden');
        }
    }

    function hideCourseInfo() {
        courseInfo.classList.add('hidden');
    }

    function calculateFee() {
        const courseOption = courseSelect.options[courseSelect.selectedIndex];
        if (!courseOption || !courseOption.value) return;

        const credits = parseFloat(courseOption.dataset.credits) || 0;
        const baseFee = 2000; // NPR 2000
        const perCreditFee = 150; // NPR 150 per credit
        const latePenalty = enrollmentTypeSelect.value === 'late' ? 500 : 0;

        const totalFee = baseFee + (credits * perCreditFee) + latePenalty;
        totalFeeSpan.textContent = `NPR ${totalFee.toLocaleString()}`;
    }

    function resetSelections(selectIds) {
        selectIds.forEach(id => {
            const select = document.getElementById(id);
            if (select) {
                select.innerHTML = '<option value="">Select ' + select.previousElementSibling.textContent.replace(' *', '') + '</option>';
                select.disabled = true;
            }
        });
        hideStudentInfo();
        hideCourseInfo();
    }

    // Initialize form
    if (academicYearSelect.value) {
        loadFaculties();
    }
});
</script>
@endpush
