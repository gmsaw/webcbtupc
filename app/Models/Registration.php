<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Registration extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['user_id', 'competition_id', 'status_pendaftaran'];

    public function user() { return $this->belongsTo(User::class); }
    public function competition() { return $this->belongsTo(Competition::class); }
    
    // Relasi ke tabel baru
    public function payment() { return $this->hasOne(Payment::class); }
    public function examResult() { return $this->hasOne(ExamResult::class); }
    public function examAnswers() { return $this->hasMany(ExamAnswer::class); }
}