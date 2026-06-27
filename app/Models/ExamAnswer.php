<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = [
        'registration_id', 
        'question_id', 
        'answer_selected', 
        'is_doubtful', 
        'is_correct'
    ];

    public function registration() { return $this->belongsTo(Registration::class); }
    public function question() { return $this->belongsTo(Question::class); }
}
