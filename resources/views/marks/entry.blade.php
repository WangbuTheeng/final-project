@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
                            <p class="mt-1 text-sm text-gray-500">Bulk Marks Entry</p>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    {{ $exam->class->course->title }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $exam->class->name }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $exam->academicYear->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <a href="{{ route('marks.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Search
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Exam Info -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Total Students</h4>
                        <p class="text-2xl font-bold text-gray-900">{{ $enrollments->count() }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Total Subjects</h4>
                        <p class="text-2xl font-bold text-blue-600">{{ $subjects->count() }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Exam Type</h4>
                        <p class="text-lg font-semibold text-gray-900">{{ $exam->getExamTypeLabel() }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Exam Date</h4>
                        <p class="text-lg font-semibold text-gray-900">{{ $exam->exam_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>



        <!-- Marks Entry Form -->
        @if($subjects->count() > 0)
        <form method="POST" action="{{ route('marks.store-bulk') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $exam->id }}">

            <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-table text-green-600 mr-2"></i>
                        Marks Entry Table
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">Enter marks for each student and subject</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                                    Student
                                </th>
                                @foreach($subjects as $examSubject)
                                    @php
                                        // Safely extract data from the object
                                        $subject = null;
                                        $theoryMarks = 0;
                                        $practicalMarks = 0;

                                        if (is_object($examSubject)) {
                                            $subject = property_exists($examSubject, 'subject') ? $examSubject->subject : null;
                                            $theoryMarks = property_exists($examSubject, 'theory_marks') ? ($examSubject->theory_marks ?? 0) : 0;
                                            $practicalMarks = property_exists($examSubject, 'practical_marks') ? ($examSubject->practical_marks ?? 0) : 0;
                                        }
                                    @endphp
                                    @if($subject && is_object($subject))
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l border-gray-200">
                                        <div class="space-y-1">
                                            <div class="font-semibold">{{ $subject->name ?? 'Unknown Subject' }}</div>
                                            <div class="text-xs text-gray-400">({{ $subject->code ?? 'N/A' }})</div>
                                            <div class="text-xs text-blue-600">
                                                @if($theoryMarks && $practicalMarks)
                                                    T:{{ $theoryMarks }} + P:{{ $practicalMarks }}
                                                @elseif($theoryMarks)
                                                    Theory: {{ $theoryMarks }}
                                                @elseif($practicalMarks)
                                                    Practical: {{ $practicalMarks }}
                                                @endif
                                            </div>
                                        </div>
                                    </th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($enrollments as $index => $enrollment)
                                @php $student = $enrollment->student; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $student->user->first_name }} {{ $student->user->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    ID: {{ $student->student_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($subjects as $examSubject)
                                        @php
                                            // Safely extract data from the object
                                            $subject = null;
                                            $theoryMarks = 0;
                                            $practicalMarks = 0;

                                            if (is_object($examSubject)) {
                                                $subject = property_exists($examSubject, 'subject') ? $examSubject->subject : null;
                                                $theoryMarks = property_exists($examSubject, 'theory_marks') ? ($examSubject->theory_marks ?? 0) : 0;
                                                $practicalMarks = property_exists($examSubject, 'practical_marks') ? ($examSubject->practical_marks ?? 0) : 0;
                                            }

                                            $existingMark = $subject ? $existingMarks->get($student->id . '_' . $subject->id) : null;
                                            $totalMarks = $theoryMarks + $practicalMarks;
                                        @endphp
                                        @if($subject && is_object($subject) && isset($subject->id))
                                        <td class="px-2 py-4 text-center border-l border-gray-200">
                                            <div class="space-y-2">
                                                <!-- Hidden fields -->
                                                <input type="hidden" name="marks[{{ $index }}_{{ $subject->id }}][student_id]" value="{{ $student->id }}">
                                                <input type="hidden" name="marks[{{ $index }}_{{ $subject->id }}][enrollment_id]" value="{{ $enrollment->id }}">
                                                <input type="hidden" name="marks[{{ $index }}_{{ $subject->id }}][subject_id]" value="{{ $subject->id }}">
                                                <input type="hidden" name="marks[{{ $index }}_{{ $subject->id }}][total_marks]" value="{{ $totalMarks }}">

                                                @if($theoryMarks > 0)
                                                    <div>
                                                        <label class="text-xs text-gray-500">Theory</label>
                                                        <input type="number"
                                                               name="marks[{{ $index }}_{{ $subject->id }}][theory_marks]"
                                                               value="{{ $existingMark ? $existingMark->theory_marks : '' }}"
                                                               max="{{ $theoryMarks }}"
                                                               min="0"
                                                               step="0.01"
                                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                               placeholder="0">
                                                    </div>
                                                @endif

                                                @if($practicalMarks > 0)
                                                    <div>
                                                        <label class="text-xs text-gray-500">Practical</label>
                                                        <input type="number"
                                                               name="marks[{{ $index }}_{{ $subject->id }}][practical_marks]"
                                                               value="{{ $existingMark ? $existingMark->practical_marks : '' }}"
                                                               max="{{ $practicalMarks }}"
                                                               min="0"
                                                               step="0.01"
                                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                               placeholder="0">
                                                    </div>
                                                @endif

                                                <div>
                                                    <label class="text-xs text-gray-500">Internal</label>
                                                    <input type="number" 
                                                           name="marks[{{ $index }}_{{ $subject->id }}][internal_marks]"
                                                           value="{{ $existingMark ? $existingMark->internal_marks : '' }}"
                                                           max="20"
                                                           min="0"
                                                           step="0.01"
                                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                                           placeholder="0">
                                                </div>

                                                @if($existingMark)
                                                    <div class="text-xs text-green-600 font-medium">
                                                        Total: {{ $existingMark->obtained_marks }}/{{ $totalMarks }}
                                                        ({{ number_format($existingMark->percentage, 1) }}%)
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Submit Section -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Enter marks for each component. Leave blank if not applicable.
                        </div>
                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Save All Marks
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @else
        <!-- No Subjects Available -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Subjects Available</h3>
                <p class="mt-1 text-sm text-gray-500">
                    This exam doesn't have any subjects configured. Please configure subjects for this exam first.
                </p>
                <div class="mt-6">
                    <a href="{{ route('exams.edit', $exam) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Configure Exam
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate totals when marks are entered
    const markInputs = document.querySelectorAll('input[type="number"]');
    
    markInputs.forEach(input => {
        input.addEventListener('input', function() {
            // You can add real-time calculation logic here if needed
        });
    });
});
</script>
@endpush
@endsection
