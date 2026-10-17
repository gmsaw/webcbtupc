<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Registration extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'competition_id',
        'babak',
        'status_pendaftaran',
    ];

    // ============================================================
    // RELASI
    // ============================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * SEMUA hasil ujian lintas babak.
     * 1 registration → N exam_results (penyisihan, semifinal, final).
     */
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function examAnswers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    // ============================================================
    // ACCESSOR & HELPER
    // ============================================================

    /**
     * Hasil ujian untuk babak AKTIF peserta.
     * Backward-compatible dengan $registration->examResult.
     */
    public function getExamResultAttribute(): ?ExamResult
    {
        $babak = $this->babak ?? 'penyisihan';

        if ($this->relationLoaded('examResults')) {
            return $this->examResults->firstWhere('babak', $babak);
        }

        return $this->examResults()->where('babak', $babak)->first();
    }

    /**
     * Hasil ujian untuk babak tertentu (untuk admin yang lihat babak mana saja).
     */
    public function examResultFor(string $babak): ?ExamResult
    {
        if ($this->relationLoaded('examResults')) {
            // kalau relasi sudah di-load dengan filter babak, firstWhere aman
            return $this->examResults->firstWhere('babak', $babak);
        }

        return $this->examResults()->where('babak', $babak)->first();
    }

    /**
     * Ambil / buat row exam_result untuk babak tertentu.
     */
    public function ensureExamResult(?string $babak = null): ExamResult
    {
        $babak = $babak ?? ($this->babak ?? 'penyisihan');

        $result = $this->examResults()->firstOrCreate(
            ['babak' => $babak],
            [
                'status'          => 'not_started',
                'violation_count' => 0,
            ]
        );

        $this->load('examResults');

        return $result;
    }

    /**
     * Riwayat semua babak, urut penyisihan → semifinal → final.
     */
    public function getRiwayatBabakAttribute()
    {
        $order = ['penyisihan' => 1, 'semifinal' => 2, 'final' => 3];

        $results = $this->relationLoaded('examResults')
            ? $this->examResults
            : $this->examResults()->get();

        return $results
            ->sortBy(fn ($r) => $order[$r->babak] ?? 99)
            ->values();
    }
}