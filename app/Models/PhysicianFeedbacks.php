<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicianFeedbacks extends Model
{

    protected $fillable = [
        'request_employee_id',
        'note',
    ];

    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class, 'request_employee_id');
    }
}
