<?php

namespace App\Repositories;

use App\Models\RequestEmployee;

class RequestEmployeeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(RequestEmployee::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    public function getByRequest(int $requestId)
    {
        return $this->modelClass::query()
            ->where('request_id', $requestId)
            ->get();
    }

    public function fullDetail(int $id)
    {
        return $this->modelClass::query()
            ->with([
                'employee',
                'request',
                'hraInstance.answers',
                'labData',
                'tebKar',
                'scores',
                'feedbacks',
            ])
            ->find($id);
    }
}
