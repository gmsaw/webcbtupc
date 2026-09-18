<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Daftar semua email admin dan panitia yang diizinkan
        $allowedAdmins = [
            'admin@upc.com',
            'aridaniswara6@gmail.com',
            'tambun.24008@student.unud.ac.id',
            'alvinandaa26@gmail.com',
            'dinanti.har03@gmail.com',
            'widnyana.24013@student.unud.ac.id',
            'nandana.2508521055@student.unud.ac.id',
        ];

        // Cek apakah user sudah login DAN emailnya ada di dalam daftar $allowedAdmins
        if (Auth::check() && in_array(Auth::user()->email, $allowedAdmins)) {
            return $next($request); // Silakan masuk
        }

        // Jika bukan admin/panitia, tolak akses
        return redirect()->route('dashboard')->with('error', 'Akses Ditolak! Anda tidak memiliki izin untuk masuk ke panel Admin.');
    }
}