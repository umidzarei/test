<?php
namespace App\Services\Admin;

use App\Repositories\EmployeeRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeService
{
    protected $repo;
    public function __construct(EmployeeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll(array $requestParams = []): LengthAwarePaginator
    {
        $searchTerm = $requestParams['search'] ?? null;
        $organizationId = isset($requestParams['organization_id']) ? (int) $requestParams['organization_id'] : null;
        $perPage = isset($requestParams['limit']) ? (int) $requestParams['limit'] : 15;
        $orderBy = $requestParams['orderBy'] ?? 'id';
        $direction = $requestParams['direction'] ?? 'DESC';


        return $this->repo->list($searchTerm, $organizationId, $perPage, $orderBy, $direction, [
            'organizationEmployee.organization',
            'organizationEmployee.departments'
        ]);
    }

    public function getById($id)
    {
        return $this->repo->find($id);
    }

    public function create($data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['photo'] = Storage::disk('s3')->put('employees/photos', $data['photo']);
        }

        $employee = $this->repo->findByNationalCodeOrEmail($data['national_code'], $data['email']);

        if (!$employee) {
            $employee = $this->repo->create($data);
        }

        $orgEmployee = $this->repo->findOrganizationEmployee($employee->id, $data['organization_id']);

        if (!$orgEmployee) {
            $orgEmployee = $this->repo->createOrganizationEmployee($employee, $data);
        } else {
            $this->repo->updateOrganizationEmployee($orgEmployee, $data);
        }

        $this->repo->syncDepartments($orgEmployee, $data['department_ids']);

        return $employee;
    }

    public function update($id, $data)
    {

        $employee = $this->repo->find($id);
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['photo'] = Storage::disk('s3')->put('employees/photos', $data['photo']);
        } else {
            unset($data['photo']);
        }
        $this->repo->update($id, $data);

        if (isset($data['organization_id'])) {
            $orgEmployee = $this->repo->findOrganizationEmployee($employee->id, $data['organization_id']);
            if ($orgEmployee) {
                $this->repo->updateOrganizationEmployee($orgEmployee, $data);
                if (isset($data['department_ids'])) {
                    $this->repo->syncDepartments($orgEmployee, $data['department_ids']);
                }
            }
        }

        return $employee;
    }

    public function delete($id)
    {
        $employee = $this->repo->find($id);
        $this->repo->deleteAllOrganizationEmployees($employee);
        return $this->repo->delete($id);
    }

    public function deleteFromOrganization($employeeId, $organizationId)
    {
        $orgEmployee = $this->repo->findOrganizationEmployee($employeeId, $organizationId);
        if ($orgEmployee) {
            $this->repo->deleteOrganizationEmployee($orgEmployee);
        }
    }
}
