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
        'is_active',
        'is_using_waves',
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

    // Tambahkan ini di dalam class Competition
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Relasi ke tabel gelombang
    public function waves()
    {
        return $this->hasMany(CompetitionWave::class);
    }

    // MAGIC FUNCTION: Mengambil harga aktif saat ini secara otomatis
    public function getActivePriceAttribute()
    {
        if (!$this->is_using_waves) {
            return $this->biaya_pendaftaran; // Kembali ke harga normal jika tidak pakai gelombang
        }

        // Cari gelombang yang tanggalnya mencakup HARI INI
        $activeWave = $this->waves()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        // Jika ada gelombang aktif, kembalikan biayanya. Jika tidak (sudah tutup), kembalikan null
        return $activeWave ? $activeWave->biaya : null; 
    }

    // MAGIC FUNCTION: Mengambil nama gelombang aktif
    public function getActiveWaveNameAttribute()
    {
        if (!$this->is_using_waves) return 'Harga Normal';

        $activeWave = $this->waves()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        return $activeWave ? $activeWave->nama_gelombang : 'Pendaftaran Tutup';
    }
}