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

            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                @csrf
                <button type="submit" class="bg-red-500/20 hover:bg-red-500/40 backdrop-blur-md border border-red-500/30 text-white px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</div>