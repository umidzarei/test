<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculatedScores extends Model
{
    protected $table = 'calculated_scores';

    protected $fillable = [
        'request_employee_id',
        'type',
        'score',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class);
    }
}
