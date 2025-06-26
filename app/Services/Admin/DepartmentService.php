<?php
namespace App\Services\Admin;

use App\Repositories\DepartmentRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentService
{
    protected $repo;
    public function __construct(DepartmentRepository $repo)
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
        return $this->repo->create($data);
    }
    public function update($id, $data)
    {
        return $this->repo->update($id, $data);
    }
    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
