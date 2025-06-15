<?php

namespace App\Http\Requests;

use App\Models\Exam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateExamRequest
 * 
 * Form request for updating an existing exam.
 * 
 * @package App\Http\Requests
 */
class UpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->can('edit-exams') || $this->user()->hasRole(['Super Admin', 'Admin']));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $exam = $this->route('exam');
        
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:classes,id',
            ],
            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],
            'exam_type' => [
                'required',
                'string',
                Rule::in(array_keys(Exam::getExamTypes())),
            ],
            'semester' => [
                'required',
                'string',
                Rule::in(array_keys(Exam::getSemesters())),
            ],
            'exam_date' => [
                'required',
                'date',
                // Only require future date if exam is not completed
                $exam && $exam->isCompleted() ? 'date' : 'after:now',
            ],
            'duration_minutes' => [
                'required',
                'integer',
                'min:15',
                'max:480', // 8 hours maximum
            ],
            'total_marks' => [
                'required',
                'numeric',
                'min:1',
                'max:1000',
            ],
            'pass_mark' => [
                'required',
                'numeric',
                'min:0',
                'lte:total_marks',
            ],
            'venue' => [
                'nullable',
                'string',
                'max:255',
            ],
            'instructions' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(array_keys(Exam::getStatuses())),
            ],
        ];
    }
}
