<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Registration;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    // ====================================================================
    // MANAJEMEN AKUN PESERTA
    // ====================================================================

    // Menampilkan semua peserta
    public function index(Request $request)
    {
        $query = User::where('email', '!=', 'admin@upc.com');

        // Fitur Pencarian Nama/Sekolah
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asal_sekolah', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Fitur Filter Status Verifikasi
        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        $peserta = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.peserta.index', compact('peserta'));
    }

    // Menampilkan form edit peserta
    public function edit(User $user)
    {
        return view('admin.peserta.edit', compact('user'));
    }

    // Menyimpan perubahan data peserta
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'no_wa' => ['required', 'string', 'max:20'],
            'status_verifikasi' => ['required', 'in:pending,verified,rejected'],
        ]);

        $user->update($request->only(['name', 'email', 'asal_sekolah', 'no_wa', 'status_verifikasi']));

        return redirect()->route('admin.peserta.index')->with('success', 'Data peserta berhasil diperbarui!');
    }

    // Menghapus data peserta
    public function destroy(User $user)
    {
        // Jika menggunakan Spatie Media Library, file kartu pelajar akan otomatis terhapus
        $user->delete();

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil dihapus dari sistem.');
    }

    /**
     * FITUR DEBUG: Reset Seluruh Pendaftaran Lomba (Untuk Akun Tester)
     */
    public function resetRegistrations(User $user)
    {
        // 1. Ambil semua pendaftaran lomba milik user ini
        $registrations = Registration::where('user_id', $user->id)->get();
        
        foreach ($registrations as $reg) {
            // 2. Bersihkan file gambar/resi pembayaran di server
            if ($reg->hasMedia('bukti_pembayaran_lomba')) {
                $reg->clearMediaCollection('bukti_pembayaran_lomba');
            }
            if ($reg->hasMedia('bukti_pembayaran')) {
                $reg->clearMediaCollection('bukti_pembayaran');
            }
            
            // 3. Hapus data (Berkat cascadeOnDelete di migrasi, ini akan otomatis 
            // menghapus data di tabel payments, exam_results, dan exam_answers juga)
            $reg->delete();
        }

        return back()->with('success', '🛠️ DEBUG: Semua riwayat pendaftaran lomba, tagihan Midtrans, dan riwayat ujian CBT milik "' . $user->name . '" berhasil dihapus bersih!');
    }


    // ====================================================================
    // EXPORT & RANKING DATA LOMBA
    // ====================================================================

    // Export CSV Semua Peserta Secara Umum
    public function export()
    {
        $peserta = Registration::with(['user', 'competition'])->get();

        $namaFile = "Data_Semua_Peserta_UPC_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$namaFile",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $kolom = ['ID Pendaftaran', 'Nama Lengkap', 'Email', 'Asal Sekolah', 'Kompetisi', 'Status Verifikasi'];

        $callback = function() use($peserta, $kolom) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, $kolom);

            foreach ($peserta as $data) {
                fputcsv($file, [
                    $data->id, 
                    $data->user->name ?? '-', 
                    $data->user->email ?? '-', 
                    $data->user->asal_sekolah ?? '-', 
                    $data->competition->nama_lomba ?? '-', 
                    $data->status_pendaftaran ?? 'pending'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export CSV Khusus 1 Lomba beserta Nilai (Diambil dari relasi examResult)
    public function exportByCompetition(Competition $competition)
    {
        // Panggil relasi 'user' dan 'examResult'
        $registrations = Registration::where('competition_id', $competition->id)
            ->with(['user', 'examResult'])
            ->get();

        $namaFile = "Data_Peserta_" . str_replace(' ', '_', $competition->nama_lomba) . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$namaFile",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID Pendaftaran', 'Nama Lengkap', 'Email', 'Asal Sekolah', 'Status Verifikasi', 'Nilai Akhir'];

        $callback = function() use ($registrations, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($registrations as $data) {
                fputcsv($file, [
                    $data->id,
                    $data->user->name ?? '-',
                    $data->user->email ?? '-',
                    $data->user->asal_sekolah ?? '-',
                    $data->status_pendaftaran ?? 'pending',
                    // Memanggil skor dari tabel exam_results (bukan lagi dari $data->nilai)
                    $data->examResult->score ?? 0 
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Fitur Papan Peringkat / Leaderboard
    public function ranking(Competition $competition)
    {
        // 1. Ambil datanya
        $registrations = Registration::where('competition_id', $competition->id)
            ->where('status_pendaftaran', 'verified')
            ->with(['user', 'examResult'])
            ->get()
            // 2. Urutkan menggunakan Collection Method berdasarkan kolom score di relasi examResult
            ->sortByDesc(function ($reg) {
                return $reg->examResult->score ?? 0;
            })
            // 3. Reset index array menjadi 0, 1, 2, dst (penting untuk tampilan Top 3)
            ->values(); 

        return view('admin.kompetisi.ranking', compact('competition', 'registrations'));
    }
}