<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HraQuestionnaireInstance extends Model
{
    protected $fillable = ['request_employee_id', 'status', 'submitted_at'];

    public function answers()
    {
        return $this->hasMany(HraAnswer::class);
    }
}
