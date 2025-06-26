<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicianFeedbacks extends Model
{
    protected $table = 'physician_feedbacks';

    protected $fillable = [
        'request_employee_id',
        'physician_id',
        'notes',
        'status',
    ];

    public function physician()
    {
        return $this->belongsTo(Physician::class);
    }

    public function requestEmployee()
    {
        return $this->belongsTo(RequestEmployee::class);
    }
}
