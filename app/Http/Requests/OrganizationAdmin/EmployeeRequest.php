<?php
namespace App\Http\Requests\OrganizationAdmin;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="OrganizationAdminEmployeeRequest",
 *     type="object",
 *     title="Organization Admin Employee Request",
 *     required={
 *         "national_code",
 *         "name",
 *         "email",
 *         "phone",
 *         "department_ids",
 *         "job_position"
 *     },
 *     properties={
 *         @OA\Property(
 *             property="national_code",
 *             type="string",
 *             example="0071234567",
 *             description="کد ملی کارمند (۸ تا ۱۰ رقم، یکتا)"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             maxLength=255,
 *             example="علی رضایی",
 *             description="نام کامل کارمند"
 *         ),
 *         @OA\Property(
 *             property="email",
 *             type="string",
 *             format="email",
 *             maxLength=255,
 *             example="ali.rezaei@example.com",
 *             description="ایمیل کارمند (یکتا)"
 *         ),
 *         @OA\Property(
 *             property="phone",
 *             type="string",
 *             example="09123456789",
 *             description="شماره موبایل کارمند (یکتا)"
 *         ),
 *         @OA\Property(
 *             property="photo",
 *             type="string",
 *             format="binary",
 *             nullable=true,
 *             description="عکس پرسنلی (jpeg, png, jpg, gif - حداکثر ۲ مگابایت، اختیاری)"
 *         ),
 *         @OA\Property(
 *             property="department_ids",
 *             type="array",
 *             minItems=1,
 *             @OA\Items(type="integer", example=2),
 *             description="آرایه آیدی دپارتمان‌ها (حداقل یک مورد، فقط دپارتمان‌های این سازمان)"
 *         ),
 *         @OA\Property(
 *             property="job_position",
 *             type="string",
 *             maxLength=100,
 *             example="برنامه‌نویس ارشد",
 *             description="سمت شغلی کارمند"
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
        $employeeIdOnRoute = $this->route('employee');
        $organizationId = auth()->user()->organization_id;

        $isCreatingNewEmployee = true;
        if ($this->isMethod('post') && ($this->national_code || $this->email)) {
            $existingEmployee = Employee::where(function ($query) {
                if ($this->national_code) {
                    $query->where('national_code', $this->national_code);
                }
                if ($this->email && $this->national_code) {
                    $query->orWhere('email', $this->email);
                } elseif ($this->email) {
                    $query->where('email', $this->email);
                }
            })->first();
            if ($existingEmployee) {
                $isCreatingNewEmployee = false;
            }
        }

        $emailRule = Rule::unique('employees', 'email');
        $mobileRule = Rule::unique('employees', 'mobile');

        $nationalCodeRule = Rule::unique('employees', 'national_code');
        if ($employeeIdOnRoute) {
            $emailRule->ignore($employeeIdOnRoute);
            $mobileRule->ignore($employeeIdOnRoute);
            $nationalCodeRule->ignore($employeeIdOnRoute);
            $isCreatingNewEmployee = false;
        }

        return [
            'national_code' => [
                $isCreatingNewEmployee || $this->isMethod('put') ? 'required' : 'nullable',
                'string',
                'digits_between:8,10',
                $nationalCodeRule,
            ],
            'name' => $isCreatingNewEmployee || $this->isMethod('put') ? 'required|string|max:255' : 'nullable|string|max:255',
            'email' => [
                $isCreatingNewEmployee || $this->isMethod('put') ? 'required' : 'nullable',
                'email',
                'max:255',
                $emailRule,
            ],
            'phone' => [
                $isCreatingNewEmployee || $this->isMethod('put') ? 'required' : 'nullable',
                $emailRule,
            ],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->where(function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }),
            ],
            'job_position' => 'required|string|max:100',
        ];
    }

}
