<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestEmployee extends Model
{
    protected $table = 'request_employees';

    protected $fillable = [
        'request_id',
        'employee_id',
        'status',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function hraInstance()
    {
        return $this->hasOne(HraQuestionnaireInstance::class);
    }

    public function labData()
    {
        return $this->hasOne(LabData::class);
    }

    public function tebKar()
    {
        return $this->hasOne(TebKar::class);
    }

    public function scores()
    {
        return $this->hasMany(CalculatedScores::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(PhysicianFeedbacks::class);
    }
}
