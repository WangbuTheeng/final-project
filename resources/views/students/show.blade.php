@extends('layouts.dashboard')

@section('title', 'Student Details')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $student->user->full_name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Student Details - {{ $student->admission_number }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('students.edit', $student) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150"
               style="background-color: #37a2bc; hover:background-color: #2d8299; focus:ring-color: #37a2bc;">
                <i class="fas fa-edit mr-2"></i>
                Edit Student
            </a>
            <a href="{{ route('students.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Students
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Student Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Status Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center
                        @if($student->status === 'active') bg-green-100 @elseif($student->status === 'graduated') bg-blue-100 @elseif($student->status === 'suspended') bg-red-100 @else bg-yellow-100 @endif">
                        <i class="fas fa-user-graduate text-sm
                            @if($student->status === 'active') text-green-600 @elseif($student->status === 'graduated') text-blue-600 @elseif($student->status === 'suspended') text-red-600 @else text-yellow-600 @endif"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <p class="text-lg font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $student->status) }}</p>
                </div>
            </div>
        </div>

        <!-- Level Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-layer-group text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Current Level</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->current_level }} Level</p>
                </div>
            </div>
        </div>

        <!-- CGPA Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">CGPA</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->cgpa ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Credits Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-award text-indigo-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Credits Earned</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->total_credits_earned }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->user->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->user->phone ?? 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->user->date_of_birth?->format('M d, Y') ?? 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Gender</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $student->user->gender ?? 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Admission Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $student->admission_number }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->user->address ?? 'Not provided' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Academic Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Faculty</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->faculty->name ?? 'Not assigned' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->department->name ?? 'Not assigned' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Admission Year</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->academicYear->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Mode of Entry</dt>
                            <dd class="mt-1 text-sm text-gray-900 uppercase">{{ str_replace('_', ' ', $student->mode_of_entry) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Study Mode</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $student->study_mode) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Expected Graduation</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->expected_graduation_date?->format('M Y') ?? 'Not calculated' }}</dd>
                        </div>
                        @if($student->actual_graduation_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Graduation Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->actual_graduation_date->format('M d, Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Guardian Information -->
            @if($student->guardian_info && count($student->guardian_info) > 0)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Guardian Information</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        @if(isset($student->guardian_info['name']))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_info['name'] }}</dd>
                        </div>
                        @endif
                        @if(isset($student->guardian_info['phone']))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_info['phone'] }}</dd>
                        </div>
                        @endif
                        @if(isset($student->guardian_info['email']))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_info['email'] }}</dd>
                        </div>
                        @endif
                        @if(isset($student->guardian_info['relationship']))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Relationship</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $student->guardian_info['relationship'] }}</dd>
                        </div>
                        @endif
                        @if(isset($student->guardian_info['address']))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_info['address'] }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('enrollments.create', ['student_id' => $student->id]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Enroll in Course
                    </a>
                    <a href="#"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-file-alt mr-2"></i>
                        View Transcript
                    </a>
                    @can('view-financial-reports')
                        <a href="{{ route('finance.reports.student-statement', ['student_id' => $student->id]) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            View Fees
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-6">
                    <div class="text-sm text-gray-500 text-center py-4">
                        No recent activity to display
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Enrollments -->
    @if($currentEnrollments && $currentEnrollments->count() > 0)
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Current Enrollments</h3>
        </div>
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($currentEnrollments as $enrollment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $enrollment->class->course->title }}</div>
                                <div class="text-sm text-gray-500">{{ $enrollment->class->course->code }} - {{ $enrollment->class->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $enrollment->class->instructor->user->full_name ?? 'Not assigned' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($enrollment->status === 'enrolled') bg-green-100 text-green-800
                                @elseif($enrollment->status === 'completed') bg-blue-100 text-blue-800
                                @elseif($enrollment->status === 'dropped') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $enrollment->final_grade ?? 'Pending' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Academic History -->
    @if(isset($academicHistory) && $academicHistory->count() > 0)
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Academic History</h3>
        </div>
        <div class="p-6">
            @foreach($academicHistory as $yearId => $yearData)
                @php $year = $yearData->first()->first()->academicYear ?? null; @endphp
                @if($year)
                <div class="mb-6 last:mb-0">
                    <h4 class="text-md font-semibold text-gray-900 mb-3">{{ $year->name }}</h4>
                    @foreach($yearData as $semester => $enrollments)
                    <div class="mb-4">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Semester {{ $semester }}</h5>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $enrollment->class->course->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $enrollment->class->course->code }}</div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $enrollment->final_grade ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($enrollment->status === 'completed') bg-green-100 text-green-800
                                                @elseif($enrollment->status === 'failed') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
