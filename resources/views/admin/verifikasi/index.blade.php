<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-6 text-slate-800">Manajemen & Verifikasi Lomba</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($competitions as $comp)
                @php
                    // Mengecek apakah waktu sekarang sudah melewati batas waktu lomba berakhir
                    $isEnded = \Carbon\Carbon::now() > \Carbon\Carbon::parse($comp->waktu_pelaksanaan)->addMinutes($comp->durasi_menit);
                @endphp

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col h-full relative overflow-hidden group hover:shadow-md transition-all">
                    
                    @if($isEnded)
                        <div class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-black px-4 py-1.5 rounded-bl-xl tracking-wider">TELAH BERAKHIR</div>
                    @else
                        <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[10px] font-black px-4 py-1.5 rounded-bl-xl tracking-wider">SEDANG AKTIF</div>
                    @endif

                    <h3 class="font-black text-xl text-slate-900 mb-1 pr-24 leading-tight">{{ $comp->nama_lomba }}</h3>
                    <p class="text-sm text-slate-500 mb-6">Total Pendaftar: <span class="font-bold text-blue-600">{{ $comp->registrations_count }}</span></p>
                    
                    <div class="mt-auto space-y-2">
                        <a href="{{ route('admin.verifikasi.show', $comp->id) }}" class="block text-center w-full py-2.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-xl text-sm font-bold transition shadow-sm">
                            Kelola & Verifikasi
                        </a>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('admin.kompetisi.export', $comp->id) }}" class="flex-1 text-center py-2.5 bg-slate-50 hover:bg-slate-800 hover:text-white text-slate-600 rounded-xl text-xs font-bold transition border border-slate-200 shadow-sm flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                CSV
                            </a>

                            @if($isEnded)
                                <a href="{{ route('admin.kompetisi.ranking', $comp->id) }}" class="flex-1 text-center py-2.5 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 rounded-xl text-xs font-bold transition border border-amber-200 shadow-sm flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    Ranking
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>