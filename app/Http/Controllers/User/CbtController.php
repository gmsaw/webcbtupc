<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class CbtController extends Controller
{
    // ============================================================
    // 1. RUANG TUNGGU (WAITING ROOM)
    // ============================================================
    public function prepare(Request $request, Registration $registration)
    {
        // Keamanan dasar
        if ((int)$registration->user_id !== (int)auth()->id()
            || strtolower($registration->status_pendaftaran) !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda belum diverifikasi.');
        }

        // Pastikan record ExamResult sudah ada
        if (!$registration->examResult) {
            $registration->examResult()->create([
                'status' => 'not_started',
                'violation_count' => 0,
            ]);
        }

        // Cek apakah sudah selesai
        if ($registration->examResult->status === 'finished') {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian CBT ini.');
        }

        // Kalau sudah punya token valid → langsung ke ujian
        $existingToken = $request->cookie('exam_access');
        if ($existingToken && ($payload = Cache::get("exam_access:{$existingToken}"))) {
            if ((int) $payload['registration_id'] === (int)$registration->id) {
                return redirect()->route('user.ujian.show', $registration);
            }
            Cache::forget("exam_access:{$existingToken}");
        }

        // Buat queue session baru
        $queueId = (string) Str::uuid();

        Cache::put("queue:{$queueId}", [
            'registration_id' => $registration->id,
            'user_id'         => auth()->id(),
            'joined_at'       => now()->toIso8601String(),
        ], now()->addMinutes(15));

        return response()
            ->view('user.cbt.prepare', compact('registration'))
            ->cookie(
                'queue_id',
                $queueId,
                15,
                '/',
                null,
                app()->environment('production'),
                true,
                false,
                'lax'
            )
            ->withCookie(cookie()->forget('exam_access'));
    }

    // ============================================================
    // 1b. API: CEK STATUS ANTRIAN
    // ============================================================
    public function queueStatus(Request $request, Registration $registration)
    {
        if ((int)$registration->user_id !== (int)auth()->id()) {
            abort(403);
        }

        if ($registration->examResult && $registration->examResult->status === 'finished') {
            return response()->json([
                'ready' => false,
                'error' => 'Anda sudah menyelesaikan ujian.',
                'reset' => true,
            ], 403);
        }

        // Race condition handler: cek token valid dulu
        $existingToken = $request->cookie('exam_access');
        if ($existingToken && ($payload = Cache::get("exam_access:{$existingToken}"))) {
            if ((int) $payload['registration_id'] === (int)$registration->id) {
                return response()->json([
                    'ready'    => true,
                    'redirect' => route('user.ujian.show', $registration),
                ]);
            }
        }

        $queueId = $request->cookie('queue_id');

        if (!$queueId || !($queueData = Cache::get("queue:{$queueId}"))) {
            return response()->json([
                'ready' => false,
                'error' => 'Sesi antrian tidak valid atau sudah kedaluwarsa.',
                'reset' => true,
            ], 403);
        }

        if ((int) $queueData['registration_id'] !== (int)$registration->id) {
            return response()->json([
                'ready' => false,
                'error' => 'Antrian tidak cocok dengan ujian ini.',
                'reset' => true,
            ], 403);
        }

        if (isset($queueData['ready_at'])) {
            return response()->json([
                'ready'    => true,
                'redirect' => route('user.ujian.show', $registration),
            ]);
        }

        // ── GLOBAL RATE LIMITER ──
        $key = 'exam-entry:global-throttle';

        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'ready'    => false,
                'position' => $this->estimatePosition($queueId),
                'eta'      => $this->estimateEta($queueId),
            ]);
        }

        RateLimiter::hit($key, 10);

        Cache::put("queue:{$queueId}", array_merge($queueData, [
            'ready_at' => now()->toIso8601String(),
        ]), now()->addMinutes(15));

        $accessToken = (string) Str::uuid();

        Cache::put("exam_access:{$accessToken}", [
            'registration_id' => $registration->id,
            'user_id'         => auth()->id(),
            'issued_at'       => now()->toIso8601String(),
            'last_seen_at'    => now()->toIso8601String(),
        ], now()->addMinutes(240));

        return response()
            ->json([
                'ready'    => true,
                'redirect' => route('user.ujian.show', $registration),
            ])
            ->cookie(
                'exam_access',
                $accessToken,
                240,
                '/',
                null,
                app()->environment('production'),
                true,
                false,
                'lax'
            )
            ->withCookie(cookie()->forget('queue_id'));
    }

    // ============================================================
    // 2. MENAMPILKAN UJIAN (WAKTU MUTLAK)
    // ============================================================
    public function show(Registration $registration)
    {
        if ((int)$registration->user_id !== auth()->id()
            || $registration->status_pendaftaran !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        if ($registration->examResult && $registration->examResult->status === 'finished') {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian.');
        }

        if ($registration->examResult->status === 'not_started') {
            $registration->examResult()->update([
                'status'     => 'in_progress',
                'start_time' => now(),
            ]);
        }

        $competition = $registration->competition;

        // ── WAKTU MUTLAK ──
        if (!$competition->waktu_pelaksanaan) {
            return redirect()->route('dashboard')
                ->with('error', 'Jadwal ujian belum ditentukan. Hubungi admin.');
        }

        $waktuMulaiJadwal = \Carbon\Carbon::parse($competition->waktu_pelaksanaan);
        $durasiMenit = $competition->durasi_menit ?? 120;
        $waktuSelesaiMutlak = $waktuMulaiJadwal->copy()->addMinutes($durasiMenit);

        if (now()->greaterThanOrEqualTo($waktuSelesaiMutlak)) {
            return redirect()->route('dashboard')
                ->with('error', 'Waktu ujian telah berakhir berdasarkan jadwal resmi.');
        }

        // ── SOAL ──
        $questionsList = $competition->questions()->orderBy('id')->get();

        $questions = $questionsList->map(function ($q) {
            return [
                'id'      => $q->id,
                'text'    => $q->pertanyaan,
                'image'   => $q->hasMedia('gambar_soal') ? $q->getFirstMediaUrl('gambar_soal') : null,
                'options' => array_filter([
                    'A' => $q->opsi_a,
                    'B' => $q->opsi_b,
                    'C' => $q->opsi_c,
                    'D' => $q->opsi_d,
                    'E' => $q->opsi_e,
                ]),
            ];
        });

        if ($questions->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'Soal ujian belum tersedia.');
        }

        // ── JAWABAN TERSIMPAN (index-based untuk Alpine) ──
        $savedAnswers = new \stdClass();
        $dbAnswers = ExamAnswer::where('registration_id', $registration->id)
            ->get()
            ->keyBy('question_id');

        foreach ($questions as $index => $q) {
            if (isset($dbAnswers[$q['id']])) {
                $savedAnswers->{$index} = $dbAnswers[$q['id']]->answer_selected;
            }
        }

        // ── SISA DETIK ──
        $sisaDetik = now()->diffInSeconds($waktuSelesaiMutlak, false);

        return view('user.cbt.ujian', compact(
            'registration',
            'competition',
            'questions',
            'savedAnswers',
            'waktuSelesaiMutlak',
            'sisaDetik'
        ));
    }

    // ============================================================
    // 3. AUTOSAVE — TERIMA question_id (FIX BUG INDEX)
    // ============================================================
    public function autosave(Request $request, Registration $registration)
    {
        // Validasi user & status
        if ((int)$registration->user_id !== auth()->id()
            || ($registration->examResult && $registration->examResult->status === 'finished')) {
            return response()->json(['status' => 'error'], 403);
        }

        // ── VALIDASI WAKTU MUTLAK ──
        $competition = $registration->competition;

        if ($competition->waktu_pelaksanaan) {
            $waktuMulaiJadwal = \Carbon\Carbon::parse($competition->waktu_pelaksanaan);
            $durasiMenit = $competition->durasi_menit ?? 120;
            $waktuSelesaiMutlak = $waktuMulaiJadwal->copy()->addMinutes($durasiMenit);

            // Toleransi 2 menit untuk delay jaringan
            if (now()->greaterThan($waktuSelesaiMutlak->copy()->addMinutes(2))) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Waktu ujian telah berakhir. Jawaban ditolak.',
                ], 403);
            }
        }

        // ── TERIMA PAYLOAD: { question_id: answer } ──
        $userAnswers = json_decode($request->input('answers'), true) ?? [];

        // Validasi ukuran payload
        if (strlen($request->input('answers', '')) > 50000) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Payload terlalu besar.',
            ], 413);
        }

        // Ambil semua question_id yang valid untuk competition ini
        $validQuestionIds = $registration->competition
            ->questions()
            ->pluck('id')
            ->toArray();

        $savedCount = 0;

        foreach ($userAnswers as $questionId => $answer) {
            // Skip kalau question_id tidak valid
            if (!in_array((int)$questionId, $validQuestionIds, true)) {
                continue;
            }

            // Skip kalau answer kosong
            if ($answer === null || $answer === '') {
                continue;
            }

            ExamAnswer::updateOrCreate(
                [
                    'registration_id' => $registration->id,
                    'question_id'     => (int)$questionId,
                ],
                [
                    'answer_selected' => $answer,
                ]
            );

            $savedCount++;
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Tersimpan otomatis',
            'saved'   => $savedCount,
        ]);
    }

    // ============================================================
    // 4. SUBMIT — HITUNG NILAI
    // ============================================================
    public function submit(Request $request, Registration $registration)
    {
        if ((int)$registration->user_id !== auth()->id()
            || ($registration->examResult && $registration->examResult->status === 'finished')) {
            return redirect()->route('dashboard');
        }

        $competition = $registration->competition;

        // ── VALIDASI WAKTU ──
        $bolehSimpanJawaban = true;

        if ($competition->waktu_pelaksanaan) {
            $waktuMulaiJadwal = \Carbon\Carbon::parse($competition->waktu_pelaksanaan);
            $durasiMenit = $competition->durasi_menit ?? 120;
            $waktuSelesaiMutlak = $waktuMulaiJadwal->copy()->addMinutes($durasiMenit);

            $bolehSimpanJawaban = now()->lessThanOrEqualTo($waktuSelesaiMutlak->copy()->addMinutes(2));

            if (!$bolehSimpanJawaban) {
                \Log::info('Submit setelah waktu habis', [
                    'registration_id' => $registration->id,
                    'submit_time'     => now()->toIso8601String(),
                    'waktu_selesai'   => $waktuSelesaiMutlak->toIso8601String(),
                ]);
            }
        }

        // Simpan jawaban terakhir dari form (kalau masih dalam waktu)
        // Payload form: { question_id: answer }
        if ($bolehSimpanJawaban) {
            $userAnswers = json_decode($request->input('answers'), true) ?? [];

            $validQuestionIds = $competition->questions()->pluck('id')->toArray();

            foreach ($userAnswers as $questionId => $answer) {
                if (!in_array((int)$questionId, $validQuestionIds, true)) {
                    continue;
                }

                ExamAnswer::updateOrCreate(
                    [
                        'registration_id' => $registration->id,
                        'question_id'     => (int)$questionId,
                    ],
                    [
                        'answer_selected' => $answer,
                    ]
                );
            }
        }

        // ── KALKULASI NILAI ──
        $dbQuestions = $competition->questions->keyBy('id');

        $allUserAnswers = ExamAnswer::where('registration_id', $registration->id)
            ->get()
            ->keyBy('question_id');

        $skorBenar  = (float) ($competition->nilai_benar  ?? 4.0);
        $skorSalah  = (float) ($competition->nilai_salah  ?? -1.0);
        $skorKosong = (float) ($competition->nilai_kosong ?? 0.0);

        $totalScore = 0;

        foreach ($dbQuestions as $qId => $q) {
            if (isset($allUserAnswers[$qId]) && !empty($allUserAnswers[$qId]->answer_selected)) {
                $userAns = $allUserAnswers[$qId];
                $isCorrect = ($q->jawaban_benar === $userAns->answer_selected);

                $userAns->update(['is_correct' => $isCorrect]);

                $totalScore += $isCorrect ? $skorBenar : $skorSalah;

            } else {
                $totalScore += $skorKosong;

                ExamAnswer::updateOrCreate(
                    ['registration_id' => $registration->id, 'question_id' => $qId],
                    ['answer_selected' => null, 'is_correct' => null]
                );
            }
        }

        $registration->examResult()->update([
            'score'    => $totalScore,
            'end_time' => now(),
            'status'   => 'finished',
        ]);

        return redirect()->route('dashboard')
            // ->with('success', 'Ujian Selesai! Anda mendapatkan skor akhir: ' . $totalScore)
            ->with('success', 'Ujian Selesai! Mohon menunggu pengumuman resmi, Terima kasih')
            ->withCookie(cookie()->forget('exam_access'));
    }

    // ============================================================
    // HELPER
    // ============================================================
    private function estimatePosition(string $queueId): int
    {
        $queueData = Cache::get("queue:{$queueId}");
        if (!$queueData) return 1;

        $joinedAt = \Carbon\Carbon::parse($queueData['joined_at']);
        $secondsWaiting = now()->diffInSeconds($joinedAt);

        $ratePerDetik = 10;
        $totalAntrian = 200;

        $sudahLolos = $secondsWaiting * $ratePerDetik;
        $estimated = $totalAntrian - $sudahLolos;

        return (int) max(1, $estimated);
    }

    private function estimateEta(string $queueId): int
    {
        $position = $this->estimatePosition($queueId);
        $ratePerDetik = 10;
        return (int) ceil($position / $ratePerDetik);
    }
}