<?php
namespace App\Http\Requests\OrganizationAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="OrganizationAdminDepartmentRequest",
 *     type="object",
 *     title="Organization Admin Department Request",
 *     required={"name"},
 *     properties={
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             maxLength=255,
 *             example="منابع انسانی",
 *             description="نام دپارتمان (در هر سازمان یکتا)"
 *         ),
 *         @OA\Property(
 *             property="description",
 *             type="string",
 *             maxLength=1000,
 *             nullable=true,
 *             example="این دپارتمان مسئول جذب و مدیریت منابع انسانی است.",
 *             description="توضیحات دپارتمان (اختیاری)"
 *         )
 *     }
 * )
 */
class DepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $organizationId = auth()->user()->organization_id;
        $departmentId   = $this->route('department');

        return [
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')
                    ->where('organization_id', $organizationId)
                    ->ignore($departmentId),
            ],
            'description' => 'nullable|string|max:1000',
        ];
    }
}
