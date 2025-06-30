<?php

namespace App\Http\Controllers\OrganizationAdmin\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\OrganizationAdmin\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * @OA\Get(
     * path="/api/hr/dashboard",
     * summary="Get dashboard data for the organization admin",
     * tags={"OrganizationAdmin/Dashboard"},
     * security={{"sanctum":{}}},
     * @OA\Response(
     * response=200,
     * description="OK",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="participation_rate", type="object", example={"percentage": 75, "description": "نرخ مشارکت در ارزیابی‌ها"}),
     * @OA\Property(property="health_scores_summary", type="object", example={"hq_score": {"value": 85, "label": "امتیاز کلی HQ"}}),
     * @OA\Property(property="teams_status", type="array", @OA\Items(type="object"), example={{"team_name": "تیم فنی", "hq_score": 92, "status": "عالی"}})
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $organizationId = Auth::user()->organization_id;
        $dashboardData = $this->dashboardService->getDashboardData($organizationId);

        return response()->apiResult(data: $dashboardData);
    }
}
