<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="EmployeeRequestListResource",
 * title="Employee Request List Item Resource",
 * @OA\Property(property="id", type="integer", example=42),
 * @OA\Property(property="tracking_code", type="string", nullable=true, example="14030408-42"),
 * @OA\Property(property="status", type="string", example="pending", description="وضعیت کلی درخواست"),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-29T10:00:00Z"),
 * @OA\Property(property="organization", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="شرکت راهبران"),
 * @OA\Property(property="logo", type="string", format="uri", example="https://example.com/logo.png")
 * )
 * )
 */
class RequestListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tracking_code' => $this->tracking_code,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'organization' => [
                'id' => $this->organization->id,
                'name' => $this->organization->name,
                'logo' => $this->organization->logo,
            ],
        ];
    }
}
