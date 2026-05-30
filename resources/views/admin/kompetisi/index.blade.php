<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm" title="Kembali ke Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    {{ __('Manajemen Lomba') }}
                </h2>
            </div>
            
            <a href="{{ route('admin.kompetisi.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-indigo-200 transition-colors transform hover:-translate-y-0.5 flex items-center gap-2 w-fit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Lomba Baru
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl shadow-sm flex items-center justify-between transition-opacity">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            <div class="space-y-4">
                @forelse ($kompetisi as $lomba)
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row items-start sm:items-center gap-6 group">
                        
                        <div class="w-full sm:w-40 h-32 rounded-2xl bg-gray-100 shrink-0 overflow-hidden relative shadow-inner border border-gray-200">
                            @if($lomba->hasMedia('gambar_lomba'))
                                <img src="{{ $lomba->getFirstMediaUrl('gambar_lomba') }}" alt="{{ $lomba->nama_lomba }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-tr from-indigo-100 to-blue-50 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            <div class="absolute top-2 left-2">
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $isDateValid = $lomba->tanggal_mulai && $lomba->tanggal_selesai && $today->between($lomba->tanggal_mulai, $lomba->tanggal_selesai);
                                    $isOpen = $lomba->is_active && $isDateValid;
                                @endphp

                                @if($isOpen)
                                    <span class="bg-green-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm uppercase tracking-wider flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span> Buka
                                    </span>
                                @else
                                    <span class="bg-red-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm uppercase tracking-wider">
                                        Tutup
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex-1 w-full">
                            <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors">{{ $lomba->nama_lomba }}</h3>
                            <p class="text-sm text-gray-500 line-clamp-2 mb-3 max-w-2xl">{{ $lomba->deskripsi ?: 'Tidak ada deskripsi untuk kompetisi ini.' }}</p>
                            
                            <div class="flex flex-wrap items-center gap-3 text-xs font-medium">
                                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100 text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08-.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $lomba->harga_pendaftaran == 0 ? 'GRATIS' : 'Rp ' . number_format($lomba->harga_pendaftaran, 0, ',', '.') }}
                                </div>
                                
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 rounded-lg border border-blue-100 text-blue-700">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @if($lomba->tanggal_mulai && $lomba->tanggal_selesai)
                                        <span><span class="font-bold">Mulai:</span> {{ \Carbon\Carbon::parse($lomba->tanggal_mulai)->translatedFormat('d M Y') }}</span>
                                        <span class="text-blue-300">|</span>
                                        <span><span class="font-bold">Selesai:</span> {{ \Carbon\Carbon::parse($lomba->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                                    @else
                                        <span class="font-bold">Tanggal Belum Diatur</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap sm:flex-col justify-end gap-2 w-full sm:w-auto mt-4 sm:mt-0 pt-4 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                            
                            <a href="{{ route('admin.kompetisi.soal.index', $lomba->id) }}" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-purple-50 hover:bg-purple-600 hover:text-white text-purple-600 border border-purple-200 hover:border-purple-600 px-4 py-2 rounded-xl text-sm font-bold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <span class="sm:hidden lg:inline">Soal</span>
                            </a>

                            <a href="{{ route('admin.kompetisi.edit', $lomba->id) }}" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-white hover:bg-indigo-50 text-indigo-600 border border-indigo-200 px-4 py-2 rounded-xl text-sm font-bold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span class="sm:hidden lg:inline">Edit</span>
                            </a>
                            
                            <form action="{{ route('admin.kompetisi.destroy', $lomba->id) }}" method="POST" class="flex-1 sm:flex-none flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus lomba ini? Semua peserta yang terdaftar juga akan terhapus dari sistem!')" class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 border border-red-200 hover:border-red-600 px-4 py-2 rounded-xl text-sm font-bold transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span class="sm:hidden lg:inline">Hapus</span>
                                </button>
                            </form>
                            
                        </div>
                        
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-16 border border-gray-100 shadow-sm text-center flex flex-col items-center">
                        <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Kompetisi</h3>
                        <p class="text-gray-500 max-w-md mx-auto mb-8">Anda belum menambahkan data perlombaan apapun. Buat kompetisi pertama Anda agar peserta dapat segera mendaftar.</p>
                        <a href="{{ route('admin.kompetisi.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-colors transform hover:-translate-y-0.5">
                            Mulai Buat Kompetisi
                        </a>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-8">
                {{ $kompetisi->links() }}
            </div>

        </div>
    </div>
</x-app-layout>