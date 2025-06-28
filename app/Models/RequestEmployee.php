<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestEmployee extends Model
{
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
    public function labData()
    {
        return $this->hasOne(LabData::class);
    }
    public function tebKar()
    {
        return $this->hasOne(TebKar::class);
    }
    public function calculatedScore()
    {
        return $this->hasOne(CalculatedScores::class);
    }
    public function physicianFeedback()
    {
        return $this->hasOne(PhysicianFeedbacks::class);
    }
    public function hraQuestionnaireInstance()
    {
        return $this->hasOne(HraQuestionnaireInstance::class);
    }
}
