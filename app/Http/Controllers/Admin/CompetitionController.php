<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    // Menampilkan daftar lomba
    public function index()
    {
        $kompetisi = Competition::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.kompetisi.index', compact('kompetisi'));
    }

    // Menampilkan form tambah lomba
    public function create()
    {
        return view('admin.kompetisi.create');
    }

    // Menyimpan data lomba baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_lomba' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_pendaftaran' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_pelaksanaan' => 'required|date', 
            'durasi_menit' => 'required|integer|min:1',
            'gambar_lomba' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $competition = Competition::create([
            'nama_lomba' => $request->nama_lomba,
            'deskripsi' => $request->deskripsi,
            'harga_pendaftaran' => $request->harga_pendaftaran,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan, 
            'durasi_menit' => $request->durasi_menit,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        if ($request->hasFile('gambar_lomba')) {
            $competition->addMediaFromRequest('gambar_lomba')->toMediaCollection('gambar_lomba');
        }

        return redirect()->route('admin.kompetisi.index')->with('success', 'Lomba baru berhasil ditambahkan!');
    }

    public function update(Request $request, Competition $competition)
    {
        $request->validate([
            'nama_lomba' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_pendaftaran' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_pelaksanaan' => 'required|date', 
            'durasi_menit' => 'required|integer|min:1',
            'gambar_lomba' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $competition->update([
            'nama_lomba' => $request->nama_lomba,
            'deskripsi' => $request->deskripsi,
            'harga_pendaftaran' => $request->harga_pendaftaran,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan, 
            'durasi_menit' => $request->durasi_menit,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        if ($request->hasFile('gambar_lomba')) {
            $competition->clearMediaCollection('gambar_lomba');
            $competition->addMediaFromRequest('gambar_lomba')->toMediaCollection('gambar_lomba');
        }

        return redirect()->route('admin.kompetisi.index')->with('success', 'Data lomba berhasil diperbarui!');
    }

    // Menampilkan form edit
    public function edit(Competition $competition)
    {
        return view('admin.kompetisi.edit', compact('competition'));
    }

    // Menghapus lomba
    public function destroy(Competition $competition)
    {
        $competition->delete();
        return redirect()->route('admin.kompetisi.index')->with('success', 'Lomba berhasil dihapus beserta data pendaftar terkait.');
    }
}