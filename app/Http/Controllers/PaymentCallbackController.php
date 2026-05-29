<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MerchandiseTransaction;
use App\Models\Registration;

class PaymentCallbackController extends Controller
{
    public function receive(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            
            // CEK TRANSAKSI: Apakah ini Lomba atau Merchandise?
            $isMerch = str_starts_with($request->order_id, 'MERCH-');
            $isLomba = str_starts_with($request->order_id, 'REG-');

            if ($isMerch) {
                $transaction = MerchandiseTransaction::where('order_id', $request->order_id)->first();
                if (!$transaction) return response()->json(['message' => 'Order Not Found'], 404);

                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $transaction->update(['status' => 'paid']);
                } else if (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
                    $transaction->update(['status' => 'rejected']);
                }
            } 
            elseif ($isLomba) {
                $registration = Registration::where('order_id', $request->order_id)->first();
                if (!$registration) return response()->json(['message' => 'Order Not Found'], 404);

                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    // Jika dibayar, langsung verified agar bisa akses soal CBT
                    $registration->update(['status_pendaftaran' => 'verified']); 
                } else if (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
                    $registration->update(['status_pendaftaran' => 'rejected']);
                }
            }

            return response()->json(['message' => 'Callback Success']);
        }

        return response()->json(['message' => 'Invalid Signature'], 403);
    }
}