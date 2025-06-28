<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHealthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    /**
     * @OA\Schema(
     * schema="AdminStoreHealthRequest",
     * title="Admin Store Health Request",
     * required={"organization_id", "occupational_medicine_id", "physician_ids"},
     * @OA\Property(property="organization_id", type="integer", example=1, description="ID سازمان مورد نظر"),
     * @OA\Property(property="occupational_medicine_id", type="integer", example=1, description="ID مرکز طب کار"),
     * @OA\Property(property="physician_ids", type="array", @OA\Items(type="integer"), example={1, 2}, description="آرایه‌ای از ID پزشکان"),
     * @OA\Property(property="target_all_employees", type="boolean", example=false, description="آیا تمام کارمندان هدف هستند؟"),
     * @OA\Property(property="target_department_ids", type="array", @OA\Items(type="integer"), example={10, 11}, description="آرایه‌ای از ID دپارتمان‌های هدف"),
     * @OA\Property(property="target_employee_ids", type="array", @OA\Items(type="integer"), example={101, 102}, description="آرایه‌ای از ID کارمندان خاص هدف"),
     * @OA\Property(property="options", type="object",
     * @OA\Property(property="physician_feedback_required", type="boolean", example=true),
     * @OA\Property(property="selected_scores", type="array", @OA\Items(type="string"), example={"diabetes_risk", "cvd_risk"})
     * )
     * )
     */
    public function rules(): array
    {
        $organizationId = $this->input('organization_id');

        return [
            'organization_id' => 'required|integer|exists:organizations,id',
            'occupational_medicine_id' => 'required|integer|exists:occupational_medicines,id',
            'physician_ids' => 'required|array|min:1',
            'physician_ids.*' => 'required|integer|exists:physicians,id',

            'target_all_employees' => 'sometimes|boolean',
            'target_department_ids' => 'sometimes|array|required_without_all:target_all_employees,target_employee_ids',
            'target_department_ids.*' => [
                'integer',
                Rule::exists('departments', 'id')->where('organization_id', $organizationId),
            ],
            'target_employee_ids' => 'sometimes|array|required_without_all:target_all_employees,target_department_ids',
            'target_employee_ids.*' => [
                'integer',
                Rule::exists('organization_employees', 'employee_id')->where('organization_id', $organizationId),
            ],

            'options' => 'nullable|array',
            'options.physician_feedback_required' => 'sometimes|boolean',
            'options.selected_scores' => 'sometimes|array',
            'options.selected_scores.*' => 'string|in:' . implode(',', $this->getAvailableScores()),
        ];
    }

    private function getAvailableScores(): array
    {
        return [
            'diabetes_risk',
            'metabolic_syndrome',
            'cvd_risk',
            'mental_health',
            'lifestyle',
            'fatty_liver',
        ];
    }
}
