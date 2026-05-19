<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Registrasi Peserta</h1>
        <p class="mt-2 text-sm text-gray-500">Lengkapi data diri Anda di bawah ini untuk bergabung dalam kompetisi.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Nama Lengkap" class="text-gray-700" />
            <x-text-input id="name" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" class="text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <x-input-label for="asal_sekolah" value="Asal Sekolah" class="text-gray-700" />
                <x-text-input id="asal_sekolah" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" type="text" name="asal_sekolah" :value="old('asal_sekolah')" required />
                <x-input-error :messages="$errors->get('asal_sekolah')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="no_wa" value="Nomor WhatsApp" class="text-gray-700" />
                <x-text-input id="no_wa" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" type="text" name="no_wa" :value="old('no_wa')" required placeholder="08..." />
                <x-input-error :messages="$errors->get('no_wa')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="kartu_pelajar" value="Unggah Kartu Pelajar (JPG/PNG, Max 2MB)" class="text-gray-700 mb-2" />
            <div class="mt-1 flex items-center justify-center w-full">
                <input id="kartu_pelajar" type="file" name="kartu_pelajar" required accept="image/*"
                    class="block w-full text-sm text-gray-500 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-blue-500
                    file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:transition-colors file:cursor-pointer" />
            </div>
            <x-input-error :messages="$errors->get('kartu_pelajar')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
            <div>
                <x-input-label for="password" value="Password" class="text-gray-700" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Password" class="text-gray-700" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Daftar Kompetisi
            </button>
        </div>

        <p class="text-center text-sm text-gray-600 mt-6">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">Masuk di sini</a>
        </p>
    </form>
</x-guest-layout>