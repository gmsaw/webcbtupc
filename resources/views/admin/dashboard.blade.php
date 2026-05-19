<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            {{ __('Administrator Control Panel') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="bg-gradient-to-r from-blue-800 to-cyan-600 rounded-3xl p-8 shadow-lg text-white relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 opacity-20">
                            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/></svg>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-3xl font-extrabold mb-2">Pusat Kendali HIMAFI UPC</h3>
                            <p class="text-blue-100 text-lg max-w-xl">Kelola pendaftaran, buat sesi kompetisi baru, dan sebarkan pengumuman ke seluruh dashboard peserta dari satu tempat.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                        <h4 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Menu Administrator</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('admin.verifikasi') }}" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all">
                                <div class="p-3 bg-blue-100 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 group-hover:text-blue-700">Verifikasi Peserta</h5>
                                    <p class="text-sm text-gray-500 mt-1">Cek kartu pelajar dan validasi pendaftaran lomba.</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.peserta.index') }}" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-cyan-500 hover:bg-cyan-50 transition-all">
                                <div class="p-3 bg-cyan-100 text-cyan-600 rounded-xl group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 group-hover:text-cyan-700">Data Peserta</h5>
                                    <p class="text-sm text-gray-500 mt-1">Kelola data peserta, edit info, dan hapus akun duplikat.</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.kompetisi.index') }}" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition-all">
                                <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 group-hover:text-indigo-700">Buat Lomba / Sesi Baru</h5>
                                    <p class="text-sm text-gray-500 mt-1">Konfigurasi jadwal CBT, aturan, dan pengaturan lomba.</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.pengumuman.index') }}" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-yellow-500 hover:bg-yellow-50 transition-all">
                                <div class="p-3 bg-yellow-100 text-yellow-600 rounded-xl group-hover:bg-yellow-500 group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 group-hover:text-yellow-700">Broadcast Pengumuman</h5>
                                    <p class="text-sm text-gray-500 mt-1">Kirim informasi penting ke seluruh dashboard peserta.</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.merchandise.index') }}" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-orange-500 hover:bg-orange-50 transition-all">
                                <div class="p-3 bg-orange-100 text-orange-600 rounded-xl group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 group-hover:text-orange-700">Manajemen Merchandise</h5>
                                    <p class="text-sm text-gray-500 mt-1">Kelola katalog produk fisik dan upload E-Book.</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.merchandise.verifikasi') }}" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-pink-500 hover:bg-pink-50 transition-all">
                                <div class="p-3 bg-pink-100 text-pink-600 rounded-xl group-hover:bg-pink-600 group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 group-hover:text-pink-700">Verifikasi Kasir</h5>
                                    <p class="text-sm text-gray-500 mt-1">Cek bukti bayar pembeli E-Book dan Merchandise.</p>
                                </div>
                            </a>

                            <a href="#" class="group flex items-start gap-4 p-5 rounded-2xl border border-gray-200 hover:border-green-500 hover:bg-green-50 transition-all">
                                <div class="p-3 bg-green-100 text-green-600 rounded-xl group-hover:bg-green-600 group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 group-hover:text-green-700">Manajemen Bank Soal</h5>
                                    <p class="text-sm text-gray-500 mt-1">Import soal via Excel, atur opsi, dan kunci jawaban.</p>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col items-center text-center relative overflow-hidden">
                        <div class="absolute top-0 w-full h-24 bg-gradient-to-b from-blue-50 to-white"></div>
                        
                        <div class="relative z-10 w-24 h-24 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 text-white flex items-center justify-center text-3xl font-bold shadow-lg mb-4 border-4 border-white ring-2 ring-gray-50 overflow-hidden">
                            @if(Auth::user()->hasMedia('foto_profil'))
                                <img src="{{ Auth::user()->getFirstMediaUrl('foto_profil') }}" alt="Profil Admin" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->name, 0, 1) }}
                            @endif
                        </div>
                        
                        <h4 class="relative z-10 text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h4>
                        <p class="relative z-10 text-sm text-gray-500 mb-6">{{ Auth::user()->email }}</p>

                        <div class="relative z-10 flex gap-3 w-full">
                            <a href="{{ route('profile.edit') }}" class="flex-1 bg-gray-50 hover:bg-blue-50 text-gray-700 hover:text-blue-700 font-semibold py-2.5 px-4 rounded-xl border border-gray-200 hover:border-blue-200 transition-colors text-sm flex justify-center items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit Profil
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 px-4 rounded-xl border border-red-100 hover:border-red-200 transition-colors text-sm flex justify-center items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Statistik Real-time</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <span class="font-medium text-gray-700">Total Pendaftar</span>
                                </div>
                                <span class="text-2xl font-black text-gray-900">{{ $total_peserta }}</span>
                            </div>

                            <div class="flex justify-between items-center p-4 bg-yellow-50 rounded-2xl border border-yellow-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <span class="font-medium text-yellow-800">Menunggu Verifikasi</span>
                                </div>
                                <span class="text-2xl font-black text-yellow-900">{{ $pending_verifikasi }}</span>
                            </div>

                            <div class="flex justify-between items-center p-4 bg-green-50 rounded-2xl border border-green-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-200 flex items-center justify-center text-green-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <span class="font-medium text-green-800">Siap Ujian CBT</span>
                                </div>
                                <span class="text-2xl font-black text-green-900">{{ $terverifikasi }}</span>
                            </div>
                        </div>
                    </div>

                    @php
                        try {
                            \Illuminate\Support\Facades\DB::connection()->getPdo();
                            $dbStatus = 'Connected';
                            $dbColor = 'text-green-400';
                            $dbDot = 'bg-green-400';
                        } catch (\Exception $e) {
                            $dbStatus = 'Error/Offline';
                            $dbColor = 'text-red-400';
                            $dbDot = 'bg-red-400';
                        }
                        $appEnv = config('app.env');
                        $cacheDriver = config('cache.default');
                    @endphp

                    <div class="bg-gray-900 rounded-3xl p-6 text-gray-300 shadow-sm border border-gray-800">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-2 h-2 {{ $dbDot }} rounded-full animate-pulse"></div>
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider">System Status</h4>
                        </div>
                        <ul class="text-xs space-y-3 font-mono">
                            <li class="flex justify-between border-b border-gray-800 pb-2">
                                <span>Database (MySQL)</span> <span class="{{ $dbColor }}">{{ $dbStatus }}</span>
                            </li>
                            <li class="flex justify-between border-b border-gray-800 pb-2">
                                <span>Cache Driver</span> <span class="text-blue-400">{{ ucfirst($cacheDriver) }}</span>
                            </li>
                            <li class="flex justify-between border-b border-gray-800 pb-2">
                                <span>App Environment</span> <span class="text-indigo-400">{{ ucfirst($appEnv) }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Server Time</span> <span class="text-gray-400">{{ now()->format('H:i:s T') }}</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>