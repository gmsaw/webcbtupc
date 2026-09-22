<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate limiter: berapa user yang boleh "lolos" ke ujian per menit
        // 60/menit = 1 user/detik. Sesuaikan dengan kapasitas server kamu.
        RateLimiter::for('exam-entry', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'ready' => false,
                        'error' => 'Server sedang sibuk. Mohon tunggu.',
                    ], 429);
                });
        });
    }
}
