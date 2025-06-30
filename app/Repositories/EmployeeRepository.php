<?php
namespace App\Repositories;

use App\Models\Employee;
use App\Models\OrganizationEmployee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
class EmployeeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Employee::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    protected function list(
        ?string $searchTerm = null,
        ?int $organizationId = null,
        int $perPage = 15,
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $relations = []
    ): LengthAwarePaginator {
        $query = $this->query();

        if ($searchTerm && trim($searchTerm) !== '') {
            $query->search(trim($searchTerm));
        }

        if ($organizationId !== null) {
            $query->whereHas('organizationEmployee', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }

        $allowedOrderByFields = ['id', 'name', 'email', 'national_code', 'phone', 'created_at'];
        if (property_exists($this->modelClass::make(), 'searchable') && is_array($this->modelClass::make()->searchable)) {
            $allowedOrderByFields = array_merge($allowedOrderByFields, $this->modelClass::make()->searchable);
        }
        $allowedOrderByFields = array_unique($allowedOrderByFields);

        if (in_array($orderBy, $allowedOrderByFields)) {
            $query->orderBy($orderBy, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->paginate($perPage);
    }

    public function findByNationalCodeOrEmail($nationalCode, $email)
    {
        return $this->modelClass::where('national_code', $nationalCode)
            ->orWhere('email', $email)
            ->first();
    }

    public function createOrganizationEmployee($employee, $data)
    {
        return $employee->organizationEmployee()->create([
            'organization_id' => $data['organization_id'],
            'job_position' => $data['job_position'],
        ]);
    }

    public function findOrganizationEmployee($employeeId, $organizationId)
    {
        return OrganizationEmployee::where('employee_id', $employeeId)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function updateOrganizationEmployee($orgEmployee, $data)
    {
        return $orgEmployee->update([
            'job_position' => $data['job_position'],
        ]);
    }

    public function syncDepartments($orgEmployee, $departmentIds)
    {
        return $orgEmployee->departments()->sync($departmentIds);
    }

    public function deleteOrganizationEmployee($orgEmployee)
    {
        $orgEmployee->departments()->detach();
        return $orgEmployee->delete();
    }

    public function deleteAllOrganizationEmployees($employee)
    {
        foreach ($employee->organizationEmployee as $orgEmployee) {
            $this->deleteOrganizationEmployee($orgEmployee);
        }
    }
    public function getEmployeesByOrganizationId(int $organizationId, $perPage = 15)
    {
        return Employee::whereHas('organizationEmployee', function ($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })
            ->with([
                'organizationEmployee' => function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId)->with('departments');
                }
            ])
            ->paginate($perPage);
    }
    public function isEmployeeAssociatedWithOrganization(int $employeeId, int $organizationId): bool
    {
        return OrganizationEmployee::where('employee_id', $employeeId)
            ->where('organization_id', $organizationId)
            ->exists();
    }

    public function getAssociatedOrganizations(Employee $employee): Collection
    {
        return $employee->organizationEmployee()
            ->with('organization')
            ->get()
            ->pluck('organization')
            ->filter(function ($organization) {
                return $organization !== null;
            });
    }
    public function getByDepartmentIds(array $departmentIds, int $organizationId): Collection
    {
        return $this->query()
            ->whereHas('organizationEmployee.departments', function ($query) use ($departmentIds) {
                $query->whereIn('departments.id', $departmentIds);
            })
            ->whereHas('organizationEmployee', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->get();
    }

    public function getAllIdsByOrganization(int $organizationId): array
    {
        return $this->query()
            ->whereHas('organizationEmployee', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->pluck('id')
            ->all();
    }
    public function listForOrganization(int $organizationId, array $params): LengthAwarePaginator
    {
        $query = $this->query()
            ->whereHas('organizationEmployee', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->with([
                'organizationEmployee' => function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId)->with('departments:id,name');
                }
            ]);

        if (!empty($params['search'])) {
            $query->search(trim($params['search']));
        }

        if (!empty($params['department_id'])) {
            $query->whereHas('organizationEmployee.departments', function ($q) use ($params) {
                $q->where('departments.id', $params['department_id']);
            });
        }

        $orderBy = $params['orderBy'] ?? 'id';
        $direction = $params['direction'] ?? 'desc';
        $allowedOrderByFields = ['id', 'first_name', 'last_name', 'national_code', 'created_at'];
        if (in_array($orderBy, $allowedOrderByFields)) {
            $query->orderBy($orderBy, $direction);
        }

        return $query->paginate($params['limit'] ?? 15);
    }
}
