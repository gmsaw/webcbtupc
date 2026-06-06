<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Persiapan Ujian - {{ config('app.name', 'HIMAFI UPC') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div x-data="waitingRoom()" class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sm:p-10 text-center relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-500"></div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 rounded-full opacity-50 blur-2xl"></div>
        
        <div class="relative w-24 h-24 mx-auto mb-6 flex items-center justify-center">
            <svg class="animate-spin text-blue-100 w-full h-full absolute inset-0" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="text-blue-600" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg class="w-10 h-10 text-blue-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>

        <h2 class="text-2xl font-black text-gray-900 mb-2">Mengalokasikan Sesi</h2>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed">
            Mohon jangan tutup halaman ini. Sistem sedang menyiapkan ruang ujian dan mengunduh soal Anda dari server pusat untuk mencegah penumpukan jalur data.
        </p>

        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Memasuki ujian dalam</p>
            <div class="flex items-center justify-center gap-2 text-3xl font-black text-blue-600 font-mono">
                <span x-text="countdown"></span>
                <span class="text-lg text-blue-400 lowercase tracking-normal">detik</span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-4 overflow-hidden">
                <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000 ease-linear" :style="'width: ' + progress + '%'"></div>
            </div>
        </div>

        <div class="mt-6 text-[10px] text-gray-400 font-medium">
            Otomatis terhubung oleh sistem HIMAFI
        </div>
    </div>

    <script>
        function waitingRoom() {
            return {
                // Menentukan delay acak antara 5 hingga 15 detik untuk memecah beban server
                countdown: Math.floor(Math.random() * (15 - 5 + 1)) + 5,
                initialTime: 0,
                progress: 0,

                init() {
                    this.initialTime = this.countdown;
                    
                    let interval = setInterval(() => {
                        this.countdown--;
                        this.progress = ((this.initialTime - this.countdown) / this.initialTime) * 100;

                        if (this.countdown <= 0) {
                            clearInterval(interval);
                            this.progress = 100;
                            // Redirect ke Controller untuk Query Soal sesungguhnya
                            window.location.replace("{{ route('user.ujian.show', $registration->id) }}");
                        }
                    }, 1000);
                }
            }
        }
    </script>
</body>
</html>