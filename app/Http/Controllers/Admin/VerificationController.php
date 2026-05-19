<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // 1. Menampilkan Halaman Antrean Verifikasi
    public function index()
    {
        // Mengambil data pendaftaran (Registration) beserta relasi User dan Lomba-nya
        // Logika orderByRaw memastikan status 'pending' selalu muncul di urutan teratas
        $registrations = Registration::with(['user', 'competition'])
            ->orderByRaw("status_pendaftaran = 'pending' DESC")
            ->latest()
            ->paginate(15);

        // Arahkan ke view admin/verifikasi/index.blade.php
        return view('admin.verifikasi.index', compact('registrations'));
    }

    // 2. Memproses Persetujuan / Penolakan Pendaftaran
    public function update(Request $request, Registration $registration)
    {
        // Validasi input dari tombol (verified, rejected, atau kembalikan ke pending)
        $request->validate([
            'status' => 'required|in:pending,verified,rejected'
        ]);

        // Update status pendaftarannya
        $registration->status_pendaftaran = $request->status;

        // Logika Cerdas Pembayaran: 
        // Jika Admin menyetujui (verified) lomba yang berbayar, otomatis anggap sudah LUNAS (paid)
        if ($request->status === 'verified' && $registration->competition->harga_pendaftaran > 0) {
            $registration->status_pembayaran = 'paid';
        } 
        // Jika statusnya dikembalikan atau ditolak, kembalikan status pembayaran ke belum lunas
        elseif ($request->status !== 'verified' && $registration->competition->harga_pendaftaran > 0) {
            $registration->status_pembayaran = 'unpaid';
        }

        // Simpan perubahan ke database
        $registration->save();

        // Pesan notifikasi dinamis berdasarkan aksi Admin
        $pesan = '';
        if ($request->status === 'verified') {
            $pesan = 'Pendaftaran peserta ' . $registration->user->name . ' berhasil disetujui!';
        } elseif ($request->status === 'rejected') {
            $pesan = 'Pendaftaran peserta ' . $registration->user->name . ' telah ditolak.';
        } else {
            $pesan = 'Status pendaftaran dikembalikan ke Antrean (Pending).';
        }

        return redirect()->back()->with('success', $pesan);
    }
}