<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">Manajemen Merchandise</h2>
            <a href="{{ route('admin.merchandise.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md transition">+ Tambah Produk</a>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl font-bold border border-green-200">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-3xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-100 uppercase text-xs">
                    <tr>
                        <th class="py-4 px-6">Produk</th>
                        <th class="py-4 px-6 text-center">Harga</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($merchandises as $item)
                    <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6 flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-gray-100 overflow-hidden border border-gray-200 shrink-0">
                                @if($item->hasMedia('gambar_produk'))
                                    <img src="{{ $item->getFirstMediaUrl('gambar_produk') }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-indigo-50"></div>
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 text-base flex items-center gap-2">
                                    {{ $item->nama_produk }}
                                    @if($item->is_digital)
                                        <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border border-purple-200">E-Book</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border border-gray-200">Fisik</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 line-clamp-1 mt-1">{{ $item->deskripsi }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center font-bold text-indigo-600">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($item->is_active)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Tersedia</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Habis/Disembunyikan</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('admin.merchandise.edit', $item->id) }}" class="text-blue-600 font-medium mr-3">Edit</a>
                            <form action="{{ route('admin.merchandise.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus produk ini?')" class="text-red-600 font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-10 text-center text-gray-500">Belum ada produk merchandise.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>