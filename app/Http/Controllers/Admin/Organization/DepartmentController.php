<?php
namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Http\Resources\Admin\DepartmentResource;
use App\Services\Admin\DepartmentService;
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
     * @OA\Get(
     * path="/api/admin/departments",
     * tags={"Admin/Departments"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="limit", in="query", description="Limit for pagination", required=false, @OA\Schema(type="integer", example=10)),
     * @OA\Parameter(name="search", in="query", description="Search term", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="organization_id", in="query", description="Filter by Organization ID", required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="orderBy", in="query", description="Field to order by", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="direction", in="query", description="Order direction (asc/desc)", required=false, @OA\Schema(type="string", enum={"asc", "desc"})),
     * @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AdminDepartmentResource")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->service->getAll($request->all())
            ->through(fn($r) => new DepartmentResource($r));
        return response()->apiResult(data: $result);

    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/admin/departments/{id}",
     *     tags={"Admin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/AdminDepartmentResource")
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        return response()->apiResult(new DepartmentResource($this->service->getById($id)));

    }
    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/api/admin/departments",
     *     tags={"Admin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(ref="#/components/schemas/DepartmentRequest"),
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

        $department = $this->service->create($request->validated());
        return response()->apiResult(data: new DepartmentResource($department), messages: [__('messages.created')]);
    }
    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/api/admin/departments/{id}",
     *     tags={"Admin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Department ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/schemas/DepartmentRequest"),
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
        $department = $this->service->update($id, $request->validated());
        return response()->apiResult(messages: [__('messages.updated')]);


    }
    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/admin/departments/{id}",
     *     tags={"Admin/Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Department ID",
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
        $this->service->delete($id);
        return response()->apiResult(messages: [__('messages.deleted')]);
    }
}
