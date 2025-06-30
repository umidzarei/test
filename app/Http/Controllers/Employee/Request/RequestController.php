<?php
namespace App\Http\Controllers\Employee\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StorePersonalRequest;
use App\Http\Resources\Employee\RequestDetailsResource;
use App\Http\Resources\Employee\RequestResource;
use App\Services\Employee\RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Employee\ListRequestsRequest;
use App\Http\Resources\Employee\RequestListResource;
class RequestController extends Controller
{
    protected RequestService $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }
    /**
     * @OA\Get(
     * path="/api/employee/requests",
     * summary="Get list of health requests for the logged-in employee",
     * tags={"Employee/Requests"},
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="organization_id", in="query", required=true, @OA\Schema(type="integer"), description="فیلتر بر اساس سازمان"),
     * @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string"), description="فیلتر بر اساس وضعیت"),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/EmployeeRequestListResource"))
     * ),
     * @OA\Response(response=422, description="Validation Error: organization_id is required")
     * )
     */
    public function index(ListRequestsRequest $request): JsonResponse
    {
        $employeeId = Auth::user()->id;
        ;
        $requests = $this->requestService->getRequestList($employeeId, $request->validated());

        return response()->apiResult(
            data: new RequestListResource($requests)
        );
    }
    /**
     * @OA\Post(
     * path="/api/employee/requests",
     * summary="Create a new personal health request by an Employee",
     * tags={"Employee/Requests"},
     * security={{"sanctum":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/EmployeeStorePersonalRequest")),
     * @OA\Response(response=201, description="Request created successfully", @OA\JsonContent(
     * @OA\Property(property="data", ref="#/components/schemas/EmployeeRequestResource")
     * )),
     * @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(StorePersonalRequest $request): JsonResponse
    {
        $employee = Auth::user();
        $healthRequest = $this->requestService->createPersonalRequest($employee, $request->validated());

        return response()->apiResult(
            data: new RequestResource($healthRequest),
            messages: [__('messages.created')],
            statusCode: 201
        );
    }

    /**
     * @OA\Get(
     * path="/api/employee/requests/{id}",
     * summary="Get details of a specific health request (Task Dashboard)",
     * tags={"Employee/Requests"},
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/EmployeeRequestDetailsResource")
     * ),
     * @OA\Response(response=404, description="Request not found or access denied")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $employeeId = Auth::user()->id;
        ;

        $result = $this->requestService->getRequestDetailsForEmployee($id, $employeeId);

        if (!$result['request']) {
            return response()->apiResult(__('messages.not_found'), 404);
        }
        return response()->apiResult(
            new RequestDetailsResource($result['request'], $result['task_status'])
        );
    }
}
