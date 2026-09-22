<?php

namespace App\Http\Middleware;

use App\Models\Registration;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckExamAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Registration $registration */
        $registration = $request->route('registration');

        // Kalau bukan objek Registration (route model binding gagal)
        if (!$registration instanceof Registration) {
            abort(404);
        }

        if ((int) $registration->user_id !== (int) auth()->id()) {
            abort(403, 'Akses tidak valid.');
        }

        if ($registration->examResult && $registration->examResult->status === 'finished') {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian.');
        }

        $token = $request->cookie('exam_access');

        // 1. Token tidak ada → balik ke persiapan
        if (!$token) {
            return redirect()->route('user.ujian.prepare', $registration);
        }

        // 2. Token tidak valid di cache (expired / dipalsukan)
        $payload = Cache::get("exam_access:{$token}");
        if (!$payload) {
            return redirect()
                ->route('user.ujian.prepare', $registration)
                ->with('error', 'Sesi ujian sudah kedaluwarsa. Silakan tunggu kembali.')
                ->withCookie(cookie()->forget('exam_access'));
        }

        // 3. SATU-SATUNYA CEK registration_id
        if ((int) $payload['registration_id'] !== (int) $registration->id) {
            return redirect()
                ->route('user.ujian.prepare', $registration)
                ->with('error', 'Sesi ujian sebelumnya tidak cocok. Silakan tunggu kembali.')
                ->withCookie(cookie()->forget('exam_access'));
        }

        // 4. Update heartbeat
        Cache::put("exam_access:{$token}", array_merge($payload, [
            'last_seen_at' => now()->toIso8601String(),
        ]), now()->addMinutes(240));

        return $next($request);
    }
}