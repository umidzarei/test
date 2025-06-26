<?php
namespace App\Http\Resources\OrganizationAdmin;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="HrOrganizationAdminResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="organization_id", type="integer", example=42),
 *     @OA\Property(property="name", type="string", example="سارا احمدی"),
 *     @OA\Property(property="email", type="string", format="email", example="sara.ahmadi@example.com"),
 *     @OA\Property(property="phone", type="string", example="09121234567"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-01T12:34:56Z"),
 * )
 */
class OrganizationAdminResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
        ];
    }
}
