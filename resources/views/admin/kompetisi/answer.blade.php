<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.verifikasi.show', $competition->id) }}"
                   class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        Jawaban Mentah — {{ $registration->user->name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $competition->nama_lomba }} • {{ $registration->user->email }}
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.kompetisi.peserta.jawaban.export', [$competition->id, $registration->id]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Statistik --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium">Skor Akhir</p>
                <p class="text-2xl font-black text-indigo-600">{{ $skor }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium">Total Soal</p>
                <p class="text-2xl font-black text-gray-800">{{ $totalSoal }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium">Benar</p>
                <p class="text-2xl font-black text-emerald-600">{{ $totalBenar }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium">Salah</p>
                <p class="text-2xl font-black text-red-600">{{ $totalSalah }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium">Kosong</p>
                <p class="text-2xl font-black text-gray-500">{{ $totalKosong }}</p>
            </div>
        </div>

        {{-- Tabel Jawaban --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Question ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pertanyaan</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Kunci</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Jawaban Peserta</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($rows as $row)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-700 font-medium">{{ $row['no'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $row['question_id'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 max-w-md">
                                    <div class="line-clamp-3">{!! $row['pertanyaan'] !!}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm">
                                        {{ $row['jawaban_benar'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($row['jawaban_peserta'])
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                            {{ $row['is_correct'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $row['jawaban_peserta'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 font-medium text-sm">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($row['status'] === 'Benar')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            Benar
                                        </span>
                                    @elseif($row['status'] === 'Salah')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Salah
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
                                            Kosong
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                    Belum ada soal untuk lomba ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>