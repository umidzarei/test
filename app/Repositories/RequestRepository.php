<?php

namespace App\Repositories;

use App\Models\Request as HealthRequest;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(HealthRequest::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }
    public function createWithDetails(array $requestData, array $employeeIds, array $physicianIds, array $options): HealthRequest
    {
        return DB::transaction(function () use ($requestData, $employeeIds, $physicianIds, $options) {
            $requestData['options'] = $options;
            $request = $this->create($requestData);
            if (!empty($physicianIds)) {
                $request->physicians()->attach($physicianIds);
            }
            $requestEmployeesData = collect($employeeIds)->map(function ($employeeId) {
                return [
                    'employee_id' => $employeeId,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();
            $request->employees()->createMany($requestEmployeesData);
            return $request->load(['physicians', 'employees']);
        });
    }
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()->with(['requester:id,name', 'organization:id,name', 'physicians:id,name']);

        $this->applyFilters($query, $filters);

        $orderBy = $filters['order_by'] ?? 'id';
        $direction = $filters['direction'] ?? 'desc';
        $query->orderBy($orderBy, $direction);

        return $query->paginate($perPage);
    }
    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }

    public function listForEmployee(int $employeeId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employees.id', $employeeId);
            })
            ->with(['organization:id,name,logo']);

        $this->applyFilters($query, $filters);

        $query->latest();

        return $query->paginate($perPage);
    }

    public function getDetailsForEmployee(int $requestId, int $employeeId)
    {
        return $this->query()
            ->where('id', $requestId)
            ->whereHas('requestEmployees', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->with([
                'organization:id,name,logo',
                'physicians:id,name',
                'requestEmployees' => function ($query) use ($employeeId) {
                    $query->where('employee_id', $employeeId)->with([
                        'hraQuestionnaireInstance',
                        'labData',
                        'tebKar',
                        'calculatedScore',
                        'physicianFeedback'
                    ]);
                }
            ])
            ->first();
    }

    public function listForOrganization(int $organizationId, array $params = []): LengthAwarePaginator
    {
        $perPage = $params['limit'] ?? 15;
        $orderBy = $params['orderBy'] ?? 'created_at';
        $direction = $params['direction'] ?? 'desc';
        $searchTerm = $params['search'] ?? null;

        $query = $this->query()
            ->where('organization_id', $organizationId)
            ->withCount('requestEmployees as total_employees_count')
            ->withCount([
                'requestEmployees as completed_employees_count' => function ($query) {
                    $query->where('status', 'done');
                }
            ]);

        if ($searchTerm) {
            $query->search($searchTerm);
        }

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        $allowedSorts = ['created_at', 'status'];
        if (in_array($orderBy, $allowedSorts)) {
            $query->orderBy($orderBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }
    public function getDetailsForOrganization(int $organizationId, int $requestId): ?\App\Models\Request
    {
        return $this->query()
            ->where('organization_id', $organizationId)
            ->where('id', $requestId)
            ->with([
                'requestEmployees.employee' => function ($query) use ($organizationId) {
                    $query->with([
                        'organizationEmployee' => function ($q) use ($organizationId) {
                            $q->where('organization_id', $organizationId)->with('departments:id,name');
                        }
                    ]);
                },
                'requestEmployees.hraQuestionnaireInstance:id,request_employee_id,status',
                'requestEmployees.labData',
                'requestEmployees.tebKar',
                'requestEmployees.calculatedScore',
            ])
            ->withCount('requestEmployees as total_employees_count')
            ->withCount([
                'requestEmployees as completed_employees_count' => function ($query) {
                    $query->where('status', 'done');
                }
            ])
            ->first();
    }
}
