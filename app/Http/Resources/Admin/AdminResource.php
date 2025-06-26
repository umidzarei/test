<?php
namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Schema (
     *     schema="AdminResource",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="email", type="string"),
     *     @OA\Property(property="phone", type="string"),
     *     @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     * )
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'roles' => $this->getRoleNames(),
        ];
    }
}
