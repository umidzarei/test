<?php
namespace App\Http\Resources\OrganizationAdmin;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="OrganizationAdminDepartmentResource",
 *     @OA\Property(property="id", type="integer", example=3),
 *     @OA\Property(property="name", type="string", example="واحد مالی"),
 *     @OA\Property(property="description", type="string", nullable=true, example="این دپارتمان مسئول امور مالی شرکت است."),
 *     @OA\Property(property="employee_count", type="integer", nullable=true, example=12, description="تعداد کارمندان دپارتمان (در صورت بارگذاری relation)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-01 08:30:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-10 11:21:00"),
 * )
 */
class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'employee_count' => $this->whenLoaded('employees', fn() => $this->employees_count),
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
