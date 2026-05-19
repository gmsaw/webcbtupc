<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Overview Kepanitiaan') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-gradient-to-r from-gray-900 to-blue-900 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 opacity-20">
                    <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-3xl font-extrabold text-white mb-2">Selamat Bertugas, Admin HIMAFI! 🚀</h3>
                    <p class="text-blue-200 text-lg max-w-2xl">Pantau perkembangan registrasi peserta dan pastikan server CBT siap untuk menampung ratusan pendaftar hari ini.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 bg-blue-50 w-24 h-24 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Pendaftar</p>
                            <h4 class="text-4xl font-black text-gray-900">{{ $total_peserta }}</h4>
                        </div>
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-2xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 bg-yellow-50 w-24 h-24 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Perlu Verifikasi</p>
                            <h4 class="text-4xl font-black text-gray-900">{{ $pending_verifikasi }}</h4>
                        </div>
                        <div class="p-3 bg-yellow-100 text-yellow-600 rounded-2xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    @if($pending_verifikasi > 0)
                        <a href="{{ route('admin.verifikasi') }}" class="relative z-10 inline-flex items-center text-sm font-semibold text-yellow-600 hover:text-yellow-700 mt-4 group">
                            Proses Sekarang 
                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    @endif
                </div>

                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 bg-green-50 w-24 h-24 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Terverifikasi</p>
                            <h4 class="text-4xl font-black text-gray-900">{{ $terverifikasi }}</h4>
                        </div>
                        <div class="p-3 bg-green-100 text-green-600 rounded-2xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Akses Cepat Panitia
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.verifikasi') }}" class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-colors border border-gray-100 border-dashed">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="font-semibold text-sm">Verifikasi Data</span>
                    </a>
                    <button class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-colors border border-gray-100 border-dashed opacity-50 cursor-not-allowed" title="Fitur dalam pengembangan">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span class="font-semibold text-sm">Bank Soal</span>
                    </button>
                    <button class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-colors border border-gray-100 border-dashed opacity-50 cursor-not-allowed" title="Fitur dalam pengembangan">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span class="font-semibold text-sm">Data Pembayaran</span>
                    </button>
                    <button class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-colors border border-gray-100 border-dashed opacity-50 cursor-not-allowed" title="Fitur dalam pengembangan">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="font-semibold text-sm">Monitoring CBT</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>