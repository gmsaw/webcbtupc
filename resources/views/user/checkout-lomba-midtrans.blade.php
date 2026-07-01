<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            {{ __('Checkout Pendaftaran') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="w-full lg:w-2/3 space-y-6">
                    
                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-10 -mt-10 opacity-70"></div>
                        <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4">Detail Pesanan</h3>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center shrink-0 border border-gray-200 overflow-hidden">
                                @if($registration->competition->hasMedia('gambar_lomba'))
                                    <img src="{{ $registration->competition->getFirstMediaUrl('gambar_lomba') }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-1">{{ $registration->competition->nama_lomba }}</h4>
                                <p class="text-sm text-gray-500 mb-2">Tiket Pendaftaran Peserta</p>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 border border-gray-200 rounded-lg text-xs font-mono text-gray-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    {{ $registration->order_id }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 border-b border-gray-100 pb-3">Data Pemesan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                                <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Alamat Email</p>
                                <p class="font-semibold text-gray-800">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-xs text-gray-500 mb-1">Asal Sekolah / Instansi</p>
                                <p class="font-semibold text-gray-800">{{ Auth::user()->asal_sekolah }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                            <h3 class="text-sm font-bold text-gray-900">Metode Pembayaran Tersedia</h3>
                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-md border border-green-100 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Otomatis Lunas
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Pilih salah satu metode berikut di halaman selanjutnya:</p>
                        
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-black text-red-500 italic text-sm">QRIS</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-bold text-blue-500 text-sm">GoPay</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-bold text-orange-500 text-sm">ShopeePay</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-black text-blue-800 tracking-wider text-sm">BCA</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-black text-yellow-500 text-sm">mandiri</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-black text-teal-600 text-sm">BNI</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-black text-blue-700 text-sm">BRI</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-bold text-red-600 text-xs">Alfamart</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-bold text-blue-600 text-xs">Indomaret</span></div>
                            <div class="border border-gray-200 rounded-xl py-2 flex items-center justify-center bg-gray-50/50"><span class="font-bold text-purple-600 text-sm">OVO</span></div>
                        </div>
                    </div>

                </div>

                <div class="w-full lg:w-1/3">
                    <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 sticky top-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Ringkasan Tagihan</h3>
                        
                        @php
                            // Ambil nominal tagihan asli yang sudah terekam di tabel payments
                            $totalAmount = $registration->payment->amount ?? 0;
                        @endphp

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Harga Tiket Lomba</span>
                                <span class="font-semibold text-gray-800">
                                    {{ $totalAmount == 0 ? 'Gratis' : 'Rp ' . number_format($totalAmount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Biaya Layanan</span>
                                <span class="font-semibold text-green-600">Gratis</span>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-4 mb-8">
                            <div class="flex justify-between items-end">
                                <span class="text-sm font-bold text-gray-900">Total Pembayaran</span>
                                <span class="text-2xl font-black {{ $totalAmount == 0 ? 'text-green-600' : 'text-blue-600' }} tracking-tight">
                                    {{ $totalAmount == 0 ? 'GRATIS' : 'Rp ' . number_format($totalAmount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl shadow-md transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span>Bayar Sekarang</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>

                        <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-400">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>Pembayaran aman dilindungi oleh <b>Midtrans</b></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            snap.pay('{{ $registration->payment->snap_token }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('dashboard') }}";
                },
                onPending: function(result){
                    alert("Menunggu pembayaran Anda diselesaikan!");
                },
                onError: function(result){
                    alert("Terjadi kesalahan pada pembayaran!");
                },
                onClose: function(){
                    // Optional: Do nothing, just let them click pay again
                }
            });
        };
    </script>
</x-app-layout>