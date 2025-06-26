<?php
namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AdminDepartmentResource",
 *     type="object",
 *     title="Department Resource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="organization_id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Department 1"),
 *     @OA\Property(property="description", type="string", example="Department 1 description"),
 *     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
 * )
 */
class DepartmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'organization_id' => $this->organization_id,
            'name'            => $this->name,
            'description'     => $this->description,
            'created_at'      => $this->created_at,
        ];
    }
}
