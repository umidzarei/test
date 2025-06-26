<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HraAnswer extends Model
{
    protected $fillable = ['hra_questionnaire_instance_id', 'question_id', 'selected_option', 'answer_text', 'score_raw'];

    public function instance()
    {
        return $this->belongsTo(HraQuestionnaireInstance::class, 'hra_questionnaire_instance_id');
    }

    public function question()
    {
        return $this->belongsTo(HraQuestion::class, 'question_id');
    }
}
