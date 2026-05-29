<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchandise;
use App\Models\MerchandiseTransaction;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class MerchandiseTransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'merchandise_id' => 'required|exists:merchandises,id',
            'metode_pembayaran' => 'required|in:manual,gateway',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' 
        ]);

        $merch = Merchandise::findOrFail($request->merchandise_id);

        // Jika E-Book, cegah beli ganda
        if ($merch->is_digital) {
            $sudahPunya = MerchandiseTransaction::where('user_id', Auth::id())
                ->where('merchandise_id', $merch->id)
                ->whereIn('status', ['pending', 'paid'])
                ->exists();

            if ($sudahPunya) {
                return back()->with('error', 'Anda sudah membeli E-Book ini.');
            }
        }

        $orderId = 'MERCH-' . time() . '-' . Auth::id();
        $statusAwal = ($merch->harga == 0) ? 'paid' : 'pending';

        // Simpan Transaksi Awal
        $transaksi = MerchandiseTransaction::create([
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'merchandise_id' => $merch->id,
            'nominal' => $merch->harga,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => $statusAwal,
        ]);

        // LOGIKA JIKA GRATIS
        if ($statusAwal === 'paid') {
            return back()->with('success', 'E-Book Gratis berhasil ditambahkan ke Pustaka Anda!');
        }

        // LOGIKA TRANSFER MANUAL
        if ($request->metode_pembayaran === 'manual') {
            if (!$request->hasFile('bukti_pembayaran')) {
                $transaksi->delete(); // Batal jika bukti kosong
                return back()->with('error', 'Bukti pembayaran wajib diunggah untuk transfer manual.');
            }
            $transaksi->addMediaFromRequest('bukti_pembayaran')->toMediaCollection('bukti_pembayaran_merch');
            return back()->with('success', 'Pesanan berhasil dibuat! Menunggu verifikasi admin.');
        }

        // LOGIKA PAYMENT GATEWAY (MIDTRANS)
        if ($request->metode_pembayaran === 'gateway') {
            // Konfigurasi Midtrans
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $merch->harga,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'item_details' => [
                    [
                        'id' => $merch->id,
                        'price' => (int) $merch->harga,
                        'quantity' => 1,
                        'name' => substr($merch->nama_produk, 0, 50)
                    ]
                ]
            ];

            // Minta Token ke Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan token ke database
            $transaksi->update(['snap_token' => $snapToken]);

            // Arahkan ke halaman pembayaran khusus
            return redirect()->route('user.merchandise.checkout', $transaksi->id);
        }
    }
}