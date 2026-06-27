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
                    $statusColors = [
                        'verified' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'rejected' => 'bg-red-100 text-red-700 border-red-200',
                    ];
                    $statusIcons = [
                        'verified' => '✓',
                        'pending' => '⏳',
                        'rejected' => '✕',
                    ];
                    $statusLabels = [
                        'verified' => 'Aktif',
                        'pending' => 'Menunggu Verifikasi',
                        'rejected' => 'Ditolak',
                    ];
                    $statusColor = $statusColors[$reg->status_pendaftaran] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                    $statusIcon = $statusIcons[$reg->status_pendaftaran] ?? '•';
                    $statusLabel = $statusLabels[$reg->status_pendaftaran] ?? ucfirst($reg->status_pendaftaran);
                    
                    $isFinished = $reg->examResult && $reg->examResult->status === 'finished';
                    $isVerified = $reg->status_pendaftaran === 'verified';
                    $isPending = $reg->status_pendaftaran === 'pending';
                    
                    $examStart = \Carbon\Carbon::parse($reg->competition->waktu_pelaksanaan);
                    $canStart = $isVerified && $examStart->lte(now()) && !$isFinished;
                @endphp

                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                    
                    {{-- Progress Indicator --}}
                    <div class="h-1.5 bg-gradient-to-r 
                        @if($reg->status_pendaftaran === 'verified') from-emerald-400 to-emerald-600
                        @elseif($reg->status_pendaftaran === 'pending') from-amber-400 to-amber-600
                        @else from-red-400 to-red-600 @endif">
                    </div>

                    <div class="p-5 flex flex-col sm:flex-row gap-5 items-start sm:items-center">
                        
                        {{-- Image --}}
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
                            
                            {{-- Status Badge on Image --}}
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $statusColor }} border text-xs font-bold rounded-full shadow-sm backdrop-blur-sm bg-opacity-90">
                                    <span class="text-sm">{{ $statusIcon }}</span>
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            {{-- Timer Badge --}}
                            @if($isVerified && !$isFinished)
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

                        {{-- Content --}}
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
                                            {{ $reg->created_at->format('d M Y') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $reg->competition->waktu_pelaksanaan ? \Carbon\Carbon::parse($reg->competition->waktu_pelaksanaan)->translatedFormat('d M Y, H:i') : 'Jadwal belum ditentukan' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Durasi: {{ $reg->competition->durasi_menit }} Menit
                                        </span>
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                <div class="w-full sm:w-auto flex-shrink-0">
                                    @if($isVerified && $isFinished)
                                        {{-- Hasil Ujian --}}
                                        <div class="px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-center min-w-[140px]">
                                            <span class="text-xs font-medium text-emerald-600">Ujian Selesai</span>
                                            <p class="text-xl font-black text-emerald-700 mt-0.5">
                                                {{ $reg->examResult->score ?? 0 }}
                                                <span class="text-xs font-normal text-emerald-500">/ 100</span>
                                            </p>
                                        </div>

                                    @elseif($isVerified && $canStart)
                                        {{-- Mulai Ujian --}}
                                        <a href="{{ route('user.ujian.prepare', $reg->id) }}" 
                                           class="inline-flex items-center justify-center gap-2 w-full px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 group/btn">
                                            <span>Mulai Ujian</span>
                                            <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                            </svg>
                                        </a>
                                        
                                    @elseif($isVerified && !$canStart && !$isFinished)
                                        {{-- Belum waktunya ujian --}}
                                        <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-center min-w-[140px]">
                                            <span class="text-xs font-medium text-gray-500">Waktu Ujian</span>
                                            <p class="text-sm font-bold text-gray-700 mt-0.5">
                                                {{ $examStart->translatedFormat('d M H:i') }}
                                            </p>
                                        </div>

                                    @elseif($isPending)
                                        {{-- Menunggu Verifikasi --}}
                                        <div class="px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-xl text-center min-w-[140px]">
                                            <div class="flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                <span class="text-sm font-medium text-amber-700">Menunggu Verifikasi</span>
                                            </div>
                                        </div>

                                    @else
                                        {{-- Ditolak --}}
                                        <div class="px-4 py-2.5 bg-red-50 border border-red-200 rounded-xl text-center min-w-[140px]">
                                            <span class="text-sm font-medium text-red-700">Pendaftaran Ditolak</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress Bar for Verified --}}
                            @if($isVerified && !$isFinished)
                                <div class="mt-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full transition-all duration-1000" 
                                                 style="width: {{ $isFinished ? 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-400 whitespace-nowrap">
                                            {{ $isFinished ? 'Selesai' : 'Belum dimulai' }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- CSS Tambahan --}}
<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
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