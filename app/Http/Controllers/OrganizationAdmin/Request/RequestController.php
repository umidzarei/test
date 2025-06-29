<?php
namespace App\Http\Controllers\OrganizationAdmin\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationAdmin\ListRequestsRequest;
use App\Http\Requests\OrganizationAdmin\StoreHealthRequest;
use App\Http\Resources\OrganizationAdmin\RequestDetailsResource;
use App\Http\Resources\OrganizationAdmin\RequestListResource;
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
     * @OA\Get(
     * path="/api/hr/requests",
     * summary="Get list of health requests for the organization",
     * tags={"OrganizationAdmin/Requests"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrgAdminListRequestsQuery")
     * ),
     * @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/OrgAdminRequestListResource")))
     * )
     */
    public function index(ListRequestsRequest $request): JsonResponse
    {
        $orgAdmin = Auth::user();
        $result = $this->requestService->getRequestListForOrganization(
            $orgAdmin->organization_id,
            $request->validated()
        )->through(fn($r) => new RequestListResource($r));
        return response()->apiResult(data: $result);
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

    /**
     * @OA\Get(
     * path="/api/hr/requests/{id}",
     * summary="Get details of a specific request for the organization",
     * tags={"OrganizationAdmin/Requests"},
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/OrgAdminRequestDetailsResource")),
     * @OA\Response(response=404, description="Request not found or not in this organization")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $orgAdmin = Auth::user();
        $requestDetails = $this->requestService->getRequestDetails($orgAdmin->organization_id, $id);

        if (!$requestDetails) {
            return response()->apiError(__('messages.not_found'), 404);
        }
        return response()->apiResult(
            data: new RequestDetailsResource($requestDetails),
        );
    }
}
