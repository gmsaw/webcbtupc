<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MerchandiseTransaction;
use App\Models\Payment; // <-- Memanggil model Payment yang baru

class PaymentCallbackController extends Controller
{
    public function receive(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Jika Signature dari Midtrans valid (Aman dari Hacker)
        if ($hashed == $request->signature_key) {
            
            // CEK TRANSAKSI: Apakah ini Lomba atau Merchandise?
            $isMerch = str_starts_with($request->order_id, 'MERCH-');
            // Prefix lomba mengikuti format baru di UserRegistrationController (UPC-...)
            $isLomba = str_starts_with($request->order_id, 'UPC-'); 

            // 1. SKENARIO PEMBELIAN MERCHANDISE / E-BOOK
            if ($isMerch) {
                $transaction = MerchandiseTransaction::where('order_id', $request->order_id)->first();
                if (!$transaction) return response()->json(['message' => 'Merch Order Not Found'], 404);

                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $transaction->update(['status' => 'paid']);
                } else if (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
                    $transaction->update(['status' => 'rejected']);
                }
            } 
            
            // 2. SKENARIO PENDAFTARAN LOMBA
            elseif ($isLomba) {
                // Cari data tagihan di tabel payments
                $payment = Payment::where('order_id', $request->order_id)->first();
                if (!$payment) return response()->json(['message' => 'Payment Order Not Found'], 404);

                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    // Update tagihan menjadi lunas
                    $payment->update([
                        'status' => 'paid',
                        'payment_type' => $request->payment_type,
                        'paid_at' => now(),
                    ]);

                    // OTOMATIS BERUBAH JADI VERIFIED AGAR BISA AKSES UJIAN CBT
                    // Menggunakan relasi untuk mengupdate tabel registrations
                    $payment->registration()->update(['status_pendaftaran' => 'verified']); 

                } else if (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
                    // Update tagihan menjadi gagal/kedaluwarsa
                    $payment->update(['status' => 'failed']);
                    
                    // Opsional: Anda bisa membiarkan pendaftaran tetap 'pending' agar user bisa
                    // mencoba bayar lagi, atau langsung ditolak (rejected):
                    // $payment->registration()->update(['status_pendaftaran' => 'rejected']);
                }
            }

            return response()->json(['message' => 'Callback Success']);
        }

        return response()->json(['message' => 'Invalid Signature'], 403);
    }
}