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

    <div x-data="waitingRoom()" x-init="init()"
         class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sm:p-10 text-center relative overflow-hidden">

        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-500"></div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 rounded-full opacity-50 blur-2xl"></div>

        <div class="relative w-24 h-24 mx-auto mb-6 flex items-center justify-center">
            <svg class="animate-spin text-blue-100 w-full h-full absolute inset-0" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="text-blue-600" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg class="w-10 h-10 text-blue-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>

        <h2 class="text-2xl font-black text-gray-900 mb-2" x-text="title"></h2>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed" x-text="message"></p>

        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                Posisi Antrian Anda
            </p>
            <div class="flex items-center justify-center gap-2 text-3xl font-black text-blue-600 font-mono">
                <template x-if="position > 0">
                    <span x-text="position"></span>
                </template>
                <template x-if="position === 0">
                    <span class="text-xl text-gray-400">Menghubungkan...</span>
                </template>
            </div>

            <p class="text-xs text-gray-400 mt-2" x-show="eta > 0">
                Estimasi: <span x-text="eta" class="font-bold text-blue-500"></span> detik
            </p>

            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-4 overflow-hidden">
                <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-500 ease-linear"
                     :style="'width: ' + progress + '%'"></div>
            </div>
        </div>

        <div x-show="error" x-cloak class="mt-4 bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-700">
            <span x-text="error"></span>
        </div>

        <div class="mt-6 text-[10px] text-gray-400 font-medium">
            Otomatis terhubung oleh sistem HIMAFI
        </div>
    </div>

    <script>
        function waitingRoom() {
            return {
                position: 0,
                eta: 0,
                progress: 0,
                title: 'Mengalokasikan Sesi',
                message: 'Mohon jangan tutup halaman ini. Sistem sedang menyiapkan ruang ujian dan mengunduh soal Anda dari server pusat.',
                error: '',
                attempts: 0,
                maxAttempts: 60, // ~5 menit

                pollUrl: "{{ route('user.ujian.queue-status', $registration->id) }}",
                redirectFallback: "{{ route('user.ujian.show', $registration->id) }}",

                init() {
                    this.poll();
                },

                async poll() {
                    this.attempts++;

                    if (this.attempts > this.maxAttempts) {
                        this.title = 'Waktu Tunggu Habis';
                        this.message = 'Silakan refresh halaman untuk mencoba lagi.';
                        this.error = 'Terlalu lama menunggu.';
                        return;
                    }

                    try {
                        const res = await fetch(this.pollUrl, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });

                        if (res.status === 403) {
                            const data = await res.json();
                            this.error = data.error || 'Sesi tidak valid.';
                            setTimeout(() => window.location.reload(), 2000);
                            return;
                        }

                        if (res.status === 429) {
                            this.error = 'Server sedang sibuk. Mohon tunggu...';
                            setTimeout(() => this.poll(), 5000);
                            return;
                        }

                        if (!res.ok) throw new Error('HTTP ' + res.status);

                        const data = await res.json();

                        if (data.ready) {
                            this.title = 'Siap!';
                            this.message = 'Mengalihkan ke ruang ujian...';
                            this.progress = 100;
                            setTimeout(() => {
                                window.location.replace(data.redirect || this.redirectFallback);
                            }, 300);
                            return;
                        }

                        this.position = data.position || 0;
                        this.eta = data.eta || 0;
                        this.error = '';

                        if (this.position > 0) {
                            this.progress = Math.min(95, Math.max(5, 100 - (this.position / 2)));
                        }

                    } catch (e) {
                        console.error('Poll error:', e);
                        this.error = 'Koneksi terputus. Mencoba lagi...';
                    }

                    const delay = this.position <= 5 ? 1000 : 3000;
                    setTimeout(() => this.poll(), delay);
                }
            }
        }
    </script>

    <style>[x-cloak] { display: none !important; }</style>
</body>
</html>