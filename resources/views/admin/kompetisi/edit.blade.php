<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.kompetisi.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Kompetisi: ') }} <span class="text-indigo-600">{{ $competition->nama_lomba }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                
                {{-- Error Alerts --}}
                @if ($errors->any() || session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">Terjadi kesalahan:</h3>
                                <ul class="mt-1 text-sm text-red-700 list-disc pl-5 space-y-1">
                                    @if(session('error'))
                                        <li>{{ session('error') }}</li>
                                    @else
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.kompetisi.update', $competition->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="competitionForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- Kolom Kiri --}}
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="nama_lomba" value="Nama Kompetisi" />
                                <x-text-input id="nama_lomba" name="nama_lomba" type="text" class="mt-1 block w-full rounded-xl @error('nama_lomba') border-red-500 @enderror" required value="{{ old('nama_lomba', $competition->nama_lomba) }}" placeholder="Cth: Olimpiade Fisika SMA" autofocus />
                                @error('nama_lomba') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="deskripsi" value="Deskripsi" />
                                <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm @error('deskripsi') border-red-500 @enderror" placeholder="Deskripsi singkat kompetisi...">{{ old('deskripsi', $competition->deskripsi) }}</textarea>
                                @error('deskripsi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Sistem Biaya --}}
                            @php
                                $wavesData = $competition->waves->map(fn($w) => [
                                    'nama_gelombang' => $w->nama_gelombang,
                                    'start_date' => $w->start_date instanceof \Carbon\Carbon ? $w->start_date->format('Y-m-d\TH:i') : '',
                                    'end_date' => $w->end_date instanceof \Carbon\Carbon ? $w->end_date->format('Y-m-d\TH:i') : '',
                                    'biaya' => $w->biaya
                                ])->toArray();
                                if (empty($wavesData)) $wavesData = [['nama_gelombang' => '', 'start_date' => '', 'end_date' => '', 'biaya' => '']];
                            @endphp

                            <div x-data="{ 
                                isUsingWaves: {{ old('is_using_waves', $competition->is_using_waves) ? 'true' : 'false' }},
                                waves: {{ json_encode(old('waves', $wavesData)) }}
                            }" class="p-5 bg-slate-50 rounded-2xl border border-slate-200">
                                <div class="flex items-center justify-between mb-5">
                                    <h3 class="text-sm font-bold text-gray-800">Sistem Biaya Pendaftaran</h3>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_using_waves" value="1" x-model="isUsingWaves" class="sr-only peer" @change="if(!isUsingWaves) waves = [{ nama_gelombang: '', start_date: '', end_date: '', biaya: '' }]">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-2 text-xs font-bold text-gray-600">Banyak Gelombang</span>
                                    </label>
                                </div>

                                <div x-show="!isUsingWaves" x-transition>
                                    <x-input-label value="Biaya Pendaftaran (Rp)" />
                                    <x-text-input name="harga_pendaftaran" type="number" class="mt-1 block w-full rounded-xl @error('harga_pendaftaran') border-red-500 @enderror" min="0" value="{{ old('harga_pendaftaran', $competition->harga_pendaftaran) }}" placeholder="Ketik 0 jika gratis" />
                                    @error('harga_pendaftaran') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div x-show="isUsingWaves" x-transition style="display:none;" class="space-y-4">
                                    <template x-for="(wave, index) in waves" :key="index">
                                        <div class="flex flex-col gap-3 p-4 bg-white border border-indigo-100 rounded-xl shadow-sm relative">
                                            <button type="button" @click="waves.splice(index, 1)" x-show="waves.length > 1" class="absolute -top-2 -right-2 w-7 h-7 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-full flex items-center justify-center transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                            <div>
                                                <label class="text-[11px] font-bold text-slate-500 uppercase">Nama Gelombang</label>
                                                <input type="text" x-bind:name="'waves[' + index + '][nama_gelombang]'" x-model="wave.nama_gelombang" placeholder="Cth: Early Bird" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm py-1.5" :required="isUsingWaves">
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="text-[11px] font-bold text-slate-500 uppercase">Tgl Mulai</label>
                                                    <input type="datetime-local" x-bind:name="'waves[' + index + '][start_date]'" x-model="wave.start_date" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-xs py-1.5" :required="isUsingWaves">
                                                </div>
                                                <div>
                                                    <label class="text-[11px] font-bold text-slate-500 uppercase">Tgl Berakhir</label>
                                                    <input type="datetime-local" x-bind:name="'waves[' + index + '][end_date]'" x-model="wave.end_date" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-xs py-1.5" :required="isUsingWaves">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-[11px] font-bold text-slate-500 uppercase">Biaya (Rp)</label>
                                                <input type="number" x-bind:name="'waves[' + index + '][biaya]'" x-model="wave.biaya" placeholder="150000" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm py-1.5" :required="isUsingWaves">
                                            </div>
                                        </div>
                                    </template>
                                    <button type="button" @click="waves.push({ nama_gelombang: '', start_date: '', end_date: '', biaya: '' })" class="w-full py-2.5 bg-indigo-50 text-indigo-600 border border-indigo-200 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Tambah Gelombang
                                    </button>
                                    @error('waves') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-50">
                                <div>
                                    <x-input-label for="tanggal_mulai" value="Tgl Buka Daftar" />
                                    <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full rounded-xl text-sm @error('tanggal_mulai') border-red-500 @enderror" required value="{{ old('tanggal_mulai', $competition->tanggal_mulai instanceof \Carbon\Carbon ? $competition->tanggal_mulai->format('Y-m-d') : '') }}" />
                                    @error('tanggal_mulai') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <x-input-label for="tanggal_selesai" value="Tgl Tutup Daftar" />
                                    <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full rounded-xl text-sm @error('tanggal_selesai') border-red-500 @enderror" required value="{{ old('tanggal_selesai', $competition->tanggal_selesai instanceof \Carbon\Carbon ? $competition->tanggal_selesai->format('Y-m-d') : '') }}" />
                                    @error('tanggal_selesai') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 bg-orange-50/50 p-4 rounded-2xl border border-orange-50">
                                <div>
                                    <x-input-label for="waktu_pelaksanaan" value="Waktu Pelaksanaan" />
                                    <x-text-input id="waktu_pelaksanaan" name="waktu_pelaksanaan" type="datetime-local" class="mt-1 block w-full rounded-xl text-sm @error('waktu_pelaksanaan') border-red-500 @enderror" required value="{{ old('waktu_pelaksanaan', $competition->waktu_pelaksanaan instanceof \Carbon\Carbon ? $competition->waktu_pelaksanaan->format('Y-m-d\TH:i') : '') }}" />
                                    @error('waktu_pelaksanaan') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <x-input-label for="durasi_menit" value="Durasi (Menit)" />
                                    <x-text-input id="durasi_menit" name="durasi_menit" type="number" class="mt-1 block w-full rounded-xl text-sm @error('durasi_menit') border-red-500 @enderror" required min="1" value="{{ old('durasi_menit', $competition->durasi_menit) }}" />
                                    @error('durasi_menit') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Sistem Penilaian --}}
                            <div class="grid grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                <div class="col-span-3">
                                    <h4 class="text-sm font-bold text-slate-800">Sistem Penilaian (Skoring)</h4>
                                    <p class="text-xs text-slate-500">Atur poin per jawaban</p>
                                </div>
                                <div>
                                    <x-input-label value="Poin Benar" />
                                    <x-text-input name="nilai_benar" type="number" step="0.1" class="mt-1 block w-full text-sm rounded-xl" value="{{ old('nilai_benar', $competition->nilai_benar ?? 4) }}" required />
                                </div>
                                <div>
                                    <x-input-label value="Poin Salah" />
                                    <x-text-input name="nilai_salah" type="number" step="0.1" class="mt-1 block w-full text-sm rounded-xl" value="{{ old('nilai_salah', $competition->nilai_salah ?? -1) }}" required />
                                </div>
                                <div>
                                    <x-input-label value="Poin Kosong" />
                                    <x-text-input name="nilai_kosong" type="number" step="0.1" class="mt-1 block w-full text-sm rounded-xl" value="{{ old('nilai_kosong', $competition->nilai_kosong ?? 0) }}" required />
                                </div>
                            </div>

                            {{-- Upload Gambar --}}
                            <div x-data="imageViewer()">
                                <x-input-label value="Banner Lomba (Opsional)" />
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl relative overflow-hidden group @error('gambar_lomba') border-red-500 @enderror">
                                    <div class="absolute inset-0 z-0 bg-gray-100">
                                        <img id="banner-preview" src="{{ $competition->getFirstMediaUrl('gambar_lomba') ?: '' }}" class="w-full h-full object-cover {{ $competition->hasMedia('gambar_lomba') ? '' : 'hidden' }} transition duration-500">
                                    </div>
                                    <div class="space-y-1 text-center relative z-10 bg-white/70 px-4 py-3 rounded-xl backdrop-blur-sm group-hover:bg-white/90 transition">
                                        <svg class="mx-auto h-10 w-10 text-indigo-500 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="gambar_lomba" class="relative cursor-pointer bg-white rounded-lg font-bold text-indigo-600 hover:text-indigo-500 px-3 py-1 shadow-sm border border-indigo-100">
                                                <span>{{ $competition->hasMedia('gambar_lomba') ? 'Ganti Gambar' : 'Pilih Gambar' }}</span>
                                                <input id="gambar_lomba" name="gambar_lomba" type="file" class="sr-only" accept="image/png, image/jpeg, image/jpg, image/webp" @change="fileChosen">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 font-bold mt-2">PNG, JPG up to 2MB</p>
                                        @if($competition->hasMedia('gambar_lomba'))
                                            <p class="text-xs text-green-600 font-bold mt-1">✓ Gambar saat ini tersimpan</p>
                                        @endif
                                    </div>
                                </div>
                                @error('gambar_lomba') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $competition->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5 cursor-pointer">
                        <label for="is_active" class="font-medium text-gray-900 text-lg cursor-pointer">Buka Pendaftaran Lomba (Status Aktif)</label>
                    </div>

                    <div class="pt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.kompetisi.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-colors">Batal</a>
                        <button type="submit" id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition-colors transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span id="submitText">Simpan Perubahan</span>
                            <svg id="loadingSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script --}}
    <script>
        function imageViewer() {
            return {
                fileChosen(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = e => {
                        const preview = document.getElementById('banner-preview');
                        if (preview) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            preview.classList.add('opacity-80');
                        }
                    };
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('competitionForm');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = document.getElementById('submitBtn');
                    const text = document.getElementById('submitText');
                    const spinner = document.getElementById('loadingSpinner');
                    if (btn && text && spinner) {
                        btn.disabled = true;
                        btn.classList.add('opacity-75', 'cursor-not-allowed');
                        text.textContent = 'Menyimpan...';
                        spinner.classList.remove('hidden');
                    }
                });
            }

            @if (session('success'))
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg max-w-md animate-slide-in';
                toast.innerHTML = `
                    <div class="flex items-center gap-3">
                        <span class="font-bold">✓</span>
                        <p class="text-sm">{{ session('success') }}</p>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white hover:text-gray-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                `;
                document.body.appendChild(toast);
                setTimeout(() => { toast.remove(); }, 5000);
            @endif

            // Auto close error
            @if ($errors->any() || session('error'))
                const errorEl = document.querySelector('.bg-red-50');
                if (errorEl) {
                    setTimeout(() => { errorEl.style.display = 'none'; }, 10000);
                }
            @endif
        });
    </script>

    <style>
        .animate-slide-in {
            animation: slideInRight 0.3s ease-in-out;
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</x-app-layout>