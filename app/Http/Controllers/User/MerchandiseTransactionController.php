<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchandise;
use App\Models\MerchandiseTransaction;
use Illuminate\Support\Facades\Auth;

class MerchandiseTransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'merchandise_id' => 'required|exists:merchandises,id',
            'metode_pembayaran' => 'required|in:manual',
            // Jika harga > 0, wajib upload bukti
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' 
        ]);

        $merch = Merchandise::findOrFail($request->merchandise_id);

        // Jika ini E-Book, cegah user beli 2 kali!
        if ($merch->is_digital) {
            $sudahPunya = MerchandiseTransaction::where('user_id', Auth::id())
                ->where('merchandise_id', $merch->id)
                ->whereIn('status', ['pending', 'paid'])
                ->exists();

            if ($sudahPunya) {
                return back()->with('error', 'Anda sudah membeli atau sedang dalam proses verifikasi untuk E-Book ini.');
            }
        }

        // Cek apakah produk gratis
        $statusAwal = ($merch->harga == 0) ? 'paid' : 'pending';

        if ($merch->harga > 0 && !$request->hasFile('bukti_pembayaran')) {
            return back()->with('error', 'Bukti pembayaran wajib diunggah untuk produk berbayar.');
        }

        // Simpan Transaksi
        $transaksi = MerchandiseTransaction::create([
            'user_id' => Auth::id(),
            'merchandise_id' => $merch->id,
            'nominal' => $merch->harga,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => $statusAwal,
        ]);

        // Simpan Bukti Pembayaran ke Media Library
        if ($request->hasFile('bukti_pembayaran')) {
            $transaksi->addMediaFromRequest('bukti_pembayaran')->toMediaCollection('bukti_pembayaran_merch');
        }

        if ($statusAwal === 'paid') {
            return back()->with('success', 'E-Book Gratis berhasil ditambahkan ke Pustaka Anda!');
        }

        return back()->with('success', 'Pesanan berhasil dibuat! Bukti pembayaran Anda sedang diverifikasi oleh panitia.');
    }
}