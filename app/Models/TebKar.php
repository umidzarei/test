<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TebKar extends Model
{
    protected $table = 'teb_kars';

    protected $fillable = [
        'request_employee_id',
        'height',
        'weight',
        'bmi',
        'waist',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'pulse',
    ];

    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class);
    }
}
