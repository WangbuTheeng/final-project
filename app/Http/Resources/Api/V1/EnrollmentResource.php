<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'status' => $this->status ?? 'enrolled',
            'enrolled_at' => $this->created_at?->toISOString() ?? now()->toISOString(),
            'completed_at' => $this->completed_at?->toISOString() ?? null,
            'grade' => $this->grade ?? null,
            'credits' => $this->credits ?? null,
        ];
    }
}
