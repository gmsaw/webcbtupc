<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class CbtController extends Controller
{
    // 1. Ruang Tunggu (Mencegah Server Down)
    public function prepare(Registration $registration)
    {
        // Keamanan dasar
        if ($registration->user_id !== auth()->id() || $registration->status_pendaftaran !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda belum diverifikasi.');
        }

        // Pastikan record ExamResult sudah ada
        if (!$registration->examResult) {
            $registration->examResult()->create([
                'status' => 'not_started',
                'violation_count' => 0
            ]);
        }

        // Cek apakah sudah selesai
        if ($registration->examResult->status === 'finished') {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian CBT ini.');
        }

        return view('user.cbt.prepare', compact('registration'));
    }

    // 2. Menampilkan Ujian
    public function show(Registration $registration)
    {
        if ($registration->user_id !== auth()->id() || $registration->status_pendaftaran !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }
        
        if ($registration->examResult && $registration->examResult->status === 'finished') {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian.');
        }

        // Ubah status ujian menjadi Sedang Berlangsung (Jika baru pertama buka)
        if ($registration->examResult->status === 'not_started') {
            $registration->examResult()->update([
                'status' => 'in_progress',
                'start_time' => now()
            ]);
        }

        $competition = $registration->competition;
        
        // PENTING: Gunakan orderBy('id') agar index array tidak berantakan saat halaman direfresh!
        $questionsList = $competition->questions()->orderBy('id')->get();
        
        $questions = $questionsList->map(function ($q) {
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

        // AMBIL JAWABAN DARI TABEL `exam_answers`
        // Kita format kembali menjadi Object JS { "0": "A", "1": "C" } agar sesuai dengan frontend Alpine.js
        $savedAnswers = new \stdClass();
        $dbAnswers = ExamAnswer::where('registration_id', $registration->id)->get()->keyBy('question_id');
        
        foreach ($questions as $index => $q) {
            if (isset($dbAnswers[$q['id']])) {
                $savedAnswers->{$index} = $dbAnswers[$q['id']]->answer_selected;
            }
        }

        return view('user.cbt.ujian', compact('registration', 'competition', 'questions', 'savedAnswers'));
    }

    // 3. METHOD AUTOSAVE (Tabel Baru)
    public function autosave(Request $request, Registration $registration)
    {
        // Mencegah kecurangan
        if ($registration->user_id !== auth()->id() || ($registration->examResult && $registration->examResult->status === 'finished')) {
            return response()->json(['status' => 'error'], 403);
        }

        $userAnswers = json_decode($request->input('answers'), true) ?? [];
        $questions = $registration->competition->questions()->orderBy('id')->get();

        foreach ($userAnswers as $index => $answer) {
            if (isset($questions[$index])) {
                $questionId = $questions[$index]->id;

                // Simpan satu per satu ke tabel exam_answers
                ExamAnswer::updateOrCreate(
                    [
                        'registration_id' => $registration->id,
                        'question_id' => $questionId
                    ],
                    [
                        'answer_selected' => $answer
                    ]
                );
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Tersimpan otomatis']);
    }

    // 4. Memproses Jawaban & Menghitung Nilai Otomatis (Tabel Baru)
    public function submit(Request $request, Registration $registration)
    {
        if ($registration->user_id !== auth()->id() || ($registration->examResult && $registration->examResult->status === 'finished')) {
            return redirect()->route('dashboard');
        }

        // Simpan sisa jawaban terakhir yang belum sempat tersave oleh ajax
        $this->autosave($request, $registration);

        // ==========================================
        // MULAI KALKULASI NILAI (BENAR, SALAH, KOSONG)
        // ==========================================
        $competition = $registration->competition;
        $dbQuestions = $competition->questions->keyBy('id');
        
        // Ambil jawaban user dan jadikan question_id sebagai key Array
        $allUserAnswers = ExamAnswer::where('registration_id', $registration->id)->get()->keyBy('question_id');

        // Aturan Nilai dari Admin
        $skorBenar = $competition->nilai_benar ?? 1;
        $skorSalah = $competition->nilai_salah ?? 0;
        $skorKosong = $competition->nilai_kosong ?? 0;

        $totalScore = 0;

        // Loop SEMUA soal yang ada di lomba ini
        foreach ($dbQuestions as $qId => $q) {
            
            // CEK 1: Apakah peserta pernah menekan jawaban dan jawabannya tidak kosong?
            if (isset($allUserAnswers[$qId]) && !empty($allUserAnswers[$qId]->answer_selected)) {
                
                $userAns = $allUserAnswers[$qId];
                $isCorrect = ($q->jawaban_benar === $userAns->answer_selected);
                
                // Update rekam jejak benar/salah ke database
                $userAns->update(['is_correct' => $isCorrect]);

                if ($isCorrect) {
                    // BENAR: Tambahkan skor benar (dikali bobot soal jika Anda menggunakan bobot per soal)
                    $totalScore += ($skorBenar * $q->bobot_nilai);
                } else {
                    // SALAH: Tambahkan skor salah (biasanya minus, cth: -1)
                    $totalScore += ($skorSalah * $q->bobot_nilai);
                }

            } else {
                // CEK 2: KOSONG (Peserta tidak memilih jawaban apa pun)
                $totalScore += ($skorKosong * $q->bobot_nilai);
                
                // Opsional: Catat ke database bahwa soal ini dilewati/kosong
                ExamAnswer::updateOrCreate(
                    ['registration_id' => $registration->id, 'question_id' => $qId],
                    ['answer_selected' => null, 'is_correct' => null] // null menandakan tidak dijawab
                );
            }
        }

        // Simpan nilai akhir ke tabel exam_results
        $registration->examResult()->update([
            'score' => $totalScore,
            'end_time' => now(),
            'status' => 'finished'
        ]);

        return redirect()->route('dashboard')->with('success', 'Ujian Selesai! Anda mendapatkan skor akhir: ' . $totalScore);
    }
}