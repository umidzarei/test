<?php
namespace App\Services\OrganizationAdmin;

use App\Repositories\EmployeeRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeService
{
    protected $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    protected function getAuthenticatedOrgId()
    {
        return auth()->user()->organization_id;
    }

    public function getAllForAuthenticatedOrgAdmin($limit = 15)
    {
        $organizationId = $this->getAuthenticatedOrgId();
        return $this->employeeRepository->getEmployeesByOrganizationId($organizationId, $limit);
    }

    public function createOrAttachForAuthenticatedOrgAdmin(array $data)
    {
        $organizationId = $this->getAuthenticatedOrgId();
        $employeeData = Arr::only($data, ['national_code', 'name', 'email', 'phone']);
        $orgEmployeeData = Arr::only($data, ['job_position']);
        $departmentIds = $data['department_ids'];
        $employee = $this->employeeRepository->findByNationalCodeOrEmail($employeeData['national_code'] ?? null, $employeeData['email'] ?? null);

        if (!$employee) {
            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
                $data['photo'] = Storage::disk('s3')->put('employees/photos', $data['photo']);
            } elseif (isset($data['photo']) && is_null($data['photo']) && $employee && $employee->photo) {
                Storage::delete($employee->photo);
                $employeeData['photo'] = null;
            }
            $employee = $this->employeeRepository->create($employeeData);
        } else {
            $updateableEmployeeData = Arr::only($data, ['name', 'phone']);
            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
                if ($employee->photo) {
                    Storage::disk(config('filesystems.default_public_disk_name', 's3_public'))->delete($employee->photo);
                }
                $updateableEmployeeData['photo'] = Storage::disk(config('filesystems.default_public_disk_name', 's3_public'))->put('employees/photos', $data['photo']);
            } elseif (isset($data['photo']) && is_null($data['photo']) && $employee->photo) {
                Storage::disk(config('filesystems.default_public_disk_name', 's3_public'))->delete($employee->photo);
                $updateableEmployeeData['photo'] = null;
            }

            if (!empty($updateableEmployeeData)) {
                $this->employeeRepository->update($employee->id, $updateableEmployeeData);
                $employee->refresh();
            }
        }

        $orgEmployee = $this->employeeRepository->findOrganizationEmployee($employee->id, $organizationId);

        $orgEmployeeInput = $orgEmployeeData + ['organization_id' => $organizationId];

        if (!$orgEmployee) {
            $orgEmployee = $this->employeeRepository->createOrganizationEmployee($employee, $orgEmployeeInput);
        } else {
            $this->employeeRepository->updateOrganizationEmployee($orgEmployee, $orgEmployeeInput);
        }

        $this->employeeRepository->syncDepartments($orgEmployee, $departmentIds);

        return $employee;
    }

    public function getByIdForAuthenticatedOrgAdmin($employeeId)
    {
        $organizationId = $this->getAuthenticatedOrgId();
        $employee = $this->employeeRepository->find($employeeId);

        if (!$employee) {
            throw new ModelNotFoundException(__('messages.employee_not_found'));
        }

        $isAssociated = $this->employeeRepository->isEmployeeAssociatedWithOrganization($employee->id, $organizationId);
        if (!$isAssociated) {
            throw new ModelNotFoundException(__('messages.employee_not_in_your_organization'));
        }
        $employee->load([
            'organizationEmployee' => function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId)->with('departments');
            }
        ]);

        return $employee;
    }

    public function updateForAuthenticatedOrgAdmin($employeeId, array $data)
    {
        $organizationId = $this->getAuthenticatedOrgId();
        $employee = $this->getByIdForAuthenticatedOrgAdmin($employeeId);

        $employeeDataToUpdate = Arr::only($data, ['national_code', 'name', 'email', 'phone']);


        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if ($employee->photo) {
                Storage::delete($employee->photo);
            }
            $employeeDataToUpdate['photo'] = Storage::disk(config('filesystems.default_public_disk_name', 's3_public'))->put('employees/photos', $data['photo']);
        } elseif (array_key_exists('photo', $data) && is_null($data['photo']) && $employee->photo) {
            Storage::delete($employee->photo);
            $employeeDataToUpdate['photo'] = null;
        }

        if (!empty($employeeDataToUpdate)) {
            $this->employeeRepository->update($employee->id, $employeeDataToUpdate);
            $employee->refresh();
        }

        $orgEmployee = $this->employeeRepository->findOrganizationEmployee($employee->id, $organizationId);
        if (!$orgEmployee) {
            throw new ModelNotFoundException('Organization employee link not found.');
        }
        $orgEmployeeDataToUpdate = Arr::only($data, ['job_position']);
        if (!empty($orgEmployeeDataToUpdate)) {
            $this->employeeRepository->updateOrganizationEmployee($orgEmployee, $orgEmployeeDataToUpdate);
        }

        // 3. Sync Departments
        if (isset($data['department_ids'])) {
            $this->employeeRepository->syncDepartments($orgEmployee, $data['department_ids']);
        }

        return $employee;
    }

    public function dissociateFromOrganizationForAuthenticatedOrgAdmin($employeeId)
    {
        $organizationId = $this->getAuthenticatedOrgId();
        $employee = $this->employeeRepository->find($employeeId);

        if (!$employee) {
            throw new ModelNotFoundException(__('messages.employee_not_found'));
        }

        $orgEmployee = $this->employeeRepository->findOrganizationEmployee($employee->id, $organizationId);

        if (!$orgEmployee) {
            throw new ModelNotFoundException(__('messages.employee_not_associated_with_your_organization_to_dissociate'));
        }

        $this->employeeRepository->deleteOrganizationEmployee($orgEmployee);
    }
}
