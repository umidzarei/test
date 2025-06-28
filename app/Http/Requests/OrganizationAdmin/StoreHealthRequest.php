<?php

namespace App\Http\Requests\OrganizationAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHealthRequest extends FormRequest
{
    /**
     * @OA\Schema(
     * schema="OrgAdminStoreHealthRequest",
     * title="Organization Admin Store Health Request",
     * @OA\Property(property="target_all_employees", type="boolean", example=false),
     * @OA\Property(property="target_department_ids", type="array", @OA\Items(type="integer"), example={10, 11}),
     * @OA\Property(property="target_employee_ids", type="array", @OA\Items(type="integer"), example={101, 102}),
     * @OA\Property(property="options", type="object",
     * @OA\Property(property="physician_feedback_required", type="boolean", example=true),
     * @OA\Property(property="selected_scores", type="array", @OA\Items(type="string"), example={"diabetes_risk"})
     * )
     * )
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $organizationId = auth()->user()->organization_id;

        return [
            'target_all_employees' => 'sometimes|boolean',
            'target_department_ids' => 'sometimes|array|required_without_all:target_all_employees,target_employee_ids',
            'target_department_ids.*' => ['integer', Rule::exists('departments', 'id')->where('organization_id', $organizationId)],
            'target_employee_ids' => 'sometimes|array|required_without_all:target_all_employees,target_department_ids',
            'target_employee_ids.*' => ['integer', Rule::exists('organization_employees', 'employee_id')->where('organization_id', $organizationId)],
            'options' => 'nullable|array',
            'options.physician_feedback_required' => 'sometimes|boolean',
            'options.selected_scores' => 'sometimes|array',
        ];
    }
}
