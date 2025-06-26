<?php
namespace App\Services\Admin;

use App\Repositories\OrganizationAdminRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class OrganizationAdminService
{
    protected $repo;

    public function __construct(OrganizationAdminRepository $repo)
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


        return $this->repo->list($searchTerm, $organizationId, $perPage, $orderBy, $direction, []);
    }

    public function getById($id)
    {
        return $this->repo->find($id);
    }

    public function create($data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->repo->create($data);
    }

    public function update($id, $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
