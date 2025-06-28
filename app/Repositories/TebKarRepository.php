<?php

namespace App\Repositories;

use App\Models\TebKar;
use Carbon\Carbon;
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
    public function findLatestValidForEmployee(int $employeeId, int $validityInMonths = 6)
    {
        $validFrom = Carbon::now()->subMonths($validityInMonths);

        return $this->query()
            ->whereHas('requestEmployee', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->where('created_at', '>=', $validFrom)
            ->latest()
            ->first();
    }
}
