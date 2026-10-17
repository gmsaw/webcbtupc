<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CbtController extends Controller
{
    // ============================================================
    // 1. RUANG TUNGGU (WAITING ROOM) — sederhana dengan delay
    // ============================================================
    public function prepare(Request $request, Registration $registration)
    {
        // ── Keamanan dasar ──
        if ((int) $registration->user_id !== (int) auth()->id()
            || strtolower($registration->status_pendaftaran) !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda belum diverifikasi.');
        }

        // ── Hanya load examResults — competition TIDAK dibutuhkan di prepare ──
        $registration->loadMissing('examResults');

        $babak = $registration->babak ?? 'penyisihan';

        // ══════════════════════════════════════════════════════════
        // Cek selesai TANPA menulis DB
        // ── Row exam_result akan dibuat saat show() (benar-benar masuk ujian)
        // ══════════════════════════════════════════════════════════
        $examResult = $registration->examResultFor($babak);

        if ($examResult && $examResult->status === 'finished') {
            return redirect()->route('dashboard')
                ->with('error', 'Anda sudah menyelesaikan ujian babak '.ucfirst($babak).'.');
        }

        // ── Kalau sudah punya token valid → langsung ke ujian ──
        $existingToken = $request->cookie('exam_access');
        if ($existingToken && ($payload = Cache::get("exam_access:{$existingToken}"))) {
            if ((int) $payload['registration_id'] === (int) $registration->id) {
                return redirect()->route('user.ujian.show', $registration);
            }
            Cache::forget("exam_access:{$existingToken}");
        }

        // ══════════════════════════════════════════════════════════
        // HITUNG DELAY — hanya jika belum punya jadwal
        // ══════════════════════════════════════════════════════════
        $queueKey  = "exam:queue:{$registration->id}";
        $queueData = Cache::get($queueKey);

        if ($queueData && isset($queueData['start_at'])) {
            // Sudah punya jadwal — pakai yang ada (refresh tidak reset)
            $startAt = \Carbon\Carbon::parse($queueData['start_at']);
        } else {
            // Hitung delay bertingkat berdasarkan user aktif
            $delaySeconds = $this->calculateDelay();

            $startAt = now()->addSeconds($delaySeconds);

            Cache::put($queueKey, [
                'registration_id' => $registration->id,
                'user_id'         => auth()->id(),
                'babak'           => $babak,
                'start_at'        => $startAt->toIso8601String(),
                'created_at'      => now()->toIso8601String(),
            ], now()->addMinutes(10));
        }

        $waitSeconds = max(0, (int) now()->diffInSeconds($startAt, false));

        return view('user.cbt.prepare', [
            'registration' => $registration,
            'waitSeconds'  => $waitSeconds,
            'startAt'      => $startAt,
        ]);
    }

    // ============================================================
    // 1b. API: CEK STATUS ANTRIAN — hanya dipanggil 1x saat countdown habis
    // ============================================================
    public function queueStatus(Request $request, Registration $registration)
    {
        if ((int) $registration->user_id !== (int) auth()->id()) {
            abort(403);
        }

        // ── Hanya load examResults — competition tidak dibutuhkan ──
        $registration->loadMissing('examResults');

        $babak = $registration->babak ?? 'penyisihan';

        // ── Cek selesai di babak INI ──
        $examResult = $registration->examResultFor($babak);
        if ($examResult && $examResult->status === 'finished') {
            return response()->json([
                'ready'    => false,
                'error'    => 'Anda sudah menyelesaikan ujian babak '.ucfirst($babak).'.',
                'reset'    => true,
                'finished' => true,
            ], 403);
        }

        // ── Race condition: sudah punya token valid ──
        $existingToken = $request->cookie('exam_access');
        if ($existingToken && ($payload = Cache::get("exam_access:{$existingToken}"))) {
            if ((int) $payload['registration_id'] === (int) $registration->id) {
                return response()->json([
                    'ready'    => true,
                    'redirect' => route('user.ujian.show', $registration),
                ]);
            }
        }

        // ── Validasi queue data ──
        $queueKey  = "exam:queue:{$registration->id}";
        $queueData = Cache::get($queueKey);

        if (! $queueData || ! isset($queueData['start_at'])) {
            return response()->json([
                'ready'    => false,
                'error'    => 'Sesi antrian tidak valid. Silakan muat ulang halaman.',
                'reset'    => true,
                'finished' => false,
            ], 403);
        }

        $startAt = \Carbon\Carbon::parse($queueData['start_at']);
        $now     = now();

        // ══════════════════════════════════════════════════════════
        // BELUM WAKTUNYA → kembalikan sisa detik (client update countdown)
        // ══════════════════════════════════════════════════════════
        if ($now->lt($startAt)) {
            $remaining = max(0, (int) $now->diffInSeconds($startAt, false));
            return response()->json([
                'ready'             => false,
                'wait_seconds'      => $remaining,
                'wait_seconds_left' => $remaining,
            ]);
        }

        // ══════════════════════════════════════════════════════════
        // SUDAH WAKTUNYA → issue token akses
        // ══════════════════════════════════════════════════════════
        $accessToken = (string) Str::uuid();

        Cache::put("exam_access:{$accessToken}", [
            'registration_id' => $registration->id,
            'user_id'         => auth()->id(),
            'babak'           => $babak,
            'issued_at'       => now()->toIso8601String(),
        ], now()->addMinutes(240));

        // Hapus queue data setelah dipakai (mencegah reuse)
        Cache::forget($queueKey);

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
            );
    }

    // ============================================================
    // 2. MENAMPILKAN UJIAN
    // ============================================================
    public function show(Registration $registration)
    {
        if ((int) $registration->user_id !== auth()->id()
            || $registration->status_pendaftaran !== 'verified') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $registration->loadMissing('examResults', 'competition');

        $babak = $registration->babak ?? 'penyisihan';

        // ── Pastikan row babak INI ada (tulis DB, tapi hanya sekali saat masuk ujian) ──
        $examResult = $registration->ensureExamResult($babak);

        if ($examResult->status === 'finished') {
            return redirect()->route('dashboard')
                ->with('error', 'Anda sudah menyelesaikan ujian babak '.ucfirst($babak).'.');
        }

        // ── Update status jadi in_progress ──
        if ($examResult->status === 'not_started') {
            $examResult->update([
                'status'     => 'in_progress',
                'start_time' => now(),
                'end_time'   => null,
            ]);
        }

        $competition = $registration->competition;

        [$waktuMulai, $durasiMenit] = $this->resolveJadwal($competition, $babak);

        if (! $waktuMulai) {
            return redirect()->route('dashboard')
                ->with('error', 'Jadwal ujian babak '.ucfirst($babak).' belum ditentukan. Hubungi admin.');
        }

        $waktuMulaiJadwal   = \Carbon\Carbon::parse($waktuMulai);
        $waktuSelesaiMutlak = $waktuMulaiJadwal->copy()->addMinutes($durasiMenit);

        if (now()->greaterThanOrEqualTo($waktuSelesaiMutlak)) {
            return redirect()->route('dashboard')
                ->with('error', 'Waktu ujian babak '.ucfirst($babak).' telah berakhir.');
        }

        // ── Ambil soal babak INI — eager load media ──
        $questionsList = $competition->questions()
            ->where('babak', $babak)
            ->with('media')
            ->orderBy('id')
            ->get();

        if ($questionsList->isEmpty()) {
            $questionsList = $competition->questions()
                ->with('media')
                ->orderBy('id')
                ->get();
        }

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

        // ── Jawaban tersimpan — HANYA soal babak INI ──
        $savedAnswers = new \stdClass();
        $dbAnswers = ExamAnswer::where('registration_id', $registration->id)
            ->whereIn('question_id', $questionsList->pluck('id'))
            ->get()
            ->keyBy('question_id');

        foreach ($questions as $index => $q) {
            if (isset($dbAnswers[$q['id']])) {
                $savedAnswers->{$index} = $dbAnswers[$q['id']]->answer_selected;
            }
        }

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
    // 3. AUTOSAVE — hanya sentuh jawaban babak ini
    // ============================================================
    public function autosave(Request $request, Registration $registration)
    {
        $registration->loadMissing('examResults', 'competition');

        $babak = $registration->babak ?? 'penyisihan';

        if ((int) $registration->user_id !== auth()->id()) {
            return response()->json(['status' => 'error'], 403);
        }

        $examResult = $registration->examResultFor($babak);
        if ($examResult && $examResult->status === 'finished') {
            return response()->json(['status' => 'error'], 403);
        }

        $competition = $registration->competition;

        [$waktuMulai, $durasiMenit] = $this->resolveJadwal($competition, $babak);

        if ($waktuMulai) {
            $waktuSelesaiMutlak = \Carbon\Carbon::parse($waktuMulai)
                ->addMinutes($durasiMenit);

            if (now()->greaterThan($waktuSelesaiMutlak->copy()->addMinutes(2))) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Waktu ujian telah berakhir. Jawaban ditolak.',
                ], 403);
            }
        }

        $rawAnswers = $request->input('answers', '');
        if (strlen($rawAnswers) > 50000) {
            return response()->json(['status' => 'error', 'message' => 'Payload terlalu besar.'], 413);
        }

        $userAnswers = json_decode($rawAnswers, true) ?? [];

        // Validasi question_id HANYA untuk babak ini
        $validQuestionIds = $competition->questions()
            ->where('babak', $babak)
            ->pluck('id')
            ->toArray();

        $validIdsFlip = array_flip($validQuestionIds);
        $now          = now();
        $rows         = [];

        foreach ($userAnswers as $questionId => $answer) {
            $qid = (int) $questionId;
            if (! isset($validIdsFlip[$qid])) continue;
            if ($answer === null || $answer === '') continue;

            $rows[] = [
                'registration_id' => $registration->id,
                'question_id'     => $qid,
                'answer_selected' => $answer,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        if (! empty($rows)) {
            ExamAnswer::upsert(
                $rows,
                ['registration_id', 'question_id'],
                ['answer_selected', 'updated_at']
            );
        }

        // Update violation count di row babak ini
        $violationCount = (int) $request->input('violation_count', 0);
        if ($examResult && $violationCount > $examResult->violation_count) {
            $examResult->update(['violation_count' => $violationCount]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Tersimpan otomatis',
            'saved'   => count($rows),
            'babak'   => $babak,
        ]);
    }

    // ============================================================
    // 4. SUBMIT — simpan hasil PER BABAK, tidak menimpa babak lain
    // ============================================================
    public function submit(Request $request, Registration $registration)
    {
        $registration->loadMissing('examResults', 'competition');

        $babak = $registration->babak ?? 'penyisihan';

        if ((int) $registration->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        $examResult = $registration->ensureExamResult($babak);

        if ($examResult->status === 'finished') {
            return redirect()->route('dashboard');
        }

        $competition = $registration->competition;

        [$waktuMulai, $durasiMenit] = $this->resolveJadwal($competition, $babak);

        $bolehSimpanJawaban = true;

        if ($waktuMulai) {
            $waktuSelesaiMutlak = \Carbon\Carbon::parse($waktuMulai)
                ->addMinutes($durasiMenit);

            $bolehSimpanJawaban = now()->lessThanOrEqualTo($waktuSelesaiMutlak->copy()->addMinutes(2));

            if (! $bolehSimpanJawaban) {
                Log::info('Submit setelah waktu habis', [
                    'registration_id' => $registration->id,
                    'babak'           => $babak,
                    'submit_time'     => now()->toIso8601String(),
                    'waktu_selesai'   => $waktuSelesaiMutlak->toIso8601String(),
                ]);
            }
        }

        // ── Simpan jawaban terakhir — HANYA soal babak INI ──
        if ($bolehSimpanJawaban) {
            $userAnswers = json_decode($request->input('answers'), true) ?? [];

            $validQuestionIds = $competition->questions()
                ->where('babak', $babak)
                ->pluck('id')
                ->toArray();

            $validIdsFlip = array_flip($validQuestionIds);
            $now          = now();
            $rows         = [];

            foreach ($userAnswers as $questionId => $answer) {
                $qid = (int) $questionId;
                if (! isset($validIdsFlip[$qid])) continue;
                if ($answer === null || $answer === '') continue;

                $rows[] = [
                    'registration_id' => $registration->id,
                    'question_id'     => $qid,
                    'answer_selected' => $answer,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }

            if (! empty($rows)) {
                ExamAnswer::upsert(
                    $rows,
                    ['registration_id', 'question_id'],
                    ['answer_selected', 'updated_at']
                );
            }
        }

        // ── Hitung nilai — hanya soal babak ini ──
        $dbQuestions = $competition->questions()
            ->where('babak', $babak)
            ->get()
            ->keyBy('id');

        $allUserAnswers = ExamAnswer::where('registration_id', $registration->id)
            ->whereIn('question_id', $dbQuestions->keys())
            ->get()
            ->keyBy('question_id');

        $skorBenar  = (float) ($competition->nilai_benar  ?? 4.0);
        $skorSalah  = (float) ($competition->nilai_salah  ?? -1.0);
        $skorKosong = (float) ($competition->nilai_kosong ?? 0.0);

        $totalScore  = 0;
        $correctRows = [];
        $emptyRows   = [];
        $now         = now();

        foreach ($dbQuestions as $qId => $q) {
            if (isset($allUserAnswers[$qId]) && ! empty($allUserAnswers[$qId]->answer_selected)) {
                $isCorrect = ($q->jawaban_benar === $allUserAnswers[$qId]->answer_selected);
                $totalScore += $isCorrect ? $skorBenar : $skorSalah;

                $correctRows[] = [
                    'registration_id' => $registration->id,
                    'question_id'     => $qId,
                    'answer_selected' => $allUserAnswers[$qId]->answer_selected,
                    'is_correct'      => $isCorrect,
                    'created_at'      => $allUserAnswers[$qId]->created_at ?? $now,
                    'updated_at'      => $now,
                ];
            } else {
                $totalScore += $skorKosong;

                $emptyRows[] = [
                    'registration_id' => $registration->id,
                    'question_id'     => $qId,
                    'answer_selected' => null,
                    'is_correct'      => null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }

        if (! empty($correctRows)) {
            ExamAnswer::upsert(
                $correctRows,
                ['registration_id', 'question_id'],
                ['answer_selected', 'is_correct', 'updated_at']
            );
        }
        if (! empty($emptyRows)) {
            ExamAnswer::upsert(
                $emptyRows,
                ['registration_id', 'question_id'],
                ['answer_selected', 'is_correct', 'updated_at']
            );
        }

        // ── Simpan hasil akhir — update row babak INI SAJA ──
        $examResult->update([
            'score'    => $totalScore,
            'end_time' => now(),
            'status'   => 'finished',
        ]);

        // Update kolom lama untuk backward-compat
        $registration->update(['nilai_cbt' => $totalScore]);

        return redirect()->route('dashboard')
            ->with('success', 'Ujian Selesai! Mohon menunggu pengumuman resmi, Terima kasih')
            ->withCookie(cookie()->forget('exam_access'));
    }

    // ============================================================
    // HELPER
    // ============================================================

    /**
     * Hitung delay bertingkat berdasarkan user yang sedang menunggu.
     * Setiap user aktif menambah 3 detik, maksimal 60 detik.
     */
    private function calculateDelay(): int
    {
        $counterKey = 'exam:queue:active_count';
        $count      = (int) Cache::get($counterKey, 0);

        // Tambah counter untuk user ini
        Cache::put($counterKey, $count + 1, now()->addMinutes(5));

        // 3 detik per user aktif, maksimal 60 detik
        return min(60, $count * 3);
    }

    /**
     * Resolve jadwal per babak.
     * @return array{0: ?string, 1: int}
     */
    private function resolveJadwal($competition, string $babak): array
    {
        $waktuMulai = match ($babak) {
            'final'     => $competition->waktu_pelaksanaan_final,
            'semifinal' => $competition->waktu_pelaksanaan_semifinal,
            default     => $competition->waktu_pelaksanaan,
        };

        $durasiMenit = match ($babak) {
            'final'     => $competition->durasi_menit_final     ?? $competition->durasi_menit ?? 120,
            'semifinal' => $competition->durasi_menit_semifinal ?? $competition->durasi_menit ?? 120,
            default     => $competition->durasi_menit ?? 120,
        };

        return [$waktuMulai, (int) $durasiMenit];
    }
}