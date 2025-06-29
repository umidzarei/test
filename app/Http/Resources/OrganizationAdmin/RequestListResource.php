<?php
namespace App\Http\Resources\OrganizationAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestListResource extends JsonResource
{
    /**
     * @OA\Schema(
     * schema="OrgAdminRequestListResource",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="created_at_formatted", type="string", example="۱۴۰۳/۰۴/۱۵"),
     * @OA\Property(property="status", type="string", example="تکمیل شده"),
     * @OA\Property(property="participation_rate", type="integer", example=95, description="نرخ مشارکت به درصد"),
     * @OA\Property(property="total_employees", type="integer", example=100),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at_formatted' => $this->created_at,
            'status' => $this->status,
            'participation_rate' => $this->participation_rate,
            'total_employees' => $this->total_employees_count,
        ];
    }
}
