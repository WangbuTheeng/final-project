<!-- Regular Mark Entry Table -->
<table class="min-w-full divide-y divide-gray-200 regular-marks-table">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Student
            </th>
            @if($exam->hasTheory())
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Theory Marks
                    <div class="text-xs text-gray-400 font-normal">
                        ({{ $exam->theory_marks }} marks)
                    </div>
                </th>
            @endif
            @if($exam->hasPractical())
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Practical Marks
                    <div class="text-xs text-gray-400 font-normal">
                        ({{ $exam->practical_marks }} marks)
                    </div>
                </th>
            @endif
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total Obtained
                <div class="text-xs text-gray-400 font-normal">
                    ({{ $exam->total_marks }} marks)
                </div>
            </th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Percentage
            </th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Grade
            </th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach($enrollments as $enrollment)
            @php
                $student = $enrollment->student;
                $existingMark = $existingMarks->get($student->id);
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $student->user->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $student->student_id }}
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="marks[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                </td>

                @if($exam->hasTheory())
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <input type="number" 
                               name="marks[{{ $loop->index }}][theory_marks]"
                               value="{{ $existingMark ? $existingMark->theory_marks : '' }}"
                               min="0" 
                               max="{{ $exam->theory_marks }}" 
                               step="0.1"
                               class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 theory-mark-input"
                               data-student-id="{{ $student->id }}"
                               placeholder="0">
                    </td>
                @endif

                @if($exam->hasPractical())
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <input type="number" 
                               name="marks[{{ $loop->index }}][practical_marks]"
                               value="{{ $existingMark ? $existingMark->practical_marks : '' }}"
                               min="0" 
                               max="{{ $exam->practical_marks }}" 
                               step="0.1"
                               class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 practical-mark-input"
                               data-student-id="{{ $student->id }}"
                               placeholder="0">
                    </td>
                @endif

                <td class="px-4 py-4 whitespace-nowrap text-center">
                    <input type="number" 
                           name="marks[{{ $loop->index }}][obtained_marks]"
                           value="{{ $existingMark ? $existingMark->obtained_marks : '' }}"
                           min="0" 
                           max="{{ $exam->total_marks }}" 
                           step="0.1"
                           class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 total-mark-input"
                           data-student-id="{{ $student->id }}"
                           placeholder="0"
                           {{ ($exam->hasTheory() || $exam->hasPractical()) ? 'readonly' : '' }}>
                </td>

                <td class="px-4 py-4 whitespace-nowrap text-center">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $existingMark ? number_format($existingMark->percentage, 1) . '%' : '-' }}
                    </div>
                </td>

                <td class="px-4 py-4 whitespace-nowrap text-center">
                    @if($existingMark && $existingMark->grade_letter)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $existingMark->grade_letter }}
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>

                <td class="px-4 py-4 whitespace-nowrap text-center">
                    @if($existingMark)
                        @if($existingMark->status === 'draft')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Draft
                            </span>
                        @elseif($existingMark->status === 'submitted')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Submitted
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($existingMark->status) }}
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Not Entered
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Exam Information -->
<div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-3 rounded-lg border border-gray-200">
            <div class="text-sm font-medium text-gray-900">Exam Structure</div>
            <div class="text-xs text-gray-500 mt-1">
                @if($exam->hasTheory() && $exam->hasPractical())
                    Theory: {{ $exam->theory_marks }} marks, Practical: {{ $exam->practical_marks }} marks
                @elseif($exam->hasTheory())
                    Theory Only: {{ $exam->theory_marks }} marks
                @elseif($exam->hasPractical())
                    Practical Only: {{ $exam->practical_marks }} marks
                @else
                    Total: {{ $exam->total_marks }} marks
                @endif
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border border-gray-200">
            <div class="text-sm font-medium text-gray-900">Pass Criteria</div>
            <div class="text-xs text-gray-500 mt-1">
                Minimum: {{ $exam->pass_mark }} marks ({{ number_format($exam->getPassPercentage(), 1) }}%)
            </div>
        </div>

        <div class="bg-white p-3 rounded-lg border border-gray-200">
            <div class="text-sm font-medium text-gray-900">Grading System</div>
            <div class="text-xs text-gray-500 mt-1">
                {{ $exam->gradingSystem ? $exam->gradingSystem->name : 'Default System' }}
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-sm text-gray-600">
        <p><strong>Instructions:</strong></p>
        <ul class="list-disc list-inside mt-2 space-y-1">
            <li>Enter marks according to the exam structure shown above</li>
            <li>Total marks will be calculated automatically if theory/practical are entered separately</li>
            <li>Percentage and grades will be calculated based on the grading system</li>
            <li>Use "Save Draft" to save your progress without submitting</li>
            <li>Use "Submit Marks" when you're ready to finalize the marks</li>
        </ul>
    </div>
</div>
