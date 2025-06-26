<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="DepartmentRequest",
 *     type="object",
 *     title="Department Request",
 *     required={"organization_id", "name"},
 *     properties={
 *         @OA\Property(
 *             property="organization_id",
 *             type="integer",
 *             example=1,
 *             description="ID of the organization"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="IT Department",
 *             description="Name of the department"
 *         ),
 *         @OA\Property(
 *             property="description",
 *             type="string",
 *             nullable=true,
 *             example="This is the IT department",
 *             description="Department description (optional)"
 *         )
 *     }
 * )
 */
class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'organization_id' => 'required|exists:organizations,id',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:255',
        ];
    }
}
