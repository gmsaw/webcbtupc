<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use App\Models\MerchandiseTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    // 1. Menampilkan Rak Buku (E-Book yang sudah dibeli)
    public function index()
    {
        // Ambil transaksi e-book user yang sudah LUNAS (paid)
        $koleksi = MerchandiseTransaction::with('merchandise')
            ->where('user_id', Auth::id())
            ->where('status', 'paid')
            ->get();

        return view('user.pustaka.index', compact('koleksi'));
    }

    // 2. Menampilkan Halaman E-Reader (Bingkai Pembaca)
    public function read($id)
    {
        // Validasi ganda: Pastikan user benar-benar sudah beli dan lunas
        $transaksi = MerchandiseTransaction::where('user_id', Auth::id())
            ->where('merchandise_id', $id)
            ->where('status', 'paid')
            ->firstOrFail();

        $ebook = $transaksi->merchandise;

        return view('user.pustaka.read', compact('ebook'));
    }

    // 3. Penjaga Pintu File PDF (Streaming Langsung dari Brankas 'local')
    public function stream($id)
    {
        // Validasi keamanan: Cegah akses langsung lewat URL jika belum bayar
        $transaksi = MerchandiseTransaction::where('user_id', Auth::id())
            ->where('merchandise_id', $id)
            ->where('status', 'paid')
            ->firstOrFail();

        $ebook = $transaksi->merchandise;

        // Ambil alamat asli file di dalam server (bukan URL public)
        $path = $ebook->getFirstMediaPath('ebook_file');

        if (!file_exists($path)) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        // Tampilkan file secara INLINE (bukan attachment/download)
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . str_replace(' ', '-', $ebook->nama_produk) . '.pdf"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function logSecurity(Request $request)
    {
        // Fitur ini bisa Anda hubungkan ke tabel 'security_logs' nanti.
        // Untuk sekarang, kita kembalikan response sukses agar JS tidak error.
        \Illuminate\Support\Facades\Log::warning('DRM Alert: ' . $request->event . ' at ' . $request->url);
        return response()->json(['status' => 'logged']);
    }
}