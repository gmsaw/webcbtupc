<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                {{ __('Broadcast Pengumuman') }}
            </h2>
            <a href="{{ route('admin.pengumuman.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2">
                + Buat Pengumuman
            </a>
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
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-6">Judul & Isi Pengumuman</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengumuman as $info)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6 font-medium">{{ $info->created_at->format('d M Y') }}</td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-900 text-base mb-1">{{ $info->judul }}</div>
                            <div class="text-xs text-gray-500 line-clamp-1">{{ $info->isi }}</div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($info->is_active)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Aktif (Tayang)</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-bold">Disembunyikan</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('admin.pengumuman.edit', $info->id) }}" class="text-blue-600 font-medium mr-3">Edit</a>
                            <form action="{{ route('admin.pengumuman.destroy', $info->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus pengumuman ini?')" class="text-red-600 font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-10 text-center text-gray-500">Belum ada pengumuman disiarkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>