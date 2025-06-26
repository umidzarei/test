<?php

namespace App\Repositories;

use App\Models\CalculatedScores;

class CalculatedScoresRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(CalculatedScores::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    public function getByRequestEmployee(int $requestEmployeeId)
    {
        return $this->modelClass::query()
            ->where('request_employee_id', $requestEmployeeId)
            ->get();
    }
}
