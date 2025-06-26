<?php
namespace App\Http\Controllers\Admin\OccupationalMedicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OccupationalMedicineRequest;
use App\Http\Resources\Admin\OccupationalMedicineResource;
use App\Services\Admin\OccupationalMedicineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OccupationalMedicineController extends Controller
{
    protected OccupationalMedicineService $service;
    public function __construct(OccupationalMedicineService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     * path="/api/admin/occupational_medicine",
     * tags={"Admin/OccupationalMedicine"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="limit", in="query", description="Limit for pagination", required=false, @OA\Schema(type="integer", example=10)),
     * @OA\Parameter(name="search", in="query", description="Search term", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="orderBy", in="query", description="Field to order by", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="direction", in="query", description="Order direction (asc/desc)", required=false, @OA\Schema(type="string", enum={"asc", "desc"})),
     * @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AdminOccupationalMedicineResource")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->service->getAll($request->all())->through(fn($r) => new OccupationalMedicineResource($r));
        return response()->apiResult(data: $result);
    }

    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/admin/occupational_medicine/{id}",
     *     tags={"Admin/OccupationalMedicine"},
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
     *         @OA\JsonContent(ref="#/components/schemas/AdminOccupationalMedicineResource")
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return response()->apiResult(new OccupationalMedicineResource($this->service->getById($id)));

    }

    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/api/admin/occupational_medicine",
     *     tags={"Admin/OccupationalMedicine"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/OccupationalMedicineRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/AdminOccupationalMedicineResource")
     *     )
     * )
     *
     * @param OccupationalMedicineRequest $request
     * @return JsonResponse
     */
    public function store(OccupationalMedicineRequest $request): JsonResponse
    {
        $employee = $this->service->create($request->validated());
        return response()->apiResult(data: new OccupationalMedicineResource($employee), messages: [__('messages.created')]);

    }

    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/api/admin/occupational_medicines/{id}",
     *     tags={"Admin/OccupationalMedicine"},
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
     *     @OA\RequestBody(ref="#/components/schemas/OccupationalMedicineRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param OccupationalMedicineRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(OccupationalMedicineRequest $request, $id): JsonResponse
    {
        $employee = $this->service->update($id, $request->validated());
        return response()->apiResult(messages: [__('messages.updated')]);


    }

    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/admin/occupational_medicine/{id}",
     *     tags={"Admin/OccupationalMedicine"},
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
    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);
        return response()->apiResult(messages: [__('messages.deleted')]);
    }

}
