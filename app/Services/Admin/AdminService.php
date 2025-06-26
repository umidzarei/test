<?php
namespace App\Services\Admin;

use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    protected $repo;

    public function __construct(AdminRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll(array $requestParams = [])
    {
        $searchTerm = $requestParams['search'] ?? null;
        $perPage = isset($requestParams['limit']) ? (int) $requestParams['limit'] : 15;
        $orderBy = $requestParams['orderBy'] ?? 'id';
        $direction = $requestParams['direction'] ?? 'DESC';

        return $this->repo->list($searchTerm, $perPage, $orderBy, $direction);
    }

    public function getById($id)
    {
        return $this->repo->find($id);
    }

    public function create($data)
    {
        $data['password'] = Hash::make($data['password']);
        $admin = $this->repo->create($data);

        $admin->assignRole('super-admin');

        return $admin;
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
