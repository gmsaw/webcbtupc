<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ __('Verifikasi Peserta UPC') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Daftar Antrean Verifikasi</h3>
                            <p class="text-sm text-gray-500">Cek kartu pelajar peserta sebelum memberikan akses ke tahap selanjutnya.</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full border border-yellow-200">
                            {{ $peserta->total() }} Menunggu
                        </span>
                    </div>

                    <div class="overflow-x-auto relative">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Nama Lengkap</th>
                                    <th scope="col" class="py-3 px-6">Asal Sekolah</th>
                                    <th scope="col" class="py-3 px-6">Kontak</th>
                                    <th scope="col" class="py-3 px-6 text-center">Kartu Pelajar</th>
                                    <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($peserta as $user)
                                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $user->name }}
                                            <div class="text-xs text-gray-500 font-normal">{{ $user->email }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $user->asal_sekolah }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->no_wa) }}" target="_blank" class="text-green-600 hover:underline flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.893-4.448 9.893-9.892 0-5.447-4.446-9.892-9.893-9.892-5.452 0-9.893 4.449-9.893 9.892 0 1.988.546 3.824 1.584 5.493l-1.096 4.003 4.113-1.196zm7.35-6.502c-.381-.191-2.253-1.112-2.603-1.24-.351-.128-.606-.191-.861.191-.254.381-.983 1.24-1.203 1.494-.22.255-.441.287-.822.096-.381-.191-1.611-.595-3.07-1.898-1.135-1.012-1.899-2.262-2.119-2.645-.22-.382-.023-.588.168-.779.171-.17.381-.446.571-.67.191-.223.254-.381.381-.637.127-.255.064-.478-.032-.67-.095-.191-.861-2.074-1.179-2.839-.311-.749-.627-.648-.861-.66-.221-.01-.476-.01-.731-.01-.254 0-.667.095-1.016.477-.35.381-1.334 1.305-1.334 3.18 0 1.874 1.366 3.685 1.556 3.94.191.254 2.684 4.095 6.502 5.744 3.818 1.649 3.818 1.099 4.517.971.699-.127 2.253-.923 2.571-1.815.318-.893.318-1.658.222-1.816-.096-.159-.35-.255-.731-.446z"/></svg>
                                                {{ $user->no_wa }}
                                            </a>
                                        </td>
                                        
                                        <td class="py-4 px-6 text-center" x-data="{ modalOpen: false }">
                                            @if($user->hasMedia('kartu_pelajar'))
                                                <button @click="modalOpen = true" class="text-blue-600 hover:text-blue-800 font-medium text-sm border border-blue-200 bg-blue-50 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition-colors">
                                                    Lihat Foto
                                                </button>

                                                <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="modalOpen = false" aria-hidden="true"></div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div x-show="modalOpen" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Kartu Pelajar: {{ $user->name }}</h3>
                                                                <img src="{{ $user->getFirstMediaUrl('kartu_pelajar') }}" alt="Kartu Pelajar" class="w-full rounded-lg border border-gray-200">
                                                            </div>
                                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                <button type="button" @click="modalOpen = false" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                                                    Tutup
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-red-500 italic">Belum unggah</span>
                                            @endif
                                        </td>

                                        <td class="py-4 px-6 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('admin.verifikasi.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" onclick="return confirm('Yakin ingin menyetujui peserta ini?')" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-1.5 focus:outline-none transition-colors">
                                                        Terima
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.verifikasi.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" onclick="return confirm('Yakin ingin MENOLAK peserta ini?')" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 focus:outline-none transition-colors">
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Hore! Tidak ada antrean verifikasi saat ini.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $peserta->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>