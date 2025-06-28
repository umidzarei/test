<?php

namespace App\Http\Resources\OrganizationAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    /**
     * @OA\Schema(
     * schema="OrganizationAdminRequestResource",
     * title="OrganizationAdmin Request Resource",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="tracking_code", type="string", example="14030408-1"),
     * @OA\Property(property="organization_id", type="integer", example=1),
     * @OA\Property(property="status", type="string", example="in_process"),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-28T12:30:00Z")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tracking_code' => $this->tracking_code,
            'organization_id' => $this->organization_id,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
