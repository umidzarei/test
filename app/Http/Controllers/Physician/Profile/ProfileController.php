<?php
namespace App\Http\Controllers\Physician\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Physician\ChangePasswordRequest;
use App\Http\Requests\Physician\UpdateProfileRequest;
use App\Http\Resources\Physician\PhysicianResource;
use App\Services\Physician\ProfileService;
use Auth;
class ProfileController extends Controller
{
    protected ProfileService $physicianProfileService;

    public function __construct(ProfileService $physicianProfileService)
    {
        $this->physicianProfileService = $physicianProfileService;
    }

    /**
     * @OA\Get(
     * path="/api/physician/profile",
     * summary="Get current Physician profile",
     * tags={"Physician/Profile"},
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PhysicianSelfResource")),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show()
    {
        $physician = Auth::user();
        $profileData = $this->physicianProfileService->getAdminProfile($physician);
        return response()->apiResult(new PhysicianResource($profileData));
    }

    /**
     * @OA\Put(
     * path="/api/physician/profile",
     * summary="Update current Physician profile",
     * tags={"Physician/Profile"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdatePhysiciansProfileRequest")),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     */
    public function update(UpdateProfileRequest $request)
    {
        $physician = Auth::user();
        $updatedPhysician = $this->physicianProfileService->updateAdminProfile($physician, $request->validated());
        return response()->apiResult(new PhysicianResource($updatedPhysician), __('messages.profile_updated_successfully'));
    }

    /**
     * @OA\Put(
     * path="/api/physician/profile/change-password",
     * summary="Change current Physician password",
     * tags={"Physician/Profile"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ChangePhysicianPasswordRequest")),
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
        $this->physicianProfileService->changeAdminPassword($admin, $request->validated());
        return response()->apiResult(null, __('passwords.password_changed_successfully'));
    }
}
