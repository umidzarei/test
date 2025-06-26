<?php
namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="ZeeoWork API Name",
 *     description="ZeeoWork API Description",
 *     @OA\Contact(
 *         email="tech@zeework.ir"
 *     )
 * )
 * @OA\Schema(
 *   schema="ApiResponse",
 *   type="object",
 *   @OA\Property(property="ok", type="boolean", example=true),
 *   @OA\Property(
 *   property="messages",
 *   type="array",
 *   description="آرایه‌ای از پیام‌ها برای کاربر (می‌تواند شامل پیام‌های موفقیت، خطا یا اطلاعاتی باشد)",
 *   nullable=true,
 *   @OA\Items(
 *   type="string",
 *   example="عملیات با موفقیت انجام شد."
 *   ),
 *   example={"عملیات موفقیت‌آمیز بود", "پیام دوم در صورت وجود"}
 *   ),
 *   @OA\Property(property="data", type="object", nullable=true, example={"id": 1, "name": "Ali"}),
 * )
 */
abstract class Controller
{
    //
}
