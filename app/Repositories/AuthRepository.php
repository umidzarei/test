<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class AuthRepository extends BaseRepository
{

    public function __construct(string $modelClass)
    {
        parent::__construct($modelClass);
    }

    protected static function instantiate(): static
    {
        return new static(static::class);
    }

    public function findOne(string $column, string $email): ?Model
    {
        return $this->modelClass::where($column, $email)->first();
    }
}
