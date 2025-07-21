@extends('layouts.dashboard')

@section('title', 'Bulk Enrollment')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Bulk Student Enrollment</h1>
        <a href="{{ route('enrollments.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4 sm:mt-0">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Enrollments
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-users mr-2"></i> Enroll Multiple Students in Courses
            </h3>
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

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Bulk Enrollment:</strong> This will enroll all active students from the selected faculty
                            into the selected classes for the specified academic year.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('enrollments.bulk-store') }}" id="bulkEnrollmentForm" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year <span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select name="academic_year_id" id="academic_year_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                        {{ $year->name }}
                                        @if($year->is_current) (Current) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="faculty_id" class="block text-sm font-medium text-gray-700">Faculty <span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select name="faculty_id" id="faculty_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                <option value="">Select Faculty</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}">
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_id')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Nepal-Specific Enrollment Fields -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-4">
                    <h4 class="text-sm font-medium text-blue-900 flex items-center">
                        <i class="fas fa-flag mr-2"></i>
                        Nepal University Enrollment Details
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="enrollment_date" class="block text-sm font-medium text-gray-700">Enrollment Date <span class="text-red-600">*</span></label>
                            <div class="mt-1">
                                <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" id="enrollment_date" name="enrollment_date"
                                       value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                                @error('enrollment_date')
                                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="enrollment_type" class="block text-sm font-medium text-gray-700">Enrollment Type <span class="text-red-600">*</span></label>
                            <div class="mt-1">
                                <select name="enrollment_type" id="enrollment_type" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Type</option>
                                    <option value="regular" selected>Regular Enrollment</option>
                                    <option value="late">Late Enrollment (+NPR 500)</option>
                                    <option value="makeup">Makeup Enrollment</option>
                                    <option value="readmission">Readmission</option>
                                </select>
                                @error('enrollment_type')
                                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status <span class="text-red-600">*</span></label>
                            <div class="mt-1">
                                <select name="payment_status" id="payment_status" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Status</option>
                                    <option value="pending" selected>Pending Payment</option>
                                    <option value="paid">Paid</option>
                                    <option value="partial">Partial Payment</option>
                                    <option value="waived">Fee Waived (Scholarship)</option>
                                </select>
                                @error('payment_status')
                                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Semester Field (Dynamic based on course selection) -->
                    <div id="semester_field" style="display: none;">
                        <label for="semester" class="block text-sm font-medium text-gray-700">
                            Semester <span class="text-red-600">*</span>
                            <span class="text-xs text-gray-500">(Required for Semester System Courses)</span>
                        </label>
                        <div class="mt-1">
                            <select name="semester" id="semester"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="">Select Semester</option>
                                <option value="first">First Semester (Shrawan - Poush)</option>
                                <option value="second">Second Semester (Magh - Ashar)</option>
                                <option value="summer">Summer Semester (Jestha - Ashar)</option>
                            </select>
                            @error('semester')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Fee Information -->
                    <div class="bg-white border border-gray-200 rounded p-3">
                        <h5 class="text-xs font-medium text-gray-700 mb-2">Nepal University Fee Structure</h5>
                        <div class="text-xs text-gray-600 space-y-1">
                            <div>• Base Enrollment Fee: NPR 2,000</div>
                            <div>• Per Credit Fee: NPR 150 per credit hour</div>
                            <div>• Late Enrollment Penalty: NPR 500 (if applicable)</div>
                            <div>• Attendance Requirement: 75% minimum</div>
                        </div>
                    </div>
                </div>

                <!-- Courses Selection -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Select Courses <span class="text-red-600">*</span></label>
                        <button type="button" id="addCourseBtn" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-1"></i> Add New Course
                        </button>
                    </div>

                    <!-- Add Course Form (Hidden by default) -->
                    <div id="addCourseForm" class="hidden mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Add New Course</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Course Code</label>
                                <input type="text" id="new_course_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="e.g., CS101">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Course Title</label>
                                <input type="text" id="new_course_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="e.g., Introduction to Computer Science">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Credit Units</label>
                                <input type="number" id="new_course_credits" min="1" max="6" value="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Course Type</label>
                                <select id="new_course_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="core">Core</option>
                                    <option value="elective">Elective</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Organization Type</label>
                                <select id="new_course_organization" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="yearly">Yearly</option>
                                    <option value="semester">Semester</option>
                                </select>
                            </div>
                            <div id="yearField">
                                <label class="block text-sm font-medium text-gray-700">Year</label>
                                <select id="new_course_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                    <option value="5">5th Year</option>
                                </select>
                            </div>
                            <div id="semesterField" class="hidden">
                                <label class="block text-sm font-medium text-gray-700">Semester Period</label>
                                <select id="new_course_semester" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="1">1st Semester</option>
                                    <option value="2">2nd Semester</option>
                                    <option value="3">3rd Semester</option>
                                    <option value="4">4th Semester</option>
                                    <option value="5">5th Semester</option>
                                    <option value="6">6th Semester</option>
                                    <option value="7">7th Semester</option>
                                    <option value="8">8th Semester</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="new_course_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Course description..."></textarea>
                        </div>
                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" id="cancelCourseBtn" class="inline-flex items-center px-3 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </button>
                            <button type="button" id="saveCourseBtn" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-1"></i> Save Course
                            </button>
                        </div>
                    </div>

                    <div id="coursesContainer" class="mt-1">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Please select academic year and faculty to load available courses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('course_ids')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Classes Selection -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Select Classes <span class="text-red-600">*</span></label>
                        <button type="button" id="addClassBtn" class="inline-flex items-center px-3 py-1 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-1"></i> Add New Class
                        </button>
                    </div>

                    <!-- Add Class Form (Hidden by default) -->
                    <div id="addClassForm" class="hidden mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Add New Class</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Class Name</label>
                                <input type="text" id="new_class_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="e.g., CS101-A, Morning Section">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Course</label>
                                <select id="new_class_course" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Course</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Capacity</label>
                                <input type="number" id="new_class_capacity" min="1" max="200" value="30" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Room</label>
                                <input type="text" id="new_class_room" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="e.g., Room 101, Lab A">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semester</label>
                                <select id="new_class_semester" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="1">1st Semester</option>
                                    <option value="2">2nd Semester</option>
                                    <option value="3">3rd Semester</option>
                                    <option value="4">4th Semester</option>
                                    <option value="5">5th Semester</option>
                                    <option value="6">6th Semester</option>
                                    <option value="7">7th Semester</option>
                                    <option value="8">8th Semester</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Year</label>
                                <select id="new_class_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                    <option value="5">5th Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700">Schedule (Optional)</label>
                            <textarea id="new_class_schedule" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="e.g., Mon-Wed-Fri 9:00-10:00 AM"></textarea>
                        </div>
                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" id="cancelClassBtn" class="inline-flex items-center px-3 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </button>
                            <button type="button" id="saveClassBtn" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-1"></i> Save Class
                            </button>
                        </div>
                    </div>

                    <div id="classesContainer" class="mt-1">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Please select courses to load available classes.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('class_ids')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Student Preview -->
                <div>
                    <h6 class="text-lg font-medium text-gray-900 mb-2">Students to be Enrolled</h6>
                    <div id="studentsPreview">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">Select department and level to preview students.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('enrollments.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" id="submitBtn" disabled>
                        <i class="fas fa-users mr-2"></i> Enroll Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const facultySelect = document.getElementById('faculty_id');
    const academicYearSelect = document.getElementById('academic_year_id');
    const coursesContainer = document.getElementById('coursesContainer');
    const classesContainer = document.getElementById('classesContainer');
    const studentsPreview = document.getElementById('studentsPreview');
    const submitBtn = document.getElementById('submitBtn');

    // Add Course/Class form elements
    const addCourseBtn = document.getElementById('addCourseBtn');
    const addCourseForm = document.getElementById('addCourseForm');
    const cancelCourseBtn = document.getElementById('cancelCourseBtn');
    const saveCourseBtn = document.getElementById('saveCourseBtn');
    const addClassBtn = document.getElementById('addClassBtn');
    const addClassForm = document.getElementById('addClassForm');
    const cancelClassBtn = document.getElementById('cancelClassBtn');
    const saveClassBtn = document.getElementById('saveClassBtn');

    function loadCourses() {
        const facultyId = facultySelect.value;
        const academicYearId = academicYearSelect.value;

        console.log('Loading courses for faculty:', facultyId, 'academic year:', academicYearId);

        if (facultyId && academicYearId) {
            // Show loading
            coursesContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-primary-500 text-2xl"></i><p class="text-gray-500 mt-2">Loading courses...</p></div>';

            const url = `/api/courses?faculty_id=${facultyId}&academic_year_id=${academicYearId}`;
            console.log('Fetching courses from URL:', url);

            // Fetch courses using the correct endpoint
            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Courses response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Courses data:', data);
                    if (data.courses && data.courses.length > 0) {
                        let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                        data.courses.forEach(course => {
                            const systemBadge = course.examination_system === 'annual' ?
                                '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Annual</span>' :
                                '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Semester</span>';

                            html += `
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="form-checkbox h-5 w-5 text-primary-600 course-checkbox"
                                               value="${course.id}" id="course_${course.id}"
                                               data-examination-system="${course.examination_system}">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <div class="flex items-center gap-2">
                                                <strong>${course.code}</strong> - ${course.title}
                                                ${systemBadge}
                                            </div>
                                            <div class="text-xs text-gray-500">${course.credit_units} credits | ${course.classes_count} class(es)</div>
                                            <div class="text-xs text-gray-400">Enrollment: ${course.current_enrollment}/${course.total_capacity}</div>
                                        </span>
                                    </label>
                                </div>
                            `;
                        });
                        html += '</div>';
                        coursesContainer.innerHTML = html;

                        // Add event listeners to checkboxes
                        document.querySelectorAll('.course-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                handleCourseSelection();
                                loadClasses();
                                validateForm();
                            });
                        });
                    } else {
                        coursesContainer.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">No courses found for the selected faculty and academic year.</p></div></div></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading courses:', error);
                    coursesContainer.innerHTML = '<div class="bg-red-50 border-l-4 border-red-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-times-circle text-red-400"></i></div><div class="ml-3"><p class="text-sm text-red-700">Error loading courses. Please try again.</p></div></div></div>';
                });
        } else {
            coursesContainer.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-info-circle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">Please select academic year and faculty to load available courses.</p></div></div></div>';
        }
    }

    function loadClasses() {
        const selectedCourses = Array.from(document.querySelectorAll('.course-checkbox:checked')).map(cb => cb.value);
        const academicYearId = academicYearSelect.value;

        if (selectedCourses.length > 0 && academicYearId) {
            // Show loading
            classesContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-primary-500 text-2xl"></i><p class="text-gray-500 mt-2">Loading classes...</p></div>';

            // Fetch classes using the correct endpoint
            fetch(`/api/classes/by-courses?course_ids[]=${selectedCourses.join('&course_ids[]=')}&academic_year_id=${academicYearId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.classes && data.classes.length > 0) {
                        let html = '<div class="grid grid-cols-1 gap-4">';
                        data.classes.forEach(classItem => {
                            const availabilityColor = classItem.available_slots > 0 ? 'text-green-600' : 'text-red-600';
                            html += `
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <label class="inline-flex items-start">
                                        <input type="checkbox" class="form-checkbox h-5 w-5 text-primary-600 class-checkbox mt-1"
                                               name="class_ids[]" value="${classItem.id}" id="class_${classItem.id}">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                ${classItem.name} - ${classItem.course.code}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                ${classItem.course.title} (${classItem.course.credit_units} credits)
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <span class="${availabilityColor}">
                                                    ${classItem.current_enrollment}/${classItem.capacity} enrolled
                                                    (${classItem.available_slots} slots available)
                                                </span>
                                                ${classItem.instructor ? `| Instructor: ${classItem.instructor.name}` : ''}
                                                ${classItem.room ? `| Room: ${classItem.room}` : ''}
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            `;
                        });
                        html += '</div>';
                        classesContainer.innerHTML = html;

                        // Add event listeners to checkboxes
                        document.querySelectorAll('.class-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                loadStudents();
                                validateForm();
                            });
                        });
                    } else {
                        classesContainer.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">No classes found for the selected courses.</p></div></div></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading classes:', error);
                    classesContainer.innerHTML = '<div class="bg-red-50 border-l-4 border-red-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-times-circle text-red-400"></i></div><div class="ml-3"><p class="text-sm text-red-700">Error loading classes. Please try again.</p></div></div></div>';
                });
        } else {
            classesContainer.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-info-circle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">Please select courses to load available classes.</p></div></div></div>';
        }
    }

    function loadStudents() {
        const facultyId = facultySelect.value;
        const academicYearId = academicYearSelect.value;
        const selectedClasses = Array.from(document.querySelectorAll('.class-checkbox:checked')).map(cb => cb.value);

        console.log('Loading students for faculty:', facultyId, 'academic year:', academicYearId, 'classes:', selectedClasses);

        if (facultyId && academicYearId) {
            // Show loading
            studentsPreview.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-primary-500 text-2xl"></i><p class="text-gray-500 mt-2">Loading students...</p></div>';

            let url = `/api/students?faculty_id=${facultyId}&academic_year_id=${academicYearId}`;
            if (selectedClasses.length > 0) {
                url += `&class_ids[]=${selectedClasses.join('&class_ids[]=')}`;
            }

            console.log('Students URL:', url);

            // Fetch students using proper headers
            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Students response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Students data:', data);
                    if (data.students && data.students.length > 0) {
                        let html = `<div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                                      <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-green-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-green-700">
                                                <strong>${data.students.length} students</strong> will be enrolled in the selected classes.
                                            </p>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">`;

                        data.students.slice(0, 10).forEach(student => {
                            html += `
                                <div>
                                    <span class="text-sm text-gray-700">${student.admission_number} - ${student.user.first_name} ${student.user.last_name}</span>
                                    ${student.department ? `<br><span class="text-xs text-gray-500">${student.department.name}</span>` : ''}
                                </div>
                            `;
                        });

                        if (data.students.length > 10) {
                            html += `<div class="col-span-full"><span class="text-xs text-gray-500">... and ${data.students.length - 10} more students</span></div>`;
                        }

                        html += '</div>';
                        studentsPreview.innerHTML = html;
                    } else {
                        studentsPreview.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">No eligible students found for the selected faculty.</p></div></div></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    studentsPreview.innerHTML = '<div class="bg-red-50 border-l-4 border-red-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-times-circle text-red-400"></i></div><div class="ml-3"><p class="text-sm text-red-700">Error loading students. Please try again.</p></div></div></div>';
                });
        } else {
            studentsPreview.innerHTML = '<div class="bg-blue-50 border-l-4 border-blue-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-info-circle text-blue-400"></i></div><div class="ml-3"><p class="text-sm text-blue-700">Select faculty to preview students.</p></div></div></div>';
        }
    }

    function validateForm() {
        const selectedClasses = document.querySelectorAll('.class-checkbox:checked').length;
        const hasRequiredFields = facultySelect.value && academicYearSelect.value;

        submitBtn.disabled = !(hasRequiredFields && selectedClasses > 0);
    }

    // Event listeners
    [facultySelect, academicYearSelect].forEach(select => {
        select.addEventListener('change', function() {
            loadCourses();
            // Clear classes and students when faculty/academic year changes
            classesContainer.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-info-circle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">Please select courses to load available classes.</p></div></div></div>';
            studentsPreview.innerHTML = '<div class="bg-blue-50 border-l-4 border-blue-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-info-circle text-blue-400"></i></div><div class="ml-3"><p class="text-sm text-blue-700">Select faculty to preview students.</p></div></div></div>';
            validateForm();
        });
    });

    facultySelect.addEventListener('change', loadStudents);

    // Add Course functionality
    addCourseBtn.addEventListener('click', function() {
        addCourseForm.classList.remove('hidden');
        addCourseBtn.style.display = 'none';
    });

    cancelCourseBtn.addEventListener('click', function() {
        addCourseForm.classList.add('hidden');
        addCourseBtn.style.display = 'inline-flex';
        clearCourseForm();
    });

    // Course organization type change handler
    document.getElementById('new_course_organization').addEventListener('change', function() {
        const yearField = document.getElementById('yearField');
        const semesterField = document.getElementById('semesterField');

        if (this.value === 'yearly') {
            yearField.classList.remove('hidden');
            semesterField.classList.add('hidden');
        } else {
            yearField.classList.add('hidden');
            semesterField.classList.remove('hidden');
        }
    });

    saveCourseBtn.addEventListener('click', function() {
        saveCourse();
    });

    // Add Class functionality
    addClassBtn.addEventListener('click', function() {
        updateClassCourseOptions();
        addClassForm.classList.remove('hidden');
        addClassBtn.style.display = 'none';
    });

    cancelClassBtn.addEventListener('click', function() {
        addClassForm.classList.add('hidden');
        addClassBtn.style.display = 'inline-flex';
        clearClassForm();
    });

    saveClassBtn.addEventListener('click', function() {
        saveClass();
    });

    function clearCourseForm() {
        document.getElementById('new_course_code').value = '';
        document.getElementById('new_course_title').value = '';
        document.getElementById('new_course_credits').value = '3';
        document.getElementById('new_course_type').value = 'core';
        document.getElementById('new_course_organization').value = 'yearly';
        document.getElementById('new_course_year').value = '1';
        document.getElementById('new_course_semester').value = '1';
        document.getElementById('new_course_description').value = '';

        // Reset visibility
        document.getElementById('yearField').classList.remove('hidden');
        document.getElementById('semesterField').classList.add('hidden');
    }

    function clearClassForm() {
        document.getElementById('new_class_name').value = '';
        document.getElementById('new_class_course').value = '';
        document.getElementById('new_class_capacity').value = '30';
        document.getElementById('new_class_room').value = '';
        document.getElementById('new_class_semester').value = '1';
        document.getElementById('new_class_year').value = '1';
        document.getElementById('new_class_schedule').value = '';
    }

    function saveCourse() {
        const facultyId = facultySelect.value;
        const academicYearId = academicYearSelect.value;

        if (!facultyId || !academicYearId) {
            alert('Please select academic year and faculty first.');
            return;
        }

        const courseData = {
            title: document.getElementById('new_course_title').value,
            code: document.getElementById('new_course_code').value,
            faculty_id: facultyId,
            credit_units: document.getElementById('new_course_credits').value,
            course_type: document.getElementById('new_course_type').value,
            organization_type: document.getElementById('new_course_organization').value,
            description: document.getElementById('new_course_description').value,
            is_active: true
        };

        if (courseData.organization_type === 'yearly') {
            courseData.year = document.getElementById('new_course_year').value;
        } else {
            courseData.semester_period = document.getElementById('new_course_semester').value;
        }

        // Validate required fields
        if (!courseData.title || !courseData.code) {
            alert('Please fill in course title and code.');
            return;
        }

        // Save course via AJAX
        fetch('/api/courses/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(courseData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Course created successfully!');
                clearCourseForm();
                addCourseForm.classList.add('hidden');
                addCourseBtn.style.display = 'inline-flex';
                loadCourses(); // Reload courses
            } else {
                alert('Error creating course: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating course. Please try again.');
        });
    }

    function updateClassCourseOptions() {
        const courseSelect = document.getElementById('new_class_course');
        courseSelect.innerHTML = '<option value="">Select Course</option>';

        // Get courses from the courses container
        const courseCheckboxes = document.querySelectorAll('.course-checkbox');
        courseCheckboxes.forEach(checkbox => {
            const courseId = checkbox.value;
            const courseText = checkbox.parentElement.querySelector('span').textContent;
            const option = document.createElement('option');
            option.value = courseId;
            option.textContent = courseText.split('\n')[0]; // Get just the first line (code - title)
            courseSelect.appendChild(option);
        });
    }

    function saveClass() {
        const academicYearId = academicYearSelect.value;
        const courseId = document.getElementById('new_class_course').value;

        if (!academicYearId || !courseId) {
            alert('Please select academic year and course first.');
            return;
        }

        const classData = {
            name: document.getElementById('new_class_name').value,
            course_id: courseId,
            academic_year_id: academicYearId,
            capacity: document.getElementById('new_class_capacity').value,
            room: document.getElementById('new_class_room').value,
            semester: document.getElementById('new_class_semester').value,
            year: document.getElementById('new_class_year').value,
            schedule: document.getElementById('new_class_schedule').value,
            status: 'active'
        };

        // Validate required fields
        if (!classData.name) {
            alert('Please fill in class name.');
            return;
        }

        // Save class via AJAX
        fetch('/api/classes/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(classData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Class created successfully!');
                clearClassForm();
                addClassForm.classList.add('hidden');
                addClassBtn.style.display = 'inline-flex';
                loadClasses(); // Reload classes
            } else {
                alert('Error creating class: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating class. Please try again.');
        });
    }

    // Handle course selection and semester field visibility
    function handleCourseSelection() {
        const selectedCourses = document.querySelectorAll('.course-checkbox:checked');
        const semesterField = document.getElementById('semester_field');
        const semesterSelect = document.getElementById('semester');

        let hasSemesterSystem = false;
        let hasAnnualSystem = false;

        // Check examination systems of selected courses
        selectedCourses.forEach(checkbox => {
            const examinationSystem = checkbox.dataset.examinationSystem;
            if (examinationSystem === 'semester') {
                hasSemesterSystem = true;
            } else if (examinationSystem === 'annual') {
                hasAnnualSystem = true;
            }
        });

        // Show/hide semester field based on course selection
        if (hasSemesterSystem && !hasAnnualSystem) {
            // Only semester system courses selected
            semesterField.style.display = 'block';
            semesterSelect.required = true;
        } else if (hasAnnualSystem && !hasSemesterSystem) {
            // Only annual system courses selected
            semesterField.style.display = 'none';
            semesterSelect.required = false;
            semesterSelect.value = '';
        } else if (hasSemesterSystem && hasAnnualSystem) {
            // Mixed systems - show warning and require semester
            semesterField.style.display = 'block';
            semesterSelect.required = true;

            // Show warning about mixed systems
            if (!document.getElementById('mixed_system_warning')) {
                const warning = document.createElement('div');
                warning.id = 'mixed_system_warning';
                warning.className = 'bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4';
                warning.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Mixed Systems Selected:</strong> You have selected both Annual and Semester system courses.
                                Semester field will be applied only to semester system courses.
                            </p>
                        </div>
                    </div>
                `;
                semesterField.parentNode.insertBefore(warning, semesterField.nextSibling);
            }
        } else {
            // No courses selected
            semesterField.style.display = 'none';
            semesterSelect.required = false;
            semesterSelect.value = '';

            // Remove warning if exists
            const warning = document.getElementById('mixed_system_warning');
            if (warning) {
                warning.remove();
            }
        }
    }

    // Initial validation
    validateForm();
});
</script>
@endpush
@endsection
