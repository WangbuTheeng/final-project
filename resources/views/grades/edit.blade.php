@extends('layouts.app')

@section('title', 'Edit Grade')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Grade</h1>
                        <p class="text-sm text-gray-600 mt-1">Modify grade information and score</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('grades.show', $grade) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Grade
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Edit Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Grade Information</h2>
                    </div>
                    
                    <form method="POST" action="{{ route('grades.update', $grade) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="px-6 py-4 space-y-6">
                            <!-- Student Information (Read-only) -->
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Student</label>
                                    <div class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                                        {{ $grade->student->user->first_name }} {{ $grade->student->user->last_name }}
                                        <div class="text-xs text-gray-500">{{ $grade->student->matric_number }}</div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Course</label>
                                    <div class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                                        {{ $grade->enrollment->class->course->title }}
                                        <div class="text-xs text-gray-500">{{ $grade->enrollment->class->course->code }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Score -->
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label for="score" class="block text-sm font-medium text-gray-700">
                                        Score <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1 relative">
                                        <input type="number" 
                                               name="score" 
                                               id="score" 
                                               value="{{ old('score', $grade->score) }}"
                                               min="0" 
                                               max="{{ $grade->max_score }}" 
                                               step="0.1"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('score') border-red-300 @enderror"
                                               required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">/ {{ $grade->max_score }}</span>
                                        </div>
                                    </div>
                                    @error('score')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="max_score" class="block text-sm font-medium text-gray-700">
                                        Maximum Score <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           name="max_score" 
                                           id="max_score" 
                                           value="{{ old('max_score', $grade->max_score) }}"
                                           min="1" 
                                           max="1000" 
                                           step="0.1"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('max_score') border-red-300 @enderror"
                                           required>
                                    @error('max_score')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Grade Type and Semester -->
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label for="grade_type" class="block text-sm font-medium text-gray-700">
                                        Grade Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="grade_type" 
                                            id="grade_type" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('grade_type') border-red-300 @enderror"
                                            required>
                                        @foreach($gradeTypes as $key => $value)
                                            <option value="{{ $key }}" {{ old('grade_type', $grade->grade_type) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('grade_type')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="semester" class="block text-sm font-medium text-gray-700">
                                        Semester <span class="text-red-500">*</span>
                                    </label>
                                    <select name="semester" 
                                            id="semester" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('semester') border-red-300 @enderror"
                                            required>
                                        @foreach($semesters as $key => $value)
                                            <option value="{{ $key }}" {{ old('semester', $grade->semester) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('semester')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div>
                                <label for="remarks" class="block text-sm font-medium text-gray-700">
                                    Remarks
                                </label>
                                <textarea name="remarks" 
                                          id="remarks" 
                                          rows="3"
                                          maxlength="500"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('remarks') border-red-300 @enderror"
                                          placeholder="Optional remarks about the grade">{{ old('remarks', $grade->remarks) }}</textarea>
                                @error('remarks')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Maximum 500 characters</p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('grades.show', $grade) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Update Grade
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Grade Preview -->
            <div class="space-y-6">
                <!-- Current Performance -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Current Grade</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ number_format($grade->score, 1) }}<span class="text-lg text-gray-500">/{{ number_format($grade->max_score, 1) }}</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ number_format($grade->getPercentage(), 1) }}%
                            </div>
                            
                            <div class="mt-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $grade->letter_grade === 'A' ? 'bg-green-100 text-green-800' : 
                                       ($grade->letter_grade === 'B' ? 'bg-blue-100 text-blue-800' : 
                                       ($grade->letter_grade === 'C' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($grade->letter_grade === 'F' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    Grade {{ $grade->letter_grade }}
                                </span>
                            </div>
                            
                            <div class="mt-2">
                                <div class="text-xs text-gray-500">Grade Point</div>
                                <div class="text-lg font-semibold text-gray-900">{{ number_format($grade->grade_point, 1) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grade Scale Reference -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Grade Scale</h4>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="font-medium">A (5.0)</span>
                                <span class="text-gray-500">80-100%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">B (4.0)</span>
                                <span class="text-gray-500">70-79%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">C (3.0)</span>
                                <span class="text-gray-500">60-69%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">D (2.0)</span>
                                <span class="text-gray-500">50-59%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">E (1.0)</span>
                                <span class="text-gray-500">40-49%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">F (0.0)</span>
                                <span class="text-gray-500">0-39%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-calculate percentage and preview grade
document.getElementById('score').addEventListener('input', function() {
    const score = parseFloat(this.value) || 0;
    const maxScore = parseFloat(document.getElementById('max_score').value) || 100;
    const percentage = (score / maxScore) * 100;
    
    // You could add real-time grade preview here
    console.log('Percentage:', percentage.toFixed(1) + '%');
});

document.getElementById('max_score').addEventListener('input', function() {
    const score = parseFloat(document.getElementById('score').value) || 0;
    const maxScore = parseFloat(this.value) || 100;
    
    // Update score input max attribute
    document.getElementById('score').setAttribute('max', maxScore);
});
</script>
@endpush
@endsection
