<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Peserta') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ registrationModal: false, comp: {}, paymentMethod: 'manual', merchModal: false, activeMerch: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    
                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-2xl shadow-sm flex items-center gap-3 transition-opacity">
                            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span class="font-bold text-sm">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl shadow-sm flex items-center gap-3 transition-opacity">
                            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-bold text-sm">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="rounded-3xl p-8 shadow-lg text-white relative overflow-hidden flex flex-col sm:flex-row items-center gap-6 group">
                        <div class="absolute inset-0 z-0">
                            <img src="https://images.unsplash.com/photo-1632516643720-e7f0d7e6a2a8?auto=format&fit=crop&q=80&w=1200" alt="Background" class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-1000">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-cyan-900/80 backdrop-blur-[2px]"></div>
                        </div>

                        <div class="relative z-10 w-24 h-24 rounded-full bg-white text-blue-700 flex items-center justify-center text-3xl font-bold shadow-2xl border-4 border-white overflow-hidden shrink-0">
                            @if(Auth::user()->hasMedia('foto_profil'))
                                <img src="{{ Auth::user()->getFirstMediaUrl('foto_profil') }}" alt="Profil" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->name, 0, 1) }}
                            @endif
                        </div>

                        <div class="relative z-10 text-center sm:text-left flex-1">
                            <h3 class="text-3xl font-extrabold mb-1 drop-shadow-md">{{ Auth::user()->name }}</h3>
                            <p class="text-blue-100 text-lg flex items-center justify-center sm:justify-start gap-2 drop-shadow-md">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                {{ Auth::user()->asal_sekolah }}
                            </p>
                            <div class="mt-4 flex flex-wrap justify-center sm:justify-start gap-2">
                                <span class="bg-blue-500/30 backdrop-blur-md border border-white/20 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    Peserta UPC 2026
                                </span>
                                <a href="{{ route('profile.edit') }}" class="bg-white/10 hover:bg-white/30 backdrop-blur-md border border-white/20 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-1 shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Edit Profil
                                </a>
                                <a href="{{ route('user.pustaka') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-1.5 shadow-md border border-indigo-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    Pustaka E-Book
                                </a>
                            </div>
                        </div>
                    </div>

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
                                                        <button class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm transition">
                                                            Mulai CBT
                                                        </button>
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

                </div>

                <div class="space-y-8">
                    
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="bg-yellow-50 px-6 py-4 border-b border-yellow-100 flex items-center justify-between">
                            <h3 class="font-bold text-yellow-800 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                Papan Informasi
                            </h3>
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                            </span>
                        </div>
                        <div class="p-0">
                            <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto">
                                @forelse($announcements as $info)
                                    <div class="block p-5 hover:bg-gray-50 transition group">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="text-xs font-semibold text-blue-600">{{ $info->created_at->translatedFormat('d M Y') }}</p>
                                            
                                            @if($info->created_at->isToday())
                                                <span class="bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full font-bold animate-pulse">BARU</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('user.pengumuman.show', $info->id) }}" class="block">
                                            <h4 class="text-sm font-bold text-gray-900 mb-1 group-hover:text-blue-700 transition">{{ $info->judul }}</h4>
                                        </a>
                                        <p class="text-xs text-gray-500 line-clamp-2 mt-1">{{ $info->isi }}</p>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                        <p class="text-xs font-bold">Belum ada pengumuman.</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="p-4 border-t border-gray-50 text-center bg-gray-50/50">
                                <a href="{{ route('user.pengumuman') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Lihat Semua Pengumuman &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                Official Merchandise
                            </h3>
                        </div>
                        <div class="p-5 space-y-5">
                            @forelse($merchandises as $item)
                                <div @click="activeMerch = { id: '{{ $item->id }}', nama: '{{ addslashes($item->nama_produk) }}', harga: {{ $item->harga }}, harga_fmt: '{{ $item->harga == 0 ? 'GRATIS' : 'Rp ' . number_format($item->harga, 0, ',', '.') }}', is_digital: {{ $item->is_digital ? 'true' : 'false' }} }; merchModal = true" 
                                     class="flex gap-4 items-center group cursor-pointer block hover:bg-indigo-50/50 p-2 -m-2 rounded-2xl transition">
                                    <div class="w-20 h-20 rounded-2xl bg-gray-100 flex-shrink-0 overflow-hidden border border-gray-200 relative">
                                        @if($item->hasMedia('gambar_produk'))
                                            <img src="{{ $item->getFirstMediaUrl('gambar_produk') }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-blue-50"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h4 class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2 pr-2">{{ $item->nama_produk }}</h4>
                                            @if($item->is_digital)
                                                <span class="bg-purple-100 text-purple-700 text-[9px] px-1.5 py-0.5 rounded font-black tracking-wider uppercase">E-Book</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mb-1 line-clamp-1">{{ $item->deskripsi }}</p>
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-sm font-black text-indigo-600">{{ $item->harga == 0 ? 'GRATIS' : 'Rp ' . number_format($item->harga, 0, ',', '.') }}</p>
                                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">Beli &rarr;</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-gray-400">
                                    <p class="text-xs font-bold">Belum ada merchandise tersedia.</p>
                                </div>
                            @endforelse

                            <a href="#" class="w-full mt-2 block text-center bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold py-3 rounded-xl text-sm transition-colors border border-indigo-100 shadow-sm">
                                Kunjungi HIMAFI Store &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-3xl p-6 border border-blue-100 text-center shadow-inner">
                        <p class="text-sm text-blue-800 font-medium mb-3">Mengalami kendala teknis atau pertanyaan seputar lomba?</p>
                        <a href="#" class="inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1DA851] text-white px-5 py-3 rounded-xl font-bold text-sm shadow-md transition-colors w-full">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.893-4.448 9.893-9.892 0-5.447-4.446-9.892-9.893-9.892-5.452 0-9.893 4.449-9.893 9.892 0 1.988.546 3.824 1.584 5.493l-1.096 4.003 4.113-1.196z"/></svg>
                            Hubungi Panitia (WA)
                        </a>
                    </div>

                </div>
            </div>

        </div>

        <div x-show="registrationModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center pt-4 px-4 pb-20 text-center">
            <div x-show="registrationModal" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="registrationModal = false"></div>
            
            <div x-show="registrationModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl z-10 w-full max-w-xl border border-gray-100">
                
                <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Konfirmasi Pendaftaran
                    </h3>
                    <button @click="registrationModal = false" class="text-blue-200 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('user.kompetisi.daftar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="competition_id" x-model="comp.id">
                    <input type="hidden" name="metode_pembayaran" x-model="paymentMethod">

                    <div class="px-6 py-6 space-y-6">
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <p class="text-sm text-gray-500 mb-1">Lomba yang dipilih:</p>
                            <h4 class="text-xl font-bold text-gray-900" x-text="comp.title"></h4>
                            <div class="flex justify-between items-end mt-4 pt-4 border-t border-gray-200">
                                <span class="text-gray-600 font-medium">Total Tagihan:</span>
                                <span class="text-2xl font-black text-blue-700" x-text="comp.price_fmt"></span>
                            </div>
                        </div>

                        <template x-if="comp.price > 0">
                            <div class="space-y-4">
                                <h4 class="font-bold text-gray-900">Pilih Metode Pembayaran:</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="cursor-pointer">
                                        <input type="radio" x-model="paymentMethod" value="manual" class="peer sr-only">
                                        <div class="rounded-xl border-2 p-4 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 border-gray-200 hover:border-blue-300">
                                            <div class="flex items-center gap-3 mb-2">
                                                <svg class="w-6 h-6 text-gray-600 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                                <span class="font-bold text-gray-900">Transfer Manual</span>
                                            </div>
                                            <p class="text-xs text-gray-500">BCA, Mandiri, BNI, BRI (Cek manual)</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" x-model="paymentMethod" value="gateway" class="peer sr-only">
                                        <div class="rounded-xl border-2 p-4 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 border-gray-200 hover:border-blue-300">
                                            <div class="flex items-center gap-3 mb-2">
                                                <svg class="w-6 h-6 text-gray-600 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                <span class="font-bold text-gray-900">Payment Gateway</span>
                                            </div>
                                            <p class="text-xs text-gray-500">QRIS, E-Wallet, VA</p>
                                        </div>
                                    </label>
                                </div>

                                <div x-show="paymentMethod === 'manual'" x-collapse class="bg-blue-50/50 border border-blue-100 p-4 rounded-2xl">
                                    <h5 class="text-sm font-bold text-blue-900 mb-2">Instruksi Transfer:</h5>
                                    <div class="bg-white p-3 rounded-xl border border-gray-200 mb-4">
                                        <p class="text-xs text-gray-500 mb-1">Bank BCA</p>
                                        <p class="font-mono font-bold text-lg tracking-wider text-gray-900">123-456-7890</p>
                                        <p class="text-xs font-semibold text-gray-700 mt-1">a.n. HIMAFI Universitas Udayana</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="bukti_pembayaran" class="block text-sm font-bold text-gray-700">Unggah Bukti Transfer <span class="text-red-500">*</span></label>
                                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-gray-200 rounded-xl bg-white">
                                    </div>
                                </div>

                                <div x-show="paymentMethod === 'gateway'" x-collapse class="bg-green-50 border border-green-100 p-4 rounded-2xl flex items-start gap-3">
                                    <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm text-green-800">Anda akan diarahkan ke halaman <b>Midtrans</b> setelah klik konfirmasi.</p>
                                </div>
                            </div>
                        </template>

                        <template x-if="comp.price == 0">
                            <div class="bg-green-50 text-green-800 p-4 rounded-2xl border border-green-200 flex items-center gap-3">
                                <svg class="w-8 h-8 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h5 class="font-bold">Pendaftaran Gratis!</h5>
                                    <p class="text-sm">Langsung konfirmasi untuk bergabung tanpa biaya.</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="registrationModal = false" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-bold text-sm shadow-sm hover:bg-gray-50 transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md transition">Konfirmasi Pendaftaran</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="merchModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center pt-4 px-4 pb-20 text-center">
            <div x-show="merchModal" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="merchModal = false"></div>
            
            <div x-show="merchModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl z-10 w-full max-w-xl border border-gray-100">
                
                <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Checkout Produk
                    </h3>
                    <button @click="merchModal = false" class="text-indigo-200 hover:text-white text-2xl font-bold">&times;</button>
                </div>

                <form action="{{ route('user.merchandise.beli') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="merchandise_id" x-model="activeMerch.id">
                    <input type="hidden" name="metode_pembayaran" value="manual">

                    <div class="px-6 py-6 space-y-6">
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <h4 class="text-xl font-bold text-gray-900" x-text="activeMerch.nama"></h4>
                            <div class="flex justify-between items-end mt-4 pt-4 border-t border-gray-200">
                                <span class="text-gray-600 font-medium">Total Tagihan:</span>
                                <span class="text-2xl font-black text-indigo-700" x-text="activeMerch.harga_fmt"></span>
                            </div>
                        </div>

                        <template x-if="activeMerch.harga > 0">
                            <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-2xl">
                                <h5 class="text-sm font-bold text-indigo-900 mb-2">Instruksi Transfer Manual:</h5>
                                <div class="bg-white p-3 rounded-xl border border-gray-200 mb-4">
                                    <p class="text-xs text-gray-500 mb-1">Bank Mandiri</p>
                                    <p class="font-mono font-bold text-lg text-gray-900">098-765-4321</p>
                                    <p class="text-xs font-semibold text-gray-700 mt-1">a.n. HIMAFI Store</p>
                                </div>

                                <div class="space-y-1">
                                    <label for="bukti_pembayaran_merch" class="block text-sm font-bold text-gray-700">Unggah Bukti Transfer <span class="text-red-500">*</span></label>
                                    <input type="file" id="bukti_pembayaran_merch" name="bukti_pembayaran" accept="image/*,.pdf" 
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer border border-gray-200 rounded-xl bg-white">
                                </div>
                            </div>
                        </template>

                        <template x-if="activeMerch.is_digital">
                            <div class="flex items-start gap-3 bg-purple-50 text-purple-800 p-4 rounded-xl text-sm font-medium border border-purple-100">
                                <svg class="w-6 h-6 shrink-0 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <div>E-Book ini akan masuk ke menu "Pustaka Saya" setelah pembayaran diverifikasi oleh Admin.</div>
                            </div>
                        </template>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="merchModal = false" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md transition">Konfirmasi Pesanan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>