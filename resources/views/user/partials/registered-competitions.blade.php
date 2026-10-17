{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- KOMPETISI YANG DIIKUTI --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div>
    {{-- Header section — TANPA mb-6, karena parent sudah gap-6 --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Kompetisi yang Diikuti
            <span class="text-sm font-medium text-gray-400 bg-gray-100 px-2.5 py-0.5 rounded-full ml-1">
                {{ $my_registrations->count() }}
            </span>
        </h3>
        @if(! $my_registrations->isEmpty())
            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium hover:underline">
                Lihat Semua
            </a>
        @endif
    </div>

    {{-- Empty state --}}
    @if($my_registrations->isEmpty())
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
        <div class="grid grid-cols-1 gap-4">
            @foreach($my_registrations as $reg)
                @php
                    // ══════════════════════════════════════════════════
                    // STATUS PENDAFTARAN
                    // ══════════════════════════════════════════════════
                    $statusMap = [
                        'verified' => ['bg-emerald-100 text-emerald-700 border-emerald-200', '✓', 'Aktif'],
                        'pending'  => ['bg-amber-100 text-amber-700 border-amber-200',       '⏳', 'Menunggu Verifikasi'],
                        'rejected' => ['bg-red-100 text-red-700 border-red-200',             '✕', 'Ditolak'],
                    ];
                    [$statusColor, $statusIcon, $statusLabel] =
                        $statusMap[$reg->status_pendaftaran]
                        ?? ['bg-gray-100 text-gray-700 border-gray-200', '•', ucfirst($reg->status_pendaftaran)];

                    // ══════════════════════════════════════════════════
                    // KONFIGURASI BABAK
                    // ══════════════════════════════════════════════════
                    $babak = $reg->babak ?? 'penyisihan';

                    $babakConfig = [
                        'penyisihan' => [
                            'label' => 'Penyisihan', 'icon' => '📝',
                            'badge' => 'bg-gray-100 text-gray-700 border-gray-200',
                            'bar'   => 'from-gray-400 to-gray-500',
                            'waktu' => $reg->competition->waktu_pelaksanaan,
                            'durasi' => $reg->competition->durasi_menit,
                        ],
                        'semifinal' => [
                            'label' => 'Semifinal', 'icon' => '🎯',
                            'badge' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'bar'   => 'from-blue-400 to-blue-600',
                            'waktu' => $reg->competition->waktu_pelaksanaan_semifinal,
                            'durasi' => $reg->competition->durasi_menit_semifinal ?? $reg->competition->durasi_menit,
                        ],
                        'final' => [
                            'label' => 'Final', 'icon' => '🏆',
                            'badge' => 'bg-purple-100 text-purple-700 border-purple-200',
                            'bar'   => 'from-purple-400 to-purple-600',
                            'waktu' => $reg->competition->waktu_pelaksanaan_final,
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

                    // ══════════════════════════════════════════════════
                    // STATUS UJIAN — pakai examResultFor() eksplisit
                    // ══════════════════════════════════════════════════
                    $currentExamResult = $reg->examResultFor($babak);
                    $isFinishedInCurrentBabak = $currentExamResult
                        && $currentExamResult->status === 'finished';

                    $isFinalBabak = $babak === 'final';
                    $isVerified   = $reg->status_pendaftaran === 'verified';
                    $isPending    = $reg->status_pendaftaran === 'pending';

                    // ══════════════════════════════════════════════════
                    // WAKTU
                    // ══════════════════════════════════════════════════
                    $examStart = $waktuMulai ? \Carbon\Carbon::parse($waktuMulai) : null;
                    $examEnd   = $examStart && $durasiMenit
                        ? $examStart->copy()->addMinutes((int) $durasiMenit)
                        : null;
                    $now = now();

                    $canStart = $isVerified && $examStart && $examStart->lte($now)
                        && $examEnd && $now->lt($examEnd)
                        && ! $isFinishedInCurrentBabak;

                    $belumWaktunya = $isVerified && $examStart
                        && $examStart->gt($now)
                        && ! $isFinishedInCurrentBabak;

                    $sudahLewat = $isVerified && $examEnd
                        && $examEnd->lt($now)
                        && ! $isFinishedInCurrentBabak;
                @endphp

                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">

                    {{-- Bar warna babak --}}
                    <div class="h-1.5 bg-gradient-to-r
                        @if($reg->status_pendaftaran === 'verified') {{ $babakBar }}
                        @elseif($reg->status_pendaftaran === 'pending') from-amber-400 to-amber-600
                        @else from-red-400 to-red-600 @endif">
                    </div>

                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row gap-4 sm:gap-5 items-start">

                        {{-- ═══ IMAGE ═══ --}}
                        <div class="relative w-full sm:w-36 h-40 sm:h-32 rounded-2xl overflow-hidden shadow-md flex-shrink-0 bg-gray-100">
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

                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $statusColor }} border text-xs font-bold rounded-full shadow-sm backdrop-blur-sm bg-opacity-90">
                                    <span class="text-sm">{{ $statusIcon }}</span>
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="absolute bottom-2 left-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $babakBadge }} border text-[10px] font-black rounded-full shadow-sm backdrop-blur-sm bg-opacity-90 uppercase tracking-wider">
                                    {{ $babakIcon }} {{ $babakLabel }}
                                </span>
                            </div>

                            @if($canStart)
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
                        <div class="flex-1 w-full min-w-0">
                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-3">

                                {{-- Kolom kiri: info --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                        {{ $reg->competition->nama_lomba }}
                                    </h4>

                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5 text-xs text-gray-500">
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

                                    {{-- INFO BABAK --}}
                                    @if($isVerified && $isFinishedInCurrentBabak && ! $isFinalBabak)
                                        <div class="mt-2.5 flex items-start gap-2 px-3 py-2 rounded-xl border bg-amber-50 border-amber-100 text-amber-700">
                                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs font-semibold">
                                                Ujian babak <b>{{ $babakLabel }}</b> telah selesai. Silakan tunggu pengumuman dari panitia untuk babak berikutnya.
                                            </span>
                                        </div>

                                    @elseif($isVerified && $isFinishedInCurrentBabak && $isFinalBabak)
                                        <div class="mt-2.5 flex items-start gap-2 px-3 py-2 rounded-xl border bg-emerald-50 border-emerald-100 text-emerald-700">
                                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs font-semibold">
                                                Anda telah menyelesaikan seluruh rangkaian lomba. Terima kasih!
                                            </span>
                                        </div>

                                    @elseif($isVerified && $babak !== 'penyisihan' && ! $isFinishedInCurrentBabak)
                                        <div class="mt-2.5 flex items-start gap-2 px-3 py-2 rounded-xl border
                                            @if($babak === 'semifinal') bg-blue-50 border-blue-100 text-blue-700
                                            @else bg-purple-50 border-purple-100 text-purple-700 @endif">
                                            <span class="text-base leading-none mt-0.5">{{ $babakIcon }}</span>
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

                                    @elseif($isVerified && $sudahLewat)
                                        <div class="mt-2.5 flex items-start gap-2 px-3 py-2 rounded-xl border bg-gray-50 border-gray-200 text-gray-600">
                                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs font-semibold">
                                                Waktu ujian babak <b>{{ $babakLabel }}</b> telah berakhir.
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Kolom kanan: action button --}}
                                <div class="w-full lg:w-auto flex-shrink-0 lg:pt-1">
                                    @if($isFinishedInCurrentBabak && $isFinalBabak)
                                        <div class="w-full lg:min-w-[160px] px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-sm font-black text-emerald-700">Ujian Selesai</p>
                                                <p class="text-[10px] text-emerald-500 font-medium">Terima kasih telah berpartisipasi</p>
                                            </div>
                                        </div>

                                    @elseif($isFinishedInCurrentBabak && ! $isFinalBabak)
                                        <div class="w-full lg:min-w-[180px] px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-sm font-black text-amber-700">Ujian Selesai</p>
                                                <p class="text-[10px] text-amber-600 font-medium">Menunggu pengumuman babak berikutnya</p>
                                            </div>
                                        </div>

                                    @elseif($canStart)
                                        <a href="{{ route('user.ujian.prepare', $reg->id) }}"
                                           class="inline-flex items-center justify-center gap-2 w-full px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 group/btn">
                                            <span>Mulai Ujian {{ $babakLabel }}</span>
                                            <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                            </svg>
                                        </a>

                                    @elseif($belumWaktunya)
                                        <div class="w-full lg:min-w-[160px] px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-center">
                                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ujian Dibuka</span>
                                            <p class="text-sm font-bold text-gray-700 mt-0.5">
                                                {{ $examStart->translatedFormat('d M Y, H:i') }}
                                            </p>
                                        </div>

                                    @elseif($isPending)
                                        <div class="w-full lg:min-w-[140px] px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-xl text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                <span class="text-sm font-medium text-amber-700">Menunggu Verifikasi</span>
                                            </div>
                                        </div>

                                    @else
                                        <div class="w-full lg:min-w-[140px] px-4 py-2.5 bg-red-50 border border-red-200 rounded-xl text-center">
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