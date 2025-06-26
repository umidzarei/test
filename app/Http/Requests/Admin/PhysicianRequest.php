<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="AdminPhysicianRequest",
 *     type="object",
 *     title="Physician Request",
 *     required={"name", "email", "phone"},
 *     properties={
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="دکتر محمد قاسمی",
 *             description="نام پزشک"
 *         ),
 *         @OA\Property(
 *             property="email",
 *             type="string",
 *             format="email",
 *             example="ghasemi@example.com",
 *             description="ایمیل پزشک"
 *         ),
 *         @OA\Property(
 *             property="password",
 *             type="string",
 *             format="password",
 *             example="password123",
 *             description="رمز عبور (هنگام ثبت اجباری)"
 *         ),
 *         @OA\Property(
 *             property="phone",
 *             type="string",
 *             example="09123456789",
 *             description="شماره موبایل پزشک"
 *         ),
 *         @OA\Property(
 *             property="photo",
 *             type="string",
 *             format="binary",
 *             nullable=true,
 *             description="عکس پزشک (آپلود تصویر اختیاری)"
 *         )
 *     }
 * )
 */

class PhysicianRequest extends FormRequest
{
    public function authorize(): bool
    {return true;}
    public function rules(): array
    {
        $id        = $this->route('physician');
        $emailRule = 'unique:physicians,email';
        $phoneRule = 'unique:physicians,phone';

        if ($id) {
            $emailRule .= ',' . $id;
            $phoneRule .= ',' . $id;
        }

        return [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', $emailRule],
            'password' => $this->isMethod('post') ? 'required|min:8' : 'nullable|min:8',
            'phone'    => ['required', $phoneRule],
            'photo'    => 'nullable|image|max:2048',
        ];
    }
}
