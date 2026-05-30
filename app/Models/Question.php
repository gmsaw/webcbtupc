<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Question extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'competition_id', 'pertanyaan', 'opsi_a', 'opsi_b', 
        'opsi_c', 'opsi_d', 'opsi_e', 'jawaban_benar', 'bobot_nilai'
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}