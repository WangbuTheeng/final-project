@extends('layouts.dashboard')

@section('title', '🇳🇵 Nepal University Examinations')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🇳🇵 Nepal University Examinations</h1>
            <p class="mt-1 text-sm text-gray-500">Manage examinations with Nepal University standards (Internal 40% + Final 60%)</p>
        </div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('examinations.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Schedule New Exam
            </a>
        </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Examinations</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $examinations->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-calendar-check text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Scheduled</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $examinations->where('status', 'scheduled')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Incomplete</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $examinations->where('status', 'incomplete')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $examinations->where('status', 'completed')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filter Examinations</h3>
        </div>
        <div class="p-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Status</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="incomplete" {{ request('status') === 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-1">Exam Type</label>
                        <select name="exam_type" id="exam_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="theory" {{ request('exam_type') === 'theory' ? 'selected' : '' }}>Theory</option>
                            <option value="practical" {{ request('exam_type') === 'practical' ? 'selected' : '' }}>Practical</option>
                            <option value="internal" {{ request('exam_type') === 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="final" {{ request('exam_type') === 'final' ? 'selected' : '' }}>Final</option>
                            <option value="supplementary" {{ request('exam_type') === 'supplementary' ? 'selected' : '' }}>Supplementary</option>
                        </select>
                    </div>
                    <div>
                        <label for="assessment_type" class="block text-sm font-medium text-gray-700 mb-1">Assessment Type</label>
                        <select name="assessment_type" id="assessment_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Assessments</option>
                            <option value="internal" {{ request('assessment_type') === 'internal' ? 'selected' : '' }}>Internal (40%)</option>
                            <option value="final" {{ request('assessment_type') === 'final' ? 'selected' : '' }}>Final (60%)</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative">
                            <input type="text" name="search" id="search"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pl-10"
                                   placeholder="Search exams..." value="{{ request('search') }}">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('examinations.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Clear Filters
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Examinations Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Examination List</h3>
        </div>
        <div class="overflow-hidden">
            <div class="overflow-x-auto compact-table">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Exam Details
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject & Class
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Schedule
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assessment
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Students
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($examinations as $examination)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $examination->exam_name }}</div>
                                    <div class="flex space-x-1 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($examination->exam_type) }}
                                        </span>
                                        @if($examination->is_supplementary)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Supp
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $examination->subject->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $examination->class->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $examination->exam_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $examination->start_time }} - {{ $examination->end_time }}
                                        ({{ $examination->duration_minutes }}min)
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $examination->assessment_type === 'internal' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($examination->assessment_type) }} ({{ $examination->weightage_percentage }}%)
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">{{ $examination->total_marks }} marks</div>
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $examination->getEnrolledStudentsCount() }} enrolled</div>
                                    <div class="text-xs text-gray-500">{{ $examination->getGradedStudentsCount() }} appeared</div>
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'scheduled' => ['bg-blue-100 text-blue-800', 'Scheduled'],
                                        'incomplete' => ['bg-yellow-100 text-yellow-800', 'Incomplete'],
                                        'completed' => ['bg-green-100 text-green-800', 'Completed'],
                                        'cancelled' => ['bg-red-100 text-red-800', 'Cancelled']
                                    ];
                                    $config = $statusConfig[$examination->status] ?? ['bg-gray-100 text-gray-800', ucfirst($examination->status)];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $config[0] }}">
                                    {{ $config[1] }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-1">
                                    <a href="{{ route('examinations.show', $examination) }}"
                                       class="text-blue-600 hover:text-blue-900 p-1" title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
                                        <a href="{{ route('examinations.marks.entry', $examination) }}"
                                           class="text-purple-600 hover:text-purple-900 p-1" title="Enter Marks">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <a href="{{ route('examinations.marks.show', $examination) }}"
                                           class="text-green-600 hover:text-green-900 p-1" title="View Results">
                                            <i class="fas fa-chart-bar text-xs"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        @if($examination->status === 'scheduled')
                                            <a href="{{ route('examinations.edit', $examination) }}"
                                               class="text-yellow-600 hover:text-yellow-900 p-1" title="Edit Exam">
                                                <i class="fas fa-cog text-xs"></i>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-calendar-times text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium mb-2">No examinations found</p>
                                    <p class="text-sm mb-4">Get started by scheduling your first examination.</p>
                                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('examinations.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <i class="fas fa-plus mr-2"></i>
                                            Schedule First Exam
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($examinations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $examinations->links() }}
        </div>
        @endif
    </div>

    <!-- Nepal University Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-900">🇳🇵 Nepal University Examination System</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-blue-800 mb-3">Assessment Distribution:</h4>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-check text-blue-500 mr-2"></i>
                        Internal Assessment: <strong class="ml-1">40%</strong>
                    </li>
                    <li class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Final Examination: <strong class="ml-1">60%</strong>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-blue-800 mb-3">Pass Requirements:</h4>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-check text-blue-500 mr-2"></i>
                        Minimum <strong>32%</strong> in each component
                    </li>
                    <li class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-check text-yellow-500 mr-2"></i>
                        Minimum <strong>40%</strong> overall
                    </li>
                    <li class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-check text-red-500 mr-2"></i>
                        <strong>75%</strong> attendance required
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
