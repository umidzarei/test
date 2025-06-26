<?php
namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="AdminEmployeeResource",
 *     @OA\Property(property="id", type="integer", example=123),
 *     @OA\Property(property="national_code", type="string", example="0071234567"),
 *     @OA\Property(property="name", type="string", example="علی رضایی"),
 *     @OA\Property(property="email", type="string", format="email", example="ali.rezaei@example.com"),
 *     @OA\Property(property="phone", type="string", example="09123456789"),
 *     @OA\Property(property="photo", type="string", nullable=true, example="https://example.com/uploads/employees/123.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-01T12:34:56Z"),
 * )
 */

class EmployeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'national_code' => $this->national_code,
            'name' => $this->name,
            'email' => $this->email,
            'organizationDetails' => $this->organizationEmployee,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'created_at' => $this->created_at,
        ];
    }
}
