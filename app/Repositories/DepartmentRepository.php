<?php
namespace App\Repositories;

use App\Models\Department;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Department::class);
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
            $query->where('organization_id', $organizationId);

        }

        $allowedOrderByFields = ['name'];
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
    public function getByOrganizationId(int $organizationId, $perPage = 15)
    {
        return $this->query()
            ->where('organization_id', $organizationId)
            ->paginate($perPage);
    }
}
