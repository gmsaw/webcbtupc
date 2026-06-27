<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = [
        'registration_id', 
        'score', 
        'start_time', 
        'end_time', 
        'violation_count', 
        'status'
    ];
    
    public function registration() { return $this->belongsTo(Registration::class); }
}
