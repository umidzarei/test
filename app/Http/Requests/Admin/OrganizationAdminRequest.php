<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="AdminOrganizationAdminRequest",
 *     type="object",
 *     title="Organization Admin Request",
 *     required={"organization_id", "name", "email", "phone"},
 *     properties={
 *         @OA\Property(
 *             property="organization_id",
 *             type="integer",
 *             example=1,
 *             description="شناسه سازمان"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="Sara Ahmadi",
 *             description="نام ادمین سازمان"
 *         ),
 *         @OA\Property(
 *             property="email",
 *             type="string",
 *             format="email",
 *             example="sara.ahmadi@example.com",
 *             description="ایمیل ادمین سازمان"
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
 *             example="09121234567",
 *             description="شماره موبایل ادمین سازمان"
 *         )
 *     }
 * )
 */
class OrganizationAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('organization_admin');
        $emailRule = 'unique:organization_admins,email';
        $phoneRule = 'unique:organization_admins,phone';

        if ($id) {
            $emailRule .= ',' . $id;
            $phoneRule .= ',' . $id;
        }

        return [
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', $emailRule],
            'password' => $this->isMethod('post') ? 'required|min:8' : 'nullable|min:8',
            'phone' => ['required', $phoneRule],
        ];
    }
}
