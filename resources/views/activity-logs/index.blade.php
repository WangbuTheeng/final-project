@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Activity Logs</h1>
        <div class="flex space-x-2">
            <button onclick="toggleFilters()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-filter mr-2"></i>Filters
            </button>
            <a href="{{ route('activity-logs.export', request()->query()) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div id="filters-section" class="bg-white shadow-md rounded-lg mb-6 hidden">
        <div class="p-6">
            <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="log_name" class="block text-sm font-medium text-gray-700 mb-1">Log Name</label>
                    <select name="log_name" id="log_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Log Names</option>
                        @foreach($logNames as $logName)
                            <option value="{{ $logName }}" {{ request('log_name') == $logName ? 'selected' : '' }}>
                                {{ $logName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="event" class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                    <select name="event" id="event" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                {{ ucfirst($event) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="subject_type" class="block text-sm font-medium text-gray-700 mb-1">Subject Type</label>
                    <select name="subject_type" id="subject_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($subjectTypes as $type)
                            <option value="App\Models\{{ $type }}" {{ request('subject_type') == "App\Models\\$type" ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Description</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search in description..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                    <input type="text" name="ip_address" id="ip_address" value="{{ request('ip_address') }}" placeholder="IP Address..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('activity-logs.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Timestamp
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($activityLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $log->id }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    @if($log->event)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($log->event == 'created') bg-green-100 text-green-800
                                            @elseif($log->event == 'updated') bg-blue-100 text-blue-800
                                            @elseif($log->event == 'deleted') bg-red-100 text-red-800
                                            @elseif($log->event == 'login') bg-purple-100 text-purple-800
                                            @elseif($log->event == 'logout') bg-gray-100 text-gray-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($log->event) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($log->subject)
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ class_basename($log->subject_type) }}</span>
                                            <span class="text-gray-500 text-xs">ID: {{ $log->subject_id }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($log->causer)
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $log->causer->name }}</span>
                                            <span class="text-gray-500 text-xs">ID: {{ $log->causer_id }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">System</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->properties['ip_address'] ?? '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span>{{ $log->created_at->format('M d, Y') }}</span>
                                        <span class="text-gray-500 text-xs">{{ $log->created_at->format('H:i:s') }}</span>
                                        <span class="text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('activity-logs.show', $log->id) }}"
                                       class="text-blue-600 hover:text-blue-900 mr-2" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500 text-lg">No activity logs found.</p>
                                        <p class="text-gray-400 text-sm">Try adjusting your filters or check back later.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $activityLogs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function toggleFilters() {
    const filtersSection = document.getElementById('filters-section');
    filtersSection.classList.toggle('hidden');
}

// Show filters if any filter is applied
@if(request()->hasAny(['date_from', 'date_to', 'user_id', 'log_name', 'event', 'subject_type', 'search', 'ip_address']))
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('filters-section').classList.remove('hidden');
    });
@endif
</script>
@endsection
