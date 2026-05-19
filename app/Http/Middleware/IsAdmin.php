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
        // Cek apakah user sudah login DAN emailnya adalah email admin
        if (Auth::check() && Auth::user()->email === 'admin@upc.com') {
            return $next($request); // Silakan masuk (Lanjut ke route)
        }

        // Jika bukan admin, tolak akses dan tendang kembali ke dashboard user
        return redirect()->route('dashboard')->with('error', 'Akses Ditolak! Anda tidak memiliki izin untuk masuk ke panel Admin.');
    }
}