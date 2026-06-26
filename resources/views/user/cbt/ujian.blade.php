<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ujian CBT - {{ config('app.name', 'HIMAFI UPC') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        window.MathJax = {
            tex: { inlineMath: [['$', '$'], ['\\(', '\\)']], displayMath: [['$$', '$$'], ['\\[', '\\]']], processEscapes: true },
            options: { ignoreHtmlClass: 'tex2jax_ignore', processHtmlClass: 'tex2jax_process' }
        };
    </script>
    <script type="text/javascript" id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
    
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .prevent-select {
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen flex flex-col selection:bg-blue-200 selection:text-blue-900 prevent-select" x-data="cbtSystem()" :class="{'overflow-hidden': mobileNavOpen || showWarningModal || !isExamStarted || showFinishModal}">

    <div x-show="!isExamStarted" class="fixed inset-0 z-[200] bg-slate-900 flex flex-col items-center justify-center p-6 text-center">
        <div class="max-w-xl bg-white p-10 rounded-[2rem] shadow-2xl">
            <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
            </div>
            <h2 class="text-3xl font-black text-slate-900 mb-4">Siap Memulai Ujian?</h2>
            <p class="text-slate-600 mb-8 font-medium leading-relaxed">
                Ujian ini mewajibkan mode <b>Layar Penuh (Full Screen)</b>. Jangan keluar dari mode layar penuh atau berpindah aplikasi selama ujian berlangsung karena akan memicu sistem pelanggaran otomatis. Waktu akan mulai berjalan setelah Anda menekan tombol di bawah ini.
            </p>
            <button @click="startExam()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl font-black text-lg shadow-lg shadow-blue-600/30 transition-all active:scale-95 flex items-center justify-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Masuk Layar Penuh & Mulai
            </button>
        </div>
    </div>

    <div x-show="showFinishModal" style="display: none;" class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full text-center shadow-2xl transform transition-all border-t-8 border-blue-600">
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            
            <h2 class="text-2xl font-black text-slate-900 mb-2">Akhiri Ujian?</h2>
            
            <template x-if="unansweredCount > 0">
                <div class="bg-red-50 border border-red-200 text-red-700 p-5 rounded-2xl mb-6 shadow-sm">
                    <p class="font-bold text-lg mb-1 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        PERINGATAN!
                    </p>
                    <p class="text-sm">Masih ada <span class="font-black text-xl mx-1" x-text="unansweredCount"></span> soal yang <b class="underline">BELUM DIJAWAB</b>. Yakin ingin menyimpan jawaban saat ini?</p>
                </div>
            </template>
            
            <template x-if="unansweredCount === 0">
                <p class="text-slate-600 mb-8 font-medium">Luar biasa! Anda telah menjawab seluruh soal. Apakah Anda yakin ingin menyimpan jawaban dan mengakhiri ujian sekarang?</p>
            </template>

            <div class="flex gap-4">
                <button @click="showFinishModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl transition-all active:scale-95 border border-slate-200">
                    Batal
                </button>
                <button @click="autoSubmit()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition-all active:scale-95 shadow-lg shadow-blue-600/30">
                    Ya, Kumpulkan
                </button>
            </div>
        </div>
    </div>

    <div x-show="showWarningModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-red-900/95 backdrop-blur-md">
        <div class="bg-white rounded-3xl p-8 max-w-lg w-full text-center shadow-2xl transform transition-all border-4 border-red-500">
            <div class="w-24 h-24 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner relative">
                <svg x-show="isFrozen" class="w-24 h-24 absolute inset-0 text-red-300 animate-ping opacity-50" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle></svg>
                <svg class="w-12 h-12 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            
            <h2 class="text-2xl font-black text-gray-900 mb-3">Peringatan Pelanggaran!</h2>
            <p class="text-gray-600 mb-6 font-medium text-sm leading-relaxed">
                Sistem mendeteksi Anda meninggalkan halaman ujian atau keluar dari Mode Layar Penuh. Tindakan ini dilarang keras.
            </p>
            
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl font-bold mb-6">
                Ini adalah peringatan ke-<span x-text="violationCount" class="text-xl"></span> dari maksimal 3 kali.
                <div class="text-xs font-medium mt-1.5 text-red-500">Jika mencapai batas maksimal, ujian Anda akan <span class="font-bold underline">dihentikan paksa</span>.</div>
            </div>

            <button @click="reenterFullscreenAndResume()" 
                    :disabled="isFrozen"
                    :class="isFrozen ? 'bg-slate-300 text-slate-500 cursor-not-allowed border-b-4 border-slate-400' : 'bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-600/30 active:scale-95'"
                    class="w-full font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-2">
                
                <template x-if="isFrozen">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Menunggu hukuman: <span x-text="freezeTimer" class="text-lg text-slate-700"></span> detik
                    </span>
                </template>
                
                <template x-if="!isFrozen">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        Kembali Layar Penuh & Lanjut
                    </span>
                </template>
            </button>
        </div>
    </div>

    <header class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex justify-between items-center">
            
            <div class="flex items-center gap-3 sm:gap-4">
                <button @click="mobileNavOpen = true" class="lg:hidden p-2 bg-white/10 hover:bg-white/20 rounded-xl transition active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                </button>

                <div class="hidden sm:flex bg-white/10 p-2.5 rounded-xl backdrop-blur-sm border border-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h1 class="font-bold text-sm sm:text-lg leading-tight truncate max-w-[150px] sm:max-w-sm md:max-w-md">{{ $competition->nama_lomba }}</h1>
                    <p class="text-[10px] sm:text-xs text-blue-200 font-medium truncate">{{ Auth::user()->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors"
                     :class="syncStatus === 'saving' ? 'bg-yellow-400 text-yellow-900 shadow-sm' : (syncStatus === 'saved' ? 'bg-emerald-500 text-white shadow-sm' : 'bg-white/10 text-white/50')">
                    <svg x-show="syncStatus === 'idle'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <svg x-show="syncStatus === 'saving'" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <svg x-show="syncStatus === 'saved'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="syncStatus === 'saving' ? 'Menyimpan...' : (syncStatus === 'saved' ? 'Tersimpan' : 'Aman')"></span>
                </div>

                <div class="bg-red-500/90 backdrop-blur-md px-3 sm:px-5 py-1.5 sm:py-2.5 rounded-xl border border-red-400 shadow-inner flex items-center gap-2 sm:gap-3 transition-colors" :class="{'animate-pulse bg-red-600 border-red-300': timeRemaining < 300}">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 hidden sm:block opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="text-right">
                        <p class="text-[9px] sm:text-[10px] font-bold text-red-100 uppercase tracking-wider leading-none mb-0.5 sm:mb-1">Sisa Waktu</p>
                        <span class="font-mono font-black text-base sm:text-xl leading-none tracking-tight" x-text="formatTime()">00:00:00</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto w-full p-4 sm:p-6 lg:p-8 flex items-start gap-8 relative">
        <div class="flex-1 w-full flex flex-col gap-6 max-w-full lg:max-w-[calc(100%-22rem)]">
            <div class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 overflow-hidden flex flex-col">
                <div class="bg-slate-50 border-b border-slate-100 px-6 sm:px-8 py-5 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-600 text-white w-10 h-10 rounded-xl flex items-center justify-center font-black text-lg shadow-md shadow-blue-600/20" x-text="activeQuestionIndex + 1"></div>
                        <h2 class="font-black text-slate-800 text-lg sm:text-xl">Soal Pilihan Ganda</h2>
                    </div>
                    
                    <label class="flex items-center gap-2.5 cursor-pointer bg-white border border-slate-200 hover:border-yellow-400 hover:bg-yellow-50 px-4 py-2 rounded-xl transition-all shadow-sm select-none group" :class="{'border-yellow-400 bg-yellow-50 ring-2 ring-yellow-400/20': doubtful[activeQuestionIndex]}">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" x-model="doubtful[activeQuestionIndex]" class="peer sr-only">
                            <div class="w-5 h-5 border-2 rounded transition-colors" :class="doubtful[activeQuestionIndex] ? 'border-yellow-500 bg-yellow-500' : 'border-slate-300 group-hover:border-yellow-400'">
                                <svg class="w-full h-full text-white scale-0 transition-transform duration-200" :class="{'scale-100': doubtful[activeQuestionIndex]}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                        <span class="font-bold text-sm transition-colors" :class="doubtful[activeQuestionIndex] ? 'text-yellow-700' : 'text-slate-600 group-hover:text-yellow-600'">Ragu-ragu</span>
                    </label>
                </div>

                <div class="p-6 sm:p-8 flex-1">
                    <div class="text-slate-800 text-lg sm:text-xl leading-relaxed mb-8 font-medium tex2jax_process" x-html="questions[activeQuestionIndex].text"></div>
                    
                    <template x-if="questions[activeQuestionIndex].image">
                        <div class="mb-8 p-2 border-2 border-slate-100 rounded-2xl overflow-hidden max-w-xl mx-auto shadow-sm">
                            <img :src="questions[activeQuestionIndex].image" class="w-full h-auto rounded-xl object-contain" alt="Gambar Pendukung Soal">
                        </div>
                    </template>

                    <div class="space-y-3 sm:space-y-4">
                        <template x-for="(opsi, huruf) in questions[activeQuestionIndex].options" :key="huruf">
                            <label class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 group relative overflow-hidden active:scale-[0.99]"
                                :class="answers[activeQuestionIndex] === huruf ? 'border-blue-600 bg-blue-50/50 shadow-md shadow-blue-900/5' : 'border-slate-200 hover:border-blue-300 hover:bg-slate-50'">
                                
                                <div class="relative flex items-center justify-center mt-0.5 shrink-0 z-10">
                                    <input type="radio" :name="'jawaban_'+activeQuestionIndex" :value="huruf" x-model="answers[activeQuestionIndex]" class="sr-only">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 flex items-center justify-center font-black text-sm sm:text-base transition-colors"
                                         :class="answers[activeQuestionIndex] === huruf ? 'border-blue-600 bg-blue-600 text-white shadow-inner' : 'border-slate-300 text-slate-500 group-hover:border-blue-400 group-hover:text-blue-500'">
                                        <span x-text="huruf"></span>
                                    </div>
                                </div>
                                <div class="flex-1 pt-1.5 sm:pt-2 text-slate-700 font-medium text-base sm:text-lg tex2jax_process z-10" x-html="opsi"></div>
                                <div class="absolute inset-0 bg-blue-50/50 opacity-0 transition-opacity duration-200" :class="{'opacity-100': answers[activeQuestionIndex] === huruf}"></div>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="bg-slate-50 border-t border-slate-100 p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <button @click="prevQuestion()" :disabled="activeQuestionIndex === 0" class="w-full sm:w-auto px-6 py-3.5 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 active:scale-95" :class="activeQuestionIndex === 0 ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-white border-2 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-100 shadow-sm'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                        Sebelumnya
                    </button>

                    <button @click="nextQuestion()" x-show="activeQuestionIndex < questions.length - 1" class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2 active:scale-95">
                        Selanjutnya
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                    
                    <button @click="confirmFinish()" x-show="activeQuestionIndex === questions.length - 1" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center gap-2 active:scale-95 animate-pulse hover:animate-none">
                        Selesaikan Ujian
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileNavOpen" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden transition-opacity" @click="mobileNavOpen = false" x-transition.opacity></div>

        <div class="fixed inset-y-0 right-0 z-50 w-[85%] max-w-sm bg-white shadow-2xl overflow-y-auto transform transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:w-80 lg:shadow-none lg:bg-transparent lg:z-0 lg:overflow-visible no-scrollbar flex flex-col"
             :class="mobileNavOpen ? 'translate-x-0' : 'translate-x-full'">
            
            <div class="bg-white lg:rounded-3xl lg:shadow-xl lg:shadow-blue-900/5 lg:border lg:border-slate-100 flex-1 lg:flex-none flex flex-col lg:sticky lg:top-24">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50 lg:rounded-t-3xl">
                    <h3 class="font-black text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Navigasi Soal
                    </h3>
                    <button @click="mobileNavOpen = false" class="lg:hidden p-1.5 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 flex-1 overflow-y-auto no-scrollbar">
                    <div class="grid grid-cols-5 gap-2.5 mb-8">
                        <template x-for="(q, index) in questions" :key="index">
                            <button @click="activeQuestionIndex = index; if(window.innerWidth < 1024) mobileNavOpen = false" 
                                class="aspect-square rounded-xl font-black text-sm flex items-center justify-center border-2 transition-all duration-200 relative overflow-hidden active:scale-95"
                                :class="{
                                    'border-blue-600 ring-4 ring-blue-600/20 scale-110 z-10': activeQuestionIndex === index,
                                    'border-yellow-400 bg-yellow-100 text-yellow-800': doubtful[index] && answers[index] && activeQuestionIndex !== index,
                                    'border-yellow-400 bg-white text-yellow-700': doubtful[index] && !answers[index] && activeQuestionIndex !== index,
                                    'border-emerald-500 bg-emerald-500 text-white shadow-md shadow-emerald-500/20': answers[index] && !doubtful[index] && activeQuestionIndex !== index,
                                    'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50': !answers[index] && !doubtful[index] && activeQuestionIndex !== index,
                                }">
                                <span x-text="index + 1" class="relative z-10"></span>
                            </button>
                        </template>
                    </div>

                    <div class="space-y-3 text-xs font-bold text-slate-600 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-5 h-5 rounded-md bg-emerald-500 shadow-sm shadow-emerald-500/30"></div> <span class="tracking-wide">Sudah Dijawab</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-5 h-5 rounded-md bg-yellow-100 border-2 border-yellow-400"></div> <span class="tracking-wide">Ragu - ragu</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-5 h-5 rounded-md bg-white border-2 border-slate-200"></div> <span class="tracking-wide">Belum Dijawab</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 bg-white lg:rounded-b-3xl mt-auto">
                    <button @click="confirmFinish()" class="w-full bg-rose-50 text-rose-600 border-2 border-rose-200 hover:bg-rose-600 hover:text-white hover:border-rose-600 font-bold py-3.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Akhiri Ujian
                    </button>
                </div>
            </div>
        </div>

    </main>

    <form id="cbt-form" method="POST" action="{{ route('user.ujian.submit', $registration->id) }}" class="hidden">
        @csrf
        <input type="hidden" name="answers" id="answers-payload">
    </form>

    <script>
        function cbtSystem() {
            return {
                isExamStarted: false, 
                isSubmitting: false,
                examInterval: null,
                timeRemaining: {{ $competition->durasi_menit * 60 }}, 
                
                activeQuestionIndex: 0,
                mobileNavOpen: false, 
                answers: @json($savedAnswers ?? new \stdClass()), 
                doubtful: {},
                questions: @json($questions), 

                syncStatus: 'idle',
                saveTimeout: null,
                
                // Variabel untuk Modals & Pelanggaran
                violationCount: 0,
                showWarningModal: false,
                showFinishModal: false,
                unansweredCount: 0,
                isFrozen: false,
                freezeTimer: 0,

                init() {
                    window.addEventListener('beforeunload', this.preventClose);

                    // Sistem Deteksi Pindah Tab & Keluar Full Screen (Anti-Cheat)
                    document.addEventListener("visibilitychange", () => {
                        this.handleViolation(document.hidden);
                    });
                    
                    document.addEventListener("fullscreenchange", () => {
                        if (!document.fullscreenElement && this.isExamStarted && !this.isSubmitting && !this.showFinishModal) {
                            this.handleViolation(true);
                        }
                    });

                    // Trigger render MathJax jika soal berganti
                    this.$watch('activeQuestionIndex', () => {
                        this.$nextTick(() => {
                            if (window.MathJax) {
                                MathJax.typesetClear();
                                MathJax.typesetPromise();
                            }
                        });
                    });

                    // Debounce Autosave
                    this.$watch('answers', (newValue) => {
                        this.syncStatus = 'saving';
                        if (this.saveTimeout) clearTimeout(this.saveTimeout);

                        this.saveTimeout = setTimeout(() => {
                            this.sendAutoSave(newValue);
                        }, 2000);
                    }, { deep: true });
                    
                    document.addEventListener('contextmenu', event => event.preventDefault());
                },

                // Fungsi Memulai Ujian (Fix MathJax Awal & Layar Penuh)
                startExam() {
                    let elem = document.documentElement;
                    if (elem.requestFullscreen) {
                        elem.requestFullscreen();
                    } else if (elem.webkitRequestFullscreen) { /* Safari */
                        elem.webkitRequestFullscreen();
                    } else if (elem.msRequestFullscreen) { /* IE11 */
                        elem.msRequestFullscreen();
                    }

                    this.isExamStarted = true;

                    // FIX: Paksa MathJax me-render setelah ujian terlihat (Start Screen hilang)
                    this.$nextTick(() => {
                        if (window.MathJax) {
                            MathJax.typesetClear();
                            MathJax.typesetPromise();
                        }
                    });

                    this.examInterval = setInterval(() => {
                        if (this.timeRemaining > 0) {
                            this.timeRemaining--;
                        } else {
                            this.autoSubmit();
                        }
                    }, 1000);
                },

                // Logika Pelanggaran
                handleViolation(isViolating) {
                    if (isViolating && this.isExamStarted && !this.isSubmitting) {
                        this.violationCount++;
                        if (this.violationCount >= 3) {
                            alert('PELANGGARAN FATAL: Anda telah melanggar aturan sebanyak 3 kali. Ujian dihentikan paksa dan jawaban Anda terkirim secara otomatis.');
                            this.autoSubmit();
                        } else {
                            this.showWarningModal = true;
                            this.startFreezePenalty();
                        }
                    }
                },

                // Kembali ke Fullscreen dari Peringatan
                reenterFullscreenAndResume() {
                    if(!this.isFrozen) {
                        this.showWarningModal = false;
                        let elem = document.documentElement;
                        if (!document.fullscreenElement) {
                            if (elem.requestFullscreen) elem.requestFullscreen();
                            else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
                            else if (elem.msRequestFullscreen) elem.msRequestFullscreen();
                        }
                    }
                },

                startFreezePenalty() {
                    this.isFrozen = true;
                    this.freezeTimer = 15; 
                    
                    let penaltyInterval = setInterval(() => {
                        if (this.freezeTimer > 0) {
                            this.freezeTimer--;
                        } else {
                            clearInterval(penaltyInterval);
                            this.isFrozen = false; 
                        }
                    }, 1000);
                },

                sendAutoSave(answersData) {
                    if(!this.isExamStarted) return;
                    
                    fetch("{{ route('user.ujian.autosave', $registration->id) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}" 
                        },
                        body: JSON.stringify({ answers: JSON.stringify(answersData) })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.syncStatus = 'saved';
                            setTimeout(() => { 
                                if(this.syncStatus === 'saved') this.syncStatus = 'idle'; 
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error("Autosave failed:", error);
                        this.syncStatus = 'idle';
                    });
                },

                preventClose(e) {
                    e.preventDefault();
                    e.returnValue = '';
                },

                formatTime() {
                    let h = Math.floor(this.timeRemaining / 3600);
                    let m = Math.floor((this.timeRemaining % 3600) / 60);
                    let s = this.timeRemaining % 60;
                    return `${h > 0 ? h.toString().padStart(2, '0') + ':' : ''}${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                },

                nextQuestion() {
                    if (this.activeQuestionIndex < this.questions.length - 1) this.activeQuestionIndex++;
                },

                prevQuestion() {
                    if (this.activeQuestionIndex > 0) this.activeQuestionIndex--;
                },

                // Menampilkan Modal Kumpulkan Ujian
                confirmFinish() {
                    this.unansweredCount = this.questions.length - Object.keys(this.answers).length;
                    this.showFinishModal = true;
                },

                autoSubmit() {
                    this.isSubmitting = true; 
                    window.removeEventListener('beforeunload', this.preventClose);
                    clearInterval(this.examInterval);
                    
                    if (document.fullscreenElement) {
                        if (document.exitFullscreen) document.exitFullscreen();
                        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                        else if (document.msExitFullscreen) document.msExitFullscreen();
                    }
                    
                    let payload = {};
                    for (let i = 0; i < this.questions.length; i++) {
                        if (this.answers[i]) {
                            payload[this.questions[i].id] = this.answers[i];
                        }
                    }
                    
                    document.getElementById('answers-payload').value = JSON.stringify(payload);
                    document.getElementById('cbt-form').submit();
                }
            }
        }
    </script>
</body>
</html>