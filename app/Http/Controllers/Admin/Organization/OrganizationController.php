<?php
namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrganizationRequest;
use App\Http\Resources\Admin\OrganizationResource;
use App\Services\Admin\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    protected OrganizationService $service;
    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/admin/organizations",
     *     tags={"Admin/Organizations"},
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
     *             @OA\Items(ref="#/components/schemas/AdminOrganizationResource")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {

        $result = $this->service->getAll($request->all())->through(fn($r) => new OrganizationResource($r));
        return response()->apiResult(data: $result);
    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/admin/organizations/{id}",
     *     tags={"Admin/Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organizations ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/AdminOrganizationResource")
     *
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        return response()->apiResult(new OrganizationResource($this->service->getById($id)));
    }
    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/api/admin/organizations",
     *     tags={"Admin/Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(ref="#/components/schemas/AdminOrganizationRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param OrganizationRequest $request
     * @return JsonResponse
     */
    public function store(OrganizationRequest $request)
    {
        $organization = $this->service->create($request->validated() + ['logo' => $request->file('logo')]);
        return response()->apiResult(data: new OrganizationResource($organization), messages: [__('messages.created')]);

    }
    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/api/admin/organizations/{id}",
     *     tags={"Admin/Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organizations ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/schemas/AdminOrganizationRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param OrganizationRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(OrganizationRequest $request, $id)
    {
        $organization = $this->service->update($id, $request->validated() + ['logo' => $request->file('logo')]);
        return response()->apiResult(messages: [__('messages.updated')]);


    }
    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/admin/organizations/{id}",
     *     tags={"Admin/Organizations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Organizations ID",
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
