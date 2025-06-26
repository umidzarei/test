<?php
namespace App\Services\Admin;

use App\Repositories\OccupationalMedicineRepository;
use Illuminate\Support\Facades\Storage;

class OccupationalMedicineService
{
    protected $repo;
    public function __construct(OccupationalMedicineRepository $repo)
    {
        $this->repo = $repo;
    }
    public function getAll(array $requestParams = [])
    {
        $searchTerm = $requestParams['search'] ?? null;
        $perPage = isset($requestParams['limit']) ? (int) $requestParams['limit'] : 15;
        $orderBy = $requestParams['orderBy'] ?? 'id';
        $direction = $requestParams['direction'] ?? 'DESC';
        return $this->repo->list($searchTerm, $perPage, $orderBy, $direction, []);
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
