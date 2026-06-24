<section class="relative min-h-screen flex items-center justify-center pt-32 pb-20 overflow-hidden bg-slate-50/50">
    
    <!-- Pattern Background -->
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.04]"></div>

    <!-- Ambient Glow (Blobs) -->
    <div class="absolute top-1/4 left-1/4 w-[30rem] h-[30rem] bg-blue-400/20 rounded-full mix-blend-multiply filter blur-[100px] animate-pulse"></div>
    <div class="absolute top-1/3 right-1/4 w-[30rem] h-[30rem] bg-indigo-400/20 rounded-full mix-blend-multiply filter blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
            
            <!-- BINGKAI FOTO KIRI -->
            <div class="hidden lg:block w-72 shrink-0 relative group perspective-1000" data-aos="fade-right" data-aos-duration="1200">
                <div class="absolute -inset-2 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-[2.5rem] blur-lg opacity-30 group-hover:opacity-70 transition duration-500"></div>
                <div class="relative bg-white/90 backdrop-blur-md p-4 rounded-[2rem] shadow-2xl transform -rotate-6 group-hover:rotate-0 hover:-translate-y-4 transition-all duration-500 ease-out border border-white/50 ring-1 ring-slate-100">
                    <div class="aspect-[4/5] rounded-xl overflow-hidden bg-slate-100 mb-5 relative group-hover:shadow-inner transition-all">
                        <img src="https://i.ibb.co.com/6JFz0vJC/hero-1-1.jpg" " alt="Laboratorium" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 ring-1 ring-inset ring-black/10 rounded-xl pointer-events-none"></div>
                    </div>
                    <div class="text-center pb-2">
                        <p class="font-black text-slate-800 text-sm tracking-wide">Malam Penganugerahan</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1.5">Jawara 2025</p>
                    </div>
                </div>
            </div>

            <!-- KONTEN TENGAH -->
            <div class="flex-1 text-center max-w-3xl mx-auto" data-aos="zoom-in" data-aos-duration="1000">
                
                <!-- Badge -->
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white/60 backdrop-blur-md border border-blue-100/80 text-blue-600 text-xs font-bold uppercase tracking-widest mb-8 shadow-sm hover:shadow-md transition-all cursor-default">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                    </span>
                    Registrasi Telah Dibuka
                </div>

                <!-- Judul Utama -->
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-slate-900 leading-[1.1] mb-6 tracking-tighter">
                    Udayana Physics <br class="hidden md:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500">Championship</span>
                </h1>

                <!-- Deskripsi Singkat -->
                <p class="text-lg md:text-xl text-slate-600 mb-10 leading-relaxed font-medium max-w-2xl mx-auto">
                    Buktikan ketajaman logikamu dan taklukkan tantangan di platform <span class="font-bold text-blue-600">Computer Based Test (CBT)</span> terintegrasi paling mutakhir tahun ini.
                </p>

                <!-- Grup Tombol Aksi -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-2xl text-base font-black shadow-xl shadow-blue-600/30 transition-all transform hover:-translate-y-1 hover:shadow-blue-600/50 flex items-center justify-center gap-3">
                            Registrasi Peserta
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </a>
                    @endif
                    <a href="#timeline" class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-700 border-2 border-slate-200/80 px-8 py-4 rounded-2xl text-base font-bold shadow-sm hover:shadow-md transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                        Lihat Jadwal
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </a>
                </div>
            </div>

            <!-- BINGKAI FOTO KANAN -->
            <div class="hidden lg:block w-72 shrink-0 relative group perspective-1000" data-aos="fade-left" data-aos-duration="1200">
                <div class="absolute -inset-2 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-[2.5rem] blur-lg opacity-30 group-hover:opacity-70 transition duration-500"></div>
                <div class="relative bg-white/90 backdrop-blur-md p-4 rounded-[2rem] shadow-2xl transform rotate-6 group-hover:rotate-0 hover:-translate-y-4 transition-all duration-500 ease-out border border-white/50 ring-1 ring-slate-100">
                    <div class="aspect-[4/5] rounded-xl overflow-hidden bg-slate-100 mb-5 relative group-hover:shadow-inner transition-all">
                        <img src="https://i.ibb.co.com/NdHfPYFj/DSC06861-2.jpg" alt="Medali Juara" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 ring-1 ring-inset ring-black/10 rounded-xl pointer-events-none"></div>
                    </div>
                    <div class="text-center pb-2">
                        <p class="font-black text-slate-800 text-sm tracking-wide">Semi Final</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1.5">Momen Juara Umum</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>