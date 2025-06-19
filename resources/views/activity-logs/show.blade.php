@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Activity Log Details</h1>
        <a href="{{ route('activity-logs.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to Logs
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Activity Log #{{ $activityLog->id }}</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Log Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $activityLog->log_name ?: 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Event Type</label>
                        <p class="mt-1">
                            @if($activityLog->event)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($activityLog->event == 'created') bg-green-100 text-green-800
                                    @elseif($activityLog->event == 'updated') bg-blue-100 text-blue-800
                                    @elseif($activityLog->event == 'deleted') bg-red-100 text-red-800
                                    @elseif($activityLog->event == 'login') bg-purple-100 text-purple-800
                                    @elseif($activityLog->event == 'logout') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($activityLog->event) }}
                                </span>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $activityLog->description }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Timestamp</label>
                        <div class="mt-1 text-sm text-gray-900">
                            <p>{{ $activityLog->created_at->format('F d, Y \a\t H:i:s') }}</p>
                            <p class="text-gray-500 text-xs">{{ $activityLog->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>

                <!-- User and Subject Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">User & Subject Information</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Performed By</label>
                        <div class="mt-1">
                            @if ($activityLog->causer)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                {{ substr($activityLog->causer->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $activityLog->causer->name }}</p>
                                        <p class="text-sm text-gray-500">ID: {{ $activityLog->causer_id }}</p>
                                        @if($activityLog->causer->email)
                                            <p class="text-sm text-gray-500">{{ $activityLog->causer->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">System Action</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <div class="mt-1">
                            @if ($activityLog->subject)
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <p class="text-sm font-medium text-gray-900">{{ class_basename($activityLog->subject_type) }}</p>
                                    <p class="text-sm text-gray-500">ID: {{ $activityLog->subject_id }}</p>
                                    @if(method_exists($activityLog->subject, 'name') && $activityLog->subject->name)
                                        <p class="text-sm text-gray-700">Name: {{ $activityLog->subject->name }}</p>
                                    @endif
                                    @if(method_exists($activityLog->subject, 'title') && $activityLog->subject->title)
                                        <p class="text-sm text-gray-700">Title: {{ $activityLog->subject->title }}</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No subject associated</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Properties Section -->
            @if($activityLog->properties && count($activityLog->properties) > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Additional Properties</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Request Information -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">Request Information</h4>
                            <div class="space-y-2">
                                @if(isset($activityLog->properties['ip_address']))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">IP Address</label>
                                        <p class="text-sm text-gray-900">{{ $activityLog->properties['ip_address'] }}</p>
                                    </div>
                                @endif

                                @if(isset($activityLog->properties['user_agent']))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">User Agent</label>
                                        <p class="text-sm text-gray-900 break-all">{{ $activityLog->properties['user_agent'] }}</p>
                                    </div>
                                @endif

                                @if(isset($activityLog->properties['url']))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">URL</label>
                                        <p class="text-sm text-gray-900 break-all">{{ $activityLog->properties['url'] }}</p>
                                    </div>
                                @endif

                                @if(isset($activityLog->properties['method']))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">HTTP Method</label>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $activityLog->properties['method'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Other Properties -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">Other Properties</h4>
                            <div class="space-y-2">
                                @foreach($activityLog->properties as $key => $value)
                                    @if(!in_array($key, ['ip_address', 'user_agent', 'url', 'method', 'old', 'attributes']))
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                            <p class="text-sm text-gray-900">
                                                @if(is_array($value) || is_object($value))
                                                    <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Changes Section (for update events) -->
            @if($activityLog->event === 'updated' && isset($activityLog->properties['old']) && isset($activityLog->properties['attributes']))
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Changes Made</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activityLog->properties['attributes'] as $field => $newValue)
                                    @if(isset($activityLog->properties['old'][$field]) && $activityLog->properties['old'][$field] != $newValue)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ ucfirst(str_replace('_', ' ', $field)) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded">
                                                    {{ $activityLog->properties['old'][$field] ?: 'Empty' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                                    {{ $newValue ?: 'Empty' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Raw Properties (for debugging) -->
            @if(auth()->user()->hasRole('Super Admin'))
                <div class="mt-8">
                    <details class="group">
                        <summary class="cursor-pointer text-lg font-medium text-gray-900 border-b pb-2 mb-4 hover:text-blue-600">
                            Raw Properties (Debug Info)
                            <span class="ml-2 transform group-open:rotate-90 transition-transform">▶</span>
                        </summary>
                        <div class="bg-gray-100 p-4 rounded-md">
                            <pre class="text-xs overflow-x-auto">{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </details>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
