<?php

namespace App\Repositories;

use App\Models\HraQuestionnaireInstance;

class HraQuestionnaireInstanceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(HraQuestionnaireInstance::class);
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

    public function getWithAnswers(int $id)
    {
        return $this->modelClass::query()
            ->with('answers')
            ->find($id);
    }
}
