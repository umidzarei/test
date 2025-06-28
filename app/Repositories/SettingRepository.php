<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Setting::class);
    }

    protected static function instantiate(): static
    {
        return new static();
    }

    /**
     * Find a setting by its unique key.
     *
     * @param string $key
     * @return Setting|null
     */
    public function findByKey(string $key): ?Setting
    {
        return $this->query()->where('key', $key)->first();
    }
}
