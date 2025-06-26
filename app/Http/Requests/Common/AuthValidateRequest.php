<?php
namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="AuthValidateRequest",
 *      title="Auth Validate Request Body",
 *      description="Data required for validating OTP or password.",
 *      type="object",
 *      required={"username", "role", "pass"},
 *      @OA\Property(
 *          property="username", *          type="string",
 *          format="email|mobile",
 *          description="User's email address or Iranian mobile number.",
 *          example="user@example.com or 09123456789",
 *          minLength=5
 *      ),
 *      @OA\Property(
 *          property="role",
 *          type="string",
 *          description="The role to validate against.", *          enum={"admin", "employee", "physician", "organization_admin"},
 *          example="employee"
 *      ),
 *      @OA\Property(
 *          property="pass",
 *          type="string",
 *          description="The OTP code or user's password.",
 *          example="1234 or S3cr3tP@sswOrd!"
 *      )
 * )
 *
 * Class AuthValidateRequest
 * @package App\Http\Requests\Common
 */
class AuthValidateRequest extends FormRequest
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
        return [
            'username' => [
                'required',
                'string',
                'min:5',
                function ($attribute, $value, $fail) {
                    $trimmedValue       = trim($value);
                    $emailRegex         = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/i';
                    $iranianMobileRegex = '/^(?:\+98|0)?9\d{9}$/';
                    $isEmail            = preg_match($emailRegex, $trimmedValue);
                    $isMobile           = preg_match($iranianMobileRegex, $trimmedValue);
                    if (! $isEmail && ! $isMobile) {
                        $fail();
                    }
                },
            ],
            'role'     => 'required|in:admin,employee,physician,organization_admin',
            'pass'     => 'required',
        ];
    }
}
