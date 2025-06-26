<?php
namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema (
 *     schema="AdminOrganizationResource",
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="name", type="string", example="شرکت فناوری آرمان"),
 *     @OA\Property(property="national_id", type="string", example="14001234567", nullable=true),
 *     @OA\Property(property="reg_number", type="string", example="123456", nullable=true),
 *     @OA\Property(property="economic_code", type="string", example="987654321", nullable=true),
 *     @OA\Property(property="logo", type="string", format="url", example="https://cdn.example.com/logos/5.png", nullable=true),
 *     @OA\Property(property="address", type="string", example="تهران، خیابان آزادی، پلاک ۲۳", nullable=true),
 *     @OA\Property(property="company_phone", type="string", example="02112345678", nullable=true),
 *     @OA\Property(property="representative_name", type="string", example="رضا محمدی", nullable=true),
 *     @OA\Property(property="representative_position", type="string", example="مدیرعامل", nullable=true),
 *     @OA\Property(property="representative_phone", type="string", example="09121234567", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-01T12:34:56Z"),
 * )
 */
class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'national_id'             => $this->national_id,
            'reg_number'              => $this->reg_number,
            'economic_code'           => $this->economic_code,
            'logo'                    => $this->logo ? Storage::disk('s3')->url($this->logo) : null,
            'address'                 => $this->address,
            'company_phone'           => $this->company_phone,
            'representative_name'     => $this->representative_name,
            'representative_position' => $this->representative_position,
            'representative_phone'    => $this->representative_phone,
            'created_at'              => $this->created_at,
        ];
    }
}
