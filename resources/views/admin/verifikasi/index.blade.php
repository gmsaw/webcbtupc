<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all shadow-sm" title="Kembali ke Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                {{ __('Verifikasi Pendaftaran') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl shadow-sm flex items-center justify-between transition-opacity">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-2xl shadow-sm flex items-center justify-between transition-opacity">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Daftar Lomba sebagai Card -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Pilih Lomba
                </h3>
                
                @if($competitions->isEmpty())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                        <svg class="w-12 h-12 mx-auto text-yellow-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-gray-600 font-medium">Belum ada lomba yang tersedia</p>
                        <p class="text-sm text-gray-400 mt-1">Silakan tambahkan lomba terlebih dahulu</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($competitions as $comp)
                            <a href="{{ route('admin.verifikasi.show', $comp->id) }}" 
                               class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-500 hover:shadow-md transition-all group">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-800 group-hover:text-indigo-600 transition">
                                            {{ $comp->nama_lomba }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Total Pendaftar: 
                                            <span class="font-semibold text-gray-700">{{ $comp->registrations_count }}</span>
                                        </p>
                                    </div>
                                    <span class="bg-indigo-50 text-indigo-600 p-2 rounded-xl group-hover:bg-indigo-100 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </span>
                                </div>
                                
                                <!-- Status Ringkasan -->
                                <div class="mt-3 flex gap-2 text-xs">
                                    <span class="px-2 py-1 bg-yellow-50 text-yellow-700 rounded-full border border-yellow-200">
                                        Pending: {{ $comp->registrations->where('status_pendaftaran', 'pending')->count() }}
                                    </span>
                                    <span class="px-2 py-1 bg-green-50 text-green-700 rounded-full border border-green-200">
                                        Verified: {{ $comp->registrations->where('status_pendaftaran', 'verified')->count() }}
                                    </span>
                                    <span class="px-2 py-1 bg-red-50 text-red-700 rounded-full border border-red-200">
                                        Rejected: {{ $comp->registrations->where('status_pendaftaran', 'rejected')->count() }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Info Tambahan -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="text-sm text-blue-800 font-medium">Tips Verifikasi</p>
                    <ul class="text-xs text-blue-700 mt-1 list-disc list-inside space-y-0.5">
                        <li>Klik pada card lomba untuk melihat daftar peserta</li>
                        <li>Pastikan dokumen dan pembayaran sudah lengkap sebelum menyetujui</li>
                        <li>Gunakan fitur "Cek Detail" untuk melihat bukti pembayaran</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>