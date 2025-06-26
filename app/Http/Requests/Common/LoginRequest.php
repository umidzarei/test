<?php
namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="LoginRequest",
 *      title="Login Request Body",
 *      description="Data required for initiating the login process.",
 *      required={"username"},
 *      @OA\Property(
 *          property="username",
 *          type="string",
 *          format="email|mobile",
 *          description="User's email address or Iranian mobile number.",
 *          example="09030907396",
 *          minLength=5
 *      )
 * )
 *
 * Class LoginRequest
 * @package App\Http\Requests\Common
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
