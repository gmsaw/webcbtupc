<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('user.pengumuman') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm" title="Kembali ke Daftar Pengumuman">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Detail Informasi') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 px-8 py-10 border-b border-yellow-100 relative overflow-hidden">
                    <svg class="absolute right-0 top-0 text-yellow-500/10 w-64 h-64 -mr-16 -mt-16 transform rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/></svg>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex items-center gap-1.5 bg-white text-yellow-700 px-4 py-1.5 rounded-xl text-sm font-bold shadow-sm border border-yellow-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $announcement->created_at->translatedFormat('l, d F Y') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 bg-white text-gray-500 px-4 py-1.5 rounded-xl text-sm font-bold shadow-sm border border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $announcement->created_at->format('H:i') }} WITA
                            </span>
                        </div>
                        
                        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-2">{{ $announcement->judul }}</h1>
                        <p class="text-yellow-800 font-medium">Oleh: Panitia HIMAFI UPC 2026</p>
                    </div>
                </div>

                <div class="p-8 md:p-12 bg-white">
                    <div class="prose max-w-none text-gray-700 text-lg leading-relaxed whitespace-pre-line">
                        {{ $announcement->isi }}
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500 font-medium">Apakah informasi ini bermanfaat?</p>
                    <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link berhasil disalin!');" class="bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-sm w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        Salin Link Pengumuman
                    </button>
                </div>

            </div>
            
        </div>
    </div>
</x-app-layout>