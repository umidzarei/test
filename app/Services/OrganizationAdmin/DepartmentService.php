<?php
namespace App\Services\OrganizationAdmin;

use App\Repositories\DepartmentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartmentService
{
    protected $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    protected function getAuthenticatedOrgAdminOrganizationId()
    {
        return auth()->user()->organization_id;
    }

    public function getAllForAuthenticatedOrgAdmin($limit = 15)
    {
        $organizationId = $this->getAuthenticatedOrgAdminOrganizationId();

        return $this->departmentRepository->getByOrganizationId($organizationId, $limit);
    }

    public function createForAuthenticatedOrgAdmin(array $data)
    {
        $organizationId = $this->getAuthenticatedOrgAdminOrganizationId();
        $data['organization_id'] = $organizationId;

        return $this->departmentRepository->create($data);
    }

    public function getByIdForAuthenticatedOrgAdmin($departmentId)
    {
        $organizationId = $this->getAuthenticatedOrgAdminOrganizationId();
        $department = $this->departmentRepository->find($departmentId);

        if (!$department || $department->organization_id !== $organizationId) {
            throw new ModelNotFoundException(__('messages.not_found_or_not_accessible'));
        }
        return $department;
    }

    public function updateForAuthenticatedOrgAdmin($departmentId, array $data)
    {
        $organizationId = $this->getAuthenticatedOrgAdminOrganizationId();
        $department = $this->departmentRepository->find($departmentId);

        if (!$department || $department->organization_id !== $organizationId) {
            throw new ModelNotFoundException(__('messages.not_found_or_not_accessible'));
        }

        unset($data['organization_id']);

        $this->departmentRepository->update($departmentId, $data);
        return $this->departmentRepository->find($departmentId);
    }

    public function deleteForAuthenticatedOrgAdmin($departmentId)
    {
        $organizationId = $this->getAuthenticatedOrgAdminOrganizationId();
        $department = $this->departmentRepository->find($departmentId);

        if (!$department || $department->organization_id !== $organizationId) {
            throw new ModelNotFoundException(__('messages.not_found_or_not_accessible'));
        }

        return $this->departmentRepository->delete($departmentId);
    }
    public function getListForSelect(int $organizationId)
    {
        return $this->departmentRepository->getForOrganization($organizationId);
    }
}
