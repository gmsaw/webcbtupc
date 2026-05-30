<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Kompetisi yang Diikuti
        </h3>
    </div>

    @if(isset($my_registrations) && $my_registrations->isEmpty())
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm text-center border-dashed border-2">
            <p class="text-gray-500">Anda belum terdaftar di kompetisi manapun.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($my_registrations as $reg)
                <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-blue-300 transition-colors flex flex-col sm:flex-row gap-5 items-start sm:items-center">
                    
                    <div class="w-full sm:w-32 h-32 sm:h-28 rounded-2xl bg-gray-100 shrink-0 overflow-hidden relative shadow-inner border border-gray-200">
                        @if($reg->competition->hasMedia('gambar_lomba'))
                            <img src="{{ $reg->competition->getFirstMediaUrl('gambar_lomba') }}" alt="Banner Lomba" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-blue-100 to-cyan-50 flex items-center justify-center">
                                <svg class="w-10 h-10 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        
                        <div class="absolute top-2 left-2">
                            @if($reg->status_pendaftaran === 'verified')
                                <span class="bg-green-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span> Aktif
                                </span>
                            @elseif($reg->status_pendaftaran === 'pending')
                                <span class="bg-yellow-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Menunggu
                                </span>
                            @else
                                <span class="bg-red-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span> Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 w-full flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $reg->competition->nama_lomba }}</h4>
                            <p class="text-xs text-gray-500 mt-1">Terdaftar: {{ $reg->created_at->format('d M Y') }}</p>
                            
                            <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $reg->competition->waktu_pelaksanaan ? \Carbon\Carbon::parse($reg->competition->waktu_pelaksanaan)->translatedFormat('d M Y, H:i') : 'Jadwal belum ditentukan' }}
                            </div>
                        </div>

                        @if($reg->status_pendaftaran === 'verified')
                            <div class="flex flex-wrap gap-2 w-full md:w-auto mt-2 md:mt-0 pt-3 md:pt-0 border-t md:border-t-0 border-gray-100">
                                @if(is_null($reg->nilai_cbt))
                                <a href="{{ route('user.ujian.show', $reg->id) }}" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm transition text-center flex items-center justify-center">
                                    Mulai CBT
                                </a>
                                @else
                                    <span class="bg-blue-50 text-blue-700 border border-blue-200 px-4 py-2.5 rounded-xl text-sm font-bold text-center flex-1 sm:flex-none">
                                        Skor: {{ $reg->nilai_cbt }}
                                    </span>
                                    <button class="flex-1 sm:flex-none bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 px-4 py-2.5 rounded-xl text-sm font-bold transition">
                                        Sertifikat
                                    </button>
                                @endif
                                <button class="flex-1 sm:flex-none bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition">
                                    Kartu
                                </button>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>