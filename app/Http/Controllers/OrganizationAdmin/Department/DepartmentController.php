<?php
namespace App\Http\Controllers\OrganizationAdmin\Department;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationAdmin\DepartmentRequest;
use App\Http\Resources\OrganizationAdmin\DepartmentResource;
use App\Services\OrganizationAdmin\DepartmentService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected DepartmentService $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/hr/departments",
     *     tags={"OrganizationAdmin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrganizationAdminDepartmentResource")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $result = $this->service->getAllForAuthenticatedOrgAdmin($request->limit)
            ->through(fn($r) => new DepartmentResource($r));
        return response()->apiResult(data: $result);
    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/hr/departments/{id}",
     *     tags={"OrganizationAdmin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="OrganizationAdmin ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/OrganizationAdminDepartmentResource")
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $department = $this->service->getByIdForAuthenticatedOrgAdmin($id);
        return response()->apiResult(new DepartmentResource($department));
    }
    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/api/hr/departments",
     *     tags={"OrganizationAdmin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(ref="#/components/schemas/OrganizationAdminDepartmentRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param DepartmentRequest $request
     * @return JsonResponse
     */
    public function store(DepartmentRequest $request)
    {
        $department = $this->service->createForAuthenticatedOrgAdmin($request->validated());
        return response()->apiResult(data: $department, messages: [__('messages.created')]);
    }
    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/api/hr/departments/{id}",
     *     tags={"OrganizationAdmin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(ref="#/components/schemas/OrganizationAdminDepartmentRequest"),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="OrganizationAdmin ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param DepartmentRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(DepartmentRequest $request, $id)
    {
        $department = $this->service->updateForAuthenticatedOrgAdmin($id, $request->validated());
        return response()->apiResult(messages: [__('messages.updated')]);

    }
    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/hr/departments/{id}",
     *     tags={"OrganizationAdmin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="OrganizationAdmin ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->service->deleteForAuthenticatedOrgAdmin($id);
        return response()->apiResult(messages: [__('messages.deleted')]);

    }
    /**
     * @OA\Get(
     * path="/api/hr/departments/list-for-select",
     * summary="Get a simple list of departments for dropdowns",
     * tags={"OrganizationAdmin/Departments"},
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="OK")
     * )
     */
    public function listForSelect(): JsonResponse
    {
        $organizationId = Auth::user()->organization_id;
        $departments = $this->service->getListForSelect($organizationId);
        return response()->apiResult(data: $departments);
    }
}
