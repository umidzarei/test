<?php

namespace App\Repositories;

use App\Models\TebKar;

class TebKarRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(TebKar::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    public function getByRequestEmployee(int $requestEmployeeId)
    {
        return $this->modelClass::query()
            ->where('request_employee_id', $requestEmployeeId)
            ->first();
    }
}
