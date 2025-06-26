<?php

namespace App\Http\Requests\Physician;

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
     * schema="UpdatePhysiciansProfileRequest",
     * title="Update physicians Profile Request",
     * required={},
     * @OA\Property(property="name", type="string", example="John", description="physicians's first name"),
     * @OA\Property(property="email", type="string", example="Doe@test.com", description="physicians's email"),
     * @OA\Property(property="phone", type="string", example="09012211111", description="physicians's phone"),
     * @OA\Property(property="photo", type="file", format="binary", description="physicians's avatar image file (optional)")
     * )
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = Auth::id();
        $emailRule = 'unique:physicians,email';
        $phoneRule = 'unique:physicians,phone';

        if ($id) {
            $emailRule .= ',' . $id;
            $phoneRule .= ',' . $id;
        }
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', $emailRule],
            'phone' => ['required', $phoneRule],
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
