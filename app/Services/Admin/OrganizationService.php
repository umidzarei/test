<?php
namespace App\Services\Admin;

use App\Repositories\OrganizationRepository;
use Illuminate\Support\Facades\Storage;

class OrganizationService
{
    protected $repo;
    public function __construct(OrganizationRepository $repo)
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
        if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['logo'] = Storage::disk('s3')->put('organizations/logos', $data['logo']);
        }

        return $this->repo->create($data);
    }
    public function update($id, $data)
    {
        if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['logo'] = Storage::disk('s3')->put('organizations/logos', $data['logo']);
        } else {
            unset($data['logo']);
        }
        return $this->repo->update($id, $data);
    }
    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
