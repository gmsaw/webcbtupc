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
    // AUTO-SUBMIT ON-ACCESS
    // ====================================================================

    /**
     * Auto-submit semua peserta yang waktu ujiannya sudah habis
     * tapi statusnya masih in_progress.
     *
     * Dipanggil sebelum menampilkan data (showAnswers, export, ranking).
     */
    private function autoSubmitExpiredForCompetition(Competition $competition): void
    {
        // Wajib ada waktu_pelaksanaan & durasi_menit
        if (!$competition->waktu_pelaksanaan || !$competition->durasi_menit) {
            return;
        }

        $waktuMulai   = Carbon::parse($competition->waktu_pelaksanaan);
        $durasiMenit  = (int) $competition->durasi_menit;
        $waktuSelesai = $waktuMulai->copy()->addMinutes($durasiMenit);

        // Belum lewat + toleransi 2 menit → tidak perlu auto-submit
        if (now()->lessThanOrEqualTo($waktuSelesai->copy()->addMinutes(2))) {
            return;
        }

        // Cari exam_results yang masih in_progress di lomba ini
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
            if ($examResult->registration) {
                $this->processAutoSubmit($examResult->registration, $competition);
            }
        }
    }

    /**
     * Proses auto-submit 1 registrasi:
     * - Hitung nilai dari jawaban tersimpan
     * - Update exam_results.status = finished
     */
    private function processAutoSubmit(Registration $registration, Competition $competition): void
    {
        // Cegah double submit
        if ($registration->examResult && $registration->examResult->status === 'finished') {
            return;
        }

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
                $userAns   = $allUserAnswers[$qId];
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

        // Optional: log biar kelihatan di laravel.log
        \Log::info('AUTO-SUBMIT (on-access)', [
            'registration_id' => $registration->id,
            'user'            => $registration->user->name ?? '-',
            'competition'     => $competition->nama_lomba,
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

            $reg->delete();
        }

        return back()->with(
            'success',
            '🛠️ DEBUG: Semua riwayat pendaftaran lomba, tagihan Midtrans, dan riwayat ujian CBT milik "' . $user->name . '" berhasil dihapus bersih!'
        );
    }

    // ====================================================================
    // EXPORT CSV
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

    public function exportByCompetition(Competition $competition): StreamedResponse
    {
        // ── AUTO-SUBMIT ON-ACCESS ──
        $this->autoSubmitExpiredForCompetition($competition);

        $registrations = Registration::where('competition_id', $competition->id)
            ->with(['user', 'examResult'])
            ->get();

        $filename = "Data_Peserta_" . Str::slug($competition->nama_lomba) . "_" . date('Y-m-d') . ".csv";

        return response()->streamDownload(function () use ($registrations) {
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
                'Nilai Akhir',
                'Waktu Mulai',
                'Waktu Selesai',
            ]);

            foreach ($registrations as $data) {
                $waktuMulai = $data->examResult?->start_time
                    ? Carbon::parse($data->examResult->start_time)->format('Y-m-d H:i:s')
                    : '-';
                $waktuSelesai = $data->examResult?->end_time
                    ? Carbon::parse($data->examResult->end_time)->format('Y-m-d H:i:s')
                    : '-';

                fputcsv($out, [
                    $data->id,
                    $data->user->name ?? '-',
                    $data->user->email ?? '-',
                    $data->user->asal_sekolah ?? '-',
                    $data->user->no_wa ?? '-',
                    $data->status_pendaftaran ?? 'pending',
                    $data->examResult?->score ?? 0,
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

    public function ranking(Competition $competition)
    {
        // ── AUTO-SUBMIT ON-ACCESS ──
        $this->autoSubmitExpiredForCompetition($competition);

        $registrations = Registration::where('competition_id', $competition->id)
            ->where('status_pendaftaran', 'verified')
            ->with(['user', 'examResult'])
            ->get()
            ->sortByDesc(function ($reg) {
                return $reg->examResult?->score ?? 0;
            })
            ->values();

        return view('admin.kompetisi.ranking', compact('competition', 'registrations'));
    }

    // ====================================================================
    // JAWABAN MENTAH PER PESERTA
    // ====================================================================

    public function showAnswers(Competition $competition, Registration $registration)
    {
        if ((int) $registration->competition_id !== (int) $competition->id) {
            abort(404, 'Peserta tidak terdaftar di lomba ini.');
        }

        // ── AUTO-SUBMIT ON-ACCESS ──
        $this->autoSubmitExpiredForCompetition($competition);

        // Refresh data setelah auto-submit
        $registration->refresh();

        $questions = $competition->questions()->orderBy('id')->get();

        $answers = ExamAnswer::where('registration_id', $registration->id)
            ->get()
            ->keyBy('question_id');

        // Aturan nilai
        $skorBenar  = (float) ($competition->nilai_benar  ?? 4.0);
        $skorSalah  = (float) ($competition->nilai_salah  ?? -1.0);
        $skorKosong = (float) ($competition->nilai_kosong ?? 0.0);

        $skorSementara = 0;

        // ── HITUNG LIVE ──
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

        $skorFinal = $registration->examResult?->score ?? 0;

        $statusUjian = match ($registration->examResult?->status) {
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
            'statusUjian'
        ));
    }

    public function exportAnswersCsv(Competition $competition): StreamedResponse
    {
        // ── AUTO-SUBMIT ON-ACCESS ──
        $this->autoSubmitExpiredForCompetition($competition);

        $questions = $competition->questions()->orderBy('id')->get();

        $skorBenar  = (float) ($competition->nilai_benar  ?? 4.0);
        $skorSalah  = (float) ($competition->nilai_salah  ?? -1.0);
        $skorKosong = (float) ($competition->nilai_kosong ?? 0.0);

        $registrations = Registration::with(['user', 'examResult'])
            ->where('competition_id', $competition->id)
            ->orderBy('id')
            ->get();

        $allAnswers = ExamAnswer::whereIn('registration_id', $registrations->pluck('id'))
            ->get()
            ->groupBy('registration_id');

        $filename = 'jawaban-mentah-' . Str::slug($competition->nama_lomba) . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($questions, $registrations, $allAnswers, $skorBenar, $skorSalah, $skorKosong) {
            if (ob_get_length()) ob_end_clean();

            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            // ── HEADER ──
            $header = [
                'No',
                'Nama Peserta',
                'Email',
                'Asal Sekolah',
                'Status Pendaftaran',
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

            // ── BARIS PER PESERTA ──
            $noUrut = 1;
            foreach ($registrations as $reg) {
                $jawabanPeserta = $allAnswers->get($reg->id, collect())->keyBy('question_id');

                $benar  = 0;
                $salah  = 0;
                $kosong = 0;
                $skorSementara = 0;

                $waktuMulai = $reg->examResult?->start_time
                    ? Carbon::parse($reg->examResult->start_time)->format('Y-m-d H:i:s')
                    : '-';
                $waktuSelesai = $reg->examResult?->end_time
                    ? Carbon::parse($reg->examResult->end_time)->format('Y-m-d H:i:s')
                    : '-';

                $statusUjian = match ($reg->examResult?->status) {
                    'not_started' => 'Belum Mulai',
                    'in_progress' => 'Sedang Ujian',
                    'finished'    => 'Selesai',
                    default       => 'Belum Mulai',
                };

                $jumlahDijawab = $jawabanPeserta->filter(function ($ans) {
                    return !empty($ans->answer_selected);
                })->count();

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

                $skorFinal = $reg->examResult?->score ?? 0;

                $row = array_merge([
                    $noUrut++,
                    $reg->user->name ?? '-',
                    $reg->user->email ?? '-',
                    $reg->user->asal_sekolah ?? '-',
                    strtoupper($reg->status_pendaftaran ?? 'pending'),
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

    public function exportSingleAnswersCsv(Competition $competition, Registration $registration): StreamedResponse
    {
        if ((int) $registration->competition_id !== (int) $competition->id) {
            abort(404);
        }

        // ── AUTO-SUBMIT ON-ACCESS ──
        $this->autoSubmitExpiredForCompetition($competition);
        $registration->refresh();

        $questions = $competition->questions()->orderBy('id')->get();

        $answers = ExamAnswer::where('registration_id', $registration->id)
            ->get()
            ->keyBy('question_id');

        $filename = 'jawaban-' . Str::slug($registration->user->name ?? 'peserta') . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($competition, $registration, $questions, $answers) {
            if (ob_get_length()) ob_end_clean();

            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            $waktuMulai = $registration->examResult?->start_time
                ? Carbon::parse($registration->examResult->start_time)->format('Y-m-d H:i:s')
                : '-';
            $waktuSelesai = $registration->examResult?->end_time
                ? Carbon::parse($registration->examResult->end_time)->format('Y-m-d H:i:s')
                : '-';

            $statusUjian = match ($registration->examResult?->status) {
                'not_started' => 'Belum Mulai',
                'in_progress' => 'Sedang Ujian',
                'finished'    => 'Selesai',
                default       => 'Belum Mulai',
            };

            // ── INFO PESERTA ──
            fputcsv($out, ['Nama', $registration->user->name ?? '-']);
            fputcsv($out, ['Email', $registration->user->email ?? '-']);
            fputcsv($out, ['Asal Sekolah', $registration->user->asal_sekolah ?? '-']);
            fputcsv($out, ['Lomba', $competition->nama_lomba]);
            fputcsv($out, ['Status Ujian', $statusUjian]);
            fputcsv($out, ['Skor Final', $registration->examResult?->score ?? 0]);
            fputcsv($out, ['Waktu Mulai', $waktuMulai]);
            fputcsv($out, ['Waktu Selesai', $waktuSelesai]);
            fputcsv($out, []);

            // ── HEADER ──
            fputcsv($out, ['No', 'Question ID', 'Pertanyaan', 'Kunci Jawaban', 'Jawaban Peserta', 'Status']);

            // ── BARIS PER SOAL ──
            $no = 1;
            foreach ($questions as $q) {
                $ans     = $answers->get($q->id);
                $jawaban = $ans?->answer_selected;

                if ($jawaban === null || $jawaban === '') {
                    $status = 'Kosong';
                } else {
                    $isCorrect = ($q->jawaban_benar === $jawaban);
                    $status = $isCorrect ? 'Benar' : 'Salah';
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
}