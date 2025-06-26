<?php

namespace App\Repositories;

use App\Models\Request;

class RequestRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Request::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    public function getByOrganization(int $organizationId)
    {
        return $this->modelClass::query()
            ->where('organization_id', $organizationId)
            ->get();
    }

    public function withEmployees(int $id)
    {
        return $this->modelClass::query()
            ->with('employees')
            ->find($id);
    }
}
