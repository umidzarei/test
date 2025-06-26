<?php

namespace App\Http\Requests\Employee;

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
     *
     * @OA\Schema(
     * schema="UpdateEmployeeProfileRequest",
     * title="Update Employee Profile Request",
     * description="Request body for updating employee profile information. Email and phone number changes might be ignored or handled separately.",
     * required={},
     * @OA\Property(property="name", type="string", example="پوریا", description="Employee's full name or first name"),
     * @OA\Property(property="national_code", type="string", example="0012345678", description="Employee's national code (if submitted, must be unique if changed)"),
     * @OA\Property(property="email", type="string", format="email", example="employee@example.com", description="Employee's email (if submitted, must be unique if changed)"),
     * @OA\Property(property="phone", type="string", example="09123456789", description="Employee's phone number (if submitted, must be unique if changed)"),
     * @OA\Property(property="photo", type="string", format="binary", description="Employee's photo/avatar image file (optional)")
     * )
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = Auth::id();
        $emailRule = 'unique:employees,email';
        $phoneRule = 'unique:employees,phone';
        $nationalCodeRule = 'unique:employees,national_code';


        if ($id) {
            $emailRule .= ',' . $id;
            $phoneRule .= ',' . $id;
            $nationalCodeRule .= ',' . $id;
        }
        return [
            'name' => 'required|string|max:255',
            'national_code' => ['required', $nationalCodeRule],
            'email' => ['required', 'email', $emailRule],
            'phone' => ['required', $phoneRule],
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
