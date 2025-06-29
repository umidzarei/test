<?php
namespace App\Http\Resources\OrganizationAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="OrgAdminParticipantStatusResource",
 * @OA\Property(property="employee", type="object", ref="#/components/schemas/MinimalEmployeeResource"),
 * @OA\Property(property="overall_status", type="string", description="وضعیت کلی این کارمند در درخواست"),
 * @OA\Property(property="tasks_status", type="object",
 * @OA\Property(property="hra", type="string", enum={"pending", "draft", "submitted"}),
 * @OA\Property(property="lab_data", type="string", enum={"pending", "completed"}),
 * @OA\Property(property="teb_kar", type="string", enum={"pending", "completed", "completed_from_history"})
 * ))
 *
 * @OA\Schema(
 * schema="MinimalEmployeeResource",
 * @OA\Property(property="name", type="string"),
 * @OA\Property(property="photo", type="string", format="uri")
 * )
 */
class ParticipantStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'employee' => [
                'name' => $this->employee->name,
                'photo' => $this->employee->photo,
            ],
            'overall_status' => $this->status,
            'tasks_status' => $this->tasks_status,
        ];
    }
}
