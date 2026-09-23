<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Peserta') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- KOMPETISI YANG DIIKUTI --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Kompetisi yang Diikuti
                        <span class="text-sm font-medium text-gray-400 bg-gray-100 px-2.5 py-0.5 rounded-full ml-1">
                            {{ $my_registrations->count() }}
                        </span>
                    </h3>
                    @if(!$my_registrations->isEmpty())
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium hover:underline">
                            Lihat Semua
                        </a>
                    @endif
                </div>

                @if(isset($my_registrations) && $my_registrations->isEmpty())
                    <div class="bg-white rounded-3xl p-12 border-2 border-dashed border-gray-200 shadow-sm text-center transition-all hover:border-indigo-300 hover:bg-indigo-50/20">
                        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium text-lg">Belum Terdaftar di Kompetisi</p>
                        <p class="text-gray-400 text-sm mt-1">Mulai daftarkan diri Anda ke kompetisi yang tersedia</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5">
                        @foreach($my_registrations as $reg)
                            @php
                                // ══════════════════════════════════════════════
                                // STATUS PENDAFTARAN
                                // ══════════════════════════════════════════════
                                $statusColors = [
                                    'verified' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'pending'  => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                ];
                                $statusIcons = [
                                    'verified' => '✓',
                                    'pending'  => '⏳',
                                    'rejected' => '✕',
                                ];
                                $statusLabels = [
                                    'verified' => 'Aktif',
                                    'pending'  => 'Menunggu Verifikasi',
                                    'rejected' => 'Ditolak',
                                ];
                                $statusColor = $statusColors[$reg->status_pendaftaran] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                $statusIcon  = $statusIcons[$reg->status_pendaftaran] ?? '•';
                                $statusLabel = $statusLabels[$reg->status_pendaftaran] ?? ucfirst($reg->status_pendaftaran);

                                // ══════════════════════════════════════════════
                                // KONFIGURASI BABAK
                                // ══════════════════════════════════════════════
                                $babak = $reg->babak ?? 'penyisihan';

                                $babakConfig = [
                                    'penyisihan' => [
                                        'label'  => 'Penyisihan',
                                        'icon'   => '📝',
                                        'badge'  => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'bar'    => 'from-gray-400 to-gray-500',
                                        'waktu'  => $reg->competition->waktu_pelaksanaan,
                                        'durasi' => $reg->competition->durasi_menit,
                                    ],
                                    'semifinal' => [
                                        'label'  => 'Semifinal',
                                        'icon'   => '🎯',
                                        'badge'  => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'bar'    => 'from-blue-400 to-blue-600',
                                        'waktu'  => $reg->competition->waktu_pelaksanaan_semifinal,
                                        'durasi' => $reg->competition->durasi_menit_semifinal ?? $reg->competition->durasi_menit,
                                    ],
                                    'final' => [
                                        'label'  => 'Final',
                                        'icon'   => '🏆',
                                        'badge'  => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'bar'    => 'from-purple-400 to-purple-600',
                                        'waktu'  => $reg->competition->waktu_pelaksanaan_final,
                                        'durasi' => $reg->competition->durasi_menit_final ?? $reg->competition->durasi_menit,
                                    ],
                                ];

                                $config      = $babakConfig[$babak] ?? $babakConfig['penyisihan'];
                                $babakLabel  = $config['label'];
                                $babakIcon   = $config['icon'];
                                $babakBadge  = $config['badge'];
                                $babakBar    = $config['bar'];
                                $waktuMulai  = $config['waktu'];
                                $durasiMenit = $config['durasi'];

                                // ══════════════════════════════════════════════
                                // STATUS UJIAN — BANDINGKAN BABAK
                                // ══════════════════════════════════════════════
                                $examBabak = $reg->examResult?->babak ?? 'penyisihan';

                                // KUNCI: cek apakah peserta sudah selesai di BABAK INI
                                $isFinishedInCurrentBabak =
                                    $reg->examResult
                                    && $reg->examResult->status === 'finished'
                                    && $examBabak === $babak;   // ← KUNCI UTAMA

                                $isFinalBabak = $babak === 'final';
                                $isVerified   = $reg->status_pendaftaran === 'verified';
                                $isPending    = $reg->status_pendaftaran === 'pending';

                                // ══════════════════════════════════════════════
                                // WAKTU
                                // ══════════════════════════════════════════════
                                $examStart = $waktuMulai ? \Carbon\Carbon::parse($waktuMulai) : null;
                                $examEnd   = $examStart && $durasiMenit
                                    ? $examStart->copy()->addMinutes($durasiMenit)
                                    : null;
                                $now       = now();

                                $canStart =
                                    $isVerified
                                    && $examStart
                                    && $examStart->lte($now)
                                    && $examEnd
                                    && $now->lt($examEnd)
                                    && !$isFinishedInCurrentBabak;

                                $belumWaktunya =
                                    $isVerified
                                    && $examStart
                                    && $examStart->gt($now)
                                    && !$isFinishedInCurrentBabak;

                                $sudahLewat =
                                    $isVerified
                                    && $examEnd
                                    && $examEnd->lt($now)
                                    && !$isFinishedInCurrentBabak;
                            @endphp

                            <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">

                                {{-- Progress Indicator (warna sesuai babak) --}}
                                <div class="h-1.5 bg-gradient-to-r
                                    @if($reg->status_pendaftaran === 'verified') {{ $babakBar }}
                                    @elseif($reg->status_pendaftaran === 'pending') from-amber-400 to-amber-600
                                    @else from-red-400 to-red-600 @endif">
                                </div>

                                <div class="p-5 flex flex-col sm:flex-row gap-5 items-start sm:items-center">

                                    {{-- ═══ IMAGE ═══ --}}
                                    <div class="relative w-full sm:w-36 h-36 sm:h-32 rounded-2xl overflow-hidden shadow-md flex-shrink-0 bg-gray-100">
                                        @if($reg->competition->hasMedia('gambar_lomba'))
                                            <img src="{{ $reg->competition->getFirstMediaUrl('gambar_lomba') }}"
                                                 alt="{{ $reg->competition->nama_lomba }}"
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-50 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif

                                        {{-- Status Badge --}}
                                        <div class="absolute top-2 left-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $statusColor }} border text-xs font-bold rounded-full shadow-sm backdrop-blur-sm bg-opacity-90">
                                                <span class="text-sm">{{ $statusIcon }}</span>
                                                {{ $statusLabel }}
                                            </span>
                                        </div>

                                        {{-- Babak Badge --}}
                                        <div class="absolute bottom-2 left-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $babakBadge }} border text-[10px] font-black rounded-full shadow-sm backdrop-blur-sm bg-opacity-90 uppercase tracking-wider">
                                                {{ $babakIcon }} {{ $babakLabel }}
                                            </span>
                                        </div>

                                        {{-- Timer Badge --}}
                                        @if($isVerified && !$isFinishedInCurrentBabak && $canStart)
                                            <div class="absolute bottom-2 right-2">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/90 backdrop-blur-sm text-indigo-600 text-[10px] font-bold rounded-full shadow-lg border border-white/20">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Siap Ujian
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ═══ CONTENT ═══ --}}
                                    <div class="flex-1 w-full">
                                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                            <div class="flex-1">
                                                <h4 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                                    {{ $reg->competition->nama_lomba }}
                                                </h4>

                                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        Daftar: {{ $reg->created_at->format('d M Y') }}
                                                    </span>

                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $waktuMulai ? \Carbon\Carbon::parse($waktuMulai)->translatedFormat('d M Y, H:i') : 'Jadwal belum ditentukan' }}
                                                    </span>

                                                    @if($durasiMenit)
                                                        <span class="inline-flex items-center gap-1">
                                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Durasi: {{ $durasiMenit }} Menit
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- ══════════════════════════════════════ --}}
                                                {{-- INFO BABAK --}}
                                                {{-- ══════════════════════════════════════ --}}

                                                {{-- Sudah selesai babak ini, admin belum naikkan --}}
                                                @if($isVerified && $isFinishedInCurrentBabak && !$isFinalBabak)
                                                    <div class="mt-2.5 flex items-center gap-2 px-3 py-2 rounded-xl border bg-amber-50 border-amber-100 text-amber-700">
                                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-xs font-semibold">
                                                            Ujian babak <b>{{ $babakLabel }}</b> telah selesai. Silakan tunggu pengumuman dari panitia untuk babak berikutnya.
                                                        </span>
                                                    </div>

                                                {{-- Sudah selesai babak final --}}
                                                @elseif($isVerified && $isFinishedInCurrentBabak && $isFinalBabak)
                                                    <div class="mt-2.5 flex items-center gap-2 px-3 py-2 rounded-xl border bg-emerald-50 border-emerald-100 text-emerald-700">
                                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-xs font-semibold">
                                                            Anda telah menyelesaikan seluruh rangkaian lomba. Terima kasih!
                                                        </span>
                                                    </div>

                                                {{-- Peserta babak semifinal/final, belum selesai — tampilkan jadwal --}}
                                                @elseif($isVerified && $babak !== 'penyisihan' && !$isFinishedInCurrentBabak)
                                                    <div class="mt-2.5 flex items-center gap-2 px-3 py-2 rounded-xl border
                                                        @if($babak === 'semifinal') bg-blue-50 border-blue-100 text-blue-700
                                                        @else bg-purple-50 border-purple-100 text-purple-700 @endif">
                                                        <span class="text-base">{{ $babakIcon }}</span>
                                                        <span class="text-xs font-semibold">
                                                            Babak <b>{{ $babakLabel }}</b> akan dilaksanakan pada
                                                            <b>
                                                                @if($waktuMulai)
                                                                    {{ \Carbon\Carbon::parse($waktuMulai)->translatedFormat('l, d F Y \p\u\k\u\l H:i') }} WITA
                                                                @else
                                                                    jadwal yang belum ditentukan
                                                                @endif
                                                            </b>
                                                        </span>
                                                    </div>

                                                {{-- Waktu sudah lewat (belum selesai) --}}
                                                @elseif($isVerified && $sudahLewat)
                                                    <div class="mt-2.5 flex items-center gap-2 px-3 py-2 rounded-xl border bg-gray-50 border-gray-200 text-gray-600">
                                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-xs font-semibold">
                                                            Waktu ujian babak <b>{{ $babakLabel }}</b> telah berakhir.
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- ══════════════════════════════════════ --}}
                                            {{-- ACTION BUTTON --}}
                                            {{-- ══════════════════════════════════════ --}}
                                            <div class="w-full sm:w-auto flex-shrink-0">

                                                @if($isFinishedInCurrentBabak && $isFinalBabak)
                                                    {{-- Sudah selesai babak FINAL — permanen --}}
                                                    <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center min-w-[160px]">
                                                        <div class="flex flex-col items-center gap-1">
                                                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <p class="text-sm font-black text-emerald-700">Ujian Selesai</p>
                                                            <p class="text-[10px] text-emerald-500 font-medium">Terima kasih telah berpartisipasi</p>
                                                        </div>
                                                    </div>

                                                @elseif($isFinishedInCurrentBabak && !$isFinalBabak)
                                                    {{-- Sudah selesai babak ini, TAPI admin belum naikkan babak --}}
                                                    <div class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-center min-w-[180px]">
                                                        <div class="flex flex-col items-center gap-1">
                                                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <p class="text-sm font-black text-amber-700">Ujian Selesai</p>
                                                            <p class="text-[10px] text-amber-600 font-medium">Menunggu pengumuman babak berikutnya</p>
                                                        </div>
                                                    </div>

                                                @elseif($isVerified && $canStart)
                                                    {{-- Bisa mulai ujian babak ini --}}
                                                    <a href="{{ route('user.ujian.prepare', $reg->id) }}"
                                                       class="inline-flex items-center justify-center gap-2 w-full px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 group/btn">
                                                        <span>Mulai Ujian {{ $babakLabel }}</span>
                                                        <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                        </svg>
                                                    </a>

                                                @elseif($belumWaktunya)
                                                    {{-- Belum waktunya ujian --}}
                                                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-center min-w-[160px]">
                                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ujian Dibuka</span>
                                                        <p class="text-sm font-bold text-gray-700 mt-0.5">
                                                            {{ $examStart->translatedFormat('d M Y, H:i') }}
                                                        </p>
                                                    </div>

                                                @elseif($isPending)
                                                    {{-- Menunggu verifikasi --}}
                                                    <div class="px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-xl text-center min-w-[140px]">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <svg class="w-4 h-4 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                            </svg>
                                                            <span class="text-sm font-medium text-amber-700">Menunggu Verifikasi</span>
                                                        </div>
                                                    </div>

                                                @else
                                                    {{-- Ditolak / lewat waktu --}}
                                                    <div class="px-4 py-2.5 bg-red-50 border border-red-200 rounded-xl text-center min-w-[140px]">
                                                        <span class="text-sm font-medium text-red-700">
                                                            {{ $sudahLewat ? 'Waktu Habis' : 'Pendaftaran Ditolak' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- KOMPETISI TERSEDIA --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            @if(isset($available_competitions) && !$available_competitions->isEmpty())
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Kompetisi Tersedia
                            <span class="text-sm font-medium text-gray-400 bg-gray-100 px-2.5 py-0.5 rounded-full ml-1">
                                {{ $available_competitions->count() }}
                            </span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($available_competitions as $comp)
                            <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                                <div class="relative h-36 bg-gradient-to-br from-indigo-500 to-blue-600 overflow-hidden">
                                    @if($comp->hasMedia('gambar_lomba'))
                                        <img src="{{ $comp->getFirstMediaUrl('gambar_lomba') }}"
                                             alt="{{ $comp->nama_lomba }}"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
                                    @endif
                                </div>

                                <div class="p-5">
                                    <h4 class="text-base font-bold text-gray-900 mb-1 line-clamp-2 min-h-[2.5rem]">
                                        {{ $comp->nama_lomba }}
                                    </h4>
                                    <p class="text-xs text-gray-500 line-clamp-2 min-h-[2rem] mb-3">
                                        {{ $comp->deskripsi ?: 'Tidak ada deskripsi' }}
                                    </p>

                                    <div class="flex items-center justify-between mb-3 text-xs">
                                        <span class="font-bold text-indigo-600">
                                            {{ $comp->harga_pendaftaran > 0 ? 'Rp ' . number_format($comp->harga_pendaftaran, 0, ',', '.') : 'GRATIS' }}
                                        </span>
                                        <span class="text-gray-400">
                                            {{ $comp->registrations_count ?? 0 }} Pendaftar
                                        </span>
                                    </div>

                                    <button type="button"
                                            onclick="document.getElementById('modalDaftar{{ $comp->id }}').classList.remove('hidden')"
                                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Daftar Sekarang
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- PENGUMUMAN --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            @if(isset($announcements) && !$announcements->isEmpty())
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                        Pengumuman Terbaru
                    </h3>

                    <div class="space-y-3">
                        @foreach($announcements as $ann)
                            <a href="{{ route('user.pengumuman.show', $ann->id) }}"
                               class="block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all p-5 group">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-1">
                                            {{ $ann->judul }}
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                            {{ Str::limit(strip_tags($ann->konten), 120) }}
                                        </p>
                                        <span class="text-[10px] text-gray-400 mt-2 inline-block">
                                            {{ $ann->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- MERCHANDISE --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            @if(isset($merchandises) && !$merchandises->isEmpty())
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Merchandise
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach($merchandises as $merch)
                            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition p-5">
                                <h4 class="font-bold text-gray-900 line-clamp-1">{{ $merch->nama_merch }}</h4>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $merch->deskripsi }}</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-sm font-black text-indigo-600">
                                        Rp {{ number_format($merch->harga, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- CSS Tambahan --}}
    <style>
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .animate-pulse-dot {
            animation: pulse-dot 1.5s ease-in-out infinite;
        }
    </style>
</x-app-layout>