@extends('layouts.dashboard')

@section('title', 'Activity Feed')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Activity Feed</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Track all system activities and user actions</p>
        </div>
        
        <div class="mt-4 lg:mt-0 flex items-center space-x-3">
            <!-- Export Button -->
            <div class="relative" x-data="{ exportOpen: false }">
                <button @click="exportOpen = !exportOpen"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Export
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>
                
                <div x-show="exportOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     @click.away="exportOpen = false"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-50"
                     style="display: none;">
                    <div class="py-1">
                        <a href="{{ route('activities.export', ['format' => 'csv'] + request()->query()) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <i class="fas fa-file-csv mr-2"></i>Export as CSV
                        </a>
                        <a href="{{ route('activities.export', ['format' => 'excel'] + request()->query()) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <i class="fas fa-file-excel mr-2"></i>Export as Excel
                        </a>
                        <a href="{{ route('activities.export', ['format' => 'pdf'] + request()->query()) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <i class="fas fa-file-pdf mr-2"></i>Export as PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cleanup Button (Admin only) -->
            @can('manage-system')
                <button onclick="showCleanupModal()"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                    <i class="fas fa-trash mr-2"></i>
                    Cleanup
                </button>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-history text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Activities</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Most Active User -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-clock text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Most Active</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        @if($statistics['by_user']->count() > 0)
                            {{ \App\Models\User::find($statistics['by_user']->keys()->first())->name ?? 'Unknown' }}
                        @else
                            No activity
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Critical Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Critical</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['by_severity']['critical'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Last 24h</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activities->where('created_at', '>=', now()->subDay())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Activity Timeline</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Real-time view of all system activities</p>
        </div>
        
        <div class="p-6">
            <x-activity.timeline 
                :activities="$activities->take(50)->map(function($activity) {
                    return [
                        'id' => $activity->id,
                        'description' => $activity->description,
                        'action' => $activity->action,
                        'event_type' => $activity->event_type,
                        'severity' => $activity->severity,
                        'icon' => $activity->icon,
                        'color' => $activity->color,
                        'created_at' => $activity->created_at->toISOString(),
                        'user' => $activity->user ? [
                            'id' => $activity->user->id,
                            'name' => $activity->user->name,
                        ] : null,
                    ];
                })->toArray()"
                :show-user="true"
                :show-filters="true"
                :limit="50"
            />
        </div>
    </div>

    <!-- Activity Breakdown Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activities by Type -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activities by Type</h3>
            <div class="space-y-3">
                @foreach($statistics['by_event_type'] as $type => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3
                                {{ $type === 'user' ? 'bg-blue-500' : '' }}
                                {{ $type === 'student' ? 'bg-green-500' : '' }}
                                {{ $type === 'teacher' ? 'bg-purple-500' : '' }}
                                {{ $type === 'course' ? 'bg-yellow-500' : '' }}
                                {{ $type === 'enrollment' ? 'bg-indigo-500' : '' }}
                                {{ $type === 'system' ? 'bg-gray-500' : '' }}"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $type }}</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full
                            {{ $type === 'user' ? 'bg-blue-500' : '' }}
                            {{ $type === 'student' ? 'bg-green-500' : '' }}
                            {{ $type === 'teacher' ? 'bg-purple-500' : '' }}
                            {{ $type === 'course' ? 'bg-yellow-500' : '' }}
                            {{ $type === 'enrollment' ? 'bg-indigo-500' : '' }}
                            {{ $type === 'system' ? 'bg-gray-500' : '' }}"
                             style="width: {{ $statistics['total'] > 0 ? ($count / $statistics['total']) * 100 : 0 }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Activities by Action -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activities by Action</h3>
            <div class="space-y-3">
                @foreach($statistics['by_action']->take(6) as $action => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3
                                {{ $action === 'create' ? 'bg-green-500' : '' }}
                                {{ $action === 'update' ? 'bg-blue-500' : '' }}
                                {{ $action === 'delete' ? 'bg-red-500' : '' }}
                                {{ $action === 'view' ? 'bg-gray-500' : '' }}
                                {{ $action === 'login' ? 'bg-emerald-500' : '' }}
                                {{ $action === 'logout' ? 'bg-yellow-500' : '' }}"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $action }}</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full
                            {{ $action === 'create' ? 'bg-green-500' : '' }}
                            {{ $action === 'update' ? 'bg-blue-500' : '' }}
                            {{ $action === 'delete' ? 'bg-red-500' : '' }}
                            {{ $action === 'view' ? 'bg-gray-500' : '' }}
                            {{ $action === 'login' ? 'bg-emerald-500' : '' }}
                            {{ $action === 'logout' ? 'bg-yellow-500' : '' }}"
                             style="width: {{ $statistics['total'] > 0 ? ($count / $statistics['total']) * 100 : 0 }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
@can('manage-system')
<div id="cleanupModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('activities.cleanup') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-trash text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Cleanup Old Activities
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    This will permanently delete activities older than the specified number of days. This action cannot be undone.
                                </p>
                                <div class="mt-4">
                                    <label for="cleanup_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Delete activities older than (days):
                                    </label>
                                    <input type="number" name="days" id="cleanup_days" min="30" max="365" value="90" required
                                           class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete Activities
                    </button>
                    <button type="button" onclick="hideCleanupModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<script>
function showCleanupModal() {
    document.getElementById('cleanupModal').classList.remove('hidden');
}

function hideCleanupModal() {
    document.getElementById('cleanupModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideCleanupModal();
    }
});
</script>
@endsection
