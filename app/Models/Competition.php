<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Competition extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'nama_lomba', 
        'deskripsi', 
        'harga_pendaftaran', 
        'tanggal_mulai', 
        'tanggal_selesai',
        'waktu_pelaksanaan',
        'durasi_menit', 
        'is_active'
    ];

    // Memberitahu Laravel bahwa ini adalah tipe Data Tanggal
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'waktu_pelaksanaan' => 'datetime',
        ];
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}