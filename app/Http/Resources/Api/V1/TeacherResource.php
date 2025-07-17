<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id ?? null,
            'position' => $this->position ?? null,
            'qualification' => $this->qualification ?? null,
            'specialization' => $this->specialization ?? null,
            'experience_years' => $this->experience_years ?? null,
            'salary' => $this->when($request->user()?->can('view-teacher-salary'), $this->salary),
            'hire_date' => $this->hire_date?->toDateString() ?? null,
            'status' => $this->status ?? 'active',
            
            // User information
            'user' => new UserResource($this->whenLoaded('user')),
            
            // Department information
            'department' => $this->when($this->department ?? null, [
                'id' => $this->department?->id,
                'name' => $this->department?->name,
                'code' => $this->department?->code,
            ]),
            
            // Courses taught
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            
            // Statistics
            'statistics' => $this->when($request->get('include_stats'), [
                'total_courses' => $this->courses_count ?? 0,
                'total_students' => $this->students_count ?? 0,
                'average_rating' => $this->average_rating ?? null,
            ]),
            
            'created_at' => $this->created_at?->toISOString() ?? now()->toISOString(),
            'updated_at' => $this->updated_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
