<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MerchandiseTransaction;
use Illuminate\Http\Request;

class MerchandiseVerificationController extends Controller
{
    public function index()
    {
        // Ambil data transaksi, urutkan yang 'pending' paling atas
        $transactions = MerchandiseTransaction::with(['user', 'merchandise'])
            ->orderByRaw("status = 'pending' DESC")
            ->latest()
            ->paginate(15);

        return view('admin.merchandise_verifikasi.index', compact('transactions'));
    }

    public function update(Request $request, MerchandiseTransaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,rejected'
        ]);

        $transaction->update(['status' => $request->status]);

        $pesan = '';
        if ($request->status === 'paid') {
            $pesan = 'Pembayaran disetujui! Peserta kini bisa mengakses produk tersebut.';
        } elseif ($request->status === 'rejected') {
            $pesan = 'Pembayaran ditolak.';
        } else {
            $pesan = 'Status dikembalikan ke Antrean (Pending).';
        }

        return back()->with('success', $pesan);
    }
}