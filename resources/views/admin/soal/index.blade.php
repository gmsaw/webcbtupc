<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.kompetisi.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Bank Soal
                </h2>
                <p class="text-sm text-gray-500 font-medium mt-1">Lomba: {{ $competition->nama_lomba }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ addModal: false }">
        
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl shadow-sm flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Pertanyaan ({{ $questions->count() }})</h3>
                <button @click="addModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Soal Baru
                </button>
            </div>

            <div class="p-6 space-y-6">
                @forelse($questions as $index => $q)
                    <div class="border border-gray-200 rounded-2xl p-5 hover:border-blue-300 transition bg-white relative">
                        <form action="{{ route('admin.kompetisi.soal.destroy', $q->id) }}" method="POST" class="absolute top-4 right-4">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus soal ini?')" class="text-red-400 hover:text-red-600 transition bg-red-50 p-2 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>

                        <div class="flex items-start gap-4">
                            <div class="bg-blue-100 text-blue-700 w-8 h-8 flex items-center justify-center rounded-lg font-black shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                @if($q->hasMedia('gambar_soal'))
                                    <div class="mb-3 max-w-sm rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                        <img src="{{ $q->getFirstMediaUrl('gambar_soal') }}" class="w-full h-auto">
                                    </div>
                                @endif
                                
                                <p class="font-semibold text-gray-900 mb-4 whitespace-pre-wrap">{{ $q->pertanyaan }}</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div class="p-2 rounded-lg border {{ $q->jawaban_benar == 'A' ? 'bg-green-50 border-green-400 font-bold text-green-800' : 'border-gray-200 text-gray-600' }}">A. {{ $q->opsi_a }}</div>
                                    <div class="p-2 rounded-lg border {{ $q->jawaban_benar == 'B' ? 'bg-green-50 border-green-400 font-bold text-green-800' : 'border-gray-200 text-gray-600' }}">B. {{ $q->opsi_b }}</div>
                                    <div class="p-2 rounded-lg border {{ $q->jawaban_benar == 'C' ? 'bg-green-50 border-green-400 font-bold text-green-800' : 'border-gray-200 text-gray-600' }}">C. {{ $q->opsi_c }}</div>
                                    <div class="p-2 rounded-lg border {{ $q->jawaban_benar == 'D' ? 'bg-green-50 border-green-400 font-bold text-green-800' : 'border-gray-200 text-gray-600' }}">D. {{ $q->opsi_d }}</div>
                                    @if($q->opsi_e)
                                        <div class="p-2 rounded-lg border {{ $q->jawaban_benar == 'E' ? 'bg-green-50 border-green-400 font-bold text-green-800' : 'border-gray-200 text-gray-600' }}">E. {{ $q->opsi_e }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <p>Belum ada soal untuk kompetisi ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div x-show="addModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center px-4 pt-4 pb-20 sm:p-0">
            <div x-show="addModal" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="addModal = false"></div>
            
            <div x-show="addModal" class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-3xl border border-gray-100 z-10">
                <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-lg font-bold">Input Soal Baru</h3>
                    <button @click="addModal = false" class="text-blue-200 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <form action="{{ route('admin.kompetisi.soal.store', $competition->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                            <textarea name="pertanyaan" rows="3" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 shadow-sm"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Gambar Pendukung (Opsional)</label>
                            <input type="file" name="gambar_soal" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-200 rounded-xl cursor-pointer">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-200">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Opsi A *</label>
                                <input type="text" name="opsi_a" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Opsi B *</label>
                                <input type="text" name="opsi_b" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Opsi C *</label>
                                <input type="text" name="opsi_c" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Opsi D *</label>
                                <input type="text" name="opsi_d" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 mb-1">Opsi E (Opsional)</label>
                                <input type="text" name="opsi_e" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Jawaban Benar <span class="text-red-500">*</span></label>
                                <select name="jawaban_benar" required class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 shadow-sm font-bold text-green-700">
                                    <option value="A">Opsi A</option>
                                    <option value="B">Opsi B</option>
                                    <option value="C">Opsi C</option>
                                    <option value="D">Opsi D</option>
                                    <option value="E">Opsi E</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Bobot Nilai</label>
                                <input type="number" name="bobot_nilai" value="1" min="1" required class="w-full rounded-xl border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                        <button type="button" @click="addModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition">Simpan Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>