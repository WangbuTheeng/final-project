<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'gender' => $this->gender,
            'address' => $this->address,
            'avatar' => $this->avatar ? url($this->avatar) : null,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'last_login_at' => $this->last_login_at?->toISOString(),
            'preferences' => $this->preferences,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Conditional fields
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
            
            'permissions' => $this->when($request->user()?->can('view-permissions'), function () {
                return $this->getAllPermissions()->pluck('name');
            }),
            
            // Student relationship
            'student' => new StudentResource($this->whenLoaded('student')),
            
            // Teacher relationship
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
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
            ],
        ];
    }
}
