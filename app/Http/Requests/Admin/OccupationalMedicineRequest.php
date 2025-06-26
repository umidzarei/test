<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OccupationalMedicineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @OA\Schema(
     * schema="OccupationalMedicineRequest",
     *     type="object",
     * title="OccupationalMedicine Request",
     *     required={"name"},
     *     properties={
     *         @OA\Property(
     *             property="name",
     *             type="string",
     *             example="شرکت نمونه",
     *             description="نام سازمان (اجباری)"
     *         ),
     *         @OA\Property(
     *             property="national_id",
     *             type="string",
     *             maxLength=30,
     *             nullable=true,
     *             example="1234567890",
     *             description="شناسه ملی سازمان"
     *         ),
     *         @OA\Property(
     *             property="reg_number",
     *             type="string",
     *             maxLength=30,
     *             nullable=true,
     *             example="987654321",
     *             description="شماره ثبت شرکت"
     *         ),
     *         @OA\Property(
     *             property="economic_code",
     *             type="string",
     *             maxLength=30,
     *             nullable=true,
     *             example="55667788",
     *             description="کد اقتصادی"
     *         ),
     *         @OA\Property(
     *             property="address",
     *             type="string",
     *             maxLength=255,
     *             nullable=true,
     *             example="تهران، خیابان آزادی، پلاک ۲۳",
     *             description="آدرس سازمان"
     *         ),
     *         @OA\Property(
     *             property="company_phone",
     *             type="string",
     *             maxLength=30,
     *             nullable=true,
     *             example="02112345678",
     *             description="شماره تلفن شرکت"
     *         ),
     *         @OA\Property(
     *             property="representative_name",
     *             type="string",
     *             maxLength=255,
     *             nullable=true,
     *             example="رضا محمدی",
     *             description="نام نماینده شرکت"
     *         ),
     *         @OA\Property(
     *             property="representative_position",
     *             type="string",
     *             maxLength=255,
     *             nullable=true,
     *             example="مدیرعامل",
     *             description="سمت نماینده"
     *         ),
     *         @OA\Property(
     *             property="representative_phone",
     *             type="string",
     *             maxLength=30,
     *             nullable=true,
     *             example="09121234567",
     *             description="شماره موبایل نماینده"
     *         )
     *     }
     * )
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:30',
            'reg_number' => 'nullable|string|max:30',
            'economic_code' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:30',
            'representative_name' => 'nullable|string|max:255',
            'representative_position' => 'nullable|string|max:255',
            'representative_phone' => 'nullable|string|max:30',
        ];
    }
}
