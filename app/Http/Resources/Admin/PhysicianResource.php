<?php
namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema (
 *     schema="AdminPhysicianResource",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="name", type="string", example="دکتر محمد قاسمی"),
 *     @OA\Property(property="email", type="string", format="email", example="ghasemi@example.com"),
 *     @OA\Property(property="phone", type="string", example="09123456789"),
 *     @OA\Property(property="photo", type="string", format="url", nullable=true, example="https://cdn.example.com/physicians/10.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-01T12:34:56Z"),
 * )
 */
class PhysicianResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'photo'      => $this->photo ? Storage::disk('s3')->url($this->photo) : null,
            'created_at' => $this->created_at,
        ];
    }
}
