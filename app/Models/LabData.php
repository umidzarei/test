<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabData extends Model
{

    protected $fillable = [
        'request_employee_id',
        'FBS',
        'total_cholesterol',
        'HDL_cholesterol',
        'triglycerides',
        'ALT',
        'AST',
    ];

    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class);
    }
}
