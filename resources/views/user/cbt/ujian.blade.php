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
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script type="text/javascript" id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen flex flex-col" x-data="cbtSystem()">

    <header class="bg-blue-700 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight truncate max-w-[200px] sm:max-w-md">{{ $competition->nama_lomba }}</h1>
                    <p class="text-xs text-blue-200">Peserta: {{ Auth::user()->name }}</p>
                </div>
            </div>

            <div class="bg-red-500 px-4 py-2 rounded-xl border border-red-400 shadow-inner flex items-center gap-2" :class="{'animate-pulse bg-red-600': timeRemaining < 300}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-red-100 uppercase tracking-wider leading-none mb-0.5">Sisa Waktu</p>
                    <span class="font-mono font-bold text-lg leading-none" x-text="formatTime()">00:00:00</span>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto w-full p-4 sm:p-6 lg:p-8 flex flex-col lg:flex-row gap-6">
        
        <div class="flex-1 flex flex-col gap-6">
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden flex-1 flex flex-col">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h2 class="font-black text-gray-800 text-xl">Soal No. <span class="text-blue-600" x-text="activeQuestionIndex + 1">1</span></h2>
                    <div class="flex items-center gap-2 text-sm font-bold text-gray-500 bg-white px-3 py-1 rounded-lg border border-gray-200">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pilihan Ganda
                    </div>
                </div>

                <div class="p-6 md:p-8 flex-1 overflow-y-auto">
                    <div class="text-gray-800 text-lg leading-relaxed mb-6 font-medium" x-html="questions[activeQuestionIndex].text"></div>

                    <template x-if="questions[activeQuestionIndex].image">
                        <div class="mb-8 border border-gray-200 rounded-xl overflow-hidden max-w-lg shadow-sm">
                            <img :src="questions[activeQuestionIndex].image" class="w-full h-auto" alt="Gambar Soal">
                        </div>
                    </template>

                    <div class="space-y-3 mt-4">
                        <template x-for="(opsi, huruf) in questions[activeQuestionIndex].options" :key="huruf">
                            <label class="flex items-start gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all group"
                                :class="answers[activeQuestionIndex] === huruf ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50'">
                                
                                <div class="relative flex items-center justify-center mt-0.5">
                                    <input type="radio" :name="'jawaban_'+activeQuestionIndex" :value="huruf" x-model="answers[activeQuestionIndex]" class="sr-only">
                                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-sm transition-colors"
                                         :class="answers[activeQuestionIndex] === huruf ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 text-gray-500 group-hover:border-blue-400'">
                                        <span x-text="huruf"></span>
                                    </div>
                                </div>
                                <div class="flex-1 pt-1 text-gray-700" x-html="opsi"></div>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex flex-wrap sm:flex-nowrap justify-between items-center gap-4">
                    <button @click="prevQuestion()" :disabled="activeQuestionIndex === 0" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2" :class="activeQuestionIndex === 0 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 shadow-sm'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Soal Sebelumnya
                    </button>

                    <label class="w-full sm:w-auto flex items-center justify-center gap-2 cursor-pointer bg-yellow-50 border border-yellow-200 hover:bg-yellow-100 text-yellow-700 px-6 py-3 rounded-xl font-bold text-sm transition shadow-sm">
                        <input type="checkbox" x-model="doubtful[activeQuestionIndex]" class="w-5 h-5 rounded border-yellow-400 text-yellow-500 focus:ring-yellow-500">
                        Ragu-ragu
                    </label>

                    <button @click="nextQuestion()" x-show="activeQuestionIndex < questions.length - 1" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                        Selanjutnya
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-80 shrink-0 flex flex-col gap-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4">Navigasi Soal</h3>
                
                <div class="grid grid-cols-5 gap-2 mb-6">
                    <template x-for="(q, index) in questions" :key="index">
                        <button @click="activeQuestionIndex = index" 
                            class="aspect-square rounded-xl font-bold text-sm flex items-center justify-center border-2 transition-all relative overflow-hidden"
                            :class="{
                                'border-blue-600 ring-2 ring-blue-200': activeQuestionIndex === index,
                                'border-yellow-400 bg-yellow-100 text-yellow-800': doubtful[index] && answers[index],
                                'border-yellow-400 bg-white text-yellow-600': doubtful[index] && !answers[index],
                                'border-green-500 bg-green-500 text-white': answers[index] && !doubtful[index],
                                'border-gray-200 bg-white text-gray-500 hover:border-gray-300': !answers[index] && !doubtful[index] && activeQuestionIndex !== index,
                            }">
                            <span x-text="index + 1" class="relative z-10"></span>
                        </button>
                    </template>
                </div>

                <div class="space-y-2 text-xs font-medium text-gray-600 bg-gray-50 p-4 rounded-2xl border border-gray-100 mb-6">
                    <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-green-500 border border-green-600"></div> Sudah Dijawab</div>
                    <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-yellow-100 border border-yellow-400"></div> Ragu-ragu</div>
                    <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-white border border-gray-200"></div> Belum Dijawab</div>
                </div>

                <button @click="confirmFinish()" class="w-full bg-red-50 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white font-bold py-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesaikan Ujian
                </button>
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
                // Ambil durasi dari database (menit diubah ke detik)
                timeRemaining: {{ $competition->durasi_menit * 60 }}, 
                activeQuestionIndex: 0,
                
                answers: {}, 
                doubtful: {},

                // INJEKSI DATA SOAL DARI DATABASE LARAVEL
                questions: @json($questions), 

                init() {
                    // Jalankan Timer
                    setInterval(() => {
                        if (this.timeRemaining > 0) this.timeRemaining--;
                        else this.autoSubmit();
                    }, 1000);

                    // Render ulang MathJax setiap kali pindah soal
                    this.$watch('activeQuestionIndex', () => {
                        this.$nextTick(() => {
                            if (window.MathJax) MathJax.typesetPromise();
                        });
                    });
                },

                formatTime() {
                    let h = Math.floor(this.timeRemaining / 3600);
                    let m = Math.floor((this.timeRemaining % 3600) / 60);
                    let s = this.timeRemaining % 60;
                    return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                },

                nextQuestion() {
                    if (this.activeQuestionIndex < this.questions.length - 1) this.activeQuestionIndex++;
                },

                prevQuestion() {
                    if (this.activeQuestionIndex > 0) this.activeQuestionIndex--;
                },

                confirmFinish() {
                    let unanswered = this.questions.length - Object.keys(this.answers).length;
                    let msg = unanswered > 0 
                        ? `Anda masih memiliki ${unanswered} soal yang BELUM DIJAWAB. Yakin ingin mengakhiri ujian sekarang?`
                        : `Anda sudah menjawab seluruh soal. Yakin ingin menyelesaikan ujian?`;
                    
                    if (confirm(msg)) this.autoSubmit();
                },

                autoSubmit() {
                    // Format jawaban untuk dikirim ke Backend: { "ID_SOAL_1": "A", "ID_SOAL_2": "C" }
                    let payload = {};
                    for (let i = 0; i < this.questions.length; i++) {
                        if (this.answers[i]) {
                            payload[this.questions[i].id] = this.answers[i];
                        }
                    }
                    
                    // Masukkan ke dalam input hidden dan Submit!
                    document.getElementById('answers-payload').value = JSON.stringify(payload);
                    document.getElementById('cbt-form').submit();
                }
            }
        }
    </script>
</body>
</html>