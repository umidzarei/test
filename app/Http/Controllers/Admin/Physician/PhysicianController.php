<?php
namespace App\Http\Controllers\Admin\Physician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PhysicianRequest;
use App\Http\Resources\Admin\PhysicianResource;
use App\Services\Admin\PhysicianService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ImportPhysiciansRequest;
use App\Imports\AdminPhysiciansImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
class PhysicianController extends Controller
{
    protected PhysicianService $service;
    public function __construct(PhysicianService $service)
    {
        $this->service = $service;
    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/admin/physicians",
     *     tags={"Admin/Physicians"},
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
     *             @OA\Items(ref="#/components/schemas/AdminPhysicianResource")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $result = $this->service->getAll($request->limit)
            ->through(fn($r) => new PhysicianResource($r));
        return response()->apiResult(data: $result);
    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/admin/physicians/{id}",
     *     tags={"Admin/Physicians"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Physicians ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\Items(ref="#/components/schemas/AdminPhysicianResource")
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        return response()->apiResult(new PhysicianResource($this->service->getById($id)));

    }
    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/api/admin/physicians",
     *     tags={"Admin/Physicians"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(ref="#/components/schemas/AdminPhysicianRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param PhysicianRequest $request
     * @return JsonResponse
     */
    public function store(PhysicianRequest $request)
    {
        $physician = $this->service->create($request->validated() + ['photo' => $request->file('photo')]);
        return response()->apiResult(data: new PhysicianResource($physician), messages: [__('messages.created')]);

    }
    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/api/admin/physicians/{id}",
     *     tags={"Admin/Physicians"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Physicians ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(ref="#/components/schemas/AdminPhysicianRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param PhysicianRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PhysicianRequest $request, $id)
    {
        $physician = $this->service->update($id, $request->validated() + ['photo' => $request->file('photo')]);
        return response()->apiResult(messages: [__('messages.updated')]);
    }
    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/admin/physicians/{id}",
     *     tags={"Admin/Physicians"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Physicians ID",
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
    /**
     * @OA\Post(
     * path="/api/admin/physicians/import",
     * tags={"Admin/Physicians"},
     * summary="Import physicians from an Excel file",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(@OA\Property(property="file", type="string", format="binary", description="Excel file with columns: name, email, phone, password"))
     * )
     * ),
     * @OA\Response(response=200, description="Successful import", @OA\JsonContent(ref="#/components/schemas/ApiResponse")),
     * @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ApiResponse"))
     * )
     *
     * @param ImportPhysiciansRequest $request
     * @return JsonResponse
     */
    public function import(ImportPhysiciansRequest $request): JsonResponse
    {
        try {
            Excel::import(new AdminPhysiciansImport(), $request->file('file'));
            return response()->apiResult(messages: [__('messages.import_success')]);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $key => $messages) {
                $rowIndex = (int) explode('.', $key)[0] + 2;
                $errors[] = __('messages.import_validation_error', [
                    'row' => $rowIndex,
                    'errors' => implode(', ', $messages)
                ]);
            }
            return response()->apiResult(messages: array_unique($errors), statusCode: 422);
        }
    }
}
