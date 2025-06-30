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
    public function listForRequest(int $requestId, array $params = [])
    {
        $perPage = $params['limit'] ?? 15;

        $query = $this->query()
            ->where('request_id', $requestId)
            ->with([
                'employee:id,first_name,last_name,national_code',
                'hraQuestionnaireInstance:id,request_employee_id,status',
                'labData:id,request_employee_id',
                'tebKar:id,request_employee_id'
            ]);


        return $query->paginate($perPage);
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
