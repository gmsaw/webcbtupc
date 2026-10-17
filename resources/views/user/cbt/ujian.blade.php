<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian CBT - {{ $competition->nama_lomba }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.MathJax = {
            tex: { inlineMath: [['$', '$'], ['\\(', '\\)']], displayMath: [['$$', '$$'], ['\\[', '\\]']], processEscapes: true },
            options: { ignoreHtmlClass: 'tex2jax_ignore', processHtmlClass: 'tex2jax_process' }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .prevent-select {
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        html { scroll-behavior: smooth; }

        /* ── GPU-accelerated pulse untuk timer kritis ── */
        @keyframes softPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.85; transform: scale(1.02); }
        }
        .animate-soft-pulse { animation: softPulse 1.5s ease-in-out infinite; will-change: transform, opacity; }

        /* ── Modal masuk halus ── */
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(8px); }
            to   { opacity: 1; transform: scale(1)    translateY(0);   }
        }
        .animate-modal-in { animation: modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); }

        /* ── Focus ring konsisten ── */
        :focus-visible {
            outline: 3px solid rgba(59, 130, 246, 0.5);
            outline-offset: 2px;
            border-radius: 0.5rem;
        }

        /* ── Prevent iOS bounce di container utama ── */
        body { overscroll-behavior-y: contain; }

        /* ── Transisi opsi jawaban ── */
        .option-item { transition: border-color 0.2s, background-color 0.2s, transform 0.1s; }
        .option-item:active { transform: scale(0.99); }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen flex flex-col selection:bg-blue-200 selection:text-blue-900 prevent-select"
      x-data="cbtSystem()"
      :class="{'overflow-hidden': mobileNavOpen || showWarningModal || !isExamStarted || showFinishModal}">

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL MULAI UJIAN --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="!isExamStarted" x-cloak
         class="fixed inset-0 z-[200] bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-4 text-center">
        <div class="max-w-xl w-full bg-white p-8 sm:p-10 rounded-3xl shadow-2xl animate-modal-in">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-500/30">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </div>

            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-3">Siap Memulai Ujian?</h2>
            <p class="text-slate-600 mb-3 font-medium leading-relaxed">
                Ujian ini mewajibkan mode <b class="text-slate-800">Layar Penuh (Full Screen)</b>. Jangan keluar dari mode layar penuh atau berpindah aplikasi. Pelanggaran akan dicatat otomatis.
            </p>
            <p class="text-xs text-slate-500 mb-8">
                Waktu ujian mengikuti <b class="text-slate-700">jadwal resmi</b> — bukan dihitung dari saat Anda menekan tombol.
            </p>

            <button @click="startExam()"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 rounded-2xl font-black text-base sm:text-lg shadow-lg shadow-blue-600/30 transition-all active:scale-95 flex items-center justify-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Masuk Layar Penuh & Mulai
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL SELESAI UJIAN --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="showFinishModal" x-cloak
         class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full text-center shadow-2xl border-t-8 border-blue-600 animate-modal-in">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h2 class="text-2xl font-black text-slate-900 mb-3">Akhiri Ujian?</h2>

            <template x-if="unansweredCount > 0">
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl mb-6 text-left">
                    <p class="font-bold text-base mb-1 flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Perhatian
                    </p>
                    <p class="text-sm">Masih ada <span class="font-black text-lg mx-1" x-text="unansweredCount"></span> soal yang <b class="underline">belum dijawab</b>. Yakin ingin menyimpan jawaban saat ini?</p>
                </div>
            </template>

            <template x-if="unansweredCount === 0">
                <p class="text-sm text-slate-600 mb-8 font-medium">
                    Luar biasa! Anda telah menjawab seluruh soal. Apakah Anda yakin ingin menyimpan jawaban dan mengakhiri ujian sekarang?
                </p>
            </template>

            <div class="flex gap-3">
                <button @click="showFinishModal = false"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl text-sm transition-all active:scale-95 border border-slate-200">
                    Batal
                </button>
                <button @click="autoSubmit()"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3.5 rounded-xl text-sm transition-all active:scale-95 shadow-lg shadow-blue-600/30">
                    Kumpulkan
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL PERINGATAN PELANGGARAN --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="showWarningModal" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-red-950/95 backdrop-blur-md">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full text-center shadow-2xl border-4 border-red-500 animate-modal-in">
            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-5 relative">
                <svg x-show="isFrozen" class="w-20 h-20 absolute inset-0 text-red-300 animate-ping opacity-50" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                </svg>
                <svg class="w-10 h-10 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h2 class="text-2xl font-black text-gray-900 mb-2">Peringatan Pelanggaran!</h2>
            <p class="text-gray-600 mb-5 font-medium text-sm leading-relaxed">
                Sistem mendeteksi Anda meninggalkan halaman ujian atau keluar dari Layar Penuh.
            </p>

            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl font-bold mb-6">
                Ini adalah pelanggaran ke-<span x-text="violationCount" class="text-xl mx-1"></span>.
                <div class="text-xs font-medium mt-1 text-red-500">
                    Catatan pelanggaran telah dikirim ke Admin. Panitia berhak membatalkan nilai jika terbukti melakukan kecurangan.
                </div>
            </div>

            <button @click="reenterFullscreenAndResume()"
                    :disabled="isFrozen"
                    :class="isFrozen
                        ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                        : 'bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-600/30 active:scale-95'"
                    class="w-full font-bold py-4 rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                <template x-if="isFrozen">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menunggu penalti: <span x-text="freezeTimer" class="text-lg text-slate-800 font-black"></span> dtk
                    </span>
                </template>
                <template x-if="!isFrozen">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                        Kembali Layar Penuh & Lanjut
                    </span>
                </template>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- HEADER --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 sm:h-18 flex items-center justify-between gap-2">

            {{-- KIRI: tombol drawer + identitas --}}
            <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                <button @click="mobileNavOpen = true"
                        class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition active:scale-95 shrink-0"
                        aria-label="Buka navigasi soal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>

                <div class="hidden sm:flex w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl items-center justify-center text-white shrink-0 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                <div class="min-w-0">
                    <h1 class="font-bold text-sm sm:text-base text-slate-900 leading-tight truncate">
                        {{ $competition->nama_lomba }}
                    </h1>
                    <p class="text-[11px] sm:text-xs text-slate-500 truncate">
                        {{ Auth::user()->name }}
                    </p>
                </div>
            </div>

            {{-- KANAN: sync indicator + timer --}}
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">

                {{-- Sync indicator (desktop) --}}
                <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors"
                     :class="syncStatus === 'saving'
                         ? 'bg-amber-100 text-amber-800'
                         : (syncStatus === 'saved'
                             ? 'bg-emerald-100 text-emerald-700'
                             : 'bg-slate-100 text-slate-500')">
                    <svg x-show="syncStatus === 'saving'" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg x-show="syncStatus !== 'saving'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="syncStatus === 'saving' ? 'Menyimpan' : (syncStatus === 'saved' ? 'Tersimpan' : 'Aman')"></span>
                </div>

                {{-- Timer --}}
                <div class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl border-2 transition-colors"
                     :class="timeRemaining > 0 && timeRemaining < 300
                         ? 'bg-red-600 border-red-500 text-white animate-soft-pulse'
                         : 'bg-white border-slate-200 text-slate-900'">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0"
                         :class="timeRemaining > 0 && timeRemaining < 300 ? 'text-red-100' : 'text-slate-400'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-right">
                        <p class="text-[9px] font-bold uppercase tracking-wider leading-none mb-0.5 hidden sm:block"
                           :class="timeRemaining > 0 && timeRemaining < 300 ? 'text-red-100' : 'text-slate-400'">
                            Sisa Waktu
                        </p>
                        <span class="font-mono font-black text-sm sm:text-lg leading-none tracking-tight tabular-nums"
                              x-text="formatTime()">00:00:00</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MAIN — 2 kolom responsif --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <main class="flex-1 w-full max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_19rem] gap-4 lg:gap-6 items-start">

            {{-- ══════════════════════════════════════════════════════════ --}}
            {{-- KOLOM SOAL --}}
            {{-- ══════════════════════════════════════════════════════════ --}}
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-lg shadow-slate-900/5 border border-slate-200 overflow-hidden flex flex-col min-h-[calc(100vh-10rem)]">

                {{-- Header soal --}}
                <div class="border-b border-slate-100 px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-3 bg-slate-50/50">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center font-black text-sm sm:text-base shadow-sm shrink-0"
                             x-text="activeQuestionIndex + 1"></div>
                        <div class="min-w-0">
                            <h2 class="font-bold text-slate-900 text-sm sm:text-base truncate">Soal Pilihan Ganda</h2>
                            <p class="text-xs text-slate-500">
                                Soal <span x-text="activeQuestionIndex + 1" class="font-bold"></span>
                                dari <span x-text="questions.length" class="font-bold"></span>
                            </p>
                        </div>
                    </div>

                    {{-- Toggle ragu-ragu --}}
                    <label class="flex items-center gap-2 cursor-pointer select-none shrink-0 px-2.5 sm:px-3 py-2 rounded-xl border-2 transition-all"
                           :class="doubtful[activeQuestionIndex]
                               ? 'bg-amber-50 border-amber-400'
                               : 'bg-white border-slate-200 hover:border-amber-300'">
                        <input type="checkbox" x-model="doubtful[activeQuestionIndex]" class="sr-only">
                        <div class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                             :class="doubtful[activeQuestionIndex] ? 'bg-amber-500 border-amber-500' : 'border-slate-300'">
                            <svg class="w-3 h-3 text-white transition-opacity"
                                 :class="doubtful[activeQuestionIndex] ? 'opacity-100' : 'opacity-0'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xs sm:text-sm"
                              :class="doubtful[activeQuestionIndex] ? 'text-amber-800' : 'text-slate-600'">
                            Ragu-ragu
                        </span>
                    </label>
                </div>

                {{-- Konten soal --}}
                <div class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="text-slate-900 text-base sm:text-lg leading-relaxed mb-6 sm:mb-8 font-medium tex2jax_process"
                         x-html="questions[activeQuestionIndex].text"></div>

                    <template x-if="questions[activeQuestionIndex].image">
                        <div class="mb-6 sm:mb-8 rounded-2xl overflow-hidden border border-slate-200 max-w-2xl mx-auto">
                            <img :src="questions[activeQuestionIndex].image"
                                 class="w-full h-auto object-contain max-h-96"
                                 alt="Gambar pendukung soal"
                                 loading="lazy">
                        </div>
                    </template>

                    <div class="space-y-2.5 sm:space-y-3">
                        <template x-for="(opsi, huruf) in questions[activeQuestionIndex].options" :key="huruf">
                            <label class="option-item flex items-start gap-3 sm:gap-4 p-3.5 sm:p-4 rounded-2xl border-2 cursor-pointer group"
                                   :class="answers[activeQuestionIndex] === huruf
                                       ? 'border-blue-600 bg-blue-50/60 shadow-sm'
                                       : 'border-slate-200 bg-white hover:border-blue-300 hover:bg-slate-50'">

                                <input type="radio"
                                       :name="'jawaban_' + activeQuestionIndex"
                                       :value="huruf"
                                       x-model="answers[activeQuestionIndex]"
                                       class="sr-only">

                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border-2 flex items-center justify-center font-black text-xs sm:text-sm shrink-0 transition-colors"
                                     :class="answers[activeQuestionIndex] === huruf
                                         ? 'bg-blue-600 border-blue-600 text-white'
                                         : 'border-slate-300 text-slate-500 group-hover:border-blue-400 group-hover:text-blue-500'">
                                    <span x-text="huruf"></span>
                                </div>

                                <div class="flex-1 pt-0.5 text-slate-700 font-medium text-sm sm:text-base tex2jax_process"
                                     x-html="opsi"></div>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Tombol navigasi — sticky di mobile --}}
                <div class="border-t border-slate-100 bg-white px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-2 sm:gap-3">
                    <button @click="prevQuestion(); window.scrollTo({top: 0, behavior: 'smooth'});"
                            :disabled="activeQuestionIndex === 0"
                            class="px-4 sm:px-6 py-3 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center gap-2 active:scale-95"
                            :class="activeQuestionIndex === 0
                                ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
                                : 'bg-white border-2 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="hidden sm:inline">Sebelumnya</span>
                        <span class="sm:hidden">Prev</span>
                    </button>

                    <button @click="nextQuestion(); window.scrollTo({top: 0, behavior: 'smooth'});"
                            x-show="activeQuestionIndex < questions.length - 1"
                            class="px-4 sm:px-8 py-3 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center gap-2 active:scale-95 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-600/20">
                        <span class="hidden sm:inline">Selanjutnya</span>
                        <span class="sm:hidden">Next</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <button @click="confirmFinish()"
                            x-show="activeQuestionIndex === questions.length - 1"
                            class="px-4 sm:px-8 py-3 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center gap-2 active:scale-95 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white shadow-md shadow-emerald-600/20">
                        <span class="hidden sm:inline">Selesai Ujian</span>
                        <span class="sm:hidden">Selesai</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════ --}}
            {{-- SIDEBAR NAVIGASI — sticky di desktop, drawer di mobile --}}
            {{-- ══════════════════════════════════════════════════════════ --}}

            {{-- Backdrop mobile --}}
            <div x-show="mobileNavOpen"
                 x-transition.opacity
                 @click="mobileNavOpen = false"
                 class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden"></div>

            <aside class="fixed inset-y-0 right-0 z-50 w-[85%] max-w-sm bg-white shadow-2xl transform transition-transform duration-300 ease-out lg:static lg:translate-x-0 lg:w-auto lg:shadow-none lg:z-0 lg:sticky lg:top-24 lg:h-[calc(100vh-7rem)]"
                   :class="mobileNavOpen ? 'translate-x-0' : 'translate-x-full'">

                <div class="bg-white lg:rounded-3xl lg:shadow-lg lg:border lg:border-slate-200 h-full flex flex-col overflow-hidden">

                    {{-- Header sidebar --}}
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
                        <h3 class="font-bold text-slate-900 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Navigasi Soal
                        </h3>
                        <button @click="mobileNavOpen = false" class="lg:hidden p-1.5 text-slate-500 hover:bg-slate-200 rounded-lg transition"
                                aria-label="Tutup navigasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Progress counter --}}
                    <div class="px-5 py-3 border-b border-slate-100 shrink-0">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Progress</span>
                            <span class="text-xs font-black text-slate-900">
                                <span x-text="Object.keys(answers).filter(k => answers[k]).length"></span>
                                <span class="text-slate-400">/</span>
                                <span x-text="questions.length"></span>
                            </span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500"
                                 :style="'width: ' + (questions.length > 0 ? (Object.keys(answers).filter(k => answers[k]).length / questions.length * 100) : 0) + '%'"></div>
                        </div>
                    </div>

                    {{-- Grid nomor soal --}}
                    <div class="flex-1 overflow-y-auto no-scrollbar p-4">
                        <div class="grid grid-cols-5 gap-2">
                            <template x-for="(q, index) in questions" :key="index">
                                <button @click="activeQuestionIndex = index; if (window.innerWidth < 1024) mobileNavOpen = false; window.scrollTo({top: 0, behavior: 'smooth'});"
                                        class="aspect-square rounded-xl font-black text-xs sm:text-sm flex items-center justify-center border-2 transition-all relative active:scale-95"
                                        :class="{
                                            'border-blue-600 bg-blue-600 text-white shadow-md scale-105 z-10': activeQuestionIndex === index,
                                            'border-emerald-500 bg-emerald-500 text-white': answers[index] && !doubtful[index] && activeQuestionIndex !== index,
                                            'border-amber-400 bg-amber-100 text-amber-800': doubtful[index] && activeQuestionIndex !== index,
                                            'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50': !answers[index] && !doubtful[index] && activeQuestionIndex !== index,
                                        }">
                                    <span x-text="index + 1"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 shrink-0">
                        <div class="space-y-2 text-xs font-semibold text-slate-600">
                            <div class="flex items-center gap-2">
                                <div class="w-3.5 h-3.5 rounded-md bg-emerald-500"></div>
                                <span>Sudah dijawab</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3.5 h-3.5 rounded-md bg-amber-100 border-2 border-amber-400"></div>
                                <span>Ragu-ragu</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3.5 h-3.5 rounded-md bg-white border-2 border-slate-200"></div>
                                <span>Belum dijawab</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol akhiri --}}
                    <div class="p-4 border-t border-slate-100 bg-white shrink-0 lg:rounded-b-3xl">
                        <button @click="confirmFinish()"
                                class="w-full bg-rose-50 text-rose-600 border-2 border-rose-200 hover:bg-rose-600 hover:text-white hover:border-rose-600 font-bold py-3 rounded-xl text-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Akhiri Ujian
                        </button>
                    </div>

                </div>
            </aside>
        </div>
    </main>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- FORM SUBMIT --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <form id="cbt-form" method="POST" action="{{ route('user.ujian.submit', $registration->id) }}" class="hidden">
        @csrf
        <input type="hidden" name="answers" id="answers-payload">
    </form>

    <script>
        function cbtSystem() {
            return {
                // ═══════════════════════════════════════════════════════
                // STATE
                // ═══════════════════════════════════════════════════════
                isExamStarted: false,
                isSubmitting: false,
                examInterval: null,

                sisaDetik: {{ $sisaDetik }},
                timeRemaining: {{ $sisaDetik }},
                endTimeMs: new Date("{{ $waktuSelesaiMutlak->toIso8601String() }}").getTime(),

                activeQuestionIndex: 0,
                mobileNavOpen: false,

                answers: @json($savedAnswers ?? new \stdClass()),
                doubtful: {},
                questions: @json($questions),

                syncStatus: 'idle',
                saveTimeout: null,
                lastAutosaveAt: 0,
                autosaveInFlight: false,

                violationCount: 0,
                showWarningModal: false,
                showFinishModal: false,
                unansweredCount: 0,
                isFrozen: false,
                freezeTimer: 0,
                freezeInterval: null,

                // ═══════════════════════════════════════════════════════
                // INIT
                // ═══════════════════════════════════════════════════════
                init() {
                    this.updateTimer();

                    window.addEventListener('beforeunload', this.preventClose);

                    document.addEventListener('visibilitychange', () => {
                        this.handleViolation(document.hidden);
                    });

                    document.addEventListener('fullscreenchange', () => {
                        if (!document.fullscreenElement && this.isExamStarted && !this.isSubmitting && !this.showFinishModal) {
                            this.handleViolation(true);
                        }
                    });

                    this.$watch('activeQuestionIndex', () => {
                        this.$nextTick(() => {
                            if (window.MathJax) {
                                MathJax.typesetClear();
                                MathJax.typesetPromise();
                            }
                        });
                    });

                    this.$watch('answers', () => {
                        this.scheduleAutosave();
                    }, { deep: true });

                    document.addEventListener('contextmenu', event => event.preventDefault());
                },

                // ═══════════════════════════════════════════════════════
                // TIMER
                // ═══════════════════════════════════════════════════════
                updateTimer() {
                    const distance = this.endTimeMs - Date.now();
                    if (distance <= 0) {
                        this.timeRemaining = 0;
                        if (this.examInterval) clearInterval(this.examInterval);
                        if (this.isExamStarted && !this.isSubmitting) this.autoSubmit();
                    } else {
                        this.timeRemaining = Math.floor(distance / 1000);
                    }
                },

                formatTime() {
                    if (this.timeRemaining <= 0) return "00:00:00";
                    const h = Math.floor(this.timeRemaining / 3600);
                    const m = Math.floor((this.timeRemaining % 3600) / 60);
                    const s = this.timeRemaining % 60;
                    return `${h > 0 ? h.toString().padStart(2, '0') + ':' : ''}${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                },

                // ═══════════════════════════════════════════════════════
                // START EXAM
                // ═══════════════════════════════════════════════════════
                startExam() {
                    if (this.timeRemaining <= 0) {
                        alert('Waktu ujian telah berakhir. Jawaban Anda akan dikumpulkan otomatis.');
                        this.autoSubmit();
                        return;
                    }

                    const elem = document.documentElement;
                    if (elem.requestFullscreen) elem.requestFullscreen();
                    else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
                    else if (elem.msRequestFullscreen) elem.msRequestFullscreen();

                    this.isExamStarted = true;

                    this.$nextTick(() => {
                        if (window.MathJax) {
                            MathJax.typesetClear();
                            MathJax.typesetPromise();
                        }
                    });

                    this.examInterval = setInterval(() => this.updateTimer(), 1000);
                },

                // ═══════════════════════════════════════════════════════
                // ANTI-CHEAT
                // ═══════════════════════════════════════════════════════
                handleViolation(isViolating) {
                    if (isViolating && this.isExamStarted && !this.isSubmitting && !this.showFinishModal) {
                        this.violationCount++;
                        this.showWarningModal = true;
                        this.startFreezePenalty();
                    }
                },

                reenterFullscreenAndResume() {
                    if (this.isFrozen) return;
                    this.showWarningModal = false;
                    const elem = document.documentElement;
                    if (!document.fullscreenElement) {
                        if (elem.requestFullscreen) elem.requestFullscreen();
                        else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
                        else if (elem.msRequestFullscreen) elem.msRequestFullscreen();
                    }
                },

                startFreezePenalty() {
                    this.isFrozen = true;
                    this.freezeTimer = 15;

                    if (this.freezeInterval) clearInterval(this.freezeInterval);
                    this.freezeInterval = setInterval(() => {
                        if (this.freezeTimer > 0) {
                            this.freezeTimer--;
                        } else {
                            clearInterval(this.freezeInterval);
                            this.freezeInterval = null;
                            this.isFrozen = false;
                        }
                    }, 1000);
                },

                // ═══════════════════════════════════════════════════════
                // AUTOSAVE
                // ═══════════════════════════════════════════════════════
                scheduleAutosave() {
                    this.syncStatus = 'saving';
                    if (this.saveTimeout) clearTimeout(this.saveTimeout);

                    this.saveTimeout = setTimeout(() => {
                        this.sendAutoSave();
                    }, 2000);
                },

                async sendAutoSave() {
                    if (!this.isExamStarted || this.isSubmitting) return;
                    if (this.autosaveInFlight) {
                        if (this.saveTimeout) clearTimeout(this.saveTimeout);
                        this.saveTimeout = setTimeout(() => this.sendAutoSave(), 1500);
                        return;
                    }

                    const now = Date.now();
                    const MIN_INTERVAL = 3000;

                    if (this.lastAutosaveAt && (now - this.lastAutosaveAt) < MIN_INTERVAL) {
                        if (this.saveTimeout) clearTimeout(this.saveTimeout);
                        this.saveTimeout = setTimeout(
                            () => this.sendAutoSave(),
                            MIN_INTERVAL - (now - this.lastAutosaveAt)
                        );
                        return;
                    }

                    this.lastAutosaveAt = now;
                    this.autosaveInFlight = true;

                    const payload = {};
                    Object.keys(this.answers).forEach(index => {
                        const answer = this.answers[index];
                        const q = this.questions[index];
                        if (answer && q && q.id) {
                            payload[q.id] = answer;
                        }
                    });

                    try {
                        const res = await fetch("{{ route('user.ujian.autosave', $registration->id) }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "X-Requested-With": "XMLHttpRequest",
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                answers: JSON.stringify(payload),
                                violation_count: this.violationCount,
                            }),
                        });

                        if (res.ok) {
                            const data = await res.json();
                            if (data.status === 'success') {
                                this.syncStatus = 'saved';
                                setTimeout(() => {
                                    if (this.syncStatus === 'saved') this.syncStatus = 'idle';
                                }, 3000);
                            }
                        } else {
                            this.syncStatus = 'idle';
                        }
                    } catch (error) {
                        console.error("Autosave failed:", error);
                        this.syncStatus = 'idle';
                    } finally {
                        this.autosaveInFlight = false;
                    }
                },

                // ═══════════════════════════════════════════════════════
                // UTIL
                // ═══════════════════════════════════════════════════════
                preventClose(e) {
                    e.preventDefault();
                    e.returnValue = '';
                },

                nextQuestion() {
                    if (this.activeQuestionIndex < this.questions.length - 1) {
                        this.activeQuestionIndex++;
                    }
                },

                prevQuestion() {
                    if (this.activeQuestionIndex > 0) {
                        this.activeQuestionIndex--;
                    }
                },

                // ═══════════════════════════════════════════════════════
                // FINISH
                // ═══════════════════════════════════════════════════════
                confirmFinish() {
                    this.unansweredCount = this.questions.length - Object.keys(this.answers).filter(
                        k => this.answers[k]
                    ).length;
                    this.showFinishModal = true;
                },

                autoSubmit() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;

                    window.removeEventListener('beforeunload', this.preventClose);
                    if (this.examInterval) clearInterval(this.examInterval);
                    if (this.saveTimeout) clearTimeout(this.saveTimeout);
                    if (this.freezeInterval) clearInterval(this.freezeInterval);

                    if (document.fullscreenElement) {
                        if (document.exitFullscreen) document.exitFullscreen();
                        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                        else if (document.msExitFullscreen) document.msExitFullscreen();
                    }

                    const payload = {};
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