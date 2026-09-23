<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = [
        'registration_id',
        'babak', 
        'score', 
        'start_time', 
        'end_time', 
        'violation_count', 
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];
    
    public function registration() { return $this->belongsTo(Registration::class); }
}
