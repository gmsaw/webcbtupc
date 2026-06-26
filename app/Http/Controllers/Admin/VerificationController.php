<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Registration;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * 1. Menampilkan Halaman Utama Verifikasi
     * Menampilkan daftar lomba dengan jumlah pendaftar
     */
    public function index()
    {
        // Mengambil semua lomba dan menghitung jumlah pendaftar per lomba
        $competitions = Competition::withCount('registrations')->get();
        
        return view('admin.verifikasi.index', compact('competitions'));
    }

    /**
     * 2. Menampilkan Daftar Peserta Berdasarkan Lomba
     * Menampilkan peserta yang mendaftar di lomba tertentu saja.
     */
    public function show(Competition $competition)
    {
        // Mengambil pendaftar untuk lomba spesifik ini dengan relasi lengkap
        $registrations = Registration::where('competition_id', $competition->id)
            ->with(['user', 'competition'])
            ->orderByRaw("status_pendaftaran = 'pending' DESC")
            ->latest()
            ->paginate(20);

        return view('admin.verifikasi.show', compact('competition', 'registrations'));
    }

    /**
     * 3. Memproses Persetujuan / Penolakan Pendaftaran (via PUT/PATCH)
     * Mendukung update status pendaftaran dan pembayaran otomatis
     */
    public function update(Request $request, Registration $registration)
    {
        // Validasi input dari tombol (pending, verified, rejected)
        $request->validate([
            'status' => 'required|in:pending,verified,rejected'
        ]);

        // Update status pendaftaran
        $registration->status_pendaftaran = $request->status;

        // Logika Cerdas Pembayaran Otomatis
        // Memastikan tidak error jika biaya pendaftaran adalah 0 atau null
        $harga = $registration->competition->harga_pendaftaran ?? 
                 $registration->competition->biaya_pendaftaran ?? 0;
        
        // Jika Admin menyetujui (verified) lomba yang berbayar, otomatis anggap sudah LUNAS (paid)
        if ($request->status === 'verified' && $harga > 0) {
            $registration->status_pembayaran = 'paid';
        } 
        // Jika statusnya dikembalikan atau ditolak, kembalikan status pembayaran ke belum lunas
        elseif ($request->status !== 'verified' && $harga > 0) {
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
            $pesan = 'Status pendaftaran ' . $registration->user->name . ' dikembalikan ke Antrean (Pending).';
        }

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * 4. Menghapus (Menolak) Pendaftaran Peserta
     * Mendukung penghapusan file bukti pembayaran
     */
    public function destroy(Registration $registration)
    {
        // 1. (Opsional) Hapus file bukti pembayaran dari server agar storage tidak penuh
        if ($registration->hasMedia('bukti_pembayaran_lomba')) {
            $registration->clearMediaCollection('bukti_pembayaran_lomba');
        }

        // 2. Hapus data pendaftaran
        $registration->delete();

        // 3. Kembalikan ke halaman verifikasi dengan pesan sukses
        return back()->with('success', 'Pendaftaran berhasil ditolak dan dihapus. Peserta kini dapat mendaftar ulang.');
    }

    /**
     * 5. Fitur Tambahan: Verifikasi Massal (Bulk Verification)
     * Memverifikasi beberapa pendaftaran sekaligus
     */
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:registrations,id',
            'status' => 'required|in:verified,rejected'
        ]);

        $registrations = Registration::whereIn('id', $request->registration_ids)->get();
        $count = 0;

        foreach ($registrations as $registration) {
            $registration->status_pendaftaran = $request->status;
            
            // Logika pembayaran otomatis
            $harga = $registration->competition->harga_pendaftaran ?? 
                     $registration->competition->biaya_pendaftaran ?? 0;
            
            if ($request->status === 'verified' && $harga > 0) {
                $registration->status_pembayaran = 'paid';
            } elseif ($request->status !== 'verified' && $harga > 0) {
                $registration->status_pembayaran = 'unpaid';
            }
            
            $registration->save();
            $count++;
        }

        return back()->with('success', "Berhasil memverifikasi {$count} pendaftaran!");
    }

    /**
     * 6. Fitur Tambahan: Export Data Pendaftaran
     * Mengexport data pendaftaran ke format Excel/CSV
     */
    public function export(Competition $competition)
    {
        $registrations = Registration::where('competition_id', $competition->id)
            ->with(['user'])
            ->get();

        // Logika export menggunakan Maatwebsite Excel
        // return Excel::download(new RegistrationsExport($registrations), 'pendaftaran.xlsx');
        
        // Sementara redirect dengan pesan
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }

    /**
     * 7. Fitur Tambahan: Statistik Verifikasi
     * Menampilkan statistik verifikasi per lomba
     */
    public function statistics()
    {
        $stats = Competition::withCount([
            'registrations as total_pendaftar',
            'registrations as pending_count' => function ($query) {
                $query->where('status_pendaftaran', 'pending');
            },
            'registrations as verified_count' => function ($query) {
                $query->where('status_pendaftaran', 'verified');
            },
            'registrations as rejected_count' => function ($query) {
                $query->where('status_pendaftaran', 'rejected');
            }
        ])->get();

        return view('admin.verifikasi.statistik', compact('stats'));
    }

    /**
     * 8. Fitur Tambahan: Cari Peserta
     * Mencari peserta berdasarkan nama atau email
     */
    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|min:2'
        ]);

        $keyword = $request->keyword;
        
        $registrations = Registration::whereHas('user', function ($query) use ($keyword) {
            $query->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%");
        })
        ->with(['user', 'competition'])
        ->orderByRaw("status_pendaftaran = 'pending' DESC")
        ->latest()
        ->paginate(20);

        return view('admin.verifikasi.search', compact('registrations', 'keyword'));
    }
}