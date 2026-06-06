<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class CbtController extends Controller
{
    // 1. Method Show Diperbarui
    public function show(Registration $registration)
    {
        if ($registration->user_id !== auth()->id() || $registration->status_pendaftaran !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }
        if (!is_null($registration->nilai_cbt)) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian.');
        }

        $competition = $registration->competition;
        $questions = $competition->questions()->inRandomOrder()->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'text' => $q->pertanyaan,
                'image' => $q->hasMedia('gambar_soal') ? $q->getFirstMediaUrl('gambar_soal') : null,
                'options' => array_filter([
                    'A' => $q->opsi_a, 'B' => $q->opsi_b, 'C' => $q->opsi_c, 'D' => $q->opsi_d, 'E' => $q->opsi_e,
                ])
            ];
        });

        if ($questions->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'Soal ujian belum tersedia.');
        }

        // AMBIL JAWABAN SEMENTARA (JIKA PESERTA REFRESH HALAMAN)
        $savedAnswers = $registration->jawaban_sementara ? json_decode($registration->jawaban_sementara, true) : new \stdClass();

        // Lempar variabel $savedAnswers ke view
        return view('user.cbt.ujian', compact('registration', 'competition', 'questions', 'savedAnswers'));
    }

    // 2. METHOD AUTOSAVE BARU
    public function autosave(Request $request, Registration $registration)
    {
        // Mencegah kecurangan / manipulasi
        if ($registration->user_id !== auth()->id() || !is_null($registration->nilai_cbt)) {
            return response()->json(['status' => 'error'], 403);
        }

        // Simpan payload JSON dari frontend ke database
        $registration->update([
            'jawaban_sementara' => $request->input('answers')
        ]);

        return response()->json(['status' => 'success', 'message' => 'Tersimpan otomatis']);
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

    // Ruang Tunggu (Mencegah Server Down)
    public function prepare(Registration $registration)
    {
        // Keamanan dasar
        if ($registration->user_id !== auth()->id() || $registration->status_pendaftaran !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda belum diverifikasi.');
        }

        if (!is_null($registration->nilai_cbt)) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian CBT ini.');
        }

        return view('user.cbt.prepare', compact('registration'));
    }
}