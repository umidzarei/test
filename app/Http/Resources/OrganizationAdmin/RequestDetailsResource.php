<?php
namespace App\Http\Resources\OrganizationAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestDetailsResource extends JsonResource
{
    /**
     * @OA\Schema(
     * schema="OrgAdminRequestDetailsResource",
     * title="Organization Admin Request Details Resource",
     * description="پاسخ کامل جزئیات یک درخواست برای ادمین سازمان",
     * @OA\Property(property="id", type="integer", example=42, description="ID درخواست"),
     * @OA\Property(property="status", type="string", example="in_process", description="وضعیت کلی درخواست"),
     * @OA\Property(property="participation_rate", type="integer", example=75, description="نرخ مشارکت کلی به درصد"),
     * @OA\Property(
     * property="reports",
     * type="object",
     * nullable=true,
     * description="این بخش فقط در صورت تکمیل شدن درخواست (status='done') نمایش داده می‌شود و شامل گزارش‌های تجمعی است.",
     * @OA\Property(
     * property="overall_score_averages",
     * type="object",
     * description="میانگین اسکورها در سطح کل درخواست",
     * example={"diabetes_risk_score": 55.5, "cvd_risk_score": 30.2}
     * )
     * ),
     * @OA\Property(
     * property="participants_status",
     * type="array",
     * nullable=true,
     * description="این بخش فقط در صورت در حال انجام بودن درخواست نمایش داده می‌شود و شامل وضعیت هر کارمند است.",
     * @OA\Items(ref="#/components/schemas/OrgAdminParticipantStatusResource")
     * )
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'participation_rate' => $this->participation_rate,
            'reports' => $this->when($this->status === 'done', $this->aggregated_reports),
            'participants_status' => $this->when(
                $this->status !== 'done',
                fn() => ParticipantStatusResource::collection($this->whenLoaded('requestEmployees'))
            ),
        ];
    }
}
