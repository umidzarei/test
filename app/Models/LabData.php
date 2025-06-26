<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabData extends Model
{
    protected $table = 'lab_data';

    protected $fillable = [
        'request_employee_id',
        'fbs',
        'hdl',
        'ldl',
        'triglyceride',
        'cholesterol',
        'sgot',
        'sgpt',
        'hba1c',
    ];

    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class);
    }
}
