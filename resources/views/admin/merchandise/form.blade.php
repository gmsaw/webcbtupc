<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ isset($merchandise) ? 'Edit Produk' : 'Tambah Produk Baru' }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto sm:px-6 lg:px-8" x-data="{ isDigital: {{ old('is_digital', $merchandise->is_digital ?? 0) ? 'true' : 'false' }} }">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
            <form action="{{ isset($merchandise) ? route('admin.merchandise.update', $merchandise->id) : route('admin.merchandise.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if(isset($merchandise)) @method('PUT') @endif

                <div class="flex items-center gap-3 p-4 bg-indigo-50 rounded-2xl border border-indigo-100 mb-6">
                    <input type="checkbox" id="is_digital" name="is_digital" value="1" x-model="isDigital" class="rounded border-gray-300 text-indigo-600 shadow-sm w-6 h-6">
                    <div>
                        <label for="is_digital" class="font-bold text-indigo-900 cursor-pointer">Ini adalah Produk Digital (E-Book)</label>
                        <p class="text-xs text-indigo-700">Centang jika ini adalah Bank Soal / Modul PDF yang akan dibaca di dalam aplikasi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <x-input-label for="nama_produk" value="Nama Produk / Judul E-Book" />
                        <x-text-input id="nama_produk" name="nama_produk" type="text" class="mt-1 block w-full rounded-xl" required value="{{ old('nama_produk', $merchandise->nama_produk ?? '') }}" />
                    </div>

                    <div>
                        <x-input-label for="harga" value="Harga (Rp)" />
                        <x-text-input id="harga" name="harga" type="number" class="mt-1 block w-full rounded-xl" required min="0" value="{{ old('harga', isset($merchandise) ? round($merchandise->harga) : 0) }}" />
                    </div>

                    <div x-show="!isDigital">
                        <x-input-label for="link_pembelian" value="Link Pembelian (Shopee/WA) - Opsional" />
                        <x-text-input id="link_pembelian" name="link_pembelian" type="url" placeholder="https://wa.me/..." class="mt-1 block w-full rounded-xl" value="{{ old('link_pembelian', $merchandise->link_pembelian ?? '') }}" />
                    </div>
                </div>

                <div>
                    <x-input-label for="deskripsi" value="Deskripsi Singkat" />
                    <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">{{ old('deskripsi', $merchandise->deskripsi ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <div>
                        <x-input-label for="gambar_produk" value="Gambar Sampul Produk" />
                        <input type="file" id="gambar_produk" name="gambar_produk" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-200 rounded-xl bg-white cursor-pointer">
                    </div>

                    <div x-show="isDigital" x-collapse>
                        <x-input-label for="ebook_file" value="Upload File E-Book (PDF) *" class="text-red-600" />
                        <input type="file" id="ebook_file" name="ebook_file" accept=".pdf" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 border border-red-200 rounded-xl bg-white cursor-pointer">
                        @if(isset($merchandise) && $merchandise->hasMedia('ebook_file'))
                            <p class="text-xs text-green-600 mt-2 font-bold">✓ File PDF saat ini sudah tersimpan di brankas server.</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $merchandise->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm w-5 h-5">
                    <label for="is_active" class="font-medium text-gray-700">Tampilkan produk ini di Etalase Peserta</label>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-md transition-colors">
                        {{ isset($merchandise) ? 'Simpan Perubahan' : 'Upload & Simpan Produk' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>