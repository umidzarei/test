<?php

namespace App\Models;

use App\Concerns\Models\Filterable;
use App\Concerns\Models\Searchable;
use Illuminate\Database\Eloquent\Model;

class OccupationalMedicine extends Model
{
    use Searchable, Filterable;

    protected $fillable = [
        'name',
        'national_id',
        'reg_number',
        'economic_code',
        'address',
        'company_phone',
        'representative_name',
        'representative_position',
        'representative_phone',
    ];

    protected array $searchable = [
        'name',
        'national_id',
        'reg_number',
        'economic_code',
        'representative_name',
        'representative_position',
        'representative_phone',
    ];
}
