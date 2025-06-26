<?php

namespace App\Repositories;

use App\Models\HraAnswer;

class HraAnswerRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(HraAnswer::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    public function getByInstance(int $instanceId)
    {
        return $this->modelClass::query()
            ->where('hra_questionnaire_instance_id', $instanceId)
            ->get();
    }
}
