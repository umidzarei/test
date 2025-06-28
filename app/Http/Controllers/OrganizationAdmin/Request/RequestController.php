<?php
namespace App\Http\Controllers\OrganizationAdmin\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationAdmin\StoreHealthRequest;
use App\Http\Resources\OrganizationAdmin\RequestResource;
use App\Services\OrganizationAdmin\RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    protected RequestService $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * @OA\Post(
     * path="/api/hr/requests",
     * summary="Create a new health request by an Organization Admin (requires approval)",
     * tags={"OrganizationAdmin/Requests"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/OrgAdminStoreHealthRequest")),
     * @OA\Response(response=201, description="Request created successfully and is pending approval", @OA\JsonContent(
     * @OA\Property(property="data", ref="#/components/schemas/OrganizationAdminRequestResource")
     * )),
     * @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(StoreHealthRequest $request): JsonResponse
    {
        $orgAdmin = Auth::user();
        $healthRequest = $this->requestService->createRequest($orgAdmin, $request->validated());

        return response()->apiResult(
            data: new RequestResource($healthRequest),
            messages: [__('messages.request_submitted_for_approval')],
            statusCode: 201
        );
    }
}
