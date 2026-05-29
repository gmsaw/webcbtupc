<div x-show="merchModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center pt-4 px-4 pb-20 text-center">
    <div x-show="merchModal" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="merchModal = false"></div>
    
    <div x-show="merchModal" 
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl z-10 w-full max-w-md border border-gray-100">
        
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 flex justify-between items-center text-white">
            <h3 class="text-lg font-bold flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Checkout Produk
            </h3>
            <button type="button" @click="merchModal = false" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 rounded-full p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('user.merchandise.beli') }}" method="POST">
            @csrf
            <input type="hidden" name="merchandise_id" x-model="activeMerch.id">
            <input type="hidden" name="metode_pembayaran" value="gateway"> 

            <div class="px-6 py-6 space-y-5">
                
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-indigo-100 rounded-bl-full -mr-10 -mt-10 opacity-50"></div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Detail Pembelian</p>
                    <h4 class="text-xl font-black text-gray-900 leading-tight pr-8 relative z-10" x-text="activeMerch.nama"></h4>
                    
                    <div class="flex justify-between items-end mt-5 pt-5 border-t border-gray-200/60 relative z-10">
                        <span class="text-gray-500 font-medium text-sm">Total Pembayaran</span>
                        <span class="text-3xl font-black text-indigo-600 tracking-tight" x-text="activeMerch.harga_fmt"></span>
                    </div>
                </div>

                <template x-if="activeMerch.harga > 0">
                    <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-2xl flex items-start gap-4">
                        <div class="bg-indigo-100 text-indigo-600 p-2 rounded-xl shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div>
                            <h5 class="font-bold text-indigo-900 text-sm mb-1">Pembayaran Digital</h5>
                            <p class="text-xs text-indigo-800/80 leading-relaxed">Penyelesaian transaksi dilakukan melalui Midtrans dengan jaminan keamanan & verifikasi otomatis.</p>
                        </div>
                    </div>
                </template>

                <template x-if="activeMerch.is_digital">
                    <div class="bg-purple-50 border border-purple-100 p-4 rounded-2xl flex items-start gap-4">
                        <div class="bg-purple-100 text-purple-600 p-2 rounded-xl shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div>
                            <h5 class="font-bold text-purple-900 text-sm mb-1">Akses Instan E-Book</h5>
                            <p class="text-xs text-purple-800/80 leading-relaxed">Begitu pembayaran selesai di-scan, Bank Soal/E-Book akan langsung tersedia di menu <b>Pustaka E-Book</b>.</p>
                        </div>
                    </div>
                </template>

            </div>

            <div class="bg-gray-50 px-6 py-5 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-3xl">
                <button type="button" @click="merchModal = false" class="w-full sm:w-auto px-5 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl font-bold text-sm shadow-sm hover:bg-gray-50 hover:text-gray-900 transition-colors text-center">
                    Batal
                </button>
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span>Checkout Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>