<?php
namespace App\Repositories;

use App\Models\Physician;
use App\Repositories\BaseRepository;

class PhysicianRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Physician::class);
    }
    protected static function instantiate(): static
    {
        return new static();
    }
    protected function list($perPage = 15)
    {
        return $this->query()->paginate($perPage);
    }
}
