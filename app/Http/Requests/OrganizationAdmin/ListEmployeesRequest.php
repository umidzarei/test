<?php

namespace App\Http\Requests\OrganizationAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListEmployeesRequest extends FormRequest
{
    /**
     * @OA\Schema(
     * schema="OrgAdminListEmployeesRequest",
     * title="Organization Admin List Employees Request",
     * description="Query parameters for listing and filtering employees in the organization admin panel.",
     * @OA\Property(property="limit", type="integer", description="Number of items per page", example=15),
     * @OA\Property(property="search", type="string", description="Search term for employee name, national code, or email", example="John Doe"),
     * @OA\Property(property="department_id", type="integer", description="Filter employees by a specific department ID", example=12),
     * @OA\Property(property="orderBy", type="string", description="Field to sort by", example="last_name", enum={"id", "first_name", "last_name", "national_code", "created_at"}),
     * @OA\Property(property="direction", type="string", description="Sort direction", example="asc", enum={"asc", "desc"})
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
            'limit' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'department_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where('organization_id', $organizationId)
            ],
            'orderBy' => 'nullable|string|in:id,first_name,last_name,national_code,created_at',
            'direction' => 'nullable|string|in:asc,desc',
        ];
    }
}
