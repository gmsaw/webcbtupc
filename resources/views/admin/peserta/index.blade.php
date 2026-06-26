<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm" title="Kembali ke Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ __('Database Peserta') }}
                </h2>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="text-sm bg-blue-50 text-blue-700 px-4 py-2.5 rounded-xl font-semibold border border-blue-100 shadow-sm flex-1 sm:flex-none text-center">
                    Total Data: {{ $peserta->total() }}
                </div>
                <a href="{{ route('admin.peserta.export') }}" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm transition-colors duration-300 flex-1 sm:flex-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                    <span class="hidden sm:inline">Download CSV</span>
                    <span class="sm:hidden">CSV</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-sm border border-gray-100 mb-6">
                <form action="{{ route('admin.peserta.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau sekolah..." class="w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm transition-colors">
                    </div>

                    <div class="w-full md:w-48">
                        <select name="status" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm text-gray-600 bg-gray-50 cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-gray-900 hover:bg-black text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md transition-colors w-full md:w-auto">
                            Terapkan
                        </button>
                        @if(request('search') || request('status'))
                            <a href="{{ route('admin.peserta.index') }}" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2.5 rounded-xl text-sm font-bold border border-red-100 transition-colors flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100 tracking-wider">
                            <tr>
                                <th scope="col" class="py-4 px-6">Informasi Peserta</th>
                                <th scope="col" class="py-4 px-6 text-center">Asal Sekolah</th>
                                <th scope="col" class="py-4 px-6 text-center">Total Lomba</th>
                                <th scope="col" class="py-4 px-6 text-right">Aksi & Debugging</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-data="{ modalOpen: false, modalData: {} }">
                            @forelse ($peserta as $user)
                                @php
                                    $jumlahLomba = \App\Models\Registration::where('user_id', $user->id)->count();
                                @endphp
                                <tr class="bg-white hover:bg-blue-50/50 transition-colors group">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-cyan-100 text-blue-700 flex items-center justify-center font-bold text-lg shadow-sm border border-white shrink-0 overflow-hidden">
                                                @if($user->hasMedia('foto_profil'))
                                                    <img src="{{ $user->getFirstMediaUrl('foto_profil') }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ substr($user->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 text-base">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                <div class="text-xs font-medium text-blue-600 mt-0.5">{{ $user->no_wa }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center font-medium text-gray-700">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            {{ $user->asal_sekolah ?? 'Belum Diisi' }}
                                        </div>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center font-medium">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $jumlahLomba > 0 ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-gray-100 text-gray-400' }}">
                                            {{ $jumlahLomba }}
                                        </span>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                            
                                            <button @click="modalData = { name: '{{ addslashes($user->name) }}', email: '{{ $user->email }}', school: '{{ addslashes($user->asal_sekolah) }}', wa: '{{ $user->no_wa }}', status: '{{ $user->status_verifikasi }}', img: '{{ $user->hasMedia('kartu_pelajar') ? $user->getFirstMediaUrl('kartu_pelajar') : '' }}' }; modalOpen = true" 
                                                    class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat Detail">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>

                                            <a href="{{ route('admin.peserta.edit', $user->id) }}" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit Data">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>

                                            <form action="{{ route('admin.peserta.reset', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('🛠️ DEBUG MODE: Yakin ingin MENGHAPUS SEMUA pendaftaran lomba dan riwayat nilai CBT milik akun ini? Akun peserta tidak akan dihapus, hanya riwayat lombanya saja yang dikosongkan.')" class="p-2 text-gray-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition" title="Reset Semua Riwayat Lomba (Debug)">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.peserta.destroy', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Peringatan: Akun beserta seluruh data lombanya akan dihapus permanen. Lanjutkan?')" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Peserta">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-16 px-6 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            <p class="text-lg font-medium text-gray-500">Tidak ada data peserta ditemukan.</p>
                                            <p class="text-sm mt-1">Coba gunakan kata kunci pencarian atau filter status yang berbeda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="modalOpen = false" aria-hidden="true"></div>
                                    
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    
                                    <div x-show="modalOpen" 
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                         class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                                        
                                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                                Profil Lengkap Peserta
                                            </h3>
                                            <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 transition bg-white rounded-lg p-1 border border-transparent hover:border-gray-200">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                        <div class="px-6 py-6 flex flex-col md:flex-row gap-8">
                                            <div class="flex-1 space-y-5">
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                                                    <div class="text-lg font-bold text-gray-900" x-text="modalData.name"></div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Asal Sekolah</label>
                                                    <div class="text-base font-medium text-gray-700" x-text="modalData.school"></div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                                                        <div class="text-sm text-gray-600" x-text="modalData.email"></div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">WhatsApp</label>
                                                        <a :href="'https://wa.me/' + modalData.wa.replace(/\D/g,'')" target="_blank" class="text-sm font-bold text-green-600 hover:underline flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/></svg>
                                                            <span x-text="modalData.wa"></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="w-full md:w-56 shrink-0">
                                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 text-center md:text-left">Kartu Pelajar</label>
                                                
                                                <template x-if="modalData.img !== ''">
                                                    <div class="rounded-xl overflow-hidden border-2 border-dashed border-gray-200 shadow-sm bg-gray-50">
                                                        <img :src="modalData.img" alt="Kartu Pelajar" class="w-full object-cover">
                                                    </div>
                                                </template>
                                                
                                                <template x-if="modalData.img === ''">
                                                    <div class="h-32 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center text-gray-400">
                                                        <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        <span class="text-xs font-medium">Belum Diunggah</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
                                            <button type="button" @click="modalOpen = false" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                                                Tutup Jendela
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6">
                {{ $peserta->links() }}
            </div>

        </div>
    </div>
</x-app-layout>