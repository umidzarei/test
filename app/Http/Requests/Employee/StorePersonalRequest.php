<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 * schema="EmployeeStorePersonalRequest",
 * title="Employee Store Personal Request",
 * required={"organization_id"},
 * @OA\Property(property="organization_id", type="integer", example=1, description="ID سازمانی که درخواست برای آن ثبت می‌شود"),
 * @OA\Property(property="options", type="object",
 * @OA\Property(property="selected_scores", type="array", @OA\Items(type="string"), example={"diabetes_risk"})
 * )
 * )
 */
class StorePersonalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => [
                'required',
                'integer',
                Rule::exists('organization_employees', 'organization_id')->where('employee_id', auth()->id()),
            ],
            'options' => 'nullable|array',
            'options.selected_scores' => 'sometimes|array',
        ];
    }
}
