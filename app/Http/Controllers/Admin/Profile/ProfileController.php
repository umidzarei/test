<?php
namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Services\Admin\ProfileService;
use Auth;
class ProfileController extends Controller
{
    protected ProfileService $adminProfileService;

    public function __construct(ProfileService $adminProfileService)
    {
        $this->adminProfileService = $adminProfileService;
    }

    /**
     * @OA\Get(
     * path="/api/admin/profile",
     * summary="Get current admin profile",
     * tags={"Admin/Profile"},
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/AdminResource")),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show()
    {
        $admin = Auth::user();
        $profileData = $this->adminProfileService->getAdminProfile($admin);
        return response()->apiResult(new AdminResource($profileData));
    }

    /**
     * @OA\Put(
     * path="/api/admin/profile",
     * summary="Update current admin profile",
     * tags={"Admin/Profile"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateAdminProfileRequest")),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     */
    public function update(UpdateProfileRequest $request)
    {
        $admin = Auth::guard('admin')->user();
        $updatedAdmin = $this->adminProfileService->updateAdminProfile($admin, $request->validated());
        return response()->apiResult(new AdminResource($updatedAdmin), __('messages.profile_updated_successfully'));
    }

    /**
     * @OA\Put(
     * path="/api/admin/profile/change-password",
     * summary="Change current admin password",
     * tags={"Admin/Profile"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ChangeAdminPasswordRequest")),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $admin = Auth::user();
        $this->adminProfileService->changeAdminPassword($admin, $request->validated());
        return response()->apiResult(null, __('passwords.password_changed_successfully'));
    }
}
