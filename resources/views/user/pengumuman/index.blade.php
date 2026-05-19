<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm" title="Kembali ke Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                {{ __('Pusat Informasi & Pengumuman') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-3xl p-8 shadow-lg text-white mb-8 relative overflow-hidden flex items-center gap-6">
                <div class="absolute -right-10 -top-10 opacity-20">
                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v6h2v-6h-2zm0-4v2h2V7h-2z"/></svg>
                </div>
                <div class="relative z-10 w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center shrink-0 border border-white/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-1">Pengumuman Panitia</h3>
                    <p class="text-yellow-50">Semua informasi terbaru terkait pelaksanaan Udayana Physics Championship 2026 akan disiarkan di sini.</p>
                </div>
            </div>

            <div class="space-y-6">
                @forelse($announcements as $info)
                    <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gray-200 group-hover:bg-yellow-400 transition-colors"></div>
                        
                        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="inline-flex items-center gap-1.5 bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-semibold border border-gray-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $info->created_at->translatedFormat('l, d F Y') }}
                                    </span>
                                    
                                    @if($info->created_at->diffInHours(now()) < 24)
                                        <span class="bg-red-100 text-red-600 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider animate-pulse">Terbaru</span>
                                    @endif
                                </div>

                                <a href="{{ route('user.pengumuman.show', $info->id) }}" class="block">
                                    <h4 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-yellow-600 transition-colors hover:underline underline-offset-4 decoration-yellow-400">{{ $info->judul }}</h4>
                                </a>
                                <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-line bg-gray-50/50 p-4 rounded-2xl border border-gray-50">
                                    {{ $info->isi }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-16 border border-gray-100 shadow-sm text-center flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Pengumuman</h4>
                        <p class="text-gray-500">Panitia belum menyiarkan informasi apapun saat ini.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $announcements->links() }}
            </div>

        </div>
    </div>
</x-app-layout>