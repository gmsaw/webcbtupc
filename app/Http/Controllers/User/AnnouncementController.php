<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('is_active', true)
            ->latest()
            ->paginate(10);

        return view('user.pengumuman.index', compact('announcements'));
    }

    // --- TAMBAHKAN METHOD INI ---
    public function show(Announcement $announcement)
    {
        // Pastikan peserta tidak bisa mengintip pengumuman yang di-hidden admin
        if (!$announcement->is_active) {
            abort(404, 'Pengumuman tidak ditemukan atau sudah tidak aktif.');
        }

        return view('user.pengumuman.show', compact('announcement'));
    }
}