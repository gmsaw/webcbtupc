<div class="pt-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            Pilihan Kompetisi
        </h3>
    </div>

    @if(isset($available_competitions) && $available_competitions->isEmpty())
        <div class="bg-gray-50 border border-gray-100 rounded-3xl p-6 text-center text-sm text-gray-500">
            Saat ini tidak ada lomba baru yang sedang membuka pendaftaran.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach($available_competitions as $comp)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm flex flex-col overflow-hidden hover:shadow-xl transition-shadow group">
                    
                    <div class="h-44 relative overflow-hidden bg-gray-200">
                        @if($comp->hasMedia('gambar_lomba'))
                            <img src="{{ $comp->getFirstMediaUrl('gambar_lomba') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $comp->nama_lomba }}">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-cyan-600 to-blue-800 group-hover:scale-110 transition-transform duration-700"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/95 via-gray-900/40 to-transparent"></div>
                        
                        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                            {{ $comp->harga_pendaftaran == 0 ? 'GRATIS' : 'Rp ' . number_format($comp->harga_pendaftaran, 0, ',', '.') }}
                        </div>
                        
                        <h4 class="absolute bottom-4 left-5 right-5 text-xl font-bold text-white leading-tight drop-shadow-md">{{ $comp->nama_lomba }}</h4>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex flex-col gap-2 mb-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Tenggat: {{ $comp->tanggal_selesai ? \Carbon\Carbon::parse($comp->tanggal_selesai)->translatedFormat('d M Y') : '-' }}
                            </div>
                            <div class="flex items-center gap-2 text-xs font-bold text-indigo-700">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pelaksanaan: {{ $comp->waktu_pelaksanaan ? \Carbon\Carbon::parse($comp->waktu_pelaksanaan)->translatedFormat('d M Y, H:i') . ' WITA' : 'TBA' }}
                            </div>
                            <div class="flex items-center gap-2 text-xs font-bold text-orange-600">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Durasi Ujian: {{ $comp->durasi_menit }} Menit
                            </div>
                        </div>

                        <p class="text-sm text-gray-500 mb-6 flex-1 line-clamp-2">{{ $comp->deskripsi ?? 'Ajang kompetisi tingkat nasional.' }}</p>
                        
                        @php
                            $today = \Carbon\Carbon::today();
                            $isOpen = $comp->is_active && $comp->tanggal_mulai && $comp->tanggal_selesai && $today->between($comp->tanggal_mulai, $comp->tanggal_selesai);
                        @endphp

                        @if($isOpen)
                            <button type="button" 
                                @click="comp = { id: '{{ $comp->id }}', title: '{{ addslashes($comp->nama_lomba) }}', price: {{ $comp->harga_pendaftaran }}, price_fmt: '{{ $comp->harga_pendaftaran == 0 ? 'GRATIS' : 'Rp ' . number_format($comp->harga_pendaftaran, 0, ',', '.') }}' }; registrationModal = true; paymentMethod = 'manual';" 
                                class="w-full bg-cyan-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-sm shadow-md transition-colors transform hover:-translate-y-0.5">
                                Daftar Sekarang
                            </button>
                        @else
                            <button type="button" disabled class="w-full bg-gray-100 text-gray-400 border border-gray-200 py-3 rounded-xl font-bold text-sm cursor-not-allowed">
                                Pendaftaran Ditutup
                            </button>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>