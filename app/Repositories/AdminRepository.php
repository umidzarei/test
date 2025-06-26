<?php
namespace App\Repositories;

use App\Models\Admin;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
class AdminRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Admin::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }
    public function list(
        ?string $searchTerm = null,
        int $perPage = 15,
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $relations = []
    ): LengthAwarePaginator {
        $query = $this->query();

        if ($searchTerm && trim($searchTerm) !== '') {
            $query->search(trim($searchTerm));
        }

        $allowedOrderByFields = [
            'name',
            'email',
            'phone'
        ];
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
}
