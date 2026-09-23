<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.verifikasi.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm" title="Kembali ke Daftar Lomba">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                {{ __('Verifikasi: ') }} {{ $competition->nama_lomba }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ docModal: false, activeData: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── FLASH MESSAGE ── --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl shadow-sm flex items-center justify-between transition-opacity">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-2xl shadow-sm flex items-center justify-between transition-opacity">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- ── STATISTIK ── --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Peserta</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $registrations->total() }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                    <p class="text-xs text-gray-600 font-medium uppercase tracking-wider">Penyisihan</p>
                    <p class="text-2xl font-bold text-gray-700">{{ $registrations->where('babak', 'penyisihan')->count() }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                    <p class="text-xs text-blue-700 font-medium uppercase tracking-wider">Semifinal</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $registrations->where('babak', 'semifinal')->count() }}</p>
                </div>
                <div class="bg-purple-50 rounded-xl border border-purple-200 p-4">
                    <p class="text-xs text-purple-700 font-medium uppercase tracking-wider">Final</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $registrations->where('babak', 'final')->count() }}</p>
                </div>
                <div class="bg-green-50 rounded-xl border border-green-200 p-4">
                    <p class="text-xs text-green-700 font-medium uppercase tracking-wider">Verified</p>
                    <p class="text-2xl font-bold text-green-700">{{ $registrations->where('status_pendaftaran', 'verified')->count() }}</p>
                </div>
            </div>

            {{-- ── LEGENDA WARNA ── --}}
            <div class="mb-4 flex flex-wrap items-center gap-4 text-xs font-bold text-gray-600 bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <span class="text-gray-500 uppercase tracking-wider">Legenda Babak:</span>
                <span class="inline-flex items-center gap-2">
                    <span class="w-4 h-4 rounded-md bg-gray-200 border border-gray-300"></span>
                    Penyisihan
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="w-4 h-4 rounded-md bg-blue-200 border border-blue-300"></span>
                    Semifinal
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="w-4 h-4 rounded-md bg-purple-200 border border-purple-300"></span>
                    Final
                </span>
            </div>

            {{-- ── TABEL ── --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="py-4 px-6">Peserta & Lomba</th>
                                <th scope="col" class="py-4 px-6 text-center">Waktu Daftar</th>
                                <th scope="col" class="py-4 px-6 text-center">Babak</th>
                                <th scope="col" class="py-4 px-6 text-center">Dokumen & Pembayaran</th>
                                <th scope="col" class="py-4 px-6 text-center">Status</th>
                                <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($registrations as $reg)
                                @php
                                    $totalAmount = $reg->payment->amount ?? 0;
                                    $paymentType = $reg->payment->payment_type ?? 'gateway';
                                    $buktiTf = $reg->hasMedia('bukti_pembayaran_lomba')
                                        ? $reg->getFirstMediaUrl('bukti_pembayaran_lomba')
                                        : '';

                                    // ── WARNA BARIS BERDASARKAN BABAK ──
                                    $rowClass = match ($reg->babak ?? 'penyisihan') {
                                        'final'      => 'bg-purple-50/40 hover:bg-purple-100/60 border-l-4 border-l-purple-400',
                                        'semifinal'  => 'bg-blue-50/40 hover:bg-blue-100/60 border-l-4 border-l-blue-400',
                                        default      => 'bg-gray-50/40 hover:bg-gray-100/60 border-l-4 border-l-gray-300',
                                    };
                                @endphp

                                <tr class="transition-colors {{ $rowClass }}">

                                    {{-- ── PESERTA & LOMBA ── --}}
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-cyan-100 text-blue-700 flex items-center justify-center font-bold text-lg shadow-sm border border-white shrink-0 overflow-hidden">
                                                @if($reg->user->hasMedia('foto_profil'))
                                                    <img src="{{ $reg->user->getFirstMediaUrl('foto_profil') }}" class="w-full h-full object-cover" alt="Foto {{ $reg->user->name }}">
                                                @else
                                                    <span class="text-sm font-bold">{{ substr($reg->user->name, 0, 1) }}</span>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="font-bold text-gray-900 text-base truncate">{{ $reg->user->name }}</div>

                                                <div class="flex flex-wrap items-center gap-1 mt-0.5">
                                                    <div class="text-xs text-gray-500 flex items-center gap-1">
                                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                        <span class="truncate max-w-[120px]">{{ $reg->user->asal_sekolah ?? 'Tidak ada asal sekolah' }}</span>
                                                    </div>

                                                    <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-xs font-bold border border-indigo-100 truncate max-w-[150px]">
                                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                        </svg>
                                                        {{ $reg->competition->nama_lomba }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- ── WAKTU DAFTAR ── --}}
                                    <td class="py-4 px-6 text-center font-medium">
                                        {{ $reg->created_at->format('d M Y') }}<br>
                                        <span class="text-xs text-gray-400">{{ $reg->created_at->format('H:i') }} WITA</span>
                                    </td>

                                    {{-- ── BABAK (HANYA DROPDOWN) ── --}}
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('admin.verifikasi.updateBabak', $reg->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PUT')
                                            <select name="babak"
                                                    onchange="this.form.submit()"
                                                    class="text-xs font-bold rounded-lg border-2 px-3 py-2 cursor-pointer transition shadow-sm
                                                           {{ $reg->babak === 'final'
                                                               ? 'bg-purple-50 border-purple-300 text-purple-700'
                                                               : ($reg->babak === 'semifinal'
                                                                   ? 'bg-blue-50 border-blue-300 text-blue-700'
                                                                   : 'bg-gray-50 border-gray-300 text-gray-700') }}">
                                                <option value="penyisihan" {{ $reg->babak === 'penyisihan' ? 'selected' : '' }}>
                                                    Penyisihan
                                                </option>
                                                <option value="semifinal" {{ $reg->babak === 'semifinal' ? 'selected' : '' }}>
                                                    Semifinal
                                                </option>
                                                <option value="final" {{ $reg->babak === 'final' ? 'selected' : '' }}>
                                                    Final
                                                </option>
                                            </select>
                                        </form>
                                    </td>

                                    {{-- ── DOKUMEN & PEMBAYARAN ── --}}
                                    <td class="py-4 px-6 text-center">
                                        <button type="button"
                                            @click="activeData = {
                                                nama: '{{ addslashes($reg->user->name) }}',
                                                lomba: '{{ addslashes($reg->competition->nama_lomba) }}',
                                                babak: '{{ $reg->babak ?? 'penyisihan' }}',
                                                harga: '{{ $totalAmount == 0 ? 'GRATIS' : 'Rp ' . number_format($totalAmount, 0, ',', '.') }}',
                                                order_id: '{{ $reg->payment->order_id ?? 'Tidak Ada' }}',
                                                payment_type: '{{ $paymentType }}',
                                                status: '{{ $reg->status_pendaftaran }}',
                                                kartu: '{{ $reg->user->hasMedia('kartu_pelajar') ? $reg->user->getFirstMediaUrl('kartu_pelajar') : '' }}',
                                                bukti_tf: '{{ $buktiTf }}'
                                            }; docModal = true"
                                            class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Cek Detail
                                        </button>
                                    </td>

                                    {{-- ── STATUS ── --}}
                                    <td class="py-4 px-6 text-center">
                                        @if($reg->status_pendaftaran === 'verified')
                                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full border border-green-200">Disetujui</span>
                                        @elseif($reg->status_pendaftaran === 'pending')
                                            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full border border-yellow-200 animate-pulse">Menunggu</span>
                                        @else
                                            <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full border border-red-200">Ditolak</span>
                                        @endif
                                    </td>

                                    {{-- ── AKSI ── --}}
                                    <td class="py-4 px-6 text-right">
                                        @if($reg->status_pendaftaran === 'pending')
                                            <div class="flex items-center justify-end gap-2">
                                                <form action="{{ route('admin.verifikasi.update', $reg->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="verified">
                                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin Menerima/Menyetujui pendaftaran ini?')" class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg transition font-bold text-xs shadow-sm" title="Terima">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Setujui
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.verifikasi.destroy', $reg->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus pendaftaran ini?')" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition font-bold text-xs shadow-sm" title="Tolak & Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Tolak & Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-end gap-2">
                                                <form action="{{ route('admin.verifikasi.update', $reg->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" onclick="return confirm('Kembalikan status ke Pending?')" class="text-xs font-medium text-gray-400 hover:text-gray-700 underline transition">
                                                        Batalkan Status
                                                    </button>
                                                </form>

                                                @if($reg->status_pendaftaran === 'rejected')
                                                    <form action="{{ route('admin.verifikasi.destroy', $reg->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus pendaftaran ini?')" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition font-bold text-xs shadow-sm" title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="font-medium">Belum ada peserta yang mendaftar</p>
                                        <p class="text-sm text-gray-400 mt-1">Silakan cek lomba lain atau tunggu peserta mendaftar</p>
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

        {{-- ── MODAL DETAIL ── --}}
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
                            <h3 class="text-lg font-bold text-gray-900" x-text="'Detail: ' + activeData.nama"></h3>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                <span x-text="activeData.lomba"></span> •
                                <span x-text="activeData.harga"></span> •
                                <span class="font-bold text-blue-600" x-text="'Babak: ' + activeData.babak.charAt(0).toUpperCase() + activeData.babak.slice(1)"></span>
                            </p>
                        </div>
                        <button @click="docModal = false" class="text-gray-400 hover:text-gray-600 transition bg-white rounded-lg p-1.5 border border-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 flex flex-col md:flex-row gap-6 bg-gray-100">

                        {{-- Kartu Pelajar --}}
                        <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col">
                            <h4 class="text-sm font-bold text-gray-700 mb-3 text-center uppercase tracking-wider">Kartu Pelajar</h4>
                            <div class="w-full flex-1 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden">
                                <template x-if="activeData.kartu !== ''">
                                    <a :href="activeData.kartu" target="_blank" title="Klik untuk memperbesar">
                                        <img :src="activeData.kartu" class="w-full max-h-56 object-contain hover:scale-105 transition-transform cursor-pointer">
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

                        {{-- Status Pembayaran --}}
                        <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col">
                            <h4 class="text-sm font-bold text-gray-700 mb-3 text-center uppercase tracking-wider">Status Pembayaran</h4>
                            <div class="w-full flex-1 min-h-[224px] bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center p-4 text-center overflow-hidden">

                                <template x-if="activeData.harga === 'GRATIS'">
                                    <div class="text-green-500 my-auto">
                                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-base font-bold">Kompetisi Gratis</p>
                                        <p class="text-xs text-gray-500 mt-1">Tidak memerlukan pembayaran tagihan.</p>
                                    </div>
                                </template>

                                <template x-if="activeData.harga !== 'GRATIS'">
                                    <div class="w-full h-full flex flex-col">

                                        <template x-if="activeData.payment_type === 'gateway'">
                                            <div class="mb-4 my-auto">
                                                <svg class="w-12 h-12 mx-auto mb-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                <p class="text-sm font-bold text-gray-900 uppercase tracking-wider">Payment Gateway</p>
                                                <div class="mt-2 bg-white border border-gray-200 rounded-lg py-1 px-3 inline-block">
                                                    <p class="text-xs font-mono text-gray-600" x-text="activeData.order_id"></p>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="activeData.payment_type === 'manual'">
                                            <div class="flex-1 flex flex-col justify-center items-center h-full mb-3 relative">
                                                <p class="text-[10px] text-gray-400 mb-2 font-bold tracking-widest uppercase">Bukti Transfer Manual</p>
                                                <template x-if="activeData.bukti_tf !== ''">
                                                    <a :href="activeData.bukti_tf" target="_blank" title="Klik untuk memperbesar gambar">
                                                        <img :src="activeData.bukti_tf" class="max-h-32 object-contain hover:scale-105 transition-transform cursor-pointer rounded shadow-sm border border-gray-200 bg-white">
                                                    </a>
                                                </template>
                                                <template x-if="activeData.bukti_tf === ''">
                                                    <div class="text-gray-400 my-4">
                                                        <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                        <p class="text-[10px] font-bold uppercase tracking-wider">Belum Upload Bukti</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <div class="w-full rounded-xl py-2.5 border mt-auto" :class="activeData.status === 'verified' ? 'bg-green-50 border-green-200 text-green-700' : (activeData.status === 'pending' ? 'bg-yellow-50 border-yellow-200 text-yellow-700' : 'bg-red-50 border-red-200 text-red-700')">
                                            <p class="text-[11px] font-black tracking-wider uppercase" x-text="activeData.status === 'verified' ? 'LUNAS DISETUJUI' : (activeData.status === 'pending' ? (activeData.payment_type === 'manual' ? 'Cek Bukti & Setujui' : 'Menunggu Midtrans') : 'DIBATALKAN / DITOLAK')"></p>
                                        </div>
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