@extends('layouts.dashboard')

@section('title', 'Role Management')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Role Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage system roles and permissions</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
            <a href="{{ route('roles.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Create New Role
            </a>
            @endif
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

    @if (session('error'))
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Roles Table -->
    <div class="bg-white overflow-hidden shadow-soft-xl sm:rounded-lg animate-fade-in">
        <div class="p-6 bg-white border-b border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4 sm:mb-0">
                    <i class="fas fa-user-tag text-primary-500 mr-2"></i> {{ __('System Roles') }}
                </h2>
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                <a href="{{ route('roles.create') }}" class="bg-primary-500 hover:bg-primary-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-150 ease-in-out shadow-soft-md flex items-center">
                    <i class="fas fa-plus mr-2"></i> {{ __('Add New Role') }}
                </a>
                @endif
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role Name</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Permissions</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($roles as $role)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out @if($loop->even) bg-gray-50/50 @endif">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600">
                                        #{{ $role->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @php
                                                $roleColors = [
                                                    'Super Admin' => 'bg-purple-100 text-purple-600',
                                                    'Admin' => 'bg-blue-100 text-blue-600',
                                                    'Teacher' => 'bg-green-100 text-green-600',
                                                    'Student' => 'bg-yellow-100 text-yellow-600',
                                                    'Accountant' => 'bg-orange-100 text-orange-600',
                                                    'Examiner' => 'bg-red-100 text-red-600',
                                                ];
                                                $roleColor = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-600';
                                            @endphp
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full {{ explode(' ', $roleColor)[0] }} flex items-center justify-center">
                                                <i class="fas fa-user-tag {{ explode(' ', $roleColor)[1] }}"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $role->permissions->count() }} permissions</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach($role->permissions->take(3) as $permission)
                                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-md border border-gray-200 bg-gray-50 text-gray-700">
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-md border border-primary-200 bg-primary-50 text-primary-700">
                                                    +{{ $role->permissions->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('roles.show', $role) }}"
                                               class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-md transition-colors duration-150 ease-in-out"
                                               title="View Role">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                            <a href="{{ route('roles.edit', $role) }}"
                                               class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors duration-150 ease-in-out"
                                               title="Edit Role">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if(!in_array($role->name, ['Super Admin', 'Admin']))
                                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors duration-150 ease-in-out"
                                                        onclick="return confirm('Are you sure you want to delete this role?')"
                                                        title="Delete Role">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-gray-400 bg-gray-50 p-1.5 rounded-md" title="System role cannot be deleted">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(isset($roles) && method_exists($roles, 'links'))
            <div class="mt-6">
                <div class="px-4 py-3 bg-white border border-gray-200 rounded-lg shadow-sm">
                    {{ $roles->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection