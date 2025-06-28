<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculatedScores extends Model
{

    protected $fillable = [
        'request_employee_id',
        'total_hq_score',
        'diabetes_risk_score',
        'metabolic_syndrome_score',
        'cvd_risk_score',
        'fatty_liver_disease_risk_score',
        'depression_score',
        'anxiety_score',
        'stress_score',
        'nutrition_score',
        'physical_activity_score',
        'sleep_health_score',
        'habits_health_engagement_score',
        'stress_management_wellbeing_score',
    ];



    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class);
    }
}
