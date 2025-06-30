<?php
namespace App\Http\Resources\OrganizationAdmin;

use App\Http\Resources\Admin\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema (
 *     schema="OrganizationAdminEmployeeResource",
 *     @OA\Property(property="id", type="integer", example=23),
 *     @OA\Property(property="national_code", type="string", example="0071234567"),
 *     @OA\Property(property="name", type="string", example="علی رضایی"),
 *     @OA\Property(property="email", type="string", format="email", example="ali.rezaei@example.com"),
 *     @OA\Property(property="phone", type="string", example="09123456789"),
 *     @OA\Property(property="photo", type="string", format="url", nullable=true, example="https://cdn.example.com/employees/23.jpg"),
 *     @OA\Property(property="job_position", type="string", nullable=true, example="برنامه‌نویس ارشد"),
 *     @OA\Property(
 *         property="departments",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrganizationAdminDepartmentResource"),
 *         description="لیست دپارتمان‌های کارمند در این سازمان"
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-01 14:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-02 09:30:00"),
 * )
 */
class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $organizationId = auth()->user()->organization_id ?? null;
        $orgEmployeeDetails = null;
        if ($organizationId && $this->relationLoaded('organizationEmployee')) {
            $orgEmployeeDetails = $this->organizationEmployee->where('organization_id', $organizationId)->first();
        }
        return [
            'id' => $this->id,
            'national_code' => $this->national_code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo ? Storage::disk(config('filesystems.default_public_disk_name', 's3_public'))->url($this->photo) : null,
            'job_position' => $orgEmployeeDetails ? $orgEmployeeDetails->job_position : null,
            'departments' => $orgEmployeeDetails && $orgEmployeeDetails->relationLoaded('departments') ? DepartmentResource::collection($orgEmployeeDetails->departments) : [],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
