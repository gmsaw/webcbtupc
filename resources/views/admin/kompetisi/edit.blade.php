<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.kompetisi.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Kompetisi: ') }} <span class="text-indigo-600">{{ $competition->nama_lomba }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                
                <form action="{{ route('admin.kompetisi.update', $competition->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="nama_lomba" value="Nama Kompetisi" />
                                <x-text-input id="nama_lomba" name="nama_lomba" type="text" class="mt-1 block w-full rounded-xl" required value="{{ old('nama_lomba', $competition->nama_lomba) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('nama_lomba')" />
                            </div>

                            <div>
                                <x-input-label for="deskripsi" value="Deskripsi Singkat" />
                                <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">{{ old('deskripsi', $competition->deskripsi) }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="harga_pendaftaran" value="Biaya Pendaftaran (Rp) - Ketik 0 jika gratis" />
                                <x-text-input id="harga_pendaftaran" name="harga_pendaftaran" type="number" class="mt-1 block w-full rounded-xl" required min="0" value="{{ old('harga_pendaftaran', round($competition->harga_pendaftaran)) }}" />
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-50">
                                <div>
                                    <x-input-label for="tanggal_mulai" value="Tgl Buka Daftar" />
                                    <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full rounded-xl text-sm" required value="{{ old('tanggal_mulai', $competition->tanggal_mulai?->format('Y-m-d')) }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('tanggal_mulai')" />
                                </div>
                                <div>
                                    <x-input-label for="tanggal_selesai" value="Tgl Tutup Daftar" />
                                    <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full rounded-xl text-sm" required value="{{ old('tanggal_selesai', $competition->tanggal_selesai?->format('Y-m-d')) }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('tanggal_selesai')" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 bg-orange-50/50 p-4 rounded-2xl border border-orange-50">
                                <div>
                                    <x-input-label for="waktu_pelaksanaan" value="Waktu Pelaksanaan Lomba" />
                                    <x-text-input id="waktu_pelaksanaan" name="waktu_pelaksanaan" type="datetime-local" class="mt-1 block w-full rounded-xl text-sm" required value="{{ old('waktu_pelaksanaan', isset($competition) && $competition->waktu_pelaksanaan ? $competition->waktu_pelaksanaan->format('Y-m-d\TH:i') : '') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('waktu_pelaksanaan')" />
                                </div>
                                <div>
                                    <x-input-label for="durasi_menit" value="Durasi Pengerjaan (Menit)" />
                                    <x-text-input id="durasi_menit" name="durasi_menit" type="number" class="mt-1 block w-full rounded-xl text-sm" required min="1" value="{{ old('durasi_menit', isset($competition) ? $competition->durasi_menit : 120) }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('durasi_menit')" />
                                </div>
                            </div>

                            <div x-data="imageViewer()">
                                <x-input-label value="Gambar Banner Lomba (Opsional)" />
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl relative overflow-hidden group">
                                    
                                    <div class="absolute inset-0 z-0">
                                        @if($competition->hasMedia('gambar_lomba'))
                                            <img id="banner-preview" src="{{ $competition->getFirstMediaUrl('gambar_lomba') }}" class="w-full h-full object-cover opacity-50 group-hover:opacity-30 transition">
                                        @else
                                            <img id="banner-preview" src="" class="w-full h-full object-cover hidden">
                                        @endif
                                    </div>

                                    <div class="space-y-1 text-center relative z-10">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="gambar_lomba" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 px-2">
                                                <span>Unggah file</span>
                                                <input id="gambar_lomba" name="gambar_lomba" type="file" class="sr-only" accept="image/*" @change="fileChosen">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 font-bold bg-white/80 px-2 rounded-full inline-block">PNG, JPG up to 2MB</p>
                                    </div>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('gambar_lomba')" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ $competition->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5">
                        <label for="is_active" class="font-medium text-gray-900 text-lg">Buka Pendaftaran Lomba Sekarang (Status Aktif)</label>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition-colors transform hover:-translate-y-0.5">
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
                        preview.classList.remove('opacity-50');
                        preview.classList.add('opacity-80');
                    })
                },
                fileToDataUrl(event, callback) {
                    if (! event.target.files.length) return
                    let file = event.target.files[0],
                        reader = new FileReader()
                    reader.readAsDataURL(file)
                    reader.onload = e => callback(e.target.result)
                },
            }
        }
    </script>
</x-app-layout>