<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Competition $competition)
    {
        $questions = $competition->questions()->latest()->get();
        return view('admin.soal.index', compact('competition', 'questions'));
    }

    public function store(Request $request, Competition $competition)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'opsi_a' => 'required', 'opsi_b' => 'required',
            'opsi_c' => 'required', 'opsi_d' => 'required',
            'jawaban_benar' => 'required|in:A,B,C,D,E',
            'gambar_soal' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $question = $competition->questions()->create($request->except('gambar_soal'));

        if ($request->hasFile('gambar_soal')) {
            $question->addMediaFromRequest('gambar_soal')->toMediaCollection('gambar_soal');
        }

        return back()->with('success', 'Soal baru berhasil ditambahkan ke Bank Soal!');
    }

    public function destroy(Question $question)
    {
        $question->delete(); // Gambar otomatis terhapus berkat cascade Spatie
        return back()->with('success', 'Soal berhasil dihapus.');
    }
}