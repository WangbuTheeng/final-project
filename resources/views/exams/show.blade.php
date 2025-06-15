@extends('layouts.app')

@section('title', 'Exam Details - ' . $exam->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
                        <p class="text-sm text-gray-600 mt-1">Exam details and management</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('exams.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Exams
                        </a>
                        @can('edit-exams')
                            <a href="{{ route('exams.edit', $exam) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Exam
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="px-6 py-3 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $exam->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 
                               ($exam->status === 'ongoing' ? 'bg-yellow-100 text-yellow-800' : 
                               ($exam->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($exam->status) }}
                        </span>
                        <span class="text-sm text-gray-600">
                            {{ $exam->exam_date->format('M d, Y \a\t H:i') }}
                        </span>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex space-x-2">
                        @if($exam->status === 'scheduled' && $exam->exam_date->isPast())
                            @can('edit-exams')
                                <form method="POST" action="{{ route('exams.start', $exam) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                        Start Exam
                                    </button>
                                </form>
                            @endcan
                        @endif
                        
                        @if($exam->status === 'ongoing')
                            @can('edit-exams')
                                <form method="POST" action="{{ route('exams.complete', $exam) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        Complete Exam
                                    </button>
                                </form>
                            @endcan
                        @endif
                        
                        @if(in_array($exam->status, ['scheduled', 'ongoing']))
                            @can('edit-exams')
                                <form method="POST" action="{{ route('exams.cancel', $exam) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to cancel this exam?')"
                                            class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                        Cancel Exam
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Exam Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Exam Information</h2>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Course</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $exam->classSection->course->title }}
                                    <div class="text-xs text-gray-500">{{ $exam->classSection->course->code }}</div>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Class</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $exam->classSection->name }}
                                    <div class="text-xs text-gray-500">{{ $exam->classSection->section }}</div>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $exam->academicYear->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Semester</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($exam->semester) }} Semester</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Exam Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($exam->exam_type) }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $exam->duration_minutes }} minutes</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Marks</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $exam->total_marks }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pass Mark</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $exam->pass_mark }} 
                                    <span class="text-gray-500">({{ number_format(($exam->pass_mark / $exam->total_marks) * 100, 1) }}%)</span>
                                </dd>
                            </div>
                            
                            @if($exam->venue)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Venue</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $exam->venue }}</dd>
                            </div>
                            @endif
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created By</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $exam->creator->first_name }} {{ $exam->creator->last_name }}
                                </dd>
                            </div>
                        </dl>
                        
                        @if($exam->instructions)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Instructions</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                                {{ $exam->instructions }}
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Enrolled Students -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                Enrolled Students ({{ $enrolledStudents->count() }})
                            </h3>
                            @if($exam->status === 'completed')
                                <a href="{{ route('grades.exam-results', $exam) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                    View Results
                                </a>
                            @elseif($exam->status === 'ongoing' || $exam->status === 'completed')
                                <a href="{{ route('grades.create-for-exam', $exam) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    Add Grades
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-4">
                        @if($enrolledStudents->count() > 0)
                            <div class="overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matric Number</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($enrolledStudents->take(10) as $student)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $student->user->first_name }} {{ $student->user->last_name }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $student->matric_number }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Enrolled
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($enrolledStudents->count() > 10)
                                    <div class="px-6 py-3 text-center text-sm text-gray-500">
                                        And {{ $enrolledStudents->count() - 10 }} more students...
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No students enrolled</h3>
                                <p class="mt-1 text-sm text-gray-500">No students are enrolled in this class.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Enrolled Students</span>
                            <span class="text-sm font-medium text-gray-900">{{ $enrolledStudents->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Duration</span>
                            <span class="text-sm font-medium text-gray-900">{{ $exam->duration_minutes }} min</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total Marks</span>
                            <span class="text-sm font-medium text-gray-900">{{ $exam->total_marks }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Pass Percentage</span>
                            <span class="text-sm font-medium text-gray-900">{{ number_format(($exam->pass_mark / $exam->total_marks) * 100, 1) }}%</span>
                        </div>
                        @if($exam->status === 'completed' && isset($gradeStats))
                        <div class="border-t pt-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Graded</span>
                                <span class="text-sm font-medium text-gray-900">{{ $gradeStats['graded'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Pass Rate</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($gradeStats['pass_rate'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Average</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($gradeStats['average'] ?? 0, 1) }}%</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        @can('edit-exams')
                            <a href="{{ route('exams.edit', $exam) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Edit Exam Details
                            </a>
                        @endcan
                        
                        @if($exam->status === 'completed' || $exam->status === 'ongoing')
                            <a href="{{ route('grades.create-for-exam', $exam) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Manage Grades
                            </a>
                        @endif
                        
                        @if($exam->status === 'completed')
                            <a href="{{ route('grades.exam-results', $exam) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                View Results
                            </a>
                        @endif
                        
                        <button onclick="window.print()" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Print Exam Details
                        </button>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Timeline</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900">Exam Created</span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500">
                                                        {{ $exam->created_at->format('M d, Y \a\t H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full {{ $exam->exam_date->isPast() ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900">Exam Scheduled</span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500">
                                                        {{ $exam->exam_date->format('M d, Y \a\t H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background: white !important;
        }
        
        .bg-gray-50 {
            background: white !important;
        }
        
        .shadow {
            box-shadow: none !important;
        }
    }
</style>
@endpush
@endsection
