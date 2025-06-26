<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HraQuestion extends Model
{

    protected $fillable = ['code', 'text', 'input_type', 'section', 'order'];

    public function options()
    {
        return $this->hasMany(HraQuestionOption::class, 'question_id');
    }
}
