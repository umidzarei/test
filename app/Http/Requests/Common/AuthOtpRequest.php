<?php
namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="AuthOtpRequest",
 *      title="Auth OTP Request Body",
 *      description="Data required for requesting an OTP.",
 *      type="object",
 *      required={"username"},
 *      @OA\Property(
 *          property="username",
 *          type="string",
 *          format="email|mobile",
 *          description="User's email address or Iranian mobile number (usually mobile for OTP).",
 *          example="09123456789",
 *          minLength=5
 *      )
 * )
 * * Class AuthOtpRequest
 * @package App\Http\Requests\Common
 */
class AuthOtpRequest extends FormRequest
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
        ];
    }
}
