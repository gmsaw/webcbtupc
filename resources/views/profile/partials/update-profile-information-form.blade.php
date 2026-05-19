<section>
    <header>
        <h2 class="text-xl font-bold text-gray-900">
            {{ __('Informasi Akun') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Perbarui data diri, kontak, dan foto profil Anda agar mudah dikenali oleh sistem.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
        @csrf
        @method('patch')

        <div class="flex items-center gap-6 pb-6 border-b border-gray-100" x-data="imageViewer()">
            <div class="relative w-24 h-24 rounded-full overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 border-4 border-white shadow-md flex-shrink-0">
                @if($user->hasMedia('foto_profil'))
                    <img id="profile-preview" src="{{ $user->getFirstMediaUrl('foto_profil') }}" alt="Foto Profil" class="w-full h-full object-cover">
                @else
                    <div id="initial-preview" class="w-full h-full flex items-center justify-center text-3xl font-bold text-blue-600 bg-blue-50">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <img id="profile-preview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                @endif
            </div>

            <div>
                <x-input-label for="foto_profil" :value="__('Foto Profil Baru (Opsional)')" class="mb-2" />
                <input type="file" id="foto_profil" name="foto_profil" accept="image/*" @change="fileChosen"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition-colors"/>
                <x-input-error class="mt-2" :messages="$errors->get('foto_profil')" />
                <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm mt-2 text-gray-800">
                            {{ __('Alamat email Anda belum terverifikasi.') }}

                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <x-input-label for="asal_sekolah" :value="__('Asal Sekolah')" />
                <x-text-input id="asal_sekolah" name="asal_sekolah" type="text" class="mt-1 block w-full rounded-xl" :value="old('asal_sekolah', $user->asal_sekolah)" required />
                <x-input-error class="mt-2" :messages="$errors->get('asal_sekolah')" />
            </div>

            <div>
                <x-input-label for="no_wa" :value="__('Nomor WhatsApp')" />
                <x-text-input id="no_wa" name="no_wa" type="text" class="mt-1 block w-full rounded-xl" :value="old('no_wa', $user->no_wa)" required />
                <x-input-error class="mt-2" :messages="$errors->get('no_wa')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition-colors">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-medium text-green-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Tersimpan.') }}
                </p>
            @endif
        </div>
    </form>

    <script>
        function imageViewer() {
            return {
                fileChosen(event) {
                    this.fileToDataUrl(event, src => {
                        const preview = document.getElementById('profile-preview');
                        const initial = document.getElementById('initial-preview');
                        preview.src = src;
                        preview.classList.remove('hidden');
                        if(initial) initial.classList.add('hidden');
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
</section>