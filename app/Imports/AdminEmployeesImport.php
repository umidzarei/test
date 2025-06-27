<?php

namespace App\Imports;

use App\Http\Requests\Admin\EmployeeRequest;
use App\Models\Department;
use App\Services\Admin\EmployeeService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class AdminEmployeesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use SkipsFailures;

    private EmployeeService $employeeService;
    protected int $organizationId;

    public function __construct(int $organizationId)
    {
        $this->employeeService = app(EmployeeService::class);
        $this->organizationId = $organizationId;
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
            'organization_id' => $this->organizationId,
            'national_code' => $row['national_code'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'job_position' => $row['job_position'],
            'department_ids' => $departmentIds,
        ];

        return $this->employeeService->create($employeeData);
    }

    public function rules(): array
    {
        $rules = (new EmployeeRequest())->rules();
        $rules['national_code'] = 'required|digits_between:8,10|unique:employees,national_code';
        $rules['email'] = 'required|email|unique:employees,email';
        $rules['phone'] = 'required|unique:employees,phone';
        $rules['departments'] = 'required|string';
        unset($rules['organization_id']);

        return $rules;
    }
}
