<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use Illuminate\Http\Request;

class MerchandiseController extends Controller
{
    public function index()
    {
        $merchandises = Merchandise::latest()->paginate(10);
        return view('admin.merchandise.index', compact('merchandises'));
    }

    public function create()
    {
        return view('admin.merchandise.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'link_pembelian' => 'nullable|url',
            'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'ebook_file' => 'nullable|mimes:pdf|max:10240', // Maksimal PDF 10MB
        ]);

        $isDigital = $request->has('is_digital');

        $merchandise = Merchandise::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'link_pembelian' => $request->link_pembelian,
            'is_active' => $request->has('is_active'),
            'is_digital' => $isDigital,
        ]);

        // Gambar Sampul (Bisa dilihat publik)
        if ($request->hasFile('gambar_produk')) {
            $merchandise->addMediaFromRequest('gambar_produk')->toMediaCollection('gambar_produk', 'public');
        }

        // File E-Book PDF (Disimpan di brankas 'local' agar tidak bisa di-download via URL)
        if ($isDigital && $request->hasFile('ebook_file')) {
            $merchandise->addMediaFromRequest('ebook_file')->toMediaCollection('ebook_file', 'local');
        }

        return redirect()->route('admin.merchandise.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Merchandise $merchandise)
    {
        return view('admin.merchandise.form', compact('merchandise'));
    }

    public function update(Request $request, Merchandise $merchandise)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'link_pembelian' => 'nullable|url',
            'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'ebook_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $isDigital = $request->has('is_digital');

        $merchandise->update([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'link_pembelian' => $request->link_pembelian,
            'is_active' => $request->has('is_active'),
            'is_digital' => $isDigital,
        ]);

        if ($request->hasFile('gambar_produk')) {
            $merchandise->clearMediaCollection('gambar_produk');
            $merchandise->addMediaFromRequest('gambar_produk')->toMediaCollection('gambar_produk', 'public');
        }

        // Jika E-book diperbarui, hapus yang lama dan simpan yang baru ke 'local'
        if ($isDigital && $request->hasFile('ebook_file')) {
            $merchandise->clearMediaCollection('ebook_file');
            $merchandise->addMediaFromRequest('ebook_file')->toMediaCollection('ebook_file', 'local');
        }

        return redirect()->route('admin.merchandise.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Merchandise $merchandise)
    {
        $merchandise->delete();
        return back()->with('success', 'Produk dihapus.');
    }
}