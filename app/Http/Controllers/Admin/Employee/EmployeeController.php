<?php
namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmployeeRequest;
use App\Http\Resources\Admin\EmployeeResource;
use App\Services\Admin\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ImportEmployeesRequest;
use App\Imports\AdminEmployeesImport;
use App\Models\Organization;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
class EmployeeController extends Controller
{
    protected EmployeeService $service;
    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     * path="/api/admin/employees",
     * tags={"Admin/Employees"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="limit", in="query", description="Limit for pagination", required=false, @OA\Schema(type="integer", example=10)),
     * @OA\Parameter(name="search", in="query", description="Search term", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="organization_id", in="query", description="Filter by Organization ID", required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="orderBy", in="query", description="Field to order by", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="direction", in="query", description="Order direction (asc/desc)", required=false, @OA\Schema(type="string", enum={"asc", "desc"})),
     * @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AdminEmployeeResource")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->service->getAll($request->all())->through(fn($r) => new EmployeeResource($r));
        return response()->apiResult(data: $result);
    }

    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/admin/employees/{id}",
     *     tags={"Admin/Employees"},
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
     *         @OA\JsonContent(ref="#/components/schemas/AdminEmployeeResource")
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return response()->apiResult(new EmployeeResource($this->service->getById($id)));

    }

    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/api/admin/employees",
     *     tags={"Admin/Employees"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/AdminEmployeeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/AdminEmployeeResource")
     *     )
     * )
     *
     * @param EmployeeRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        $employee = $this->service->create($request->validated() + ['photo' => $request->file('photo')]);
        return response()->apiResult(data: new EmployeeResource($employee), messages: [__('messages.created')]);

    }

    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/api/admin/employees/{id}",
     *     tags={"Admin/Employees"},
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
     *     @OA\RequestBody(ref="#/components/schemas/AdminEmployeeRequest"),
     *   @OA\Response(
     *     response=200,
     *     description="خروجی استاندارد",
     *     @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *   )
     * )
     *
     * @param EmployeeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(EmployeeRequest $request, $id): JsonResponse
    {
        $employee = $this->service->update($id, $request->validated() + ['photo' => $request->file('photo')]);
        return response()->apiResult(messages: [__('messages.updated')]);


    }

    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/admin/employees/{id}",
     *     tags={"Admin/Employees"},
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

    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/admin/employees/destroy-from-organization/{organizationId}/{employeeId}",
     *     tags={"Admin/Employees"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="organizationId",
     *         in="path",
     *         description="Organization ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *
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
     * @param $employeeId
     * @param $organizationId
     * @return JsonResponse
     */
    public function destroyFromOrganization($employeeId, $organizationId): JsonResponse
    {
        $this->service->deleteFromOrganization($employeeId, $organizationId);
        return response()->apiResult(messages: [__('messages.deleted')]);
    }
    /**
     * @OA\Post(
     * path="/api/admin/organizations/{organizationId}/employees/import",
     * tags={"Admin/Employees"},
     * summary="Import employees for a specific organization by Admin",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="organizationId",
     * in="path",
     * required=true,
     * description="The ID of the organization to import employees into",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Excel file with columns: `national_code`, `name`, `email`, `phone`, `job_position`, `departments`.",
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(@OA\Property(property="file", type="string", format="binary"))
     * )
     * ),
     * @OA\Response(response=200, description="Successful import", @OA\JsonContent(ref="#/components/schemas/ApiResponse")),
     * @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ApiResponse"))
     * )
     *
     * @param ImportEmployeesRequest $request
     * @param Organization $organization
     * @return JsonResponse
     */
    public function import(ImportEmployeesRequest $request, Organization $organization): JsonResponse
    {
        try {
            Excel::import(new AdminEmployeesImport($organization->id), $request->file('file'));

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
