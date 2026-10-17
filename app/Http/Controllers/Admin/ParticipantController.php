<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Registration;
use App\Models\Competition;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantController extends Controller
{
    // ====================================================================
    // AUTO-SUBMIT ON-ACCESS — per babak
    // ====================================================================

    /**
     * Auto-submit peserta yang waktunya habis tapi status masih in_progress.
     * Dicek PER BABAK (bukan hanya babak penyisihan).
     */
    private function autoSubmitExpiredForCompetition(Competition $competition): void
    {
        // Ambil exam_result yang masih in_progress di lomba ini
        $expired = ExamResult::where('status', 'in_progress')
            ->whereHas('registration', function ($q) use ($competition) {
                $q->where('competition_id', $competition->id);
            })
            ->with('registration')
            ->get();

        if ($expired->isEmpty()) {
            return;
        }

        foreach ($expired as $examResult) {
            $reg = $examResult->registration;
            if (! $reg) continue;

            // ── Cek apakah waktu babak INI sudah habis ──
            [$waktuMulai, $durasiMenit] = $this->resolveJadwal($competition, $examResult->babak);

            if (! $waktuMulai) continue;

            $waktuSelesai = Carbon::parse($waktuMulai)->addMinutes($durasiMenit);

            // Belum lewat + toleransi 2 menit → skip
            if (now()->lessThanOrEqualTo($waktuSelesai->copy()->addMinutes(2))) {
                continue;
            }

            $this->processAutoSubmit($reg, $competition, $examResult->babak);
        }
    }

    /**
     * Proses auto-submit 1 registrasi untuk babak tertentu.
     */
    private function processAutoSubmit(Registration $registration, Competition $competition, string $babak): void
    {
        // ── Cegah double submit ──
        $existing = $registration->examResultFor($babak);
        if ($existing && $existing->status === 'finished') {
            return;
        }

        // ══════════════════════════════════════════════════════════
        // AMBIL SOAL HANYA UNTUK BABAK INI
        // ══════════════════════════════════════════════════════════
        $dbQuestions = $competition->questions()
            ->where('babak', $babak)
            ->get()
            ->keyBy('id');

        if ($dbQuestions->isEmpty()) {
            return; // tidak ada soal → jangan auto-submit
        }

        // ══════════════════════════════════════════════════════════
        // AMBIL JAWABAN PESERTA — HANYA UNTUK SOAL BABAK INI
        // ══════════════════════════════════════════════════════════
        $allUserAnswers = ExamAnswer::where('registration_id', $registration->id)
            ->whereIn('question_id', $dbQuestions->keys())
            ->get()
            ->keyBy('question_id');

        $skorBenar  = (float) ($competition->nilai_benar  ?? 4.0);
        $skorSalah  = (float) ($competition->nilai_salah  ?? -1.0);
        $skorKosong = (float) ($competition->nilai_kosong ?? 0.0);

        $totalScore = 0;

        foreach ($dbQuestions as $qId => $q) {
            if (isset($allUserAnswers[$qId]) && ! empty($allUserAnswers[$qId]->answer_selected)) {
                $isCorrect = ($q->jawaban_benar === $allUserAnswers[$qId]->answer_selected);
                $allUserAnswers[$qId]->update(['is_correct' => $isCorrect]);
                $totalScore += $isCorrect ? $skorBenar : $skorSalah;
            } else {
                $totalScore += $skorKosong;
                ExamAnswer::updateOrCreate(
                    ['registration_id' => $registration->id, 'question_id' => $qId],
                    ['answer_selected' => null, 'is_correct' => null]
                );
            }
        }

        // ══════════════════════════════════════════════════════════
        // SIMPAN HASIL — per babak, TIDAK menimpa babak lain
        // ══════════════════════════════════════════════════════════
        $registration->examResults()->updateOrCreate(
            ['babak' => $babak],
            [
                'score'    => $totalScore,
                'end_time' => now(),
                'status'   => 'finished',
            ]
        );

        \Log::info('AUTO-SUBMIT (on-access)', [
            'registration_id' => $registration->id,
            'user'            => $registration->user->name ?? '-',
            'competition'     => $competition->nama_lomba,
            'babak'           => $babak,
            'total_soal'      => $dbQuestions->count(),
            'score'           => $totalScore,
        ]);
    }

    // ====================================================================
    // MANAJEMEN AKUN PESERTA
    // ====================================================================

    public function index(Request $request)
    {
        $query = User::where('email', '!=', 'admin@upc.com');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asal_sekolah', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        $peserta = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.peserta.index', compact('peserta'));
    }

    public function edit(User $user)
    {
        return view('admin.peserta.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'asal_sekolah'      => ['required', 'string', 'max:255'],
            'no_wa'             => ['required', 'string', 'max:20'],
            'status_verifikasi' => ['required', 'in:pending,verified,rejected'],
        ]);

        $user->update($request->only([
            'name', 'email', 'asal_sekolah', 'no_wa', 'status_verifikasi',
        ]));

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Data peserta berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Peserta berhasil dihapus dari sistem.');
    }

    public function resetRegistrations(User $user)
    {
        $registrations = Registration::where('user_id', $user->id)->get();

        foreach ($registrations as $reg) {
            if ($reg->hasMedia('bukti_pembayaran_lomba')) {
                $reg->clearMediaCollection('bukti_pembayaran_lomba');
            }
            if ($reg->hasMedia('bukti_pembayaran')) {
                $reg->clearMediaCollection('bukti_pembayaran');
            }

            $reg->delete(); // cascade → exam_results & exam_answers ikut terhapus
        }

        return back()->with(
            'success',
            '🛠️ DEBUG: Semua riwayat pendaftaran lomba, tagihan Midtrans, dan riwayat ujian CBT milik "'
            . $user->name . '" berhasil dihapus bersih!'
        );
    }

    // ====================================================================
    // EXPORT CSV — semua peserta (tanpa nilai)
    // ====================================================================

    public function export(): StreamedResponse
    {
        $peserta = Registration::with(['user', 'competition'])->get();

        $filename = "Data_Semua_Peserta_UPC_" . date('Y-m-d') . ".csv";

        return response()->streamDownload(function () use ($peserta) {
            if (ob_get_length()) ob_end_clean();

            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'ID Pendaftaran',
                'Nama Lengkap',
                'Email',
                'Asal Sekolah',
                'No. WA',
                'Kompetisi',
                'Status Pendaftaran',
            ]);

            foreach ($peserta as $data) {
                fputcsv($out, [
                    $data->id,
                    $data->user->name ?? '-',
                    $data->user->email ?? '-',
                    $data->user->asal_sekolah ?? '-',
                    $data->user->no_wa ?? '-',
                    $data->competition->nama_lomba ?? '-',
                    $data->status_pendaftaran ?? 'pending',
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ====================================================================
    // EXPORT PER KOMPETISI — filter babak via query string
    // ====================================================================

    public function exportByCompetition(Request $request, Competition $competition): StreamedResponse
    {
        // ── AUTO-SUBMIT ON-ACCESS ──
        $this->autoSubmitExpiredForCompetition($competition);

        // ── Babak dari query string (default: penyisihan) ──
        $babak = $request->query('babak', 'penyisihan');

        $registrations = Registration::where('competition_id', $competition->id)
            ->with([
                'user',
                'examResults' => fn ($q) => $q->where('babak', $babak),
            ])
            ->get();

        $filename = "Data_Peserta_" . Str::slug($competition->nama_lomba)
            . "_{$babak}_" . date('Y-m-d') . ".csv";

        return response()->streamDownload(function () use ($registrations, $babak) {
            if (ob_get_length()) ob_end_clean();

            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'ID Pendaftaran',
                'Nama Lengkap',
                'Email',
                'Asal Sekolah',
                'No. WA',
                'Status Pendaftaran',
                'Babak',
                'Nilai Akhir',
                'Waktu Mulai',
                'Waktu Selesai',
            ]);

            foreach ($registrations as $data) {
                $result = $data->examResultFor($babak);

                $waktuMulai = $result?->start_time
                    ? Carbon::parse($result->start_time)->format('Y-m-d H:i:s')
                    : '-';
                $waktuSelesai = $result?->end_time
                    ? Carbon::parse($result->end_time)->format('Y-m-d H:i:s')
                    : '-';

                fputcsv($out, [
                    $data->id,
                    $data->user->name ?? '-',
                    $data->user->email ?? '-',
                    $data->user->asal_sekolah ?? '-',
                    $data->user->no_wa ?? '-',
                    $data->status_pendaftaran ?? 'pending',
                    ucfirst($babak),
                    $result?->score ?? 0,
                    $waktuMulai,
                    $waktuSelesai,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

        // ====================================================================
    // RANKING — KUMULATIF per babak
    // Peserta yang sudah naik babak tetap tampil di babak sebelumnya.
    // ====================================================================
    public function ranking(Request $request, Competition $competition)
    {
        // ── AUTO-SUBMIT ON-ACCESS ──
        $this->autoSubmitExpiredForCompetition($competition);

        // ── Urutan babak ──
        $order = ['penyisihan' => 1, 'semifinal' => 2, 'final' => 3];

        // ── Babak tersedia di kompetisi ini ──
        $babakTersedia = $competition->questions()
            ->select('babak')
            ->distinct()
            ->pluck('babak')
            ->toArray();

        usort($babakTersedia, fn ($a, $b) => ($order[$a] ?? 99) <=> ($order[$b] ?? 99));

        if (empty($babakTersedia)) {
            $babakTersedia = ['penyisihan'];
        }

        // ── Babak aktif dari query string ──
        $babakAktif = $request->query('babak', $babakTersedia[0]);
        if (! in_array($babakAktif, $babakTersedia, true)) {
            $babakAktif = $babakTersedia[0];
        }

        // ══════════════════════════════════════════════════════════
        // KUNCI: babak aktif + semua babak SESUDAHNYA.
        // Peserta yang sudah naik ke semifinal/final TETAP muncul
        // di penyisihan (dan semifinal) karena posisinya "lebih tinggi".
        //
        // Contoh:
        // - Filter penyisihan → babak IN ('penyisihan', 'semifinal', 'final')
        // - Filter semifinal  → babak IN ('semifinal', 'final')
        // - Filter final      → babak IN ('final')
        // ══════════════════════════════════════════════════════════
        $orderAktif = $order[$babakAktif] ?? 1;
        $babakYangDitampilkan = array_keys(array_filter(
            $order,
            fn ($level) => $level >= $orderAktif
        ));

        // ── Ambil peserta ──
        $registrations = Registration::where('competition_id', $competition->id)
            ->where('status_pendaftaran', 'verified')
            ->whereIn('babak', $babakYangDitampilkan)   // ← filter kumulatif
            ->with([
                'user',
                // eager-load exam_results untuk babak aktif (untuk skor)
                'examResults' => fn ($q) => $q->where('babak', $babakAktif),
            ])
            ->get()
            ->sortByDesc(fn ($reg) => $reg->examResultFor($babakAktif)?->score ?? -999)
            ->values();

        // ── Konteks tambahan untuk header ──
        $totalSemuaPeserta = Registration::where('competition_id', $competition->id)
            ->where('status_pendaftaran', 'verified')
            ->count();

        return view('admin.kompetisi.ranking', compact(
            'competition',
            'registrations',
            'babakTersedia',
            'babakAktif',
            'totalSemuaPeserta',
            'babakYangDitampilkan'   // ← opsional, untuk badge di view
        ));
    }

    // ====================================================================
    // JAWABAN MENTAH PER PESERTA — per babak
    // ====================================================================

    public function showAnswers(Competition $competition, Registration $registration)
    {
        if ((int) $registration->competition_id !== (int) $competition->id) {
            abort(404, 'Peserta tidak terdaftar di lomba ini.');
        }

        $this->autoSubmitExpiredForCompetition($competition);
        $registration->refresh();

        // ── Babak dari query string (default: babak aktif peserta) ──
        $babakTersedia = $competition->questions()
            ->select('babak')->distinct()->pluck('babak')->toArray();

        $order = ['penyisihan' => 1, 'semifinal' => 2, 'final' => 3];
        usort($babakTersedia, fn ($a, $b) => ($order[$a] ?? 99) <=> ($order[$b] ?? 99));

        $babak = request()->query('babak', $registration->babak ?? ($babakTersedia[0] ?? 'penyisihan'));
        if (! in_array($babak, $babakTersedia, true) && ! empty($babakTersedia)) {
            $babak = $babakTersedia[0];
        }

        // ── Soal & jawaban HANYA babak ini ──
        $questions = $competition->questions()
            ->where('babak', $babak)
            ->orderBy('id')
            ->get();

        $answers = ExamAnswer::where('registration_id', $registration->id)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        $skorBenar  = (float) ($competition->nilai_benar  ?? 4.0);
        $skorSalah  = (float) ($competition->nilai_salah  ?? -1.0);
        $skorKosong = (float) ($competition->nilai_kosong ?? 0.0);

        $skorSementara = 0;

        $rows = $questions->map(function ($q, $index) use ($answers, $skorBenar, $skorSalah, $skorKosong, &$skorSementara) {
            $ans = $answers->get($q->id);
            $jawabanPeserta = $ans?->answer_selected;

            if ($jawabanPeserta === null || $jawabanPeserta === '') {
                $status = 'Kosong';
                $isCorrect = null;
                $skorSementara += $skorKosong;
            } else {
                $isCorrect = ($q->jawaban_benar === $jawabanPeserta);
                $status = $isCorrect ? 'Benar' : 'Salah';
                $skorSementara += $isCorrect ? $skorBenar : $skorSalah;
            }

            return [
                'no'              => $index + 1,
                'question_id'     => $q->id,
                'pertanyaan'      => $q->pertanyaan,
                'jawaban_benar'   => $q->jawaban_benar,
                'jawaban_peserta' => $jawabanPeserta,
                'is_correct'      => $isCorrect,
                'status'          => $status,
            ];
        });

        $totalSoal   = $questions->count();
        $totalBenar  = $rows->where('status', 'Benar')->count();
        $totalSalah  = $rows->where('status', 'Salah')->count();
        $totalKosong = $rows->where('status', 'Kosong')->count();

        $examResult  = $registration->examResultFor($babak);
        $skorFinal   = $examResult?->score ?? 0;

        $statusUjian = match ($examResult?->status) {
            'not_started' => 'Belum Mulai',
            'in_progress' => 'Sedang Ujian',
            'finished'    => 'Selesai',
            default       => 'Belum Mulai',
        };

        return view('admin.kompetisi.peserta-jawaban', compact(
            'competition',
            'registration',
            'rows',
            'totalSoal',
            'totalBenar',
            'totalSalah',
            'totalKosong',
            'skorSementara',
            'skorFinal',
            'statusUjian',
            'babak',
            'babakTersedia'
        ));
    }

    // ====================================================================
    // EXPORT CSV — jawaban semua peserta (per babak)
    // ====================================================================

    public function exportAnswersCsv(Request $request, Competition $competition): StreamedResponse
    {
        $this->autoSubmitExpiredForCompetition($competition);

        $babak = $request->query('babak', 'penyisihan');

        $questions = $competition->questions()
            ->where('babak', $babak)
            ->orderBy('id')
            ->get();

        $skorBenar  = (float) ($competition->nilai_benar  ?? 4.0);
        $skorSalah  = (float) ($competition->nilai_salah  ?? -1.0);
        $skorKosong = (float) ($competition->nilai_kosong ?? 0.0);

        $registrations = Registration::with([
                'user',
                'examResults' => fn ($q) => $q->where('babak', $babak),
            ])
            ->where('competition_id', $competition->id)
            ->orderBy('id')
            ->get();

        $allAnswers = ExamAnswer::whereIn('registration_id', $registrations->pluck('id'))
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->groupBy('registration_id');

        $filename = 'jawaban-mentah-' . Str::slug($competition->nama_lomba)
            . "_{$babak}-" . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($questions, $registrations, $allAnswers, $skorBenar, $skorSalah, $skorKosong, $babak) {
            if (ob_get_length()) ob_end_clean();

            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            $header = [
                'No',
                'Nama Peserta',
                'Email',
                'Asal Sekolah',
                'Status Pendaftaran',
                'Babak',
                'Status Ujian',
                'Skor Final',
                'Skor Sementara',
                'Selisih',
                'Waktu Mulai',
                'Waktu Selesai',
                'Jumlah Dijawab',
            ];

            foreach ($questions as $i => $q) {
                $no = $i + 1;
                $header[] = "Soal {$no} (Kunci: {$q->jawaban_benar})";
            }

            $header[] = 'Total Benar';
            $header[] = 'Total Salah';
            $header[] = 'Total Kosong';

            fputcsv($out, $header);

            $noUrut = 1;
            foreach ($registrations as $reg) {
                $jawabanPeserta = $allAnswers->get($reg->id, collect())->keyBy('question_id');
                $result = $reg->examResultFor($babak);

                $benar  = 0;
                $salah  = 0;
                $kosong = 0;
                $skorSementara = 0;

                $waktuMulai = $result?->start_time
                    ? Carbon::parse($result->start_time)->format('Y-m-d H:i:s') : '-';
                $waktuSelesai = $result?->end_time
                    ? Carbon::parse($result->end_time)->format('Y-m-d H:i:s') : '-';

                $statusUjian = match ($result?->status) {
                    'not_started' => 'Belum Mulai',
                    'in_progress' => 'Sedang Ujian',
                    'finished'    => 'Selesai',
                    default       => 'Belum Mulai',
                };

                $jumlahDijawab = $jawabanPeserta->filter(
                    fn ($ans) => ! empty($ans->answer_selected)
                )->count();

                $rowJawaban = [];
                foreach ($questions as $q) {
                    $ans     = $jawabanPeserta->get($q->id);
                    $jawaban = $ans?->answer_selected;

                    if ($jawaban === null || $jawaban === '') {
                        $rowJawaban[] = '-';
                        $kosong++;
                        $skorSementara += $skorKosong;
                    } else {
                        $rowJawaban[] = $jawaban;
                        $isCorrect = ($q->jawaban_benar === $jawaban);
                        if ($isCorrect) {
                            $benar++;
                            $skorSementara += $skorBenar;
                        } else {
                            $salah++;
                            $skorSementara += $skorSalah;
                        }
                    }
                }

                $skorFinal = $result?->score ?? 0;

                $row = array_merge([
                    $noUrut++,
                    $reg->user->name ?? '-',
                    $reg->user->email ?? '-',
                    $reg->user->asal_sekolah ?? '-',
                    strtoupper($reg->status_pendaftaran ?? 'pending'),
                    ucfirst($babak),
                    $statusUjian,
                    $skorFinal,
                    $skorSementara,
                    $skorFinal - $skorSementara,
                    $waktuMulai,
                    $waktuSelesai,
                    $jumlahDijawab,
                ], $rowJawaban);

                $row[] = $benar;
                $row[] = $salah;
                $row[] = $kosong;

                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ====================================================================
    // EXPORT CSV — jawaban 1 peserta (per babak)
    // ====================================================================

    public function exportSingleAnswersCsv(Request $request, Competition $competition, Registration $registration): StreamedResponse
    {
        if ((int) $registration->competition_id !== (int) $competition->id) {
            abort(404);
        }

        $this->autoSubmitExpiredForCompetition($competition);
        $registration->refresh();

        $babak = $request->query('babak', $registration->babak ?? 'penyisihan');

        $questions = $competition->questions()
            ->where('babak', $babak)
            ->orderBy('id')
            ->get();

        $answers = ExamAnswer::where('registration_id', $registration->id)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        $examResult = $registration->examResultFor($babak);

        $filename = 'jawaban-' . Str::slug($registration->user->name ?? 'peserta')
            . "_{$babak}-" . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($competition, $registration, $questions, $answers, $examResult, $babak) {
            if (ob_get_length()) ob_end_clean();

            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            $waktuMulai = $examResult?->start_time
                ? Carbon::parse($examResult->start_time)->format('Y-m-d H:i:s') : '-';
            $waktuSelesai = $examResult?->end_time
                ? Carbon::parse($examResult->end_time)->format('Y-m-d H:i:s') : '-';

            $statusUjian = match ($examResult?->status) {
                'not_started' => 'Belum Mulai',
                'in_progress' => 'Sedang Ujian',
                'finished'    => 'Selesai',
                default       => 'Belum Mulai',
            };

            fputcsv($out, ['Nama', $registration->user->name ?? '-']);
            fputcsv($out, ['Email', $registration->user->email ?? '-']);
            fputcsv($out, ['Asal Sekolah', $registration->user->asal_sekolah ?? '-']);
            fputcsv($out, ['Lomba', $competition->nama_lomba]);
            fputcsv($out, ['Babak', ucfirst($babak)]);
            fputcsv($out, ['Status Ujian', $statusUjian]);
            fputcsv($out, ['Skor Final', $examResult?->score ?? 0]);
            fputcsv($out, ['Waktu Mulai', $waktuMulai]);
            fputcsv($out, ['Waktu Selesai', $waktuSelesai]);
            fputcsv($out, []);

            fputcsv($out, ['No', 'Question ID', 'Pertanyaan', 'Kunci Jawaban', 'Jawaban Peserta', 'Status']);

            $no = 1;
            foreach ($questions as $q) {
                $ans     = $answers->get($q->id);
                $jawaban = $ans?->answer_selected;

                if ($jawaban === null || $jawaban === '') {
                    $status = 'Kosong';
                } else {
                    $status = ($q->jawaban_benar === $jawaban) ? 'Benar' : 'Salah';
                }

                fputcsv($out, [
                    $no++,
                    $q->id,
                    strip_tags($q->pertanyaan),
                    $q->jawaban_benar,
                    $jawaban ?? '-',
                    $status,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ====================================================================
    // HELPER: jadwal per babak
    // ====================================================================

    private function resolveJadwal(Competition $competition, string $babak): array
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