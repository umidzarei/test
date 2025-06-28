<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HraQuestionnaireInstance extends Model
{
    protected $fillable = ['request_employee_id', 'status', 'submitted_at'];

    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class);
    }
    public function answers()
    {
        return $this->hasMany(HraAnswer::class, 'hra_questionnaire_instance_id');
    }

}
