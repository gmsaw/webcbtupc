<div x-show="registrationModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center pt-4 px-4 pb-20 text-center">
    <div x-show="registrationModal" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="registrationModal = false"></div>
    
    <div x-show="registrationModal" 
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl z-10 w-full max-w-md border border-gray-100">
        
        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-5 flex justify-between items-center text-white">
            <h3 class="text-lg font-bold flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Checkout Pendaftaran
            </h3>
            <button type="button" @click="registrationModal = false" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 rounded-full p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('user.kompetisi.daftar') }}" method="POST">
            @csrf
            <input type="hidden" name="competition_id" x-model="comp.id">
            <input type="hidden" name="metode_pembayaran" value="gateway">

            <div class="px-6 py-6 space-y-5">
                
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-blue-100 rounded-bl-full -mr-10 -mt-10 opacity-50"></div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tiket Lomba</p>
                    <h4 class="text-xl font-black text-gray-900 leading-tight pr-8 relative z-10" x-text="comp.title"></h4>
                    
                    <div class="flex justify-between items-end mt-5 pt-5 border-t border-gray-200/60 relative z-10">
                        <span class="text-gray-500 font-medium text-sm">Total Tagihan</span>
                        <span class="text-3xl font-black text-blue-600 tracking-tight" x-text="comp.price_fmt"></span>
                    </div>
                </div>

                <template x-if="comp.price > 0">
                    <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-2xl flex items-start gap-4">
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-xl shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h5 class="font-bold text-blue-900 text-sm mb-1">Pembayaran Otomatis</h5>
                            <p class="text-xs text-blue-800/80 leading-relaxed">Anda akan diarahkan ke halaman Midtrans untuk membayar menggunakan <b>QRIS, GoPay, ShopeePay, atau Virtual Account</b>.</p>
                        </div>
                    </div>
                </template>

                <template x-if="comp.price == 0">
                    <div class="bg-green-50 border border-green-100 p-4 rounded-2xl flex items-start gap-4">
                        <div class="bg-green-100 text-green-600 p-2 rounded-xl shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h5 class="font-bold text-green-900 text-sm mb-1">Pendaftaran Bebas Biaya</h5>
                            <p class="text-xs text-green-800/80 leading-relaxed">Pendaftaran lomba ini gratis. Silakan klik lanjutkan untuk mengaktifkan status pendaftaran Anda.</p>
                        </div>
                    </div>
                </template>

            </div>

            <div class="bg-gray-50 px-6 py-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-3xl">
                <button type="button" @click="registrationModal = false" class="w-full sm:w-auto px-5 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl font-bold text-sm shadow-sm hover:bg-gray-50 hover:text-gray-900 transition-colors text-center">
                    Batal
                </button>
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-600/20 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span>Lanjutkan Pembayaran</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>