<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class CbtController extends Controller
{
    // 1. Menampilkan Halaman Ujian & Mengirim Data Soal
    public function show(Registration $registration)
    {
        // Keamanan: Cek apakah user yang akses adalah pemilik pendaftaran ini dan statusnya verified
        if ($registration->user_id !== auth()->id() || $registration->status_pendaftaran !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda belum diverifikasi.');
        }

        // Keamanan: Cek apakah peserta sudah pernah mengerjakan
        if (!is_null($registration->nilai_cbt)) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian CBT ini.');
        }

        $competition = $registration->competition;

        // Ambil semua soal untuk lomba ini (diacak urutannya)
        $questions = $competition->questions()->inRandomOrder()->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'text' => $q->pertanyaan,
                'image' => $q->hasMedia('gambar_soal') ? $q->getFirstMediaUrl('gambar_soal') : null,
                'options' => array_filter([ // Hapus opsi E jika kosong
                    'A' => $q->opsi_a,
                    'B' => $q->opsi_b,
                    'C' => $q->opsi_c,
                    'D' => $q->opsi_d,
                    'E' => $q->opsi_e,
                ])
            ];
        });

        // Jika panitia belum membuat soal
        if ($questions->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'Maaf, soal ujian belum tersedia.');
        }

        return view('user.cbt.ujian', compact('registration', 'competition', 'questions'));
    }

    // 2. Memproses Jawaban & Menghitung Nilai Otomatis
    public function submit(Request $request, Registration $registration)
    {
        if ($registration->user_id !== auth()->id() || !is_null($registration->nilai_cbt)) {
            return redirect()->route('dashboard');
        }

        // Ambil data jawaban (berbentuk JSON dari Alpine.js)
        $userAnswers = json_decode($request->input('answers'), true) ?? [];
        $dbQuestions = $registration->competition->questions->keyBy('id');

        $score = 0;
        $totalBobot = $dbQuestions->sum('bobot_nilai');

        // Cegah error dibagi 0
        if ($totalBobot == 0) $totalBobot = 1; 

        // Cocokkan jawaban peserta dengan database
        foreach ($userAnswers as $questionId => $answer) {
            if (isset($dbQuestions[$questionId])) {
                if ($dbQuestions[$questionId]->jawaban_benar === $answer) {
                    $score += $dbQuestions[$questionId]->bobot_nilai;
                }
            }
        }

        // Hitung skala 1-100 (Bisa disesuaikan dengan rumus HIMAFI)
        $finalScore = round(($score / $totalBobot) * 100, 2);

        // Simpan nilai ke tabel registrations
        $registration->update(['nilai_cbt' => $finalScore]);

        return redirect()->route('dashboard')->with('success', 'Ujian Selesai! Anda mendapatkan skor: ' . $finalScore);
    }
}