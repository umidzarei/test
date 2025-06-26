<?php

namespace App\Http\Requests\OrganizationAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
class ChangePasswordRequest extends FormRequest
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
     * schema="ChangeOrganizationAdminPasswordRequest",
     * title="Change OrganizationAdmin Password Request",
     * required={"current_password", "password", "password_confirmation"},
     * @OA\Property(property="current_password", type="string", format="password", example="current_secret", description="Admin's current password"),
     * @OA\Property(property="password", type="string", format="password", example="NewS3cr3t!", description="Admin's new password"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="NewS3cr3t!", description="Confirmation of the new password")
     * )
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed'
            ],
        ];
    }


}
