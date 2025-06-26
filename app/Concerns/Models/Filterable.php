<?php

namespace App\Concerns\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

trait Filterable
{

    /*
     * @param Builder $query The Eloquent query builder instance.
     * @param array|null $filterableAttributes An optional array of attribute names that are allowed to be filtered.
     * If null, the method attempts to use `$this->filterableAttributes` from the model.
     * @return Builder The modified query builder instance with applied filters.
     */
    public function scopeFilter(Builder $query, ?array $filterableAttributes = null): Builder
    {
        $request = Request::all();

        // Dynamically use provided attributes or fall back to the model's predefined filterable attributes
        $filterableAttributes = $filterableAttributes ?? $this->filterableAttributes ?? [];

        foreach ($request as $key => $value) {
            if (in_array($key, $filterableAttributes, true)) {
                if (filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
                    $query->where($key, filter_var($value, FILTER_VALIDATE_BOOLEAN));
                } elseif (is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }
        return $query;
    }
}
