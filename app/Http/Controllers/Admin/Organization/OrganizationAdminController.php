<?php
namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrganizationAdminRequest;
use App\Http\Resources\Admin\OrganizationAdminResource;
use App\Services\Admin\OrganizationAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationAdminController extends Controller
{
    protected OrganizationAdminService $service;

    public function __construct(OrganizationAdminService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Get(
     * path="/api/admin/organization-admins",
     * tags={"Admin/OrganizationAdmin"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="limit", in="query", description="Limit for pagination", required=false, @OA\Schema(type="integer", example=10)),
     * @OA\Parameter(name="search", in="query", description="Search term", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="organization_id", in="query", description="Filter by Organization ID", required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="orderBy", in="query", description="Field to order by", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="direction", in="query", description="Order direction (asc/desc)", required=false, @OA\Schema(type="string", enum={"asc", "desc"})),
     * @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AdminOrganizationAdminResource")))
     * )
     */
    public function index(Request $request)
    {
        $result = $this->service->getAll($request->all())
            ->through(fn($r) => new OrganizationAdminResource($r));
        return response()->apiResult(data: $result);

    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/admin/organization-admins/{id}",
     *     tags={"Admin/OrganizationAdmin"},
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
     *          @OA\Items(ref="#/components/schemas/AdminOrganizationAdminResource")
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        return response()->apiResult(new OrganizationAdminResource($this->service->getById($id)));
    }
    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/api/admin/organization-admins",
     *     tags={"Admin/OrganizationAdmin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(ref="#/components/schemas/AdminOrganizationAdminRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param OrganizationAdminRequest $request
     * @return JsonResponse
     */
    public function store(OrganizationAdminRequest $request)
    {
        $admin = $this->service->create($request->validated());
        return response()->apiResult(data: new OrganizationAdminResource($admin), messages: [__('messages.created')]);
    }
    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/api/admin/organization-admins/{id}",
     *     tags={"Admin/OrganizationAdmin"},
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
     *     @OA\RequestBody(ref="#/components/schemas/AdminOrganizationAdminRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param OrganizationAdminRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(OrganizationAdminRequest $request, $id)
    {
        $admin = $this->service->update($id, $request->validated());
        return response()->apiResult(messages: [__('messages.updated')]);

    }
    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/admin/organization-admins/{id}",
     *     tags={"Admin/OrganizationAdmin"},
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
        $this->service->delete($id);
        return response()->apiResult(messages: [__('messages.deleted')]);
    }
}
