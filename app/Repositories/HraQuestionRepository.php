<?php

namespace App\Repositories;

use App\Models\HraQuestion;

class HraQuestionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(HraQuestion::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    public function allWithOptions()
    {
        return $this->modelClass::query()
            ->with('options')
            ->orderBy('order')
            ->get();
    }
}
