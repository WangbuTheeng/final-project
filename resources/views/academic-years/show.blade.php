@extends('layouts.app')

@section('title', 'Academic Year Details')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">Academic Year Details</h1>

        <div class="bg-white shadow rounded-lg p-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                <p class="text-gray-900">{{ $academicYear->name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Code:</label>
                <p class="text-gray-900">{{ $academicYear->code }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                <p class="text-gray-900">{{ $academicYear->start_date }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                <p class="text-gray-900">{{ $academicYear->end_date }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                <p class="text-gray-900">{{ $academicYear->description }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Is Current:</label>
                <p class="text-gray-900">{{ $academicYear->is_current ? 'Yes' : 'No' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Is Active:</label>
                <p class="text-gray-900">{{ $academicYear->is_active ? 'Yes' : 'No' }}</p>
            </div>

            <a href="{{ route('academic-years.edit', $academicYear) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('academic-years.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back
            </a>
        </div>
    </div>
@endsection