<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm" title="Kembali ke Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                {{ __('Verifikasi Pendaftaran') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ docModal: false, activeData: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl shadow-sm flex items-center justify-between transition-opacity">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="py-4 px-6">Peserta & Lomba</th>
                                <th scope="col" class="py-4 px-6 text-center">Waktu Daftar</th>
                                <th scope="col" class="py-4 px-6 text-center">Bukti Bayar & Dokumen</th>
                                <th scope="col" class="py-4 px-6 text-center">Status</th>
                                <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($registrations as $reg)
                                <tr class="bg-white hover:bg-blue-50/30 transition-colors {{ $reg->status_pendaftaran === 'pending' ? 'bg-yellow-50/10' : '' }}">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 text-base">{{ $reg->user->name }}</div>
                                        <div class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            {{ $reg->user->asal_sekolah }}
                                        </div>
                                        <div class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-xs font-bold border border-indigo-100 mt-1">
                                            Lomba: {{ $reg->competition->nama_lomba }}
                                        </div>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center font-medium">
                                        {{ $reg->created_at->format('d M Y') }}<br>
                                        <span class="text-xs text-gray-400">{{ $reg->created_at->format('H:i') }} WITA</span>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center">
                                        <button type="button" 
                                            @click="activeData = { 
                                                nama: '{{ addslashes($reg->user->name) }}',
                                                lomba: '{{ addslashes($reg->competition->nama_lomba) }}',
                                                harga: '{{ $reg->competition->harga_pendaftaran == 0 ? 'GRATIS' : 'Rp ' . number_format($reg->competition->harga_pendaftaran, 0, ',', '.') }}',
                                                bukti: '{{ $reg->hasMedia('bukti_pembayaran_lomba') ? $reg->getFirstMediaUrl('bukti_pembayaran_lomba') : ($reg->hasMedia('bukti_pembayaran') ? $reg->getFirstMediaUrl('bukti_pembayaran') : '') }}',
                                                kartu: '{{ $reg->user->hasMedia('kartu_pelajar') ? $reg->user->getFirstMediaUrl('kartu_pelajar') : '' }}'
                                            }; docModal = true" 
                                            class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Cek Dokumen
                                        </button>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center">
                                        @if($reg->status_pendaftaran === 'verified')
                                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full border border-green-200">Disetujui</span>
                                        @elseif($reg->status_pendaftaran === 'pending')
                                            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full border border-yellow-200 animate-pulse">Menunggu</span>
                                        @else
                                            <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full border border-red-200">Ditolak</span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-4 px-6 text-right">
                                        @if($reg->status_pendaftaran === 'pending')
                                            <div class="flex items-center justify-end gap-2">
                                                <form action="{{ route('admin.verifikasi.update', $reg->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="verified">
                                                    <button type="submit" onclick="return confirm('Setujui pendaftaran ini?')" class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg transition font-bold text-xs shadow-sm" title="Terima">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Setujui
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.verifikasi.destroy', $reg->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak dan menghapus pendaftaran ini? Peserta harus mengulang pendaftaran dari awal.')" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition font-bold text-xs shadow-sm" title="Tolak & Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Tolak & Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <form action="{{ route('admin.verifikasi.update', $reg->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" onclick="return confirm('Kembalikan status ke Pending?')" class="text-xs font-medium text-gray-400 hover:text-gray-700 underline transition">
                                                    Batalkan Status
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        Tidak ada data pendaftaran masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6">
                {{ $registrations->links() }}
            </div>
        </div>

        <div x-show="docModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="docModal" x-transition.opacity class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="docModal = false" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="docModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100">
                    
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900" x-text="'Dokumen: ' + activeData.nama"></h3>
                            <p class="text-xs text-gray-500 font-medium mt-0.5" x-text="activeData.lomba + ' (' + activeData.harga + ')'"></p>
                        </div>
                        <button @click="docModal = false" class="text-gray-400 hover:text-gray-600 transition bg-white rounded-lg p-1.5 border border-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 flex flex-col md:flex-row gap-6 bg-gray-100">
                        
                        <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-700 mb-3 text-center uppercase tracking-wider">Kartu Pelajar</h4>
                            <div class="w-full h-64 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden">
                                <template x-if="activeData.kartu !== ''">
                                    <a :href="activeData.kartu" target="_blank" title="Klik untuk memperbesar">
                                        <img :src="activeData.kartu" class="w-full h-full object-contain hover:scale-105 transition-transform cursor-pointer">
                                    </a>
                                </template>
                                <template x-if="activeData.kartu === ''">
                                    <div class="text-center text-gray-400">
                                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <p class="text-xs font-bold">Belum Diunggah</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-700 mb-3 text-center uppercase tracking-wider">Bukti Pembayaran</h4>
                            <div class="w-full h-64 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden">
                                <template x-if="activeData.harga === 'GRATIS'">
                                    <div class="text-center text-green-500">
                                        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-sm font-bold">Kompetisi Gratis</p>
                                        <p class="text-xs text-gray-500 mt-1">Tidak memerlukan bukti transfer</p>
                                    </div>
                                </template>
                                
                                <template x-if="activeData.harga !== 'GRATIS' && activeData.bukti !== ''">
                                    <a :href="activeData.bukti" target="_blank" title="Klik untuk memperbesar">
                                        <img :src="activeData.bukti" class="w-full h-full object-contain hover:scale-105 transition-transform cursor-pointer">
                                    </a>
                                </template>

                                <template x-if="activeData.harga !== 'GRATIS' && activeData.bukti === ''">
                                    <div class="text-center text-red-400">
                                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <p class="text-xs font-bold">Belum Ada Bukti</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>