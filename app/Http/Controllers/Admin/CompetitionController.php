<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Wave;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CompetitionController extends Controller
{
    // Menampilkan daftar lomba
    public function index()
    {
        $kompetisi = Competition::with('waves')->orderBy('created_at', 'desc')->paginate(10);
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
        // Cek apakah menggunakan sistem gelombang
        $isUsingWaves = $request->has('is_using_waves') && $request->is_using_waves == 1;
        
        // Validasi dasar
        $rules = [
            'nama_lomba' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_pelaksanaan' => 'required|date',
            'durasi_menit' => 'required|integer|min:1',
            'gambar_lomba' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_using_waves' => 'nullable|boolean',
            'nilai_benar' => 'required|numeric',
            'nilai_salah' => 'required|numeric',
            'nilai_kosong' => 'required|numeric',
        ];

        // Validasi conditional untuk harga dan waves
        if ($isUsingWaves) {
            $rules['waves'] = 'required|array|min:1';
            $rules['waves.*.nama_gelombang'] = 'required|string|max:255';
            $rules['waves.*.start_date'] = 'required|date';
            $rules['waves.*.end_date'] = 'required|date|after:waves.*.start_date';
            $rules['waves.*.biaya'] = 'required|numeric|min:0';
        } else {
            $rules['harga_pendaftaran'] = 'required|numeric|min:0';
        }

        $request->validate($rules);

        // Mulai transaction database
        \DB::beginTransaction();
        
        try {
            // Set harga pendaftaran
            $hargaPendaftaran = $isUsingWaves ? 0 : ($request->harga_pendaftaran ?? 0);

            // Buat kompetisi baru
            $competition = Competition::create([
                'nama_lomba' => $request->nama_lomba,
                'deskripsi' => $request->deskripsi,
                'harga_pendaftaran' => $hargaPendaftaran,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
                'durasi_menit' => $request->durasi_menit,
                'is_active' => $request->has('is_active') ? true : false,
                'is_using_waves' => $isUsingWaves,
                'nilai_benar' => $request->nilai_benar,
                'nilai_salah' => $request->nilai_salah,
                'nilai_kosong' => $request->nilai_kosong,
            ]);

            // Simpan waves jika menggunakan sistem gelombang
            if ($isUsingWaves && $request->has('waves')) {
                foreach ($request->waves as $waveData) {
                    // Validasi tambahan per wave
                    if (empty($waveData['nama_gelombang']) || 
                        empty($waveData['start_date']) || 
                        empty($waveData['end_date']) || 
                        !isset($waveData['biaya'])) {
                        continue; // Skip jika data tidak lengkap
                    }

                    $competition->waves()->create([
                        'nama_gelombang' => $waveData['nama_gelombang'],
                        'start_date' => Carbon::parse($waveData['start_date']),
                        'end_date' => Carbon::parse($waveData['end_date']),
                        'biaya' => (int) $waveData['biaya'],
                    ]);
                }
            }

            // Upload gambar jika ada
            if ($request->hasFile('gambar_lomba')) {
                $competition->addMediaFromRequest('gambar_lomba')
                    ->toMediaCollection('gambar_lomba');
            }

            \DB::commit();

            return redirect()
                ->route('admin.kompetisi.index')
                ->with('success', 'Lomba baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            // Log error untuk debugging
            \Log::error('Error creating competition: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan lomba: ' . $e->getMessage());
        }
    }

    // Menampilkan form edit
    public function edit(Competition $competition)
    {
        // Load relasi waves
        $competition->load('waves');
        return view('admin.kompetisi.edit', compact('competition'));
    }

    // Mengupdate data lomba
    public function update(Request $request, Competition $competition)
    {
        // Cek apakah menggunakan sistem gelombang
        $isUsingWaves = $request->has('is_using_waves') && $request->is_using_waves == 1;
        
        // Validasi dasar
        $rules = [
            'nama_lomba' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_pelaksanaan' => 'required|date',
            'durasi_menit' => 'required|integer|min:1',
            'gambar_lomba' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_using_waves' => 'nullable|boolean',
            'nilai_benar' => 'required|numeric',
            'nilai_salah' => 'required|numeric',
            'nilai_kosong' => 'required|numeric',
        ];

        // Validasi conditional untuk harga dan waves
        if ($isUsingWaves) {
            $rules['waves'] = 'required|array|min:1';
            $rules['waves.*.nama_gelombang'] = 'required|string|max:255';
            $rules['waves.*.start_date'] = 'required|date';
            $rules['waves.*.end_date'] = 'required|date|after:waves.*.start_date';
            $rules['waves.*.biaya'] = 'required|numeric|min:0';
        } else {
            $rules['harga_pendaftaran'] = 'required|numeric|min:0';
        }

        $request->validate($rules);

        // Mulai transaction database
        \DB::beginTransaction();
        
        try {
            // Set harga pendaftaran
            $hargaPendaftaran = $isUsingWaves ? 0 : ($request->harga_pendaftaran ?? 0);

            // Update kompetisi
            $competition->update([
                'nama_lomba' => $request->nama_lomba,
                'deskripsi' => $request->deskripsi,
                'harga_pendaftaran' => $hargaPendaftaran,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
                'durasi_menit' => $request->durasi_menit,
                'is_active' => $request->has('is_active') ? true : false,
                'is_using_waves' => $isUsingWaves,
                'nilai_benar' => $request->nilai_benar,
                'nilai_salah' => $request->nilai_salah,
                'nilai_kosong' => $request->nilai_kosong,
            ]);

            // Update waves jika menggunakan sistem gelombang
            if ($isUsingWaves && $request->has('waves')) {
                // Hapus waves lama
                $competition->waves()->delete();
                
                // Buat waves baru
                foreach ($request->waves as $waveData) {
                    // Validasi tambahan per wave
                    if (empty($waveData['nama_gelombang']) || 
                        empty($waveData['start_date']) || 
                        empty($waveData['end_date']) || 
                        !isset($waveData['biaya'])) {
                        continue; // Skip jika data tidak lengkap
                    }

                    $competition->waves()->create([
                        'nama_gelombang' => $waveData['nama_gelombang'],
                        'start_date' => Carbon::parse($waveData['start_date']),
                        'end_date' => Carbon::parse($waveData['end_date']),
                        'biaya' => (int) $waveData['biaya'],
                    ]);
                }
            } else {
                // Jika tidak menggunakan waves, hapus semua waves yang ada
                $competition->waves()->delete();
            }

            // Update gambar jika ada
            if ($request->hasFile('gambar_lomba')) {
                // Hapus gambar lama
                $competition->clearMediaCollection('gambar_lomba');
                // Upload gambar baru
                $competition->addMediaFromRequest('gambar_lomba')
                    ->toMediaCollection('gambar_lomba');
            }

            \DB::commit();

            return redirect()
                ->route('admin.kompetisi.index')
                ->with('success', 'Data lomba berhasil diperbarui!');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            // Log error untuk debugging
            \Log::error('Error updating competition: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui lomba: ' . $e->getMessage());
        }
    }

    // Menghapus lomba
    public function destroy(Competition $competition)
    {
        try {
            // Mulai transaction
            \DB::beginTransaction();
            
            // Hapus waves terkait
            $competition->waves()->delete();
            
            // Hapus gambar jika ada
            if ($competition->getMedia('gambar_lomba')->count() > 0) {
                $competition->clearMediaCollection('gambar_lomba');
            }
            
            // Hapus kompetisi (akan cascade ke registrations jika ada relasi)
            $competition->delete();
            
            \DB::commit();

            return redirect()
                ->route('admin.kompetisi.index')
                ->with('success', 'Lomba berhasil dihapus beserta data pendaftar terkait.');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            \Log::error('Error deleting competition: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus lomba: ' . $e->getMessage());
        }
    }

    // Method tambahan untuk melihat detail kompetisi (opsional)
    public function show(Competition $competition)
    {
        $competition->load(['waves', 'registrations.user']);
        return view('admin.kompetisi.show', compact('competition'));
    }

    // Method untuk toggle status aktif (opsional)
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