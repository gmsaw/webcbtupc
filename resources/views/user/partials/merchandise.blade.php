<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            Official Merchandise
        </h3>
    </div>
    <div class="p-5 space-y-5">
        @forelse($merchandises as $item)
            <div @click="activeMerch = { id: '{{ $item->id }}', nama: '{{ addslashes($item->nama_produk) }}', harga: {{ $item->harga }}, harga_fmt: '{{ $item->harga == 0 ? 'GRATIS' : 'Rp ' . number_format($item->harga, 0, ',', '.') }}', is_digital: {{ $item->is_digital ? 'true' : 'false' }} }; merchModal = true" 
                 class="flex gap-4 items-center group cursor-pointer block hover:bg-indigo-50/50 p-2 -m-2 rounded-2xl transition">
                <div class="w-20 h-20 rounded-2xl bg-gray-100 flex-shrink-0 overflow-hidden border border-gray-200 relative">
                    @if($item->hasMedia('gambar_produk'))
                        <img src="{{ $item->getFirstMediaUrl('gambar_produk') }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-blue-50"></div>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <h4 class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2 pr-2">{{ $item->nama_produk }}</h4>
                        @if($item->is_digital)
                            <span class="bg-purple-100 text-purple-700 text-[9px] px-1.5 py-0.5 rounded font-black tracking-wider uppercase">E-Book</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mb-1 line-clamp-1">{{ $item->deskripsi }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-sm font-black text-indigo-600">{{ $item->harga == 0 ? 'GRATIS' : 'Rp ' . number_format($item->harga, 0, ',', '.') }}</p>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">Beli &rarr;</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-gray-400">
                <p class="text-xs font-bold">Belum ada merchandise tersedia.</p>
            </div>
        @endforelse

        <a href="#" class="w-full mt-2 block text-center bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 font-bold py-3 rounded-xl text-sm transition-colors border border-indigo-100 shadow-sm">
            Kunjungi HIMAFI Store &rarr;
        </a>
    </div>
</div>