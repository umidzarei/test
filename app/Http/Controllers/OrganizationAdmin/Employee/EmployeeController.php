<?php
namespace App\Http\Controllers\OrganizationAdmin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationAdmin\EmployeeRequest;
use App\Http\Requests\OrganizationAdmin\ImportEmployeeRequest;
use App\Http\Resources\OrganizationAdmin\EmployeeResource;
use App\Imports\OrganizationEmployeesImport;
use App\Services\OrganizationAdmin\EmployeeService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
class EmployeeController extends Controller
{
    protected EmployeeService $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Get(
     * path="/api/hr/employees",
     * summary="Get a paginated list of employees for the organization",
     * tags={"OrganizationAdmin/Employees"},
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="limit", in="query", description="Number of items per page", required=false, @OA\Schema(type="integer", example=15)),
     * @OA\Parameter(name="search", in="query", description="Search term for employee name, national code, or email", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="department_id", in="query", description="Filter employees by a specific department ID", required=false, @OA\Schema(type="integer")),
     * @OA\Parameter(name="orderBy", in="query", description="Field to sort by", required=false, @OA\Schema(type="string", enum={"id", "first_name", "last_name", "national_code", "created_at"})),
     * @OA\Parameter(name="direction", in="query", description="Sort direction", required=false, @OA\Schema(type="string", enum={"asc", "desc"})),
     * @OA\Response(
     * response=200,
     * description="OK",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AdminEmployeeResource")),
     * @OA\Property(property="links", type="object"),
     * @OA\Property(property="meta", type="object")
     * )
     * )
     * )
     */
    public function index(Request $request)
    {
        $organizationId = Auth::user()->organization_id;
        $result = $this->service->getEmployeeList($organizationId, $request->validated())
            ->through(fn($r) => new EmployeeResource($r));
        return response()->apiResult(data: $result);

    }
    /**
     * OA docs for this route
     * @OA\Post(
     *     path="/api/hr/employees",
     *     tags={"OrganizationAdmin/Employees"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/OrganizationAdminEmployeeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/OrganizationAdminEmployeeResource")
     *     )
     * )
     *
     * @param EmployeeRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo');
        }

        $employee = $this->service->createOrAttachForAuthenticatedOrgAdmin($validatedData);

        return response()->apiResult(
            data: new EmployeeResource($employee->load('organizationEmployee.departments')),
            messages: [__('messages.created')],
            statusCode: 201
        );
    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/hr/employees/{id}",
     *     tags={"OrganizationAdmin/Employees"},
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
     *         @OA\JsonContent(ref="#/components/schemas/OrganizationAdminEmployeeResource")
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $employee = $this->service->getByIdForAuthenticatedOrgAdmin($id);
        return response()->apiResult(data: new EmployeeResource($employee->load('organizationEmployee.departments')));
    }
    /**
     * OA docs for this route
     * @OA\Put(
     *     path="/api/hr/employees/{id}",
     *     tags={"OrganizationAdmin/Employees"},
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
     *     @OA\RequestBody(ref="#/components/schemas/OrganizationAdminEmployeeRequest"),
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
    public function update(EmployeeRequest $request, $id)
    {
        $validatedData = $request->validated();
        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo');
        } elseif ($request->exists('photo') && is_null($request->photo)) {
            $validatedData['photo'] = null;
        }

        $employee = $this->service->updateForAuthenticatedOrgAdmin($id, $validatedData);
        return response()->apiResult(messages: [__('messages.updated')]);
    }
    /**
     * OA docs for this route
     * @OA\Delete(
     *     path="/api/hr/employees/{id}",
     *     tags={"OrganizationAdmin/Employees"},
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
        $this->service->dissociateFromOrganizationForAuthenticatedOrgAdmin($id);
        return response()->apiResult(messages: [__('messages.dissociated_from_organization')]);
    }

    /**
     * @OA\Post(
     * path="/api/hr/employees/import",
     * tags={"OrganizationAdmin/Employees"},
     * summary="Import employees from an Excel file for the authenticated admin's organization",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * description="Excel file containing employee data. The file must have a header row with columns: `national_code`, `name`, `email`, `phone`, `job_position`, `departments`. The `departments` column should contain comma-separated department names.",
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(
     * property="file",
     * description="The .xlsx or .csv file to import.",
     * type="string",
     * format="binary"
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Import was successful.",
     * @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error, for example, if the file is missing or has validation errors in rows.",
     * @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     * )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(ImportEmployeeRequest $request): JsonResponse
    {
        try {
            Excel::import(new OrganizationEmployeesImport(), $request->file('file'));

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
