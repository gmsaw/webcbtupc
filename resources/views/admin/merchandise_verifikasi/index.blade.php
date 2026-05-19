<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">Verifikasi Transaksi Merchandise</h2>
    </x-slot>

    <div class="py-10" x-data="{ docModal: false, activeData: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-200 font-bold">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-3xl border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 border-b border-gray-100 uppercase text-xs">
                        <tr>
                            <th class="py-4 px-6">Pembeli & Produk</th>
                            <th class="py-4 px-6 text-center">Nominal</th>
                            <th class="py-4 px-6 text-center">Bukti Bayar</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($transactions as $trx)
                            <tr class="hover:bg-orange-50/30 {{ $trx->status === 'pending' ? 'bg-yellow-50/10' : '' }}">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-bold text-gray-900">{{ $trx->user->name }}</div>
                                    <div class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-xs font-bold mt-1">
                                        {{ $trx->merchandise->nama_produk }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1">{{ $trx->created_at->format('d M Y, H:i') }} WITA</div>
                                </td>
                                
                                <td class="py-4 px-6 text-center font-black text-indigo-600">
                                    Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </td>
                                
                                <td class="py-4 px-6 text-center">
                                    @if($trx->nominal > 0)
                                        <button @click="activeData = { nama: '{{ addslashes($trx->user->name) }}', produk: '{{ addslashes($trx->merchandise->nama_produk) }}', bukti: '{{ $trx->hasMedia('bukti_pembayaran_merch') ? $trx->getFirstMediaUrl('bukti_pembayaran_merch') : '' }}' }; docModal = true" class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            Cek Bukti
                                        </button>
                                    @else
                                        <span class="text-xs text-green-500 font-bold">Gratis (Tanpa Bukti)</span>
                                    @endif
                                </td>
                                
                                <td class="py-4 px-6 text-center">
                                    @if($trx->status === 'paid')
                                        <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">LUNAS</span>
                                    @elseif($trx->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full animate-pulse">Menunggu</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">Ditolak</span>
                                    @endif
                                </td>
                                
                                <td class="py-4 px-6 text-right">
                                    @if($trx->status === 'pending')
                                        <form action="{{ route('admin.merchandise.verifikasi.update', $trx->id) }}" method="POST" class="inline">
                                            @csrf @method('PUT') <input type="hidden" name="status" value="paid">
                                            <button type="submit" onclick="return confirm('Setujui pembayaran ini?')" class="p-2 bg-green-50 text-green-600 hover:bg-green-500 hover:text-white rounded-lg transition" title="Terima">✓</button>
                                        </form>
                                        <form action="{{ route('admin.merchandise.verifikasi.update', $trx->id) }}" method="POST" class="inline ml-1">
                                            @csrf @method('PUT') <input type="hidden" name="status" value="rejected">
                                            <button type="submit" onclick="return confirm('Tolak pembayaran ini?')" class="p-2 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white rounded-lg transition" title="Tolak">✗</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.merchandise.verifikasi.update', $trx->id) }}" method="POST" class="inline">
                                            @csrf @method('PUT') <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-gray-700 underline">Batalkan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-gray-500">Tidak ada transaksi masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>

        <div x-show="docModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" @click="docModal = false"></div>
            <div class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl z-10 w-full max-w-lg">
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b">
                    <h3 class="font-bold text-gray-900">Bukti Transfer <span x-text="activeData.nama"></span></h3>
                    <button @click="docModal = false" class="text-gray-400 hover:text-gray-900 text-2xl font-bold">&times;</button>
                </div>
                <div class="p-6 bg-gray-100">
                    <template x-if="activeData.bukti !== ''">
                        <img :src="activeData.bukti" class="w-full rounded-xl border border-gray-200 shadow-sm">
                    </template>
                    <template x-if="activeData.bukti === ''">
                        <div class="text-center py-10 text-red-500 font-bold">Bukti tidak dilampirkan.</div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>