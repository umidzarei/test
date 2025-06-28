<?php

namespace App\Http\Controllers\Common\RequestOption;

use App\Http\Controllers\Controller;
use App\Services\Common\RequestOptionService;
use Illuminate\Http\JsonResponse;

class RequestOptionController extends Controller
{
    protected RequestOptionService $optionService;

    public function __construct(RequestOptionService $optionService)
    {
        $this->optionService = $optionService;
    }

    /**
     * @OA\Get(
     * path="/api/common/request-options",
     * summary="Get available options for creating a health request",
     * tags={"Common"},
     * security={{"sanctum":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="available_scores", type="array", @OA\Items(type="string"), example={"diabetes_risk", "cvd_risk"}),
     * @OA\Property(property="physician_feedback_available", type="boolean", example=true)
     * )
     * )
     * )
     */
    public function index(): JsonResponse
    {
        $options = $this->optionService->getOptionsForCurrentUser();
        return response()->apiResult($options);
    }
}
