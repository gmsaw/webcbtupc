<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Persiapan Ujian - {{ config('app.name', 'HIMAFI UPC') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div x-data="waitingRoom({{ $waitSeconds }})" x-init="init()"
         class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sm:p-10 text-center relative overflow-hidden">

        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-500"></div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 rounded-full opacity-50 blur-2xl"></div>

        {{-- Icon --}}
        <div class="relative w-24 h-24 mx-auto mb-6 flex items-center justify-center">
            <svg class="animate-spin text-blue-100 w-full h-full absolute inset-0" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="text-blue-600" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg class="w-10 h-10 text-blue-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>

        {{-- Judul --}}
        <h2 class="text-2xl font-black text-gray-900 mb-2" x-text="title"></h2>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed" x-text="message"></p>

        {{-- Countdown --}}
        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                Ujian Dimulai Dalam
            </p>

            <div class="text-5xl font-black text-blue-600 font-mono tabular-nums">
                <span x-text="formattedTime"></span>
            </div>

            <p class="text-xs text-gray-400 mt-3">
                Mohon jangan tutup halaman ini. Anda akan otomatis diarahkan ke ruang ujian.
            </p>

            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-4 overflow-hidden">
                <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000 ease-linear"
                     :style="'width: ' + progress + '%'"></div>
            </div>
        </div>

        {{-- Status --}}
        <div x-show="statusText" x-cloak
             class="mt-4 rounded-xl p-3 text-sm font-medium"
             :class="statusType === 'error'
                ? 'bg-red-50 border border-red-200 text-red-700'
                : (statusType === 'success'
                    ? 'bg-emerald-50 border border-emerald-200 text-emerald-700'
                    : 'bg-amber-50 border border-amber-200 text-amber-700')">
            <span x-text="statusText"></span>
        </div>

        <div class="mt-6 text-[10px] text-gray-400 font-medium">
            Otomatis terhubung oleh sistem CBT UPC
        </div>
    </div>

    <script>
        function waitingRoom(initialWaitSeconds) {
            return {
                // ── State ──
                waitSeconds: initialWaitSeconds,
                totalSeconds: initialWaitSeconds,
                title: 'Mempersiapkan Ujian',
                message: 'Sistem sedang mengalokasikan sesi ujian Anda. Mohon tunggu sebentar.',
                progress: 0,
                statusText: '',
                statusType: 'info',
                isDone: false,
                isChecking: false,
                countdownInterval: null,

                // ── Timestamp-based countdown (anti-throttle) ──
                endTime: 0,

                // ── URL ──
                checkUrl: "{{ route('user.ujian.queue-status', $registration->id) }}",
                redirectFallback: "{{ route('user.ujian.show', $registration->id) }}",
                dashboardUrl: "{{ route('dashboard') }}",

                get formattedTime() {
                    const s = Math.max(0, this.waitSeconds);
                    const m = Math.floor(s / 60);
                    const sec = s % 60;
                    return `${m.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
                },

                // ═══════════════════════════════════════════════════
                // INIT
                // ═══════════════════════════════════════════════════
                async init() {
                    if (this.$nextTick) await this.$nextTick();

                    // Kalau tidak ada delay → langsung cek
                    if (this.waitSeconds <= 0) {
                        this.checkReady();
                        return;
                    }

                    this.updateProgress();
                    this.startCountdown();
                },

                // ═══════════════════════════════════════════════════
                // COUNTDOWN — timestamp-based (anti-throttle)
                // ═══════════════════════════════════════════════════
                startCountdown() {
                    // Tentukan waktu selesai yang PASTI di masa depan
                    this.endTime = Date.now() + (this.waitSeconds * 1000);

                    // Reset interval kalau ada yang lama
                    if (this.countdownInterval) {
                        clearInterval(this.countdownInterval);
                    }

                    this.countdownInterval = setInterval(() => {
                        // Hitung sisa waktu berdasarkan waktu AKTUAL
                        // Ini tetap akurat meskipun browser throttle interval
                        const now = Date.now();
                        const remaining = Math.max(0, Math.round((this.endTime - now) / 1000));

                        this.waitSeconds = remaining;
                        this.updateProgress();

                        if (this.waitSeconds <= 0) {
                            clearInterval(this.countdownInterval);
                            this.countdownInterval = null;
                            this.checkReady();
                        }
                    }, 1000);
                },

                updateProgress() {
                    if (this.totalSeconds <= 0) {
                        this.progress = 100;
                        return;
                    }
                    const done = this.totalSeconds - Math.max(0, this.waitSeconds);
                    this.progress = Math.min(100, Math.round((done / this.totalSeconds) * 100));
                },

                // ═══════════════════════════════════════════════════
                // CEK READY
                // ═══════════════════════════════════════════════════
                async checkReady() {
                    if (this.isDone || this.isChecking) return;
                    this.isChecking = true;

                    this.statusText = 'Memeriksa kesiapan...';
                    this.statusType = 'info';

                    try {
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 10000);

                        const res = await fetch(this.checkUrl, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            signal: controller.signal,
                        });

                        clearTimeout(timeoutId);

                        // ── 403 ──
                        if (res.status === 403) {
                            const data = await res.json().catch(() => ({}));
                            this.isDone = true;

                            if (data.finished) {
                                this.title = 'Ujian Selesai';
                                this.message = data.error || 'Anda sudah menyelesaikan ujian ini.';
                                this.statusText = 'Mengalihkan ke dashboard...';
                                this.statusType = 'success';
                                setTimeout(() => window.location.href = this.dashboardUrl, 2000);
                            } else {
                                this.title = 'Sesi Berakhir';
                                this.message = data.error || 'Sesi tidak valid.';
                                this.statusText = 'Mengalihkan...';
                                this.statusType = 'error';
                                setTimeout(() => window.location.reload(), 2000);
                            }
                            return;
                        }

                        // ── 5xx ──
                        if (res.status >= 500) {
                            this.statusText = 'Server sedang sibuk. Mencoba lagi dalam 5 detik...';
                            this.statusType = 'error';
                            this.isChecking = false;
                            this.waitSeconds = 5;
                            this.totalSeconds = 5;
                            this.updateProgress();
                            this.startCountdown();
                            return;
                        }

                        // ── OK ──
                        const data = await res.json();

                        if (data.ready) {
                            this.title = 'Siap!';
                            this.message = 'Mengalihkan ke ruang ujian...';
                            this.statusText = 'Berhasil! Mengalihkan...';
                            this.statusType = 'success';
                            this.progress = 100;
                            this.isDone = true;

                            setTimeout(() => {
                                window.location.replace(data.redirect || this.redirectFallback);
                            }, 500);
                            return;
                        }

                        // ── Belum waktunya (server bilang masih harus tunggu) ──
                        if (data.wait_seconds && data.wait_seconds > 0) {
                            this.waitSeconds = data.wait_seconds;
                            this.totalSeconds = data.wait_seconds;
                            this.updateProgress();
                            this.statusText = 'Masih menunggu giliran...';
                            this.statusType = 'info';
                            this.isChecking = false;
                            this.startCountdown();
                            return;
                        }

                        // Fallback: coba lagi dalam 5 detik
                        this.waitSeconds = 5;
                        this.totalSeconds = 5;
                        this.updateProgress();
                        this.statusText = 'Menunggu konfirmasi server...';
                        this.statusType = 'info';
                        this.isChecking = false;
                        this.startCountdown();

                    } catch (e) {
                        console.error('Check error:', e);

                        this.statusText = e.name === 'AbortError'
                            ? 'Server tidak merespons. Mencoba lagi...'
                            : 'Koneksi terputus. Mencoba lagi...';
                        this.statusType = 'error';

                        this.waitSeconds = 5;
                        this.totalSeconds = 5;
                        this.updateProgress();
                        this.isChecking = false;
                        this.startCountdown();
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .tabular-nums { font-variant-numeric: tabular-nums; }
    </style>
</body>
</html>