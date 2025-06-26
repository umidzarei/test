<?php
namespace App\Services\Admin;

use App\Repositories\PhysicianRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PhysicianService
{
    protected $repo;
    public function __construct(PhysicianRepository $repo)
    {
        $this->repo = $repo;
    }
    public function getAll($limit)
    {
        return $this->repo->list($limit);
    }
    public function getById($id)
    {
        return $this->repo->find($id);
    }

    public function create($data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['photo'] = Storage::disk('s3')->put('physicians/photos', $data['photo']);
        }
        $data['password'] = Hash::make($data['password']);
        return $this->repo->create($data);
    }
    public function update($id, $data)
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['photo'] = Storage::disk('s3')->put('physicians/photos', $data['photo']);
        } else {
            unset($data['photo']);
        }
        return $this->repo->update($id, $data);
    }
    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
