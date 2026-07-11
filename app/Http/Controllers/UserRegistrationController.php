<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Competition;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'competition_id' => 'required|exists:competitions,id',
            'metode_pembayaran' => 'nullable|in:manual,gateway',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' // Max 2MB
        ]);

        $user = Auth::user();
        $newComp = Competition::findOrFail($request->competition_id);
        $today = Carbon::today();

        // 2. Cek Masa Pendaftaran Umum
        $isDateValid = $newComp->tanggal_mulai && $newComp->tanggal_selesai && $today->between($newComp->tanggal_mulai, $newComp->tanggal_selesai);
        if (!$newComp->is_active || !$isDateValid) {
            return back()->with('error', 'Pendaftaran untuk kompetisi ini sedang ditutup atau melewati tenggat waktu.');
        }

        // 3. Cek Harga Gelombang Aktif
        $activePrice = $newComp->active_price;
        if (is_null($activePrice)) {
            return back()->with('error', 'Maaf, belum ada gelombang pendaftaran yang aktif saat ini.');
        }

        // 4. Cek Duplikasi (Apakah sudah pernah mendaftar?)
        if (Registration::where('user_id', $user->id)->where('competition_id', $newComp->id)->exists()) {
            return back()->with('error', 'Anda sudah terdaftar di kompetisi ini.');
        }

        // 5. Cek Validasi Pembayaran (Jika Berbayar)
        if ($activePrice > 0) {
            $request->validate(
                ['metode_pembayaran' => 'required|in:manual,gateway'],
                ['metode_pembayaran.required' => 'Pilih metode pembayaran terlebih dahulu.']
            );

            // Jika pilih manual, bukti WAJIB ada
            if ($request->metode_pembayaran === 'manual') {
                $request->validate(
                    ['bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'],
                    ['bukti_pembayaran.required' => 'Bukti transfer wajib diunggah!']
                );
            }
        }

        // 6. Cek Bentrok Jadwal Pelaksanaan Ujian
        if ($newComp->waktu_pelaksanaan && $newComp->durasi_menit) {
            $newStart = Carbon::parse($newComp->waktu_pelaksanaan);
            
            $newEnd = $newStart->copy()->addMinutes((int)$newComp->durasi_menit);

            $myRegistrations = Registration::with('competition')
                ->where('user_id', $user->id)
                ->whereIn('status_pendaftaran', ['pending', 'verified'])
                ->get();

            foreach ($myRegistrations as $reg) {
                if ($reg->competition->waktu_pelaksanaan && $reg->competition->durasi_menit) {
                    $oldStart = Carbon::parse($reg->competition->waktu_pelaksanaan);
                    
                    // PERBAIKAN: Tambahkan (int) juga di sini
                    $oldEnd = $oldStart->copy()->addMinutes((int)$reg->competition->durasi_menit);

                    // Rumus Bentrok Waktu (Overlap)
                    if ($newStart->lt($oldEnd) && $newEnd->gt($oldStart)) {
                        return back()->with('error', 'Gagal mendaftar: Jadwal kompetisi ini BENTROK dengan lomba "' . $reg->competition->nama_lomba . '" yang sudah Anda ikuti.');
                    }
                }
            }
        }

        // 7. Tentukan Status Awal
        $statusPendaftaran = ($activePrice == 0) ? 'verified' : 'pending';
        $statusPembayaran  = ($activePrice == 0) ? 'paid' : 'unpaid';
        $orderId = 'UPC-' . strtoupper(Str::random(6)) . '-' . time();

        // ====================================================================
        // EKSEKUSI PEMISAHAN TABEL (Registrasi, Payment, dan Ujian)
        // ====================================================================

        // A. Simpan Data Induk Pendaftaran (Tabel registrations)
        $registration = Registration::create([
            'user_id' => $user->id,
            'competition_id' => $newComp->id,
            'status_pendaftaran' => $statusPendaftaran,
        ]);

        // B. Simpan Data Tagihan Keuangan (Tabel payments)
        $payment = $registration->payment()->create([
            'order_id' => $orderId,
            'amount' => $activePrice,
            'status' => $statusPembayaran,
            'payment_type' => $request->metode_pembayaran ?? 'free',
        ]);

        // C. Inisialisasi Lembar Hasil Ujian (Tabel exam_results)
        $registration->examResult()->create([
            'status' => 'not_started',
            'violation_count' => 0
        ]);

        // ====================================================================
        // EKSEKUSI PEMBAYARAN (MANUAL vs GATEWAY)
        // ====================================================================

        if ($activePrice > 0) {
            
            if ($request->metode_pembayaran === 'manual') {
                // Simpan Bukti Transfer menggunakan Spatie Media Library di relasi registration
                if ($request->hasFile('bukti_pembayaran')) {
                    $registration->addMediaFromRequest('bukti_pembayaran')->toMediaCollection('bukti_pembayaran_lomba');
                }
                return back()->with('success', 'Berhasil mendaftar! Bukti pembayaran Anda telah dikirim dan sedang menunggu verifikasi panitia.');
            
            } elseif ($request->metode_pembayaran === 'gateway') {
                // Integrasi Midtrans
                Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                Config::$isSanitized = true;
                Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $activePrice,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                    ],
                    'item_details' => [
                        [
                            'id' => 'COMP-'.$newComp->id,
                            'price' => (int) $activePrice,
                            'quantity' => 1,
                            'name' => substr('Tiket: '.$newComp->nama_lomba, 0, 50)
                        ]
                    ]
                ];

                // Request Token ke Midtrans
                $snapToken = Snap::getSnapToken($params);
                
                // Simpan token ke tabel payments
                $payment->update(['snap_token' => $snapToken]);

                // Arahkan ke halaman khusus pop-up Midtrans
                return redirect()->route('user.kompetisi.checkout', $registration->id);
            }
        }

        // Jika harga lomba gratis
        return back()->with('success', 'Berhasil mendaftar kompetisi secara gratis! Pendaftaran Anda otomatis divalidasi oleh sistem.');
    }
}