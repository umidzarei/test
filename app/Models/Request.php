<?php

namespace App\Models;

use App\Concerns\Models\Searchable;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use Searchable;
    protected $fillable = [
        'tracking_code',
        'organization_id',
        'occupational_medicine_id',
        'requester_id',
        'requester_type',
        'status',
        'options'
    ];
    protected array $searchable = [
        'tracking_code',
    ];
    protected $casts = [
        'options' => 'array',
    ];
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function employees()
    {
        return $this->hasMany(RequestEmployee::class);
    }
    public function physicians()
    {
        return $this->belongsToMany(Physician::class, 'request_physician');
    }
    public function requester()
    {
        return $this->morphTo();
    }
    public function occupationalMedicine()
    {
        return $this->belongsTo(OccupationalMedicine::class);
    }
    public function requestEmployees()
    {
        return $this->hasMany(RequestEmployee::class);
    }
}
