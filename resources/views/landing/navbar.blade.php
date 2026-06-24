<nav class="bg-white/90 backdrop-blur-md fixed w-full z-50 border-b border-gray-100 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center font-extrabold text-xl text-blue-700 tracking-tight">
                    HIMAFI <span class="text-gray-900 ml-1">UPC 2026</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#tentang" class="text-gray-600 hover:text-blue-600 font-medium transition">Tentang</a>
                    <a href="#cabang" class="text-gray-600 hover:text-blue-600 font-medium transition">Cabang Lomba</a>
                    <a href="#informasi" class="text-gray-600 hover:text-blue-600 font-medium transition">Informasi</a>
                    <a href="#timeline" class="text-gray-600 hover:text-blue-600 font-medium transition">Jadwal</a>
                    <a href="#galeri" class="text-gray-600 hover:text-blue-600 font-medium transition">Galeri</a>
                </div>
                <div>
                    @if (Route::has('login'))
                        <div class="flex items-center space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium hidden sm:block">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-5 py-2 rounded-xl font-semibold shadow-md shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                                        Daftar Sekarang
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>