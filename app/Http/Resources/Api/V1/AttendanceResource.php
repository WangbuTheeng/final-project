<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'date' => $this->date?->toDateString() ?? null,
            'status' => $this->status ?? 'present',
            'remarks' => $this->remarks ?? null,
            'created_at' => $this->created_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
