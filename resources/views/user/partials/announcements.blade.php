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