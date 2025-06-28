<?php

namespace App\Repositories;

use App\Models\LabData;

class LabDataRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(LabData::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    public function findByRequestEmployee(int $requestEmployeeId)
    {
        return $this->modelClass::query()
            ->where('request_employee_id', $requestEmployeeId)
            ->first();
    }
    public function createForRequestEmployee(int $requestEmployeeId, array $data): LabData
    {
        $data['request_employee_id'] = $requestEmployeeId;
        return $this->create($data);
    }
}
