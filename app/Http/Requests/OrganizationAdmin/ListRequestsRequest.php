<?php

namespace App\Http\Requests\OrganizationAdmin;

use Illuminate\Foundation\Http\FormRequest;

class ListRequestsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    /**
     * @OA\Schema(
     * schema="OrgAdminListRequestsQuery",
     * type="object",
     * title="Organization Admin List Requests Query Parameters",
     * description="پارامترهای مورد استفاده برای لیست و فیلتر کردن درخواست‌های سلامت",
     * properties={
     * @OA\Property(property="limit", type="integer", description="تعداد آیتم‌ها در هر صفحه", default=15, example=10),
     * @OA\Property(property="page", type="integer", description="شماره صفحه مورد نظر", default=1, example=1),
     * @OA\Property(property="search", type="string", description="عبارت جستجو در فیلدهای قابل جستجو", example=""),
     * @OA\Property(property="status", type="string", enum={"pending", "in_process", "done", "reject", "pending_admin_approval"}, description="فیلتر بر اساس وضعیت"),
     * @OA\Property(property="orderBy", type="string", enum={"created_at", "status"}, default="created_at", description="فیلد مورد نظر برای مرتب‌سازی"),
     * @OA\Property(property="direction", type="string", enum={"asc", "desc"}, default="desc", description="جهت مرتب‌سازی")
     * }
     * )
     */
    public function rules(): array
    {
        return [
            'limit' => 'sometimes|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,in_process,done,reject,pending_admin_approval',
            'orderBy' => 'nullable|string|in:created_at,status',
            'direction' => 'nullable|string|in:asc,desc',
        ];
    }
}
