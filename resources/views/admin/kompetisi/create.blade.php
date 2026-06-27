<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.kompetisi.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Buat Kompetisi Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                
                {{-- Alert Error Global --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">Terjadi kesalahan dalam pengisian form:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Alert Session Error --}}
                @if (session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.kompetisi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="competitionForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Kolom Kiri -->
                        <div class="space-y-6">
                            <!-- Nama Kompetisi -->
                            <div>
                                <x-input-label for="nama_lomba" value="Nama Kompetisi" />
                                <x-text-input 
                                    id="nama_lomba" 
                                    name="nama_lomba" 
                                    type="text" 
                                    class="mt-1 block w-full rounded-xl @error('nama_lomba') border-red-500 @enderror" 
                                    required 
                                    value="{{ old('nama_lomba') }}" 
                                    placeholder="Cth: Olimpiade Fisika SMA" 
                                    autofocus 
                                />
                                @error('nama_lomba')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <x-input-label for="deskripsi" value="Deskripsi Singkat" />
                                <textarea 
                                    id="deskripsi" 
                                    name="deskripsi" 
                                    rows="4" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm @error('deskripsi') border-red-500 @enderror" 
                                    placeholder="Cth: UPC 2026 - Persembahan Kabinet Arunika Swakarsa..."
                                >{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sistem Biaya Pendaftaran -->
                            @php
                                $oldWaves = old('waves', [['nama_gelombang' => '', 'start_date' => '', 'end_date' => '', 'biaya' => '']]);
                                if (!is_array($oldWaves) || empty($oldWaves)) {
                                    $oldWaves = [['nama_gelombang' => '', 'start_date' => '', 'end_date' => '', 'biaya' => '']];
                                }
                            @endphp
                            
                            <div x-data="{ 
                                isUsingWaves: {{ old('is_using_waves') ? 'true' : 'false' }},
                                waves: {{ json_encode($oldWaves) }}
                            }" class="p-5 bg-slate-50 rounded-2xl border border-slate-200">
                                
                                <div class="flex items-center justify-between mb-5">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-800">Sistem Biaya Pendaftaran</h3>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            name="is_using_waves" 
                                            value="1" 
                                            x-model="isUsingWaves" 
                                            class="sr-only peer"
                                            @change="if(!isUsingWaves) waves = [{ nama_gelombang: '', start_date: '', end_date: '', biaya: '' }]"
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-2 text-xs font-bold text-gray-600">Banyak Gelombang</span>
                                    </label>
                                </div>

                                <!-- Biaya Pendaftaran (tanpa waves) -->
                                <div x-show="!isUsingWaves" x-transition>
                                    <x-input-label for="harga_pendaftaran" value="Biaya Pendaftaran (Rp)" />
                                    <x-text-input 
                                        id="harga_pendaftaran" 
                                        name="harga_pendaftaran" 
                                        type="number" 
                                        class="mt-1 block w-full rounded-xl @error('harga_pendaftaran') border-red-500 @enderror" 
                                        min="0" 
                                        value="{{ old('harga_pendaftaran', 0) }}" 
                                        placeholder="Ketik 0 jika gratis" 
                                    />
                                    @error('harga_pendaftaran')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Waves -->
                                <div x-show="isUsingWaves" x-transition style="display: none;" class="space-y-4">
                                    <template x-for="(wave, index) in waves" :key="index">
                                        <div class="flex flex-col gap-3 p-4 bg-white border border-indigo-100 rounded-xl shadow-sm relative">
                                            
                                            <button 
                                                type="button" 
                                                @click="waves.splice(index, 1)" 
                                                x-show="waves.length > 1" 
                                                class="absolute -top-2 -right-2 w-7 h-7 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-full flex items-center justify-center transition shadow-sm"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>

                                            <div>
                                                <label class="text-[11px] font-bold text-slate-500 uppercase">Nama Gelombang</label>
                                                <input 
                                                    type="text" 
                                    x-bind:name="'waves[' + index + '][nama_gelombang]'" 
                                                    x-model="wave.nama_gelombang" 
                                                    placeholder="Cth: Early Bird" 
                                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm py-1.5"
                                                    :required="isUsingWaves"
                                                >
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="text-[11px] font-bold text-slate-500 uppercase">Tgl Mulai</label>
                                                    <input 
                                                        type="datetime-local" 
                                    x-bind:name="'waves[' + index + '][start_date]'" 
                                                        x-model="wave.start_date" 
                                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-xs py-1.5"
                                                        :required="isUsingWaves"
                                                    >
                                                </div>
                                                <div>
                                                    <label class="text-[11px] font-bold text-slate-500 uppercase">Tgl Berakhir</label>
                                                    <input 
                                                        type="datetime-local" 
                                    x-bind:name="'waves[' + index + '][end_date]'" 
                                                        x-model="wave.end_date" 
                                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-xs py-1.5"
                                                        :required="isUsingWaves"
                                                    >
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-[11px] font-bold text-slate-500 uppercase">Biaya (Rp)</label>
                                                <input 
                                                    type="number" 
                                    x-bind:name="'waves[' + index + '][biaya]'" 
                                                    x-model="wave.biaya" 
                                                    placeholder="150000" 
                                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm py-1.5"
                                                    :required="isUsingWaves"
                                                >
                                            </div>
                                        </div>
                                    </template>

                                    <button 
                                        type="button" 
                                        @click="waves.push({ nama_gelombang: '', start_date: '', end_date: '', biaya: '' })" 
                                        class="w-full py-2.5 bg-indigo-50 text-indigo-600 border border-indigo-200 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Gelombang Lomba
                                    </button>

                                    @error('waves')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <!-- Kolom Kanan -->
                        <div class="space-y-6">
                            
                            <!-- Tanggal Pendaftaran -->
                            <div class="grid grid-cols-2 gap-4 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-50">
                                <div>
                                    <x-input-label for="tanggal_mulai" value="Tgl Buka Daftar Umum" />
                                    <x-text-input 
                                        id="tanggal_mulai" 
                                        name="tanggal_mulai" 
                                        type="date" 
                                        class="mt-1 block w-full rounded-xl text-sm @error('tanggal_mulai') border-red-500 @enderror" 
                                        required 
                                        value="{{ old('tanggal_mulai', \Carbon\Carbon::today()->format('Y-m-d')) }}" 
                                    />
                                    @error('tanggal_mulai')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <x-input-label for="tanggal_selesai" value="Tgl Tutup Daftar Umum" />
                                    <x-text-input 
                                        id="tanggal_selesai" 
                                        name="tanggal_selesai" 
                                        type="date" 
                                        class="mt-1 block w-full rounded-xl text-sm @error('tanggal_selesai') border-red-500 @enderror" 
                                        required 
                                        value="{{ old('tanggal_selesai', \Carbon\Carbon::today()->addDays(30)->format('Y-m-d')) }}" 
                                    />
                                    @error('tanggal_selesai')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Waktu Pelaksanaan -->
                            <div class="grid grid-cols-2 gap-4 bg-orange-50/50 p-4 rounded-2xl border border-orange-50">
                                <div>
                                    <x-input-label for="waktu_pelaksanaan" value="Waktu Pelaksanaan Lomba" />
                                    <x-text-input 
                                        id="waktu_pelaksanaan" 
                                        name="waktu_pelaksanaan" 
                                        type="datetime-local" 
                                        class="mt-1 block w-full rounded-xl text-sm @error('waktu_pelaksanaan') border-red-500 @enderror" 
                                        required 
                                        value="{{ old('waktu_pelaksanaan') }}" 
                                    />
                                    @error('waktu_pelaksanaan')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <x-input-label for="durasi_menit" value="Durasi Pengerjaan (Menit)" />
                                    <x-text-input 
                                        id="durasi_menit" 
                                        name="durasi_menit" 
                                        type="number" 
                                        class="mt-1 block w-full rounded-xl text-sm @error('durasi_menit') border-red-500 @enderror" 
                                        required 
                                        min="1" 
                                        value="{{ old('durasi_menit', 120) }}" 
                                    />
                                    @error('durasi_menit')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Upload Gambar -->
                            <div x-data="imageViewer()">
                                <x-input-label value="Gambar Banner Lomba (Opsional)" />
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl relative overflow-hidden group @error('gambar_lomba') border-red-500 @enderror">
                                    
                                    <div class="absolute inset-0 z-0 bg-gray-100">
                                        <img id="banner-preview" src="" class="w-full h-full object-cover hidden transition duration-500">
                                    </div>

                                    <div class="space-y-1 text-center relative z-10 bg-white/70 px-4 py-3 rounded-xl backdrop-blur-sm group-hover:bg-white/90 transition">
                                        <svg class="mx-auto h-10 w-10 text-indigo-500 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="gambar_lomba" class="relative cursor-pointer bg-white rounded-lg font-bold text-indigo-600 hover:text-indigo-500 px-3 py-1 shadow-sm border border-indigo-100">
                                                <span>Pilih Gambar</span>
                                                <input 
                                                    id="gambar_lomba" 
                                                    name="gambar_lomba" 
                                                    type="file" 
                                                    class="sr-only" 
                                                    accept="image/png, image/jpeg, image/jpg, image/webp" 
                                                    @change="fileChosen"
                                                >
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 font-bold mt-2">PNG, JPG up to 2MB</p>
                                    </div>
                                </div>
                                @error('gambar_lomba')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status Aktif -->
                    <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                        <input 
                            type="checkbox" 
                            id="is_active" 
                            name="is_active" 
                            value="1" 
                            {{ old('is_active', true) ? 'checked' : '' }} 
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5 cursor-pointer"
                        >
                        <label for="is_active" class="font-medium text-gray-900 text-lg cursor-pointer">
                            Buka Pendaftaran Lomba Sekarang (Status Aktif)
                        </label>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.kompetisi.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-colors">
                            Batal
                        </a>
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition-colors transform hover:-translate-y-0.5 flex items-center gap-2"
                        >
                            <span id="submitText">Simpan & Publikasikan</span>
                            <svg id="loadingSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Modal Error Detail --}}
    @if ($errors->any() || session('error'))
        <div id="errorModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeErrorModal()"></div>
                
                <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-xl transform transition-all p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button onclick="closeErrorModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Error!</h3>
                    </div>

                    <div class="mt-2">
                        @if (session('error'))
                            <p class="text-sm text-red-600">{{ session('error') }}</p>
                        @else
                            <div class="max-h-60 overflow-y-auto">
                                <ul class="space-y-2">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm text-red-600 flex items-start gap-2">
                                            <span class="inline-block mt-1">•</span>
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button onclick="closeErrorModal()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Script --}}
    <script>
        // Image Viewer
        function imageViewer() {
            return {
                fileChosen(event) {
                    this.fileToDataUrl(event, src => {
                        const preview = document.getElementById('banner-preview');
                        if (preview) {
                            preview.src = src;
                            preview.classList.remove('hidden');
                            preview.classList.add('opacity-80');
                        }
                    })
                },
                fileToDataUrl(event, callback) {
                    if (!event.target.files.length) return
                    let file = event.target.files[0]
                    let reader = new FileReader()
                    reader.readAsDataURL(file)
                    reader.onload = e => callback(e.target.result)
                },
            }
        }

        // Modal Error
        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Auto close modal after 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('errorModal');
            if (modal) {
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 10000);
            }
        });

        // Loading spinner on form submit
        document.getElementById('competitionForm')?.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            if (submitBtn && submitText && loadingSpinner) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitText.textContent = 'Menyimpan...';
                loadingSpinner.classList.remove('hidden');
            }
        });

        // Toast notification for success/error
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                showToast('success', '{{ session('success') }}');
            @endif
            
            @if (session('error'))
                showToast('error', '{{ session('error') }}');
            @endif
        });

        function showToast(type, message) {
            const toast = document.createElement('div');
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            toast.className = `fixed top-4 right-4 z-50 ${colors[type] || 'bg-gray-500'} text-white px-6 py-4 rounded-xl shadow-lg transform transition-all duration-500 max-w-md`;
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="font-bold">${type === 'success' ? '✓' : '✕'}</span>
                    <p class="text-sm">${message}</p>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white hover:text-gray-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        // Prevent double submit
        let isSubmitting = false;
        document.getElementById('competitionForm')?.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
        });
    </script>

    {{-- CSS tambahan untuk modal --}}
    <style>
        #errorModal {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .toast-enter {
            animation: slideInRight 0.3s ease-in-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</x-app-layout>