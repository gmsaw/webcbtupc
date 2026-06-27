<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Overview Kepanitiaan') }}
            </h2>
            <div class="flex items-center gap-3">
                {{-- Foto Profil Admin --}}
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm">
                    <img src="{{ Auth::user()->getProfilePictureUrlOrDefault('avatar') }}" 
                         alt="Foto Profil" 
                         class="h-8 w-8 rounded-full object-cover border-2 border-indigo-200">
                    <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 opacity-20">
                    <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/></svg>
                </div>
                <div class="absolute bottom-0 left-0 opacity-10">
                    <svg class="w-48 h-48 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ Auth::user()->getProfilePictureUrlOrDefault('thumb') }}" 
                             alt="Admin" 
                             class="h-14 w-14 rounded-full border-3 border-white/50 shadow-lg object-cover">
                        <div>
                            <h3 class="text-3xl font-extrabold text-white mb-1">Selamat Bertugas, {{ Auth::user()->name }}! 🚀</h3>
                            <p class="text-white/80 text-lg max-w-2xl">Pantau perkembangan registrasi peserta dan pastikan server CBT siap untuk menampung ratusan pendaftar hari ini.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 bg-blue-50 w-24 h-24 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Pendaftar</p>
                            <h4 class="text-4xl font-black text-gray-900">{{ $total_peserta }}</h4>
                        </div>
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-2xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <div class="relative z-10 mt-3">
                        <div class="text-xs text-gray-400">Total peserta yang mendaftar</div>
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
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    @if($pending_verifikasi > 0)
                        <a href="{{ route('admin.verifikasi.index') }}" class="relative z-10 inline-flex items-center text-sm font-semibold text-yellow-600 hover:text-yellow-700 mt-4 group">
                            Proses Sekarang 
                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    @else
                        <div class="relative z-10 mt-3 text-xs text-green-600 font-medium">✓ Semua telah terverifikasi</div>
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
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="relative z-10 mt-3">
                        <div class="text-xs text-gray-400">
                            {{ $total_peserta > 0 ? round(($terverifikasi / $total_peserta) * 100) : 0 }}% dari total pendaftar
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Access --}}
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Akses Cepat Panitia
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.verifikasi.index') }}" class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition-all border border-gray-100 hover:border-indigo-200 group">
                        <div class="p-3 bg-indigo-100 rounded-xl group-hover:bg-indigo-200 transition-colors">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <span class="font-semibold text-sm mt-2 text-gray-700 group-hover:text-indigo-600">Verifikasi Data</span>
                        @if($pending_verifikasi > 0)
                            <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full mt-1">{{ $pending_verifikasi }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.kompetisi.index') }}" class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition-all border border-gray-100 hover:border-indigo-200 group">
                        <div class="p-3 bg-purple-100 rounded-xl group-hover:bg-purple-200 transition-colors">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <span class="font-semibold text-sm mt-2 text-gray-700 group-hover:text-indigo-600">Manajemen Lomba</span>
                    </a>

                    <a href="{{ route('admin.peserta.index') }}" class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition-all border border-gray-100 hover:border-indigo-200 group">
                        <div class="p-3 bg-green-100 rounded-xl group-hover:bg-green-200 transition-colors">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="font-semibold text-sm mt-2 text-gray-700 group-hover:text-indigo-600">Manajemen Peserta</span>
                    </a>

                    <a href="{{ route('admin.pengumuman.index') }}" class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition-all border border-gray-100 hover:border-indigo-200 group">
                        <div class="p-3 bg-orange-100 rounded-xl group-hover:bg-orange-200 transition-colors">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <span class="font-semibold text-sm mt-2 text-gray-700 group-hover:text-indigo-600">Pengumuman</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>