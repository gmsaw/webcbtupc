<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.peserta.index') }}" class="text-gray-500 hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Data Peserta') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">
                
                <form action="{{ route('admin.peserta.update', $user->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nama Lengkap" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" :value="old('name', $user->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Alamat Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl bg-gray-50" :value="old('email', $user->email)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="asal_sekolah" value="Asal Sekolah" />
                            <x-text-input id="asal_sekolah" name="asal_sekolah" type="text" class="mt-1 block w-full rounded-xl" :value="old('asal_sekolah', $user->asal_sekolah)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('asal_sekolah')" />
                        </div>

                        <div>
                            <x-input-label for="no_wa" value="Nomor WhatsApp" />
                            <x-text-input id="no_wa" name="no_wa" type="text" class="mt-1 block w-full rounded-xl" :value="old('no_wa', $user->no_wa)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('no_wa')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status_verifikasi" value="Status Verifikasi" />
                        <select id="status_verifikasi" name="status_verifikasi" class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                            <option value="pending" {{ $user->status_verifikasi == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi (Pending)</option>
                            <option value="verified" {{ $user->status_verifikasi == 'verified' ? 'selected' : '' }}>Terverifikasi (Verified)</option>
                            <option value="rejected" {{ $user->status_verifikasi == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status_verifikasi')" />
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>