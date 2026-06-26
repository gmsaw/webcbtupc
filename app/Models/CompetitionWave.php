<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionWave extends Model
{
    protected $fillable = ['competition_id', 'nama_gelombang', 'start_date', 'end_date', 'biaya'];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}