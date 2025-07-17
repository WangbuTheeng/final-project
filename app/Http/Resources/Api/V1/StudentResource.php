<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admission_number' => $this->admission_number,
            'status' => $this->status,
            'admission_date' => $this->admission_date?->toDateString(),
            'graduation_date' => $this->graduation_date?->toDateString(),
            
            // User information
            'user' => new UserResource($this->whenLoaded('user')),
            
            // Academic information
            'faculty' => $this->when($this->faculty, [
                'id' => $this->faculty?->id,
                'name' => $this->faculty?->name,
                'code' => $this->faculty?->code,
            ]),
            
            'department' => $this->when($this->department, [
                'id' => $this->department?->id,
                'name' => $this->department?->name,
                'code' => $this->department?->code,
            ]),
            
            // Guardian information
            'guardian' => $this->when($this->guardian_name, [
                'name' => $this->guardian_name,
                'phone' => $this->guardian_phone,
                'email' => $this->guardian_email,
                'relationship' => $this->guardian_relationship,
                'address' => $this->guardian_address,
            ]),
            
            // Academic records
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
            'grades' => GradeResource::collection($this->whenLoaded('grades')),
            'attendance' => AttendanceResource::collection($this->whenLoaded('attendance')),
            
            // Financial information (if authorized)
            'financial' => $this->when($request->user()?->can('view-student-finances'), [
                'total_fees' => $this->total_fees,
                'paid_fees' => $this->paid_fees,
                'outstanding_fees' => $this->outstanding_fees,
                'scholarship_amount' => $this->scholarship_amount,
            ]),
            
            // Statistics
            'statistics' => $this->when($request->get('include_stats'), [
                'total_courses' => $this->enrollments_count ?? $this->enrollments()->count(),
                'completed_courses' => $this->completed_enrollments_count ?? $this->enrollments()->where('status', 'completed')->count(),
                'current_gpa' => $this->current_gpa,
                'total_credits' => $this->total_credits,
                'attendance_percentage' => $this->attendance_percentage,
            ]),
            
            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'deleted_at' => $this->when($this->deleted_at, $this->deleted_at?->toISOString()),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => 'v1',
                'timestamp' => now()->toISOString(),
                'includes' => $this->getIncludes($request),
            ],
        ];
    }

    /**
     * Get the includes that were requested.
     */
    protected function getIncludes(Request $request): array
    {
        $include = $request->get('include', '');
        return $include ? explode(',', $include) : [];
    }
}
