<?php

namespace App\Http\Resources\Employee;

use App\Http\Resources\Admin\OrganizationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class RequestDetailsResource extends JsonResource
{
    protected array $taskStatus;

    public function __construct($resource, array $taskStatus)
    {
        parent::__construct($resource);
        $this->taskStatus = $taskStatus;
    }
    /**
     * @OA\Schema(
     * schema="EmployeeRequestDetailsResource",
     * title="Employee Request Details Resource",
     * @OA\Property(property="id", type="integer", example=42),
     * @OA\Property(property="tracking_code", type="string", nullable=true, example="14030408-42"),
     * @OA\Property(property="status", type="string", example="in_process"),
     * @OA\Property(property="options", type="object", description="آپشن‌های انتخاب شده برای این درخواست"),
     * @OA\Property(property="organization", type="object", ref="#/components/schemas/AdminOrganizationResource"),
     * @OA\Property(property="tasks", type="object",
     * @OA\Property(property="hra", type="string", example="pending", enum={"pending", "completed"}),
     * @OA\Property(property="lab_data", type="string", example="pending", enum={"pending", "completed"}),
     * @OA\Property(property="teb_kar", type="string", example="pending_self_declaration", enum={"completed", "completed_from_history", "pending_self_declaration"})
     * ),
     * @OA\Property(property="final_results", type="object", nullable=true, description="این بخش فقط در صورت تکمیل تمام تسک‌ها نمایش داده می‌شود",
     * @OA\Property(property="scores", type="object", description="اسکورهای محاسبه شده"),
     * @OA\Property(property="physician_feedback", type="string", description="نظر پزشک")
     * )
     * )
     */
    public function toArray(Request $request): array
    {
        $allTasksCompleted = !in_array('pending', $this->taskStatus) && !in_array('pending_self_declaration', $this->taskStatus);

        return [
            'id' => $this->id,
            'tracking_code' => $this->tracking_code,
            'status' => $this->status,
            'options' => $this->options,
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'tasks' => $this->taskStatus,
            'final_results' => $this->when($allTasksCompleted, function () {
                $requestEmployee = $this->requestEmployees->first();
                return [
                    'scores' => $requestEmployee?->calculatedScore,
                    'physician_feedback' => $this->options['physician_feedback_required'] ? $requestEmployee?->physicianFeedback : null,
                ];
            }),
        ];
    }
}
