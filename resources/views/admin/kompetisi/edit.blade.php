<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.kompetisi.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Kompetisi: ') }} <span class="text-indigo-600">{{ $competition->nama_lomba }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                
                <form action="{{ route('admin.kompetisi.update', $competition->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="nama_lomba" value="Nama Kompetisi" />
                                <x-text-input id="nama_lomba" name="nama_lomba" type="text" class="mt-1 block w-full rounded-xl" required value="{{ old('nama_lomba', $competition->nama_lomba) }}" />
                            </div>

                            <div>
                                <x-input-label for="deskripsi" value="Deskripsi Singkat" />
                                <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">{{ old('deskripsi', $competition->deskripsi) }}</textarea>
                            </div>

                            <div x-data="{ 
                                isUsingWaves: {{ $competition->is_using_waves ? 'true' : 'false' }},
                                waves: {{ json_encode($competition->waves->map(function($w) {
                                    return [
                                        'name' => $w->nama_gelombang, 
                                        'start' => $w->start_date->format('Y-m-d\TH:i'), 
                                        'end' => $w->end_date->format('Y-m-d\TH:i'), 
                                        'price' => $w->biaya
                                    ];
                                })) }}
                            }" class="p-5 bg-slate-50 rounded-2xl border border-slate-200">
                                
                                <div class="flex items-center justify-between mb-5">
                                    <h3 class="text-sm font-bold text-gray-800">Sistem Biaya Pendaftaran</h3>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_using_waves" value="1" x-model="isUsingWaves" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                        <span class="ml-2 text-xs font-bold text-gray-600">Banyak Gelombang</span>
                                    </label>
                                </div>

                                <div x-show="!isUsingWaves" x-transition>
                                    <x-input-label for="biaya_pendaftaran" value="Biaya Pendaftaran (Rp)" />
                                    <x-text-input name="biaya_pendaftaran" type="number" class="mt-1 block w-full rounded-xl" min="0" value="{{ old('biaya_pendaftaran', $competition->biaya_pendaftaran) }}" />
                                </div>

                                <div x-show="isUsingWaves" x-transition style="display: none;" class="space-y-4">
                                    <template x-for="(wave, index) in waves" :key="index">
                                        <div class="flex flex-col gap-3 p-4 bg-white border border-indigo-100 rounded-xl shadow-sm relative">
                                            <button type="button" @click="waves.splice(index, 1)" x-show="waves.length > 1" class="absolute -top-2 -right-2 w-7 h-7 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-full flex items-center justify-center transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <input type="text" x-bind:name="`waves[${index}][nama_gelombang]`" x-model="wave.name" placeholder="Nama Gelombang" class="w-full rounded-lg border-gray-300 text-sm py-1.5" required>
                                            <div class="grid grid-cols-2 gap-2">
                                                <input type="datetime-local" x-bind:name="`waves[${index}][start_date]`" x-model="wave.start" class="rounded-lg border-gray-300 text-xs py-1.5" required>
                                                <input type="datetime-local" x-bind:name="`waves[${index}][end_date]`" x-model="wave.end" class="rounded-lg border-gray-300 text-xs py-1.5" required>
                                            </div>
                                            <input type="number" x-bind:name="`waves[${index}][biaya]`" x-model="wave.price" placeholder="Biaya (Rp)" class="w-full rounded-lg border-gray-300 text-sm py-1.5" required>
                                        </div>
                                    </template>
                                    <button type="button" @click="waves.push({ name: '', start: '', end: '', price: '' })" class="w-full py-2 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold">Tambah Gelombang</button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-50">
                                <div>
                                    <x-input-label value="Tgl Buka Daftar" />
                                    <x-text-input name="tanggal_mulai" type="date" class="mt-1 block w-full rounded-xl text-sm" required value="{{ old('tanggal_mulai', $competition->tanggal_mulai?->format('Y-m-d')) }}" />
                                </div>
                                <div>
                                    <x-input-label value="Tgl Tutup Daftar" />
                                    <x-text-input name="tanggal_selesai" type="date" class="mt-1 block w-full rounded-xl text-sm" required value="{{ old('tanggal_selesai', $competition->tanggal_selesai?->format('Y-m-d')) }}" />
                                </div>
                            </div>

                            <div x-data="imageViewer()">
                                <x-input-label value="Gambar Banner Lomba" />
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl relative overflow-hidden group">
                                    <div class="absolute inset-0 z-0 bg-gray-100">
                                        <img id="banner-preview" src="{{ $competition->getFirstMediaUrl('gambar_lomba') ?: '' }}" class="w-full h-full object-cover {{ $competition->hasMedia('gambar_lomba') ? '' : 'hidden' }}">
                                    </div>
                                    <div class="space-y-1 text-center relative z-10 bg-white/70 px-4 py-3 rounded-xl backdrop-blur-sm">
                                        <label for="gambar_lomba" class="cursor-pointer bg-white rounded-lg font-bold text-indigo-600 px-3 py-1 shadow-sm border border-indigo-100">
                                            <span>Pilih Gambar Baru</span>
                                            <input id="gambar_lomba" name="gambar_lomba" type="file" class="sr-only" accept="image/*" @change="fileChosen">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function imageViewer() {
            return {
                fileChosen(event) {
                    this.fileToDataUrl(event, src => {
                        const preview = document.getElementById('banner-preview');
                        preview.src = src;
                        preview.classList.remove('hidden');
                    })
                },
                fileToDataUrl(event, callback) {
                    if (! event.target.files.length) return
                    let file = event.target.files[0], reader = new FileReader()
                    reader.readAsDataURL(file)
                    reader.onload = e => callback(e.target.result)
                },
            }
        }
    </script>
</x-app-layout>