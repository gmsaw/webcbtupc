<x-app-layout>
    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-black text-slate-800">Papan Peringkat Akhir</h2>
                <p class="text-slate-500 font-medium mt-1">{{ $competition->nama_lomba }}</p>
            </div>
            <a href="{{ route('admin.verifikasi.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl font-bold text-sm text-slate-600 transition shadow-sm">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 border border-slate-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs tracking-wider">
                        <th class="p-5 font-black text-center w-20">Peringkat</th>
                        <th class="p-5 font-black">Nama Peserta</th>
                        <th class="p-5 font-black">Asal Sekolah</th>
                        <th class="p-5 font-black text-right pr-8">Skor Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($registrations as $index => $reg)
                        <tr class="hover:bg-slate-50/50 transition-colors {{ $index < 3 ? 'bg-amber-50/30' : '' }}">
                            
                            <td class="p-5 text-center">
                                @if($index === 0)
                                    <div class="w-10 h-10 mx-auto bg-yellow-400 text-white rounded-full flex items-center justify-center font-black text-lg shadow-lg shadow-yellow-400/40">1</div>
                                @elseif($index === 1)
                                    <div class="w-10 h-10 mx-auto bg-slate-300 text-white rounded-full flex items-center justify-center font-black text-lg shadow-lg shadow-slate-300/40">2</div>
                                @elseif($index === 2)
                                    <div class="w-10 h-10 mx-auto bg-orange-400 text-white rounded-full flex items-center justify-center font-black text-lg shadow-lg shadow-orange-400/40">3</div>
                                @else
                                    <div class="w-10 h-10 mx-auto bg-slate-100 text-slate-500 rounded-full flex items-center justify-center font-bold">{{ $index + 1 }}</div>
                                @endif
                            </td>

                            <td class="p-5">
                                <div class="font-bold text-slate-800 text-base">{{ $reg->user->name }}</div>
                                <div class="text-xs text-slate-400">{{ $reg->user->email }}</div>
                            </td>
                            <td class="p-5 text-sm font-medium text-slate-600">
                                {{ $reg->user->asal_sekolah }}
                            </td>
                            <td class="p-5 text-right pr-8">
                                <span class="text-2xl font-black {{ $index < 3 ? 'text-amber-500' : 'text-slate-700' }}">
                                    {{ $reg->nilai ?? 0 }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-slate-500 font-medium">
                                Belum ada data peserta yang terverifikasi atau menyelesaikan ujian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>