<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Daftar soal per lomba, filter berdasarkan babak.
     *
     * URL: /admin/kompetisi/{competition}/soal?babak=penyisihan
     */
    public function index(Request $request, Competition $competition)
    {
        // Babak aktif — default 'penyisihan'
        $babak = $request->get('babak', 'penyisihan');

        // Validasi babak yang diizinkan
        if (!in_array($babak, ['penyisihan', 'semifinal', 'final'], true)) {
            $babak = 'penyisihan';
        }

        // Ambil soal sesuai babak
        $questions = $competition->questions()
            ->where('babak', $babak)
            ->orderBy('id')
            ->get();

        // Statistik jumlah soal per babak
        $stats = [
            'penyisihan' => $competition->questions()->where('babak', 'penyisihan')->count(),
            'semifinal'  => $competition->questions()->where('babak', 'semifinal')->count(),
            'final'      => $competition->questions()->where('babak', 'final')->count(),
        ];

        return view('admin.soal.index', compact(
            'competition',
            'questions',
            'babak',
            'stats'
        ));
    }

    /**
     * Simpan soal baru (dengan babak).
     */
    public function store(Request $request, Competition $competition)
    {
        $request->validate([
            'babak'         => 'required|in:penyisihan,semifinal,final',
            'pertanyaan'    => 'required',
            'opsi_a'        => 'required',
            'opsi_b'        => 'required',
            'opsi_c'        => 'required',
            'opsi_d'        => 'required',
            'jawaban_benar' => 'required|in:A,B,C,D,E',
            'gambar_soal'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Simpan soal
        $question = $competition->questions()->create([
            'babak'         => $request->babak,
            'pertanyaan'    => $request->pertanyaan,
            'opsi_a'        => $request->opsi_a,
            'opsi_b'        => $request->opsi_b,
            'opsi_c'        => $request->opsi_c,
            'opsi_d'        => $request->opsi_d,
            'opsi_e'        => $request->opsi_e,
            'jawaban_benar' => $request->jawaban_benar,
            'bobot_nilai'   => $request->bobot_nilai ?? 1,
        ]);

        // Upload gambar soal (kalau ada)
        if ($request->hasFile('gambar_soal')) {
            $question->addMediaFromRequest('gambar_soal')
                ->toMediaCollection('gambar_soal');
        }

        return redirect()
            ->route('admin.kompetisi.soal.index', [
                'competition' => $competition->id,
                'babak'       => $request->babak,
            ])
            ->with('success', 'Soal babak ' . ucfirst($request->babak) . ' berhasil ditambahkan!');
    }

    /**
     * Hapus soal.
     */
    public function destroy(Question $question)
    {
        // Simpan info babak & competition sebelum dihapus
        $babak         = $question->babak;
        $competitionId = $question->competition_id;

        // Hapus soal (gambar otomatis terhapus via Spatie Media Library cascade)
        $question->delete();

        return redirect()
            ->route('admin.kompetisi.soal.index', [
                'competition' => $competitionId,
                'babak'       => $babak,
            ])
            ->with('success', 'Soal berhasil dihapus.');
    }
}