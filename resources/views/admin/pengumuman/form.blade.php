<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ isset($announcement) ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
            <form action="{{ isset($announcement) ? route('admin.pengumuman.update', $announcement->id) : route('admin.pengumuman.store') }}" method="POST" class="space-y-6">
                @csrf
                @if(isset($announcement)) @method('PUT') @endif

                <div>
                    <x-input-label for="judul" value="Judul Pengumuman" />
                    <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full rounded-xl" required value="{{ old('judul', $announcement->judul ?? '') }}" autofocus />
                </div>

                <div>
                    <x-input-label for="isi" value="Isi Pesan (Informasi lengkap)" />
                    <textarea id="isi" name="isi" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" required>{{ old('isi', $announcement->isi ?? '') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $announcement->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-yellow-600 shadow-sm w-5 h-5">
                    <label for="is_active" class="font-medium text-gray-700">Tampilkan pengumuman ini di Dashboard Peserta</label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-xl font-bold shadow-md transition-colors">
                        {{ isset($announcement) ? 'Simpan Perubahan' : 'Siarkan Pengumuman' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>