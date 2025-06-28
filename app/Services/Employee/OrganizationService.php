<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Support\Collection;

class OrganizationService
{

    protected EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }
    public function getOrganizationsForEmployee(Employee $employee): Collection
    {
        return $this->employeeRepository->getAssociatedOrganizations($employee);
    }
}
