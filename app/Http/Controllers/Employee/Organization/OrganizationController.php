<?php

namespace App\Http\Controllers\Employee\Organization;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrganizationResource;
use App\Services\Employee\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    protected OrganizationService $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * @OA\Get(
     * path="/api/employee/organizations",
     * summary="Get the list of organizations for the logged-in employee",
     * tags={"Employee/Organizations"},
     * security={{"sanctum":{}}},
     * @OA\Response(
     * response=200,
     * description="A list of the employee's organizations.",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AdminOrganizationResource"))
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $employee = Auth::user();
        $organizations = $this->organizationService->getOrganizationsForEmployee($employee);
        return response()->apiResult(data: OrganizationResource::collection($organizations));
    }
}
