<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            {{ __('Pustaka E-Book Saya') }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if($koleksi->isEmpty())
            <div class="bg-white rounded-3xl p-16 border border-gray-100 shadow-sm text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Pustaka Masih Kosong</h3>
                <p class="text-gray-500 mb-6">Anda belum memiliki E-Book. Beli E-Book di menu Merchandise untuk mulai membaca.</p>
                <a href="{{ route('dashboard') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 transition">Cari E-Book</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($koleksi as $item)
                    @php $ebook = $item->merchandise; @endphp
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden group hover:shadow-lg transition">
                        <div class="h-64 bg-gray-100 relative overflow-hidden">
                            @if($ebook->hasMedia('gambar_produk'))
                                <img src="{{ $ebook->getFirstMediaUrl('gambar_produk') }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-purple-50"></div>
                            @endif
                        </div>
                        <div class="p-5">
                            <h4 class="font-bold text-gray-900 mb-1 line-clamp-2">{{ $ebook->nama_produk }}</h4>
                            <p class="text-xs text-green-600 font-bold mb-4 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Akses Permanen
                            </p>
                            <a href="{{ route('user.pustaka.read', $ebook->id) }}" class="block w-full text-center bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white font-bold py-2.5 rounded-xl text-sm transition-colors border border-indigo-100">
                                Baca Sekarang
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>