<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-indigo-100 rounded-xl">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ __('Manajemen & Verifikasi Lomba') }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Kelola pendaftaran dan verifikasi peserta per kompetisi</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Statistik Header --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-800">{{ $competitions->count() }}</p>
                        <p class="text-xs text-gray-500 font-medium">Total Lomba</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        @php
                            $now = \Carbon\Carbon::now();
                            $activeCount = $competitions->filter(function($comp) use ($now) {
                                if (!$comp->is_active || !$comp->tanggal_mulai || !$comp->tanggal_selesai) {
                                    return false;
                                }
                                $registrationStart = \Carbon\Carbon::parse($comp->tanggal_mulai);
                                $registrationEnd = \Carbon\Carbon::parse($comp->tanggal_selesai);
                                return $now->between($registrationStart, $registrationEnd);
                            })->count();
                        @endphp
                        <p class="text-2xl font-black text-gray-800">{{ $activeCount }}</p>
                        <p class="text-xs text-gray-500 font-medium">Lomba Aktif (Pendaftaran)</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        @php
                            $now = \Carbon\Carbon::now();
                            $upcomingCount = $competitions->filter(function($comp) use ($now) {
                                if (!$comp->tanggal_mulai) return false;
                                $registrationStart = \Carbon\Carbon::parse($comp->tanggal_mulai);
                                return $now->lt($registrationStart);
                            })->count();
                        @endphp
                        <p class="text-2xl font-black text-gray-800">{{ $upcomingCount }}</p>
                        <p class="text-xs text-gray-500 font-medium">Akan Datang</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-800">{{ $competitions->sum('registrations_count') }}</p>
                        <p class="text-xs text-gray-500 font-medium">Total Pendaftar</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid Kompetisi --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($competitions as $comp)
                @php
                    $now = \Carbon\Carbon::now();
                    
                    // Cek tanggal pendaftaran
                    $registrationStart = $comp->tanggal_mulai ? \Carbon\Carbon::parse($comp->tanggal_mulai) : null;
                    $registrationEnd = $comp->tanggal_selesai ? \Carbon\Carbon::parse($comp->tanggal_selesai) : null;
                    
                    // Cek waktu pelaksanaan
                    $examStart = $comp->waktu_pelaksanaan ? \Carbon\Carbon::parse($comp->waktu_pelaksanaan) : null;
                    $examEnd = $examStart && $comp->durasi_menit ? $examStart->copy()->addMinutes((int)$comp->durasi_menit) : null;
                    
                    // Status dengan logika yang benar
                    $isActive = false;
                    $isRegistrationOpen = false;
                    $isExamEnded = false;
                    $isRegistrationClosed = false;
                    $isUpcoming = false;
                    
                    if ($registrationStart && $registrationEnd) {
                        $isRegistrationOpen = $now->between($registrationStart, $registrationEnd);
                        $isRegistrationClosed = $now->gt($registrationEnd);
                        $isUpcoming = $now->lt($registrationStart);
                    }
                    
                    if ($examEnd) {
                        $isExamEnded = $now->gt($examEnd);
                    }
                    
                    // Aktif jika: lomba aktif, pendaftaran terbuka, dan belum berakhir
                    $isActive = $comp->is_active && $isRegistrationOpen && !$isExamEnded;
                    
                    // Badge status
                    $statusBadge = '';
                    $headerGradient = '';
                    $icon = '';
                    
                    if ($isActive) {
                        $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500 text-white rounded-full text-xs font-bold shadow-lg"><span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>Aktif</span>';
                        $headerGradient = 'from-emerald-500 to-teal-500';
                        $icon = '🏆';
                    } elseif ($isUpcoming) {
                        $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-500 text-white rounded-full text-xs font-bold shadow-lg"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Akan Datang</span>';
                        $headerGradient = 'from-blue-500 to-indigo-500';
                        $icon = '📅';
                    } elseif ($isRegistrationClosed && !$isExamEnded) {
                        $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-500 text-white rounded-full text-xs font-bold shadow-lg"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Pendaftaran Tutup</span>';
                        $headerGradient = 'from-yellow-500 to-orange-500';
                        $icon = '📝';
                    } elseif ($isExamEnded) {
                        $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-500 text-white rounded-full text-xs font-bold shadow-lg"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Berakhir</span>';
                        $headerGradient = 'from-gray-400 to-gray-500';
                        $icon = '📋';
                    } else {
                        $statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-500 text-white rounded-full text-xs font-bold shadow-lg">Tidak Aktif</span>';
                        $headerGradient = 'from-gray-400 to-gray-500';
                        $icon = '📌';
                    }
                @endphp

                <div class="group bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    
                    {{-- Card Header with Gradient & Image --}}
                    <div class="relative h-28 bg-gradient-to-r {{ $headerGradient }}">
                        
                        {{-- Background Image --}}
                        @if($comp->hasMedia('gambar_lomba'))
                            <div class="absolute inset-0">
                                <img src="{{ $comp->getFirstMediaUrl('gambar_lomba') }}" 
                                     alt="{{ $comp->nama_lomba }}" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-black/10"></div>
                            </div>
                        @endif
                        
                        {{-- Dekorasi --}}
                        <div class="absolute inset-0 opacity-10">
                            <svg class="absolute -top-10 -right-10 w-32 h-32 text-white" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                            <svg class="absolute -bottom-10 -left-10 w-24 h-24 text-white" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
                        </div>
                        
                        {{-- Status Badge --}}
                        <div class="absolute top-3 right-3 z-10">
                            {!! $statusBadge !!}
                        </div>

                        {{-- Icon --}}
                        <div class="absolute -bottom-7 left-6 z-10">
                            <div class="w-14 h-14 rounded-2xl bg-white shadow-lg flex items-center justify-center text-2xl border-2 border-white">
                                {{ $icon }}
                            </div>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="pt-9 px-6 pb-6">
                        <h3 class="font-bold text-lg text-gray-900 mb-1 pr-20 line-clamp-2 leading-tight">
                            {{ $comp->nama_lomba }}
                        </h3>
                        
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2 min-h-[40px]">
                            {{ $comp->deskripsi ?: 'Tidak ada deskripsi' }}
                        </p>

                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-4 pb-4 border-b border-gray-100">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $comp->registrations_count }} Pendaftar
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $comp->tanggal_mulai ? \Carbon\Carbon::parse($comp->tanggal_mulai)->format('d/m/Y') : '-' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $comp->tanggal_selesai ? \Carbon\Carbon::parse($comp->tanggal_selesai)->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        
                        <div class="space-y-2">
                            <a href="{{ route('admin.verifikasi.show', $comp->id) }}" 
                               class="flex items-center justify-center w-full py-2.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-sm hover:shadow-lg gap-2 group">
                                <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Kelola & Verifikasi
                                @if($comp->registrations_count > 0)
                                    <span class="bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded-full group-hover:bg-white group-hover:text-indigo-600 transition">{{ $comp->registrations_count }}</span>
                                @endif
                            </a>
                            
                            <div class="flex gap-2">
                                <a href="{{ route('admin.kompetisi.export', $comp->id) }}" 
                                   class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-gray-50 hover:bg-gray-800 hover:text-white text-gray-600 rounded-xl text-xs font-bold transition-all duration-300 border border-gray-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    CSV
                                </a>

                                @if($isExamEnded || $isRegistrationClosed)
                                    <a href="{{ route('admin.kompetisi.ranking', $comp->id) }}" 
                                       class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 rounded-xl text-xs font-bold transition-all duration-300 border border-amber-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                        Ranking
                                    </a>
                                @endif

                                <a href="{{ route('admin.kompetisi.edit', $comp->id) }}" 
                                   class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-gray-50 hover:bg-indigo-600 hover:text-white text-gray-600 rounded-xl text-xs font-bold transition-all duration-300 border border-gray-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 mb-2">Belum Ada Lomba</h3>
                    <p class="text-gray-500">Belum ada kompetisi yang dibuat. Mulai buat kompetisi baru untuk mengelola pendaftaran.</p>
                    <a href="{{ route('admin.kompetisi.create') }}" class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-lg shadow-indigo-600/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Lomba Baru
                    </a>
                </div>
            @endforelse
        </div>

    </div>

    {{-- CSS Tambahan --}}
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .animate-pulse {
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        
        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
        }
    </style>
</x-app-layout>