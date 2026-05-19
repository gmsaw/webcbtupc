<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $pengumuman = Announcement::latest()->paginate(10);
        return view('admin.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        return view('admin.pengumuman.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        Announcement::create([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil disiarkan!');
    }

    public function edit(Announcement $announcement) // Pastikan parameter ini cocok dengan route
    {
        return view('admin.pengumuman.form', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $announcement->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}