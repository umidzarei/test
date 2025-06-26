<?php

namespace App\Http\Requests\OrganizationAdmin;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @OA\Schema(
     * schema="UpdateOrganizationAdminProfileRequest",
     * title="Update OrganizationAdmin Profile Request",
     * required={},
     * @OA\Property(property="name", type="string", example="John", description="Admin's first name"),
     * @OA\Property(property="email", type="string", example="Doe@test.com", description="Admin's email"),
     * @OA\Property(property="phone", type="string", example="09012211111", description="Admin's phone"),
     *
     * )
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = Auth::id();
        $emailRule = 'unique:organization_admins,email';
        $phoneRule = 'unique:organization_admins,phone';

        if ($id) {
            $emailRule .= ',' . $id;
            $phoneRule .= ',' . $id;
        }
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', $emailRule],
            'phone' => ['required', $phoneRule],
        ];
    }
}
