<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 2. Cek Masa Pendaftaran
        $isDateValid = $newComp->tanggal_mulai && $newComp->tanggal_selesai && $today->between($newComp->tanggal_mulai, $newComp->tanggal_selesai);
        if (!$newComp->is_active || !$isDateValid) {
            return back()->with('error', 'Pendaftaran untuk kompetisi ini sedang ditutup atau melewati tenggat waktu.');
        }

        // 3. Cek Duplikasi (Apakah sudah pernah mendaftar?)
        if (Registration::where('user_id', $user->id)->where('competition_id', $newComp->id)->exists()) {
            return back()->with('error', 'Anda sudah terdaftar di kompetisi ini.');
        }

        // 4. Cek Validasi Pembayaran (Jika Berbayar)
        if ($newComp->harga_pendaftaran > 0) {
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

        // 5. Cek Bentrok Jadwal Pelaksanaan Ujian
        if ($newComp->waktu_pelaksanaan && $newComp->durasi_menit) {
            $newStart = Carbon::parse($newComp->waktu_pelaksanaan);
            $newEnd = $newStart->copy()->addMinutes($newComp->durasi_menit);

            $myRegistrations = Registration::with('competition')
                ->where('user_id', $user->id)
                ->whereIn('status_pendaftaran', ['pending', 'verified'])
                ->get();

            foreach ($myRegistrations as $reg) {
                if ($reg->competition->waktu_pelaksanaan && $reg->competition->durasi_menit) {
                    $oldStart = Carbon::parse($reg->competition->waktu_pelaksanaan);
                    $oldEnd = $oldStart->copy()->addMinutes($reg->competition->durasi_menit);

                    // Rumus Bentrok Waktu (Overlap)
                    if ($newStart->lt($oldEnd) && $newEnd->gt($oldStart)) {
                        return back()->with('error', 'Gagal mendaftar: Jadwal kompetisi ini BENTROK dengan lomba "' . $reg->competition->nama_lomba . '" yang sudah Anda ikuti.');
                    }
                }
            }
        }

        // 6. Tentukan Status Pembayaran Awal
        // Jika lomba gratis, langsung anggap 'paid'
        $statusPembayaran = ($newComp->harga_pendaftaran == 0) ? 'paid' : 'unpaid';

        // 7. Simpan Data Pendaftaran ke Database
        $registration = Registration::create([
            'user_id' => $user->id,
            'competition_id' => $newComp->id,
            'status_pendaftaran' => 'pending',
            'status_pembayaran' => $statusPembayaran,
        ]);

        // 8. Eksekusi Pembayaran Berdasarkan Metode
        if ($newComp->harga_pendaftaran > 0) {
            
            if ($request->metode_pembayaran === 'manual') {
                // Simpan Bukti Transfer
                if ($request->hasFile('bukti_pembayaran')) {
                    $registration->addMediaFromRequest('bukti_pembayaran')->toMediaCollection('bukti_pembayaran');
                }
                return back()->with('success', 'Berhasil mendaftar! Bukti pembayaran Anda telah dikirim dan sedang menunggu verifikasi panitia.');
            
            } elseif ($request->metode_pembayaran === 'gateway') {
                // TODO: Integrasi Midtrans
                // Di sini nanti kita akan generate Snap Token dari Midtrans 
                // dan me-redirect user ke halaman pembayaran otomatis.
                return back()->with('success', 'Berhasil mendaftar! (Catatan: Sistem Payment Gateway sedang dalam tahap pengembangan).');
            }

        }

        // Jika gratis
        return back()->with('success', 'Berhasil mendaftar kompetisi secara gratis! Pendaftaran Anda sedang divalidasi oleh panitia.');
    }
}