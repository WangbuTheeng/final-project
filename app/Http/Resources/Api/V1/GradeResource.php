<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'grade' => $this->grade ?? null,
            'points' => $this->points ?? null,
            'percentage' => $this->percentage ?? null,
            'remarks' => $this->remarks ?? null,
            'created_at' => $this->created_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
