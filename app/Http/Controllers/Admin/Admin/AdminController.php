<?php
namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Services\Admin\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected AdminService $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     * path="/admin/admins",
     * tags={"Admin/Admin"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="limit",
     * in="query",
     * description="Limit",
     * required=false,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="search",
     * in="query",
     * required=false,
     * @OA\Schema(type="string"),
     * description="Search term for admin fields"
     * ),
     * @OA\Parameter(
     * name="orderBy",
     * in="query",
     * required=false,
     * @OA\Schema(type="string"),
     * description="Field to order by"
     * ),
     * @OA\Parameter(
     * name="direction",
     * in="query",
     * required=false,
     * @OA\Schema(type="string", enum={"asc", "desc"}),
     * description="Order direction"
     * ),
     * @OA\Response(
     * response="200",
     * description="OK",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/AdminResource")
     * )
     * )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->service->getAll($request->all())->through(fn($r) => new AdminResource($r));
        return response()->apiResult(data: $result);
    }


    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/admin/admins/{id}",
     *     tags={"Admin/Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AdminResource")
     *         )
     *     )
     * )
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return response()->apiResult(new AdminResource($this->service->getById($id)));
    }

    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/admin/admins",
     *     tags={"Admin/Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AdminRequest")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AdminResource")
     *         )
     *     )
     * )
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function store(AdminRequest $request): JsonResponse
    {
        $admin = $this->service->create($request->validated());

        return response()->apiResult(data: new AdminResource($admin), messages: [__('messages.created')]);
    }

    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/admin/admins/{id}",
     *     tags={"Admin/Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AdminRequest")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AdminResource")
     *         )
     *     )
     * )
     * @param AdminRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(AdminRequest $request, $id): JsonResponse
    {
        $admin = $this->service->update($id, $request->validated());

        return response()->apiResult(messages: [__('messages.updated')]);
    }

    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/admin/admins/{id}",
     *     tags={"Admin/Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AdminResource")
     *         )
     *     )
     * )
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);

        return response()->apiResult(messages: [__('messages.deleted')]);
    }
}
