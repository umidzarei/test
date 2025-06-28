<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TebKar extends Model
{

    protected $fillable = [
        'request_employee_id',
        'height',
        'weight',
        'waist_circumference',
        'SBP',
        'DBP',
        'BMI',
    ];

    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class);
    }
}
