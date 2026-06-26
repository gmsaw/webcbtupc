<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
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
        $registrations = \App\Models\Registration::where('user_id', $user->id)->get();
        
        foreach ($registrations as $reg) {
            // 2. Bersihkan file gambar/resi pembayaran di server (agar storage tidak penuh)
            if ($reg->hasMedia('bukti_pembayaran_lomba')) {
                $reg->clearMediaCollection('bukti_pembayaran_lomba');
            }
            if ($reg->hasMedia('bukti_pembayaran')) {
                $reg->clearMediaCollection('bukti_pembayaran');
            }
            
            // 3. Hapus data (Otomatis mereset status Midtrans dan nilai CBT)
            $reg->delete();
        }

        return back()->with('success', '🛠️ DEBUG: Semua riwayat pendaftaran lomba, tagihan Midtrans, dan nilai CBT milik "' . $user->name . '" berhasil dihapus bersih!');
    }

    public function export()
{
    // Mengambil semua data registrasi peserta beserta relasi tabel user dan kompetisi
    // Sesuaikan nama model 'Registration' dengan model pendaftaran yang kamu gunakan
    $peserta = \App\Models\Registration::with(['user', 'competition'])->get();

    // Menentukan nama file yang akan diunduh dengan menyertakan tanggal hari ini
    $namaFile = "Data_Peserta_CBT_UPC_" . date('Y-m-d') . ".csv";

    // Mengatur header HTTP agar peramban mengenali respons ini sebagai file unduhan
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$namaFile",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // Menentukan judul kolom pada baris pertama di Excel
    $kolom = ['ID Pendaftaran', 'Nama Lengkap', 'Email', 'Asal Sekolah', 'Kompetisi', 'Status Verifikasi'];

    // Membuat fungsi callback untuk menulis data baris demi baris ke dalam output
    $callback = function() use($peserta, $kolom) {
        $file = fopen('php://output', 'w');
        
        // Menuliskan baris judul kolom
        fputcsv($file, $kolom);

        // Melakukan perulangan untuk setiap peserta dan memasukkannya ke baris baru
        foreach ($peserta as $data) {
            $baris['ID Pendaftaran']  = $data->id;
            $baris['Nama Lengkap']    = $data->user->name ?? '-';
            $baris['Email']           = $data->user->email ?? '-';
            $baris['Asal Sekolah']    = $data->user->asal_sekolah ?? '-';
            $baris['Kompetisi']       = $data->competition->nama_lomba ?? '-';
            $baris['Status Verifikasi'] = $data->status ?? 'pending';

            fputcsv($file, array(
                $baris['ID Pendaftaran'], 
                $baris['Nama Lengkap'], 
                $baris['Email'], 
                $baris['Asal Sekolah'], 
                $baris['Kompetisi'], 
                $baris['Status Verifikasi']
            ));
        }

        fclose($file);
    };

    // Mengembalikan response stream yang akan langsung memicu unduhan di peramban
    return response()->stream($callback, 200, $headers);
}
}