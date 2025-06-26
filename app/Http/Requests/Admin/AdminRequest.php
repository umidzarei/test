<?php
namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="AdminRequest",
 *     type="object",
 *     title="Admin Request",
 *     required={"name", "email", "phone", "password"},
 *     properties={
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *         @OA\Property(property="phone", type="string", example="1234567890"),
 *         @OA\Property(property="password", type="string", format="password", example="password123"),
 *     }
 * )
 */
class AdminRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('admin');
        $emailRule = 'unique:admins,email';
        $phoneRule = 'unique:admins,phone';

        if ($id) {
            $emailRule .= ',' . $id;
            $phoneRule .= ',' . $id;
        }

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', $emailRule],
            'phone' => ['required', $phoneRule],
            'password' => $this->isMethod('post') ? 'required|min:8' : 'nullable|min:8',
        ];
    }
}
