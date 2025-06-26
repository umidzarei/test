<?php

namespace App\Repositories;

use App\Models\PhysicianFeedbacks;

class PhysicianFeedbacksRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(PhysicianFeedbacks::class);
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

    public function getByPhysician(int $physicianId)
    {
        return $this->modelClass::query()
            ->where('physician_id', $physicianId)
            ->get();
    }
}
