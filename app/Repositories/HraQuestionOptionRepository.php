<?php

namespace App\Repositories;

use App\Models\HraQuestionOption;

class HraQuestionOptionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(HraQuestionOption::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    public function getByQuestion(int $questionId)
    {
        return $this->modelClass::query()
            ->where('question_id', $questionId)
            ->orderBy('order')
            ->get();
    }
}
