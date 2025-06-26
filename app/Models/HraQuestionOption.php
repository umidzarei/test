<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HraQuestionOption extends Model
{
    protected $fillable = ['question_id', 'value', 'label', 'order'];

    public function question()
    {
        return $this->belongsTo(HraQuestion::class, 'question_id');
    }
}
