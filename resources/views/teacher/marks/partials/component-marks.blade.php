<!-- Component-wise Mark Entry Table for Bachelor's Internal Assessment -->
<table class="min-w-full divide-y divide-gray-200 component-marks-table">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                Student
            </th>
            @foreach($examComponents as $component)
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $component->name }}
                    <div class="text-xs text-gray-400 font-normal">
                        ({{ $component->default_marks }} marks)
                    </div>
                </th>
            @endforeach
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total
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
                $studentComponentMarks = $existingComponentMarks->get($student->id, collect());
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10">
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

                @foreach($examComponents as $component)
                    @php
                        $componentMark = $studentComponentMarks->where('exam_component_id', $component->id)->first();
                        $currentMarks = $componentMark ? $componentMark->marks_obtained : '';
                    @endphp
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <input type="number" 
                               name="component_marks[{{ $student->id }}][{{ $component->id }}][marks]"
                               value="{{ $currentMarks }}"
                               min="0" 
                               max="{{ $component->default_marks }}" 
                               step="0.1"
                               class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 component-mark-input"
                               data-student-id="{{ $student->id }}"
                               data-component-id="{{ $component->id }}"
                               placeholder="0">
                    </td>
                @endforeach

                <td class="px-4 py-4 whitespace-nowrap text-center">
                    <div class="text-lg font-semibold text-blue-600" id="total-{{ $student->id }}">
                        {{ $existingMark ? number_format($existingMark->obtained_marks, 1) : '0.0' }}
                    </div>
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

<!-- Component Information -->
<div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($examComponents as $component)
            <div class="bg-white p-3 rounded-lg border border-gray-200">
                <div class="text-sm font-medium text-gray-900">{{ $component->name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $component->description }}</div>
                <div class="text-sm text-blue-600 mt-1">
                    Max: {{ $component->default_marks }} marks 
                    ({{ $component->default_weightage }}%)
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="mt-4 text-sm text-gray-600">
        <p><strong>Instructions:</strong></p>
        <ul class="list-disc list-inside mt-2 space-y-1">
            <li>Enter marks for each component based on student performance</li>
            <li>Maximum marks for each component are shown in the header</li>
            <li>Total marks will be calculated automatically</li>
            <li>Use "Save Draft" to save your progress without submitting</li>
            <li>Use "Submit Marks" when you're ready to finalize the marks</li>
        </ul>
    </div>
</div>
