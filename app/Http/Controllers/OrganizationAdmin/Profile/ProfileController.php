<?php
namespace App\Http\Controllers\OrganizationAdmin\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationAdmin\ChangePasswordRequest;
use App\Http\Requests\OrganizationAdmin\UpdateProfileRequest;
use App\Http\Resources\OrganizationAdmin\OrganizationAdminResource;
use App\Services\OrganizationAdmin\ProfileService;
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
     * path="/api/hr/profile",
     * summary="Get current OrganizationAdmin profile",
     * tags={"OrganizationAdmin/Profile"},
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/HrOrganizationAdminResource")),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show()
    {
        $admin = Auth::user();
        $profileData = $this->adminProfileService->getAdminProfile($admin);
        return response()->apiResult(new OrganizationAdminResource($profileData));
    }

    /**
     * @OA\Put(
     * path="/api/hr/profile",
     * summary="Update current OrganizationAdmin profile",
     * tags={"OrganizationAdmin/Profile"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateOrganizationAdminProfileRequest")),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     */
    public function update(UpdateProfileRequest $request)
    {
        $admin = Auth::user();
        $this->adminProfileService->updateAdminProfile($admin, $request->validated());
        return response()->apiResult(__('messages.profile_updated_successfully'));
    }

    /**
     * @OA\Put(
     * path="/api/hr/profile/change-password",
     * summary="Change current OrganizationAdmin password",
     * tags={"OrganizationAdmin/Profile"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ChangeOrganizationAdminPasswordRequest")),
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
