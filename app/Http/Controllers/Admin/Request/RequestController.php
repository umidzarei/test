<?php

namespace App\Http\Controllers\Admin\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHealthRequest;
use App\Http\Resources\Admin\RequestResource;
use App\Services\Admin\RequestService;
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
     * path="/api/admin/requests",
     * summary="Create a new health assessment request by an Admin",
     * tags={"Admin/Requests"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * required=true,
     * description="Data for creating a new health request",
     * @OA\JsonContent(ref="#/components/schemas/AdminStoreHealthRequest")
     * ),
     * @OA\Response(
     * response=201,
     * description="Request created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="data", ref="#/components/schemas/AdminRequestResource"),
     * @OA\Property(property="messages", type="array", @OA\Items(type="string")),
     * @OA\Property(property="meta", type="object")
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(StoreHealthRequest $request): JsonResponse
    {
        $admin = Auth::user();

        $healthRequest = $this->requestService->createRequest($admin, $request->validated());

        return response()->apiResult(
            data: new RequestResource($healthRequest),
            messages: [__('messages.created')],
            statusCode: 201
        );
    }
}
