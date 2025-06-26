<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    protected EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     *
     * @param Employee $admin
     * @return Employee
     */
    public function getProfile(Employee $admin): Employee
    {
        return $admin;
    }

    /**
     *
     * @param Employee $admin
     * @param array $data
     * @return Employee
     */
    public function updateProfile(Employee $admin, array $data): Employee
    {
        return $this->employeeRepository->update($admin->id, $data);
    }

}
