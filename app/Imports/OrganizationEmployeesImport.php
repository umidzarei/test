<?php

namespace App\Imports;

use App\Http\Requests\OrganizationAdmin\EmployeeRequest;
use App\Models\Department;
use App\Services\OrganizationAdmin\EmployeeService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class OrganizationEmployeesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use SkipsFailures;

    private EmployeeService $employeeService;
    private int $organizationId;

    public function __construct()
    {
        $this->employeeService = app(EmployeeService::class);
        $this->organizationId = auth()->user()->organization_id;
    }

    public function model(array $row)
    {
        $departmentNames = array_map('trim', explode(',', $row['departments']));
        $departmentIds = Department::where('organization_id', $this->organizationId)
            ->whereIn('name', $departmentNames)
            ->pluck('id')
            ->toArray();

        if (empty($departmentIds)) {
            $failure = new Failure(0, 'departments', ['دپارتمان مشخص شده یافت نشد.'], $row);
            $this->onFailure($failure);
            return null;
        }

        $employeeData = [
            'national_code' => $row['national_code'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'job_position' => $row['job_position'],
            'department_ids' => $departmentIds,
        ];

        return $this->employeeService->createOrAttachForAuthenticatedOrgAdmin($employeeData);
    }

    public function rules(): array
    {
        $rules = (new EmployeeRequest())->rules();
        $rules['national_code'] = 'required|digits_between:8,10|unique:employees,national_code';
        $rules['email'] = 'required|email|unique:employees,email';
        $rules['phone'] = 'required|unique:employees,phone';
        $rules['departments'] = 'required|string';
        return $rules;
    }
}
