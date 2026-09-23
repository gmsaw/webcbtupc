<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Wave;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompetitionController extends Controller
{
    // ====================================================================
    // INDEX — Daftar Lomba
    // ====================================================================
    public function index()
    {
        $kompetisi = Competition::with('waves')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kompetisi.index', compact('kompetisi'));
    }

    // ====================================================================
    // CREATE — Form Tambah Lomba
    // ====================================================================
    public function create()
    {
        return view('admin.kompetisi.create');
    }

    // ====================================================================
    // STORE — Simpan Lomba Baru
    // ====================================================================
    public function store(Request $request)
    {
        // Cek apakah menggunakan sistem gelombang
        $isUsingWaves = $request->has('is_using_waves') && $request->is_using_waves == 1;

        // ── VALIDASI DASAR ──
        $rules = [
            'nama_lomba'                   => 'required|string|max:255',
            'deskripsi'                    => 'nullable|string',
            'tanggal_mulai'                => 'required|date',
            'tanggal_selesai'              => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_pelaksanaan'            => 'required|date',
            'waktu_pelaksanaan_semifinal'  => 'nullable|date',   // ← BARU
            'waktu_pelaksanaan_final'      => 'nullable|date',   // ← BARU
            'durasi_menit'                 => 'required|integer|min:1',
            'gambar_lomba'                 => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_using_waves'               => 'nullable|boolean',
            'nilai_benar'                  => 'required|numeric',
            'nilai_salah'                  => 'required|numeric',
            'nilai_kosong'                 => 'required|numeric',
        ];

        // Validasi conditional untuk harga dan waves
        if ($isUsingWaves) {
            $rules['waves']                       = 'required|array|min:1';
            $rules['waves.*.nama_gelombang']      = 'required|string|max:255';
            $rules['waves.*.start_date']          = 'required|date';
            $rules['waves.*.end_date']            = 'required|date|after:waves.*.start_date';
            $rules['waves.*.biaya']               = 'required|numeric|min:0';
        } else {
            $rules['harga_pendaftaran']           = 'required|numeric|min:0';
        }

        $request->validate($rules);

        // Mulai transaction database
        DB::beginTransaction();

        try {
            // Set harga pendaftaran
            $hargaPendaftaran = $isUsingWaves ? 0 : ($request->harga_pendaftaran ?? 0);

            // ── BUAT KOMPETISI ──
            $competition = Competition::create([
                'nama_lomba'                   => $request->nama_lomba,
                'deskripsi'                    => $request->deskripsi,
                'harga_pendaftaran'            => $hargaPendaftaran,
                'tanggal_mulai'                => $request->tanggal_mulai,
                'tanggal_selesai'              => $request->tanggal_selesai,
                'waktu_pelaksanaan'            => $request->waktu_pelaksanaan,
                'waktu_pelaksanaan_semifinal'  => $request->waktu_pelaksanaan_semifinal,   // ← BARU
                'waktu_pelaksanaan_final'      => $request->waktu_pelaksanaan_final,       // ← BARU
                'durasi_menit'                 => $request->durasi_menit,
                'is_active'                    => $request->has('is_active'),
                'is_using_waves'               => $isUsingWaves,
                'nilai_benar'                  => $request->nilai_benar,
                'nilai_salah'                  => $request->nilai_salah,
                'nilai_kosong'                 => $request->nilai_kosong,
            ]);

            // ── SIMPAN WAVES ──
            if ($isUsingWaves && $request->has('waves')) {
                foreach ($request->waves as $waveData) {
                    if (empty($waveData['nama_gelombang']) ||
                        empty($waveData['start_date']) ||
                        empty($waveData['end_date']) ||
                        !isset($waveData['biaya'])) {
                        continue;
                    }

                    $competition->waves()->create([
                        'nama_gelombang' => $waveData['nama_gelombang'],
                        'start_date'     => Carbon::parse($waveData['start_date']),
                        'end_date'       => Carbon::parse($waveData['end_date']),
                        'biaya'          => (int) $waveData['biaya'],
                    ]);
                }
            }

            // ── UPLOAD GAMBAR ──
            if ($request->hasFile('gambar_lomba')) {
                $competition->addMediaFromRequest('gambar_lomba')
                    ->toMediaCollection('gambar_lomba');
            }

            DB::commit();

            return redirect()
                ->route('admin.kompetisi.index')
                ->with('success', 'Lomba baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating competition: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan lomba: ' . $e->getMessage());
        }
    }

    // ====================================================================
    // EDIT — Form Edit Lomba
    // ====================================================================
    public function edit(Competition $competition)
    {
        $competition->load('waves');
        return view('admin.kompetisi.edit', compact('competition'));
    }

    // ====================================================================
    // UPDATE — Update Lomba
    // ====================================================================
    public function update(Request $request, Competition $competition)
    {
        $isUsingWaves = $request->has('is_using_waves') && $request->is_using_waves == 1;

        // ── VALIDASI DASAR ──
        $rules = [
            'nama_lomba'                   => 'required|string|max:255',
            'deskripsi'                    => 'nullable|string',
            'tanggal_mulai'                => 'required|date',
            'tanggal_selesai'              => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_pelaksanaan'            => 'required|date',
            'waktu_pelaksanaan_semifinal'  => 'nullable|date',   // ← BARU
            'waktu_pelaksanaan_final'      => 'nullable|date',   // ← BARU
            'durasi_menit'                 => 'required|integer|min:1',
            'gambar_lomba'                 => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_using_waves'               => 'nullable|boolean',
            'nilai_benar'                  => 'required|numeric',
            'nilai_salah'                  => 'required|numeric',
            'nilai_kosong'                 => 'required|numeric',
        ];

        if ($isUsingWaves) {
            $rules['waves']                       = 'required|array|min:1';
            $rules['waves.*.nama_gelombang']      = 'required|string|max:255';
            $rules['waves.*.start_date']          = 'required|date';
            $rules['waves.*.end_date']            = 'required|date|after:waves.*.start_date';
            $rules['waves.*.biaya']               = 'required|numeric|min:0';
        } else {
            $rules['harga_pendaftaran']           = 'required|numeric|min:0';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $hargaPendaftaran = $isUsingWaves ? 0 : ($request->harga_pendaftaran ?? 0);

            // ── UPDATE KOMPETISI ──
            $competition->update([
                'nama_lomba'                   => $request->nama_lomba,
                'deskripsi'                    => $request->deskripsi,
                'harga_pendaftaran'            => $hargaPendaftaran,
                'tanggal_mulai'                => $request->tanggal_mulai,
                'tanggal_selesai'              => $request->tanggal_selesai,
                'waktu_pelaksanaan'            => $request->waktu_pelaksanaan,
                'waktu_pelaksanaan_semifinal'  => $request->waktu_pelaksanaan_semifinal,   // ← BARU
                'waktu_pelaksanaan_final'      => $request->waktu_pelaksanaan_final,       // ← BARU
                'durasi_menit'                 => $request->durasi_menit,
                'is_active'                    => $request->has('is_active'),
                'is_using_waves'               => $isUsingWaves,
                'nilai_benar'                  => $request->nilai_benar,
                'nilai_salah'                  => $request->nilai_salah,
                'nilai_kosong'                 => $request->nilai_kosong,
            ]);

            // ── UPDATE WAVES ──
            if ($isUsingWaves && $request->has('waves')) {
                $competition->waves()->delete();

                foreach ($request->waves as $waveData) {
                    if (empty($waveData['nama_gelombang']) ||
                        empty($waveData['start_date']) ||
                        empty($waveData['end_date']) ||
                        !isset($waveData['biaya'])) {
                        continue;
                    }

                    $competition->waves()->create([
                        'nama_gelombang' => $waveData['nama_gelombang'],
                        'start_date'     => Carbon::parse($waveData['start_date']),
                        'end_date'       => Carbon::parse($waveData['end_date']),
                        'biaya'          => (int) $waveData['biaya'],
                    ]);
                }
            } else {
                $competition->waves()->delete();
            }

            // ── UPDATE GAMBAR ──
            if ($request->hasFile('gambar_lomba')) {
                $competition->clearMediaCollection('gambar_lomba');
                $competition->addMediaFromRequest('gambar_lomba')
                    ->toMediaCollection('gambar_lomba');
            }

            DB::commit();

            return redirect()
                ->route('admin.kompetisi.index')
                ->with('success', 'Data lomba berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating competition: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui lomba: ' . $e->getMessage());
        }
    }

    // ====================================================================
    // DESTROY — Hapus Lomba
    // ====================================================================
    public function destroy(Competition $competition)
    {
        try {
            DB::beginTransaction();

            $competition->waves()->delete();

            if ($competition->getMedia('gambar_lomba')->count() > 0) {
                $competition->clearMediaCollection('gambar_lomba');
            }

            $competition->delete();

            DB::commit();

            return redirect()
                ->route('admin.kompetisi.index')
                ->with('success', 'Lomba berhasil dihapus beserta data pendaftar terkait.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting competition: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus lomba: ' . $e->getMessage());
        }
    }

    // ====================================================================
    // SHOW — Detail Lomba (Opsional)
    // ====================================================================
    public function show(Competition $competition)
    {
        $competition->load(['waves', 'registrations.user']);
        return view('admin.kompetisi.show', compact('competition'));
    }

    // ====================================================================
    // TOGGLE ACTIVE — Aktif/Nonaktifkan Lomba
    // ====================================================================
    public function toggleActive(Competition $competition)
    {
        $competition->update([
            'is_active' => !$competition->is_active
        ]);

        $status = $competition->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->back()
            ->with('success', "Status lomba berhasil {$status}");
    }
}