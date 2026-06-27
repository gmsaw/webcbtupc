<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Registrasi Peserta</h1>
        <p class="mt-2 text-sm text-gray-500">Lengkapi data diri Anda di bawah ini untuk bergabung dalam kompetisi.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Foto Profil --}}
        <div x-data="avatarViewer()" class="mb-2">
            <x-input-label value="Foto Profil / Pas Foto" class="text-gray-700" />
            
            <div class="mt-2 flex items-center gap-4">
                <div class="h-20 w-20 rounded-full bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden relative shadow-sm shrink-0">
                    <img id="avatar-preview" src="" class="h-full w-full object-cover hidden">
                    <svg id="avatar-placeholder" class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                
                <div class="flex-1">
                    <label for="foto_profil" class="cursor-pointer inline-block bg-white py-2 px-4 border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                        Pilih Foto
                        <input id="foto_profil" name="foto_profil" type="file" class="sr-only" accept="image/png, image/jpeg, image/jpg, image/webp" @change="fileChosen" required>
                    </label>
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">Format: JPG, PNG, WEBP (Maks: 5MB).</p>
                </div>
            </div>
            <x-input-error :messages="$errors->get('foto_profil')" class="mt-2" />
        </div>

        {{-- Nama Lengkap --}}
        <div>
            <x-input-label for="name" value="Nama Lengkap" class="text-gray-700" />
            <x-text-input id="name" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Alamat Email" class="text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Asal Sekolah & No WA --}}
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

        {{-- Password --}}
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

        {{-- Tombol Submit --}}
        <div class="pt-4">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Daftar Kompetisi
            </button>
        </div>

        {{-- Link Login --}}
        <p class="text-center text-sm text-gray-600 mt-6">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">Masuk di sini</a>
        </p>
    </form>

    {{-- Script Avatar Viewer --}}
    <script>
        function avatarViewer() {
            return {
                fileChosen(event) {
                    if (!event.target.files.length) return;
                    let file = event.target.files[0];
                    let reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = e => {
                        const preview = document.getElementById('avatar-preview');
                        const placeholder = document.getElementById('avatar-placeholder');
                        if (preview && placeholder) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            placeholder.classList.add('hidden');
                        }
                    };
                }
            }
        }
    </script>
</x-guest-layout>