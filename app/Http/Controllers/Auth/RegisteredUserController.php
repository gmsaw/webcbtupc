<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi dengan foto_profil
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'nisn' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'no_wa' => ['required', 'string', 'max:20'],
            'foto_profil' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Wajib, Maksimal 5MB
        ]);

        // 2. Buat User Baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nisn' => $request->nisn,
            'password' => Hash::make($request->password),
            'asal_sekolah' => $request->asal_sekolah,
            'no_wa' => $request->no_wa,
            'status_verifikasi' => 'pending',
        ]);

        // 3. Simpan Foto Profil & Jalankan Kompresi Otomatis
        if ($request->hasFile('foto_profil')) {
            $user->addMediaFromRequest('foto_profil')
                 ->toMediaCollection('foto_profil');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}