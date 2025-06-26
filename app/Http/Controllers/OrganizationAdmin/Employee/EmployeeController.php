<?php
namespace App\Http\Controllers\OrganizationAdmin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationAdmin\EmployeeRequest;
use App\Http\Resources\OrganizationAdmin\EmployeeResource;
use App\Services\OrganizationAdmin\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected EmployeeService $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }
    /**
     * OA docs for this route
     * @OA\Get(
     *     path="/api/hr/employees",
     *     tags={"OrganizationAdmin/Employees"},
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
     *             @OA\Items(ref="#/components/schemas/OrganizationAdminEmployeeResource")
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
}
