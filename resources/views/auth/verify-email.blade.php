<x-guest-layout>
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-4 shadow-inner border border-blue-100">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Cek Email Anda</h1>
        <p class="mt-3 text-sm text-gray-500 leading-relaxed">
            Terima kasih telah mendaftar di HIMAFI UPC 2026! <br>
            Sebelum Anda bisa mengakses Dashboard Peserta, mohon verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke email Anda.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 font-bold text-sm text-green-700 bg-green-50 rounded-xl border border-green-200 text-center flex items-center justify-center gap-2 animate-pulse">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Tautan verifikasi baru telah berhasil dikirim!
        </div>
    @endif

    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 flex flex-col gap-4">
        <p class="text-xs text-center text-gray-500 font-medium">Belum menerima email verifikasi dari kami? Cek folder Spam/Junk, atau minta sistem mengirimkan ulang.</p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto flex justify-center items-center gap-2 py-2.5 px-5 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Kirim Ulang Tautan
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto flex justify-center py-2.5 px-5 border border-gray-300 rounded-xl shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Keluar Akun
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>