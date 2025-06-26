<?php
namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateProfileRequest;
use App\Http\Resources\Employee\EmployeeResource;
use App\Services\Employee\ProfileService;
use Auth;
class ProfileController extends Controller
{
    protected ProfileService $employeeProfileService;

    public function __construct(ProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }

    /**
     * @OA\Get(
     * path="/api/employee/profile",
     * summary="Get current Employee profile",
     * tags={"Employee/Profile"},
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/EmployeeSelfResource")),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show()
    {
        $employee = Auth::user();
        $profileData = $this->employeeProfileService->getProfile($employee);
        return response()->apiResult(new EmployeeResource($profileData));
    }

    /**
     * @OA\Put(
     * path="/api/employee/profile",
     * summary="Update current Employee profile",
     * tags={"Employee/Profile"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateEmployeeProfileRequest")),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     */
    public function update(UpdateProfileRequest $request)
    {
        $employee = Auth::user();
        $updatedEmployee = $this->employeeProfileService->updateProfile($employee, $request->validated());
        return response()->apiResult(new EmployeeResource($updatedEmployee), __('messages.profile_updated_successfully'));
    }


}
