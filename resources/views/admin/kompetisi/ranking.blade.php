<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.verifikasi.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Papan Peringkat Akhir') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section dengan Background Gradient --}}
        <div class="relative mb-10 overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 p-8 shadow-2xl">
            <div class="absolute inset-0 opacity-20">
                <svg class="absolute -top-20 -right-20 w-64 h-64 text-white" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                <svg class="absolute -bottom-20 -left-20 w-48 h-48 text-white" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                <svg class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 text-white opacity-10" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
            </div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-8 h-8 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-white/80 font-medium text-sm tracking-wider uppercase">🏆 Papan Peringkat</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-white leading-tight">
                        {{ $competition->nama_lomba }}
                    </h1>
                    <p class="text-white/80 text-sm mt-1 font-medium">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            {{ $registrations->count() }} Peserta
                        </span>
                        <span class="mx-2">•</span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ $registrations->where('examResult.score', '>', 0)->count() }} Sudah Ujian
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.kompetisi.export', $competition->id) }}" class="px-5 py-2.5 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl font-bold text-sm transition-all border border-white/20 flex items-center gap-2 shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Ekspor Data
                    </a>
                </div>
            </div>
        </div>

        {{-- Podium untuk Top 3 --}}
        @if($registrations->count() >= 3)
            <div class="mb-12">
                <div class="flex flex-col md:flex-row items-end justify-center gap-4 md:gap-0">
                    {{-- Juara 2 --}}
                    <div class="flex-1 max-w-xs w-full order-2 md:order-1">
                        <div class="bg-gradient-to-b from-slate-200 to-slate-100 rounded-t-3xl p-6 text-center transform hover:scale-105 transition-all duration-300 shadow-lg">
                            <div class="relative">
                                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-slate-300 to-slate-400 flex items-center justify-center text-4xl font-black text-white shadow-xl mb-3">
                                    2
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-slate-400 rounded-full flex items-center justify-center text-white text-xs font-black shadow-lg">🥈</div>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg truncate">{{ $registrations[1]->user->name }}</h3>
                            <p class="text-xs text-slate-500 truncate">{{ $registrations[1]->user->asal_sekolah }}</p>
                            <div class="mt-3 text-3xl font-black text-slate-700">{{ $registrations[1]->examResult->score ?? 0 }}</div>
                            <div class="text-xs text-slate-400 font-medium">Skor Akhir</div>
                        </div>
                        <div class="h-24 bg-gradient-to-b from-slate-200 to-slate-300 rounded-b-3xl flex items-center justify-center text-slate-600 font-bold text-sm shadow-lg">
                            JUARA 2
                        </div>
                    </div>

                    {{-- Juara 1 --}}
                    <div class="flex-1 max-w-xs w-full order-1 md:order-2 transform md:-translate-y-8">
                        <div class="bg-gradient-to-b from-yellow-400 via-yellow-300 to-yellow-200 rounded-t-3xl p-6 text-center transform hover:scale-105 transition-all duration-300 shadow-2xl relative overflow-hidden">
                            <div class="absolute inset-0 opacity-20">
                                <svg class="absolute -top-10 -right-10 w-32 h-32 text-yellow-600" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                                <svg class="absolute -bottom-10 -left-10 w-24 h-24 text-yellow-600" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                            </div>
                            <div class="relative">
                                <div class="w-28 h-28 mx-auto rounded-full bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center text-5xl font-black text-white shadow-2xl mb-3">
                                    1
                                </div>
                                <div class="absolute -top-2 -right-2 w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-xl shadow-lg">👑</div>
                                <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                                    <svg class="w-12 h-12 text-yellow-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </div>
                            </div>
                            <h3 class="font-bold text-slate-800 text-xl truncate">{{ $registrations[0]->user->name }}</h3>
                            <p class="text-xs text-slate-600 truncate">{{ $registrations[0]->user->asal_sekolah }}</p>
                            <div class="mt-3 text-4xl font-black text-yellow-700">{{ $registrations[0]->examResult->score ?? 0 }}</div>
                            <div class="text-xs text-yellow-700 font-medium">Skor Akhir</div>
                        </div>
                        <div class="h-28 bg-gradient-to-b from-yellow-400 to-yellow-500 rounded-b-3xl flex items-center justify-center text-white font-bold text-lg shadow-2xl relative">
                            🏆 JUARA 1
                            <div class="absolute inset-0 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 animate-shine"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Juara 3 --}}
                    <div class="flex-1 max-w-xs w-full order-3">
                        <div class="bg-gradient-to-b from-orange-300 to-orange-200 rounded-t-3xl p-6 text-center transform hover:scale-105 transition-all duration-300 shadow-lg">
                            <div class="relative">
                                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center text-4xl font-black text-white shadow-xl mb-3">
                                    3
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-orange-400 rounded-full flex items-center justify-center text-white text-xs font-black shadow-lg">🥉</div>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg truncate">{{ $registrations[2]->user->name }}</h3>
                            <p class="text-xs text-slate-500 truncate">{{ $registrations[2]->user->asal_sekolah }}</p>
                            <div class="mt-3 text-3xl font-black text-orange-700">{{ $registrations[2]->examResult->score ?? 0 }}</div>
                            <div class="text-xs text-orange-600 font-medium">Skor Akhir</div>
                        </div>
                        <div class="h-20 bg-gradient-to-b from-orange-300 to-orange-400 rounded-b-3xl flex items-center justify-center text-white font-bold text-sm shadow-lg">
                            JUARA 3
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabel Ranking --}}
        <div class="bg-white rounded-[2rem] shadow-2xl shadow-indigo-900/10 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-100">
                            <th class="p-5 font-black text-indigo-600 text-xs uppercase tracking-wider text-center w-20">Peringkat</th>
                            <th class="p-5 font-black text-indigo-600 text-xs uppercase tracking-wider">Nama Peserta</th>
                            <th class="p-5 font-black text-indigo-600 text-xs uppercase tracking-wider hidden md:table-cell">Asal Sekolah</th>
                            <th class="p-5 font-black text-indigo-600 text-xs uppercase tracking-wider text-right pr-8">Skor Akhir</th>
                            <th class="p-5 font-black text-indigo-600 text-xs uppercase tracking-wider text-center hidden lg:table-cell">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($registrations as $index => $reg)
                            @php
                                $isTop3 = $index < 3;
                                $medalClass = '';
                                $medalIcon = '';
                                if ($index === 0) {
                                    $medalClass = 'bg-yellow-400 text-white shadow-lg shadow-yellow-400/40';
                                    $medalIcon = '🥇';
                                } elseif ($index === 1) {
                                    $medalClass = 'bg-slate-300 text-white shadow-lg shadow-slate-300/40';
                                    $medalIcon = '🥈';
                                } elseif ($index === 2) {
                                    $medalClass = 'bg-orange-400 text-white shadow-lg shadow-orange-400/40';
                                    $medalIcon = '🥉';
                                }
                            @endphp
                            <tr class="hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-transparent transition-all duration-300 {{ $isTop3 ? 'bg-gradient-to-r from-amber-50/50 to-transparent' : '' }}">
                                
                                <td class="p-5 text-center">
                                    @if($isTop3)
                                        <div class="w-12 h-12 mx-auto {{ $medalClass }} rounded-full flex items-center justify-center text-2xl font-black transform hover:scale-110 transition-all duration-300">
                                            {{ $index + 1 }}
                                        </div>
                                    @else
                                        <div class="w-10 h-10 mx-auto bg-slate-100 text-slate-500 rounded-full flex items-center justify-center font-bold text-sm hover:bg-slate-200 transition-colors">
                                            {{ $index + 1 }}
                                        </div>
                                    @endif
                                </td>

                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center font-bold text-indigo-600 text-sm">
                                            {{ substr($reg->user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-base {{ $isTop3 ? 'text-indigo-700' : '' }}">
                                                {{ $reg->user->name }}
                                                @if($isTop3)
                                                    <span class="ml-1 text-sm">{{ $medalIcon }}</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-400">{{ $reg->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="p-5 text-sm font-medium text-slate-600 hidden md:table-cell">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 rounded-full border border-slate-200 text-xs">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        {{ $reg->user->asal_sekolah ?? 'Tidak tersedia' }}
                                    </span>
                                </td>
                                
                                <td class="p-5 text-right pr-8">
                                    <div class="inline-flex flex-col items-end">
                                        <span class="text-2xl font-black {{ $isTop3 ? 'text-amber-500' : 'text-slate-700' }}">
                                            {{ $reg->examResult->score ?? 0 }}
                                        </span>
                                        @if($isTop3)
                                            <span class="text-xs text-amber-400 font-medium">⭐ Top Performer</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-5 text-center hidden lg:table-cell">
                                    @if($reg->examResult->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Selesai
                                        </span>
                                    @elseif($reg->examResult->status === 'in_progress')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                                            <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Sedang Ujian
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Belum
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-slate-500 font-medium text-lg">Belum ada data peserta yang terverifikasi atau menyelesaikan ujian.</p>
                                        <p class="text-slate-400 text-sm">Tunggu hingga peserta menyelesaikan ujian untuk melihat papan peringkat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Tabel --}}
            <div class="bg-slate-50/50 px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-slate-500">
                    Menampilkan <span class="font-bold text-slate-700">{{ $registrations->count() }}</span> peserta
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-400">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                        Juara 1
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                        Juara 2
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-orange-400"></span>
                        Juara 3
                    </span>
                </div>
            </div>
        </div>

        {{-- Statistik Tambahan --}}
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 shadow-lg shadow-indigo-900/5 border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800">{{ $registrations->count() }}</p>
                        <p class="text-xs text-slate-500 font-medium">Total Peserta</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-lg shadow-indigo-900/5 border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800">{{ $registrations->where('examResult.status', 'completed')->count() }}</p>
                        <p class="text-xs text-slate-500 font-medium">Selesai Ujian</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-lg shadow-indigo-900/5 border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800">{{ $registrations->where('examResult.score', '>', 80)->count() }}</p>
                        <p class="text-xs text-slate-500 font-medium">Skor > 80</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-lg shadow-indigo-900/5 border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800">{{ $registrations->avg('examResult.score') ?? 0 }}</p>
                        <p class="text-xs text-slate-500 font-medium">Rata-rata Skor</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- CSS Animasi Kustom --}}
    <style>
        @keyframes shine {
            from {
                transform: translateX(-100%) skewX(-12deg);
            }
            to {
                transform: translateX(200%) skewX(-12deg);
            }
        }
        .animate-shine {
            animation: shine 2s infinite;
        }
        @keyframes pulse-slow {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s infinite;
        }
    </style>
</x-app-layout>