<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Competition;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // ═══════════════════════════════════════════════════════════
        // 1. VALIDASI INPUT DASAR
        // ═══════════════════════════════════════════════════════════
        $request->validate([
            'competition_id'    => 'required|exists:competitions,id',
            'metode_pembayaran' => 'nullable|in:manual,gateway',
            'bukti_pembayaran'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user   = Auth::user();
        $newComp = Competition::findOrFail($request->competition_id);
        $today  = Carbon::today();

        // ═══════════════════════════════════════════════════════════
        // 2. CEK MASA PENDAFTARAN
        // ═══════════════════════════════════════════════════════════
        $isDateValid = $newComp->tanggal_mulai
            && $newComp->tanggal_selesai
            && $today->between($newComp->tanggal_mulai, $newComp->tanggal_selesai);

        if (! $newComp->is_active || ! $isDateValid) {
            return back()->with('error', 'Pendaftaran untuk kompetisi ini sedang ditutup atau melewati tenggat waktu.');
        }

        // ═══════════════════════════════════════════════════════════
        // 3. CEK HARGA GELOMBANG AKTIF
        // ═══════════════════════════════════════════════════════════
        $activePrice = $newComp->active_price;
        if (is_null($activePrice)) {
            return back()->with('error', 'Maaf, belum ada gelombang pendaftaran yang aktif saat ini.');
        }

        // ═══════════════════════════════════════════════════════════
        // 4. CEK DUPLIKASI
        // ═══════════════════════════════════════════════════════════
        if (Registration::where('user_id', $user->id)
                ->where('competition_id', $newComp->id)
                ->exists()) {
            return back()->with('error', 'Anda sudah terdaftar di kompetisi ini.');
        }

        // ═══════════════════════════════════════════════════════════
        // 5. VALIDASI PEMBAYARAN (JIKA BERBAYAR)
        // ═══════════════════════════════════════════════════════════
        if ($activePrice > 0) {
            $request->validate(
                ['metode_pembayaran' => 'required|in:manual,gateway'],
                ['metode_pembayaran.required' => 'Pilih metode pembayaran terlebih dahulu.']
            );

            if ($request->metode_pembayaran === 'manual') {
                $request->validate(
                    ['bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'],
                    ['bukti_pembayaran.required' => 'Bukti transfer wajib diunggah!']
                );
            }
        }

        // ═══════════════════════════════════════════════════════════
        // 6. CEK BENTROK JADWAL PELAKSANAAN
        // ═══════════════════════════════════════════════════════════
        if ($newComp->waktu_pelaksanaan && $newComp->durasi_menit) {
            $newStart = Carbon::parse($newComp->waktu_pelaksanaan);
            $newEnd   = $newStart->copy()->addMinutes((int) $newComp->durasi_menit);

            $myRegistrations = Registration::with('competition')
                ->where('user_id', $user->id)
                ->whereIn('status_pendaftaran', ['pending', 'verified'])
                ->get();

            foreach ($myRegistrations as $reg) {
                $comp = $reg->competition;
                if (! $comp || ! $comp->waktu_pelaksanaan || ! $comp->durasi_menit) {
                    continue;
                }

                $oldStart = Carbon::parse($comp->waktu_pelaksanaan);
                $oldEnd   = $oldStart->copy()->addMinutes((int) $comp->durasi_menit);

                // Overlap: newStart < oldEnd && newEnd > oldStart
                if ($newStart->lt($oldEnd) && $newEnd->gt($oldStart)) {
                    return back()->with(
                        'error',
                        'Gagal mendaftar: Jadwal kompetisi ini BENTROK dengan lomba "'
                        . $comp->nama_lomba . '" yang sudah Anda ikuti.'
                    );
                }
            }
        }

        // ═══════════════════════════════════════════════════════════
        // 7. TENTUKAN STATUS AWAL
        // ═══════════════════════════════════════════════════════════
        $statusPendaftaran = ($activePrice == 0) ? 'verified' : 'pending';
        $statusPembayaran  = ($activePrice == 0) ? 'paid'     : 'unpaid';
        $orderId           = 'UPC-' . strtoupper(Str::random(6)) . '-' . time();

        // ═══════════════════════════════════════════════════════════
        // 8. SIMPAN DATA — DALAM TRANSAKSI
        // ═══════════════════════════════════════════════════════════
        DB::beginTransaction();

        try {
            // ── A. Registration ──
            $registration = Registration::create([
                'user_id'            => $user->id,
                'competition_id'     => $newComp->id,
                'status_pendaftaran' => $statusPendaftaran,
                'babak'              => 'penyisihan', // default babak pertama
            ]);

            // ── B. Payment ──
            $payment = $registration->payment()->create([
                'order_id'       => $orderId,
                'amount'         => $activePrice,
                'status'         => $statusPembayaran,
                'payment_type'   => $request->metode_pembayaran ?? 'free',
            ]);

            // ── C. Exam Result — babak penyisihan (multi-babak ready) ──
            // Pakai relasi hasMany examResults() karena 1 registration bisa punya
            // banyak row exam_results (penyisihan, semifinal, final).
            $registration->examResults()->create([
                'babak'           => 'penyisihan',
                'status'          => 'not_started',
                'violation_count' => 0,
            ]);

            // ── D. Upload bukti pembayaran (manual) ──
            if ($activePrice > 0 && $request->metode_pembayaran === 'manual') {
                if ($request->hasFile('bukti_pembayaran')) {
                    $registration->addMediaFromRequest('bukti_pembayaran')
                        ->toMediaCollection('bukti_pembayaran_lomba');
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal mendaftar kompetisi', [
                'user_id'        => $user->id,
                'competition_id' => $newComp->id,
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
            ]);

            return back()->with(
                'error',
                'Terjadi kesalahan saat menyimpan pendaftaran. Silakan coba lagi.'
            );
        }

        // ═══════════════════════════════════════════════════════════
        // 9. HANDLE PEMBAYARAN
        // ═══════════════════════════════════════════════════════════

        // ── Manual: selesai, tinggal menunggu verifikasi ──
        if ($activePrice > 0 && $request->metode_pembayaran === 'manual') {
            return back()->with(
                'success',
                'Berhasil mendaftar! Bukti pembayaran Anda telah dikirim dan sedang menunggu verifikasi panitia.'
            );
        }

        // ── Gateway (Midtrans) ──
        if ($activePrice > 0 && $request->metode_pembayaran === 'gateway') {
            try {
                Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
                Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                Config::$isSanitized  = true;
                Config::$is3ds        = true;

                $params = [
                    'transaction_details' => [
                        'order_id'     => $orderId,
                        'gross_amount' => (int) $activePrice,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email'      => $user->email,
                    ],
                    'item_details' => [
                        [
                            'id'       => 'COMP-' . $newComp->id,
                            'price'    => (int) $activePrice,
                            'quantity' => 1,
                            'name'     => substr('Tiket: ' . $newComp->nama_lomba, 0, 50),
                        ],
                    ],
                ];

                $snapToken = Snap::getSnapToken($params);

                $payment->update(['snap_token' => $snapToken]);

                return redirect()->route('user.kompetisi.checkout', $registration->id);

            } catch (\Throwable $e) {
                Log::error('Midtrans Snap error', [
                    'registration_id' => $registration->id,
                    'order_id'        => $orderId,
                    'error'           => $e->getMessage(),
                ]);

                return redirect()
                    ->route('user.kompetisi.checkout', $registration->id)
                    ->with('error', 'Pendaftaran tersimpan, tetapi gagal membuat sesi pembayaran otomatis. Silakan coba lagi atau hubungi panitia.');
            }
        }

        // ── Gratis ──
        return back()->with(
            'success',
            'Berhasil mendaftar kompetisi secara gratis! Pendaftaran Anda otomatis divalidasi oleh sistem.'
        );
    }
}