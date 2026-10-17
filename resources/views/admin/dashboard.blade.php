<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            {{ __('Administrator Control Panel') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- KOLOM KIRI (2/3) --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- ── HERO BANNER ── --}}
                    <div class="hero-banner relative overflow-hidden rounded-3xl p-8 shadow-xl text-white">
                        {{-- Ornamen SVG ringan --}}
                        <div class="absolute -right-16 -top-16 opacity-15 pointer-events-none">
                            <svg class="w-72 h-72" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/>
                            </svg>
                        </div>
                        <div class="absolute -left-20 -bottom-20 opacity-10 pointer-events-none">
                            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                        </div>

                        <div class="relative z-10">
                            <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm px-3 py-1.5 rounded-full mb-4 border border-white/20">
                                <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                <span class="text-xs font-bold tracking-wider uppercase">Sistem Aktif</span>
                            </div>

                            <h3 class="text-3xl md:text-4xl font-extrabold mb-3 tracking-tight leading-tight">
                                Pusat Kendali <span class="text-cyan-200">HIMAFI UPC</span>
                            </h3>
                            <p class="text-blue-100 text-base md:text-lg max-w-xl leading-relaxed">
                                Kelola pendaftaran, buat sesi kompetisi baru, dan sebarkan pengumuman ke seluruh dashboard peserta dari satu tempat.
                            </p>

                            {{-- Quick actions — tidak query, hanya navigasi --}}
                            <div class="flex flex-wrap gap-3 mt-6">
                                <a href="{{ route('admin.verifikasi.index') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-blue-700 rounded-xl font-bold text-sm hover:bg-blue-50 transition-all shadow-sm hover:shadow-md active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Verifikasi
                                </a>
                                <a href="{{ route('admin.kompetisi.index') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white rounded-xl font-bold text-sm border border-white/20 hover:bg-white/20 transition-all active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Kompetisi
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- ── MENU ADMINISTRATOR ── --}}
                    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                            <h4 class="text-lg font-bold text-gray-900">Menu Administrator</h4>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{-- Hitung jumlah menu tanpa query --}}
                                8 Akses
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $menus = [
                                    [
                                        'route'  => 'admin.verifikasi.index',
                                        'title'  => 'Verifikasi Peserta',
                                        'desc'   => 'Cek kartu pelajar dan validasi pendaftaran lomba.',
                                        'color'  => 'blue',
                                        'icon'   => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                                    ],
                                    [
                                        'route'  => 'admin.peserta.index',
                                        'title'  => 'Data Peserta',
                                        'desc'   => 'Kelola data peserta, edit info, dan hapus akun duplikat.',
                                        'color'  => 'cyan',
                                        'icon'   => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                                    ],
                                    [
                                        'route'  => 'admin.kompetisi.index',
                                        'title'  => 'Buat Lomba / Sesi Baru',
                                        'desc'   => 'Konfigurasi jadwal CBT, aturan, dan pengaturan lomba.',
                                        'color'  => 'indigo',
                                        'icon'   => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                                    ],
                                    [
                                        'route'  => 'admin.pengumuman.index',
                                        'title'  => 'Broadcast Pengumuman',
                                        'desc'   => 'Kirim informasi penting ke seluruh dashboard peserta.',
                                        'color'  => 'yellow',
                                        'icon'   => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
                                    ],
                                    [
                                        'route'  => 'admin.merchandise.index',
                                        'title'  => 'Manajemen Merchandise',
                                        'desc'   => 'Kelola katalog produk fisik dan upload E-Book.',
                                        'color'  => 'orange',
                                        'icon'   => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                                    ],
                                    [
                                        'route'  => 'admin.merchandise.verifikasi',
                                        'title'  => 'Verifikasi Kasir',
                                        'desc'   => 'Cek bukti bayar pembeli E-Book dan Merchandise.',
                                        'color'  => 'pink',
                                        'icon'   => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                    ],
                                    [
                                        'route'  => null, // belum ada
                                        'title'  => 'Manajemen Bank Soal',
                                        'desc'   => 'Import soal via Excel, atur opsi, dan kunci jawaban.',
                                        'color'  => 'green',
                                        'icon'   => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                                    ],
                                ];

                                $colorMap = [
                                    'blue'   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-600',   'hoverBg' => 'hover:bg-blue-50',   'hoverBorder' => 'hover:border-blue-500',   'hoverText' => 'group-hover:text-blue-700',   'hoverIcon' => 'group-hover:bg-blue-600'],
                                    'cyan'   => ['bg' => 'bg-cyan-100',   'text' => 'text-cyan-600',   'hoverBg' => 'hover:bg-cyan-50',   'hoverBorder' => 'hover:border-cyan-500',   'hoverText' => 'group-hover:text-cyan-700',   'hoverIcon' => 'group-hover:bg-cyan-600'],
                                    'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'hoverBg' => 'hover:bg-indigo-50', 'hoverBorder' => 'hover:border-indigo-500', 'hoverText' => 'group-hover:text-indigo-700', 'hoverIcon' => 'group-hover:bg-indigo-600'],
                                    'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'hoverBg' => 'hover:bg-yellow-50', 'hoverBorder' => 'hover:border-yellow-500', 'hoverText' => 'group-hover:text-yellow-700', 'hoverIcon' => 'group-hover:bg-yellow-500'],
                                    'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'hoverBg' => 'hover:bg-orange-50', 'hoverBorder' => 'hover:border-orange-500', 'hoverText' => 'group-hover:text-orange-700', 'hoverIcon' => 'group-hover:bg-orange-600'],
                                    'pink'   => ['bg' => 'bg-pink-100',   'text' => 'text-pink-600',   'hoverBg' => 'hover:bg-pink-50',   'hoverBorder' => 'hover:border-pink-500',   'hoverText' => 'group-hover:text-pink-700',   'hoverIcon' => 'group-hover:bg-pink-600'],
                                    'green'  => ['bg' => 'bg-green-100',  'text' => 'text-green-600',  'hoverBg' => 'hover:bg-green-50',  'hoverBorder' => 'hover:border-green-500',  'hoverText' => 'group-hover:text-green-700',  'hoverIcon' => 'group-hover:bg-green-600'],
                                ];
                            @endphp

                            @foreach($menus as $menu)
                                @php $c = $colorMap[$menu['color']] ?? $colorMap['blue']; @endphp

                                @if($menu['route'])
                                    <a href="{{ route($menu['route']) }}"
                                       class="group relative flex items-start gap-4 p-5 rounded-2xl border border-gray-200 {{ $c['hoverBg'] }} {{ $c['hoverBorder'] }} transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                                @else
                                    <div class="group relative flex items-start gap-4 p-5 rounded-2xl border border-gray-200 bg-gray-50/50 opacity-60 cursor-not-allowed">
                                @endif

                                        {{-- Ikon --}}
                                        <div class="p-3 {{ $c['bg'] }} {{ $c['text'] }} rounded-xl {{ $c['hoverIcon'] }} group-hover:text-white transition-colors duration-300 shrink-0">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}"/>
                                            </svg>
                                        </div>

                                        {{-- Teks --}}
                                        <div class="flex-1 min-w-0">
                                            <h5 class="font-bold text-gray-900 {{ $c['hoverText'] }} transition-colors">
                                                {{ $menu['title'] }}
                                                @if(!$menu['route'])
                                                    <span class="ml-1 text-[10px] font-bold uppercase text-gray-400 bg-gray-200 px-1.5 py-0.5 rounded">Segera</span>
                                                @endif
                                            </h5>
                                            <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $menu['desc'] }}</p>
                                        </div>

                                        {{-- Arrow (muncul saat hover) --}}
                                        @if($menu['route'])
                                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 self-center shrink-0">
                                                <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        @endif

                                @if($menu['route'])
                                    </a>
                                @else
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- KOLOM KANAN (1/3) --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div class="space-y-8">

                    {{-- ── KARTU PROFIL ── --}}
                    <div class="relative bg-white rounded-3xl p-6 border border-gray-100 shadow-sm overflow-hidden">
                        {{-- Background gradient dekoratif --}}
                        <div class="absolute top-0 left-0 right-0 h-28 bg-gradient-to-br from-blue-500 via-cyan-500 to-teal-400"></div>
                        <div class="absolute top-0 right-0 w-40 h-40 opacity-15 pointer-events-none">
                            <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                        </div>

                        <div class="relative z-10 flex flex-col items-center text-center pt-6">
                            {{-- Avatar --}}
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 text-white flex items-center justify-center text-3xl font-bold shadow-xl mb-4 border-4 border-white ring-4 ring-white/50 overflow-hidden">
                                @if(Auth::user()->hasMedia('foto_profil'))
                                    <img src="{{ Auth::user()->getFirstMediaUrl('foto_profil') }}"
                                         alt="Profil Admin"
                                         loading="lazy"
                                         class="w-full h-full object-cover">
                                @else
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                @endif
                            </div>

                            <h4 class="text-xl font-bold text-gray-900 truncate max-w-full">{{ Auth::user()->name }}</h4>
                            <p class="text-sm text-gray-500 mb-1 truncate max-w-full">{{ Auth::user()->email }}</p>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full mt-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                ADMIN
                            </span>

                            <div class="flex gap-3 w-full mt-6">
                                <a href="{{ route('profile.edit') }}"
                                   class="flex-1 bg-gray-50 hover:bg-blue-50 text-gray-700 hover:text-blue-700 font-semibold py-2.5 px-4 rounded-xl border border-gray-200 hover:border-blue-200 transition-colors text-sm flex justify-center items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 px-4 rounded-xl border border-red-100 hover:border-red-200 transition-colors text-sm flex justify-center items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ── STATISTIK ── --}}
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Statistik Real-time
                        </h4>

                        <div class="space-y-4">
                            {{-- Total Pendaftar --}}
                            <div class="group flex justify-between items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-2xl border border-blue-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <span class="font-medium text-blue-900">Total Pendaftar</span>
                                </div>
                                <span class="text-2xl font-black text-blue-900">{{ $total_peserta ?? 0 }}</span>
                            </div>

                            {{-- Pending Verifikasi --}}
                            <div class="group flex justify-between items-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100/50 rounded-2xl border border-yellow-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="font-medium text-yellow-900">Menunggu Verifikasi</span>
                                </div>
                                <span class="text-2xl font-black text-yellow-900">{{ $pending_verifikasi ?? 0 }}</span>
                            </div>

                            {{-- Siap Ujian --}}
                            <div class="group flex justify-between items-center p-4 bg-gradient-to-br from-green-50 to-green-100/50 rounded-2xl border border-green-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="font-medium text-green-900">Siap Ujian CBT</span>
                                </div>
                                <span class="text-2xl font-black text-green-900">{{ $terverifikasi ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- ── SYSTEM STATUS ── --}}
                    @php
                        try {
                            \Illuminate\Support\Facades\DB::connection()->getPdo();
                            $dbStatus = 'Connected';
                            $dbColor  = 'text-green-400';
                            $dbDot    = 'bg-green-400';
                        } catch (\Exception $e) {
                            $dbStatus = 'Error/Offline';
                            $dbColor  = 'text-red-400';
                            $dbDot    = 'bg-red-400';
                        }
                        $appEnv      = config('app.env');
                        $cacheDriver = config('cache.default');
                    @endphp

                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-6 text-gray-300 shadow-sm border border-gray-800">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-2 h-2 {{ $dbDot }} rounded-full animate-pulse"></div>
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider">System Status</h4>
                        </div>
                        <ul class="text-xs space-y-3 font-mono">
                            <li class="flex justify-between border-b border-gray-700/50 pb-2">
                                <span class="text-gray-400">Database (MySQL)</span>
                                <span class="{{ $dbColor }} font-bold">{{ $dbStatus }}</span>
                            </li>
                            <li class="flex justify-between border-b border-gray-700/50 pb-2">
                                <span class="text-gray-400">Cache Driver</span>
                                <span class="text-blue-400 font-bold">{{ ucfirst($cacheDriver) }}</span>
                            </li>
                            <li class="flex justify-between border-b border-gray-700/50 pb-2">
                                <span class="text-gray-400">App Environment</span>
                                <span class="text-indigo-400 font-bold">{{ ucfirst($appEnv) }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span class="text-gray-400">Server Time</span>
                                <span class="text-gray-300">{{ now()->format('H:i:s T') }}</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- CSS OPTIMASI (GPU-accelerated, tidak query, tidak request) --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <style>
        /* Hero gradient — static, tidak ada animasi berat */
        .hero-banner {
            background: linear-gradient(135deg, #1e40af 0%, #0891b2 50%, #06b6d4 100%);
        }

        /* Animasi pulse ringan — hanya opacity, tidak reflow */
        @keyframes softPulse {
            0%, 100% { opacity: 1; }
            50%      { opacity: 0.4; }
        }

        .animate-pulse {
            animation: softPulse 2s ease-in-out infinite;
            will-change: opacity;
        }

        /* Efek hover card — GPU-accelerated */
        .group {
            will-change: transform;
        }

        /* Prevent select di area non-teks */
        .pointer-events-none {
            pointer-events: none;
        }
    </style>
</x-app-layout>