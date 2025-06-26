<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="AdminEmployeeRequest",
 *     type="object",
 *     title="Employee Request",
 *     required={
 *         "national_code",
 *         "name",
 *         "email",
 *         "phone",
 *         "organization_id",
 *         "department_ids",
 *         "job_position"
 *     },
 *     properties={
 *         @OA\Property(
 *             property="national_code",
 *             type="string",
 *             example="1234567890",
 *             description="کد ملی کارمند"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="Ali Karimi",
 *             description="نام کارمند"
 *         ),
 *         @OA\Property(
 *             property="email",
 *             type="string",
 *             format="email",
 *             example="ali.karimi@example.com",
 *             description="ایمیل کارمند"
 *         ),
 *         @OA\Property(
 *             property="phone",
 *             type="string",
 *             example="09123456789",
 *             description="شماره موبایل"
 *         ),
 *         @OA\Property(
 *             property="photo",
 *             type="string",
 *             format="binary",
 *             nullable=true,
 *             description="عکس پرسنلی (اپلود عکس اختیاری)"
 *         ),
 *         @OA\Property(
 *             property="organization_id",
 *             type="integer",
 *             example=1,
 *             description="آیدی سازمان"
 *         ),
 *         @OA\Property(
 *             property="department_ids",
 *             type="array",
 *             @OA\Items(type="integer", example=2),
 *             minItems=1,
 *             description="لیست آیدی دپارتمان‌ها"
 *         ),
 *         @OA\Property(
 *             property="job_position",
 *             type="string",
 *             example="Software Engineer",
 *             description="سمت شغلی"
 *         )
 *     }
 * )
 */
class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('employee');
        $emailRule = 'unique:employees,email';
        $nationalCodeRule = 'unique:employees,national_code';
        $phoneRule = 'unique:employees,phone';

        if ($id) {
            $emailRule .= ',' . $id;
            $nationalCodeRule .= ',' . $id;
            $phoneRule .= ',' . $id;
        }


        return [
            'national_code' => ['required', 'string', $nationalCodeRule],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', $emailRule],
            'phone' => ['required', $phoneRule],
            'photo' => 'nullable|image|max:2048',
            'organization_id' => 'required|exists:organizations,id',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
            'job_position' => 'required|string|max:50',
        ];
    }
}
