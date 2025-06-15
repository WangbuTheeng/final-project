@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Class Section</h1>
            <p class="mt-1 text-sm text-gray-500">Update class section information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('classes.show', $classSection) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-eye mr-2"></i>
                View Details
            </a>
            <a href="{{ route('classes.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Classes
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium">There were some errors with your submission:</h3>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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

    <!-- Edit Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Class Section Information</h3>
        </div>

        <form action="{{ route('classes.update', $classSection) }}" method="POST" class="px-6 py-4 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Class Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Class Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $classSection->name) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                           placeholder="e.g., CSC101-A"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course -->
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700">Course</label>
                    <select name="course_id" 
                            id="course_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('course_id') border-red-300 @enderror"
                            required>
                        <option value="">Select a course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $classSection->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->code }} - {{ $course->title }} ({{ $course->credit_units }} units)
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Year -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <select name="academic_year_id" 
                            id="academic_year_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('academic_year_id') border-red-300 @enderror"
                            required>
                        <option value="">Select academic year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id', $classSection->academic_year_id) == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Semester -->
                <div id="semester-group" style="display: none;">
                    <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                    <select name="semester"
                            id="semester"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('semester') border-red-300 @enderror"
                            required>
                        <option value="">Select semester</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester }}" {{ old('semester', $classSection->semester) == $semester ? 'selected' : '' }}>
                                Semester {{ $semester }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year -->
                <div id="year-group" style="display: none;">
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <select name="year"
                            id="year"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('year') border-red-300 @enderror"
                            required>
                        <option value="">Select year</option>
                        <option value="1" {{ old('year', $classSection->year) == 1 ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ old('year', $classSection->year) == 2 ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ old('year', $classSection->year) == 3 ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ old('year', $classSection->year) == 4 ? 'selected' : '' }}>4th Year</option>
                    </select>
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instructor -->
                <div>
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700">Instructor</label>
                    <select name="instructor_id" 
                            id="instructor_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('instructor_id') border-red-300 @enderror">
                        <option value="">No instructor assigned</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id', $classSection->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->name }} ({{ $instructor->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('instructor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Room -->
                <div>
                    <label for="room" class="block text-sm font-medium text-gray-700">Room</label>
                    <input type="text" 
                           name="room" 
                           id="room" 
                           value="{{ old('room', $classSection->room) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('room') border-red-300 @enderror"
                           placeholder="e.g., Room 101, Lab A">
                    @error('room')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" 
                           name="capacity" 
                           id="capacity" 
                           value="{{ old('capacity', $classSection->capacity) }}"
                           min="1"
                           max="500"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('capacity') border-red-300 @enderror"
                           required>
                    @error('capacity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" 
                            id="status" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('status') border-red-300 @enderror"
                            required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', $classSection->status) == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" 
                           name="start_date" 
                           id="start_date" 
                           value="{{ old('start_date', $classSection->start_date ? $classSection->start_date->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('start_date') border-red-300 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date" 
                           value="{{ old('end_date', $classSection->end_date ? $classSection->end_date->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('end_date') border-red-300 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Schedule Section -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Class Schedule</h4>
                <div id="schedule-container" class="space-y-4">
                    @if($classSection->schedule && count($classSection->schedule) > 0)
                        @foreach($classSection->schedule as $index => $scheduleItem)
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border border-gray-200 rounded-md">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Day</label>
                                    <select name="schedule[{{ $index }}][day]" class='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500'>
                                        <option value="">Select day</option>
                                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                            <option value="{{ $day }}" {{ ($scheduleItem['day'] ?? '') == $day ? 'selected' : '' }}>
                                                {{ ucfirst($day) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Time</label>
                                    <input type="text" name="schedule[{{ $index }}][time]" value="{{ $scheduleItem['time'] ?? '' }}" placeholder="e.g., 09:00-10:30" class='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500'>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Duration</label>
                                    <input type="text" name="schedule[{{ $index }}][duration]" value="{{ $scheduleItem['duration'] ?? '' }}" placeholder="e.g., 1.5 hours" class='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500'>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" class="remove-schedule w-full px-3 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" 
                        id="add-schedule" 
                        class="mt-4 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-plus mr-2"></i>
                    Add Schedule
                </button>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('classes.show', $classSection) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Update Class Section
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let scheduleIndex = {{ $classSection->schedule ? count($classSection->schedule) : 0 }};
    const scheduleContainer = document.getElementById('schedule-container');
    const addScheduleBtn = document.getElementById('add-schedule');

    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-schedule').forEach(button => {
        button.addEventListener('click', function() {
            button.closest('.grid').remove();
        });
    });

    addScheduleBtn.addEventListener('click', function() {
        addScheduleItem();
    });

    function addScheduleItem() {
        const scheduleItem = document.createElement('div');
        scheduleItem.className = 'grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border border-gray-200 rounded-md';
        scheduleItem.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700">Day</label>
                <select name="schedule[${scheduleIndex}][day]" class='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500'>
                    <option value="">Select day</option>
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                    <option value="sunday">Sunday</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Time</label>
                <input type="text" name="schedule[${scheduleIndex}][time]" placeholder="e.g., 09:00-10:30" class='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500'>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Duration</label>
                <input type="text" name="schedule[${scheduleIndex}][duration]" placeholder="e.g., 1.5 hours" class='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500'>
            </div>
            <div class="flex items-end">
                <button type="button" class="remove-schedule w-full px-3 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;

        scheduleContainer.appendChild(scheduleItem);

        // Add remove functionality
        scheduleItem.querySelector('.remove-schedule').addEventListener('click', function() {
            scheduleItem.remove();
        });

        scheduleIndex++;
    }

    // Handle course type switching for semester/year fields
    const courseSelect = document.getElementById('course_id');
    const semesterGroup = document.getElementById('semester-group');
    const yearGroup = document.getElementById('year-group');
    const semester = document.getElementById('semester');
    const year = document.getElementById('year');

    // Function to show/hide fields based on course type
    function toggleFields(courseType) {
        if (courseType === 'semester') {
            semesterGroup.style.display = 'block';
            yearGroup.style.display = 'none';
            semester.setAttribute('required', 'required');
            year.removeAttribute('required');
        } else if (courseType === 'yearly') {
            semesterGroup.style.display = 'none';
            yearGroup.style.display = 'block';
            year.setAttribute('required', 'required');
            semester.removeAttribute('required');
        } else {
            semesterGroup.style.display = 'none';
            yearGroup.style.display = 'none';
            semester.removeAttribute('required');
            year.removeAttribute('required');
        }
    }

    // Initialize fields based on current course
    if (courseSelect.value) {
        fetch('/get-course-type?course_id=' + courseSelect.value)
            .then(response => response.json())
            .then(data => {
                toggleFields(data.type);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Handle course change
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;

        if (courseId) {
            fetch('/get-course-type?course_id=' + courseId)
                .then(response => response.json())
                .then(data => {
                    toggleFields(data.type);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            toggleFields(null);
        }
    });
});
</script>
@endsection
