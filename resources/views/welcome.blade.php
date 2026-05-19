<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Udayana Physics Championship 2026</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-figtree antialiased text-gray-900 bg-gray-50 overflow-x-hidden">

    <!-- Navbar -->
    <nav class="bg-white/90 backdrop-blur-md fixed w-full z-50 border-b border-gray-100 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center font-extrabold text-xl text-blue-700 tracking-tight">
                    HIMAFI <span class="text-gray-900 ml-1">UPC 2026</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#tentang" class="text-gray-600 hover:text-blue-600 font-medium transition">Tentang</a>
                    <a href="#cabang" class="text-gray-600 hover:text-blue-600 font-medium transition">Cabang Lomba</a>
                    <a href="#informasi" class="text-gray-600 hover:text-blue-600 font-medium transition">Informasi</a>
                    <a href="#timeline" class="text-gray-600 hover:text-blue-600 font-medium transition">Jadwal</a>
                    <a href="#galeri" class="text-gray-600 hover:text-blue-600 font-medium transition">Galeri</a>
                </div>
                <div>
                    @if (Route::has('login'))
                        <div class="flex items-center space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium hidden sm:block">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-5 py-2 rounded-xl font-semibold shadow-md shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                                        Daftar Sekarang
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-white">
        <!-- Background Blobs -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3">
            <div class="w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
        </div>
        <div class="absolute top-0 left-0 translate-y-24 -translate-x-1/3">
            <div class="w-72 h-72 bg-cyan-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
            <div data-aos="zoom-in" data-aos-duration="1000">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 border border-blue-200 text-blue-600 text-sm font-semibold mb-6 tracking-wide">
                    Kompetisi Fisika Nasional SMA/SMK Sederajat
                </span>
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 mb-6 leading-tight">
                    Udayana Physics <br class="hidden md:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Championship</span>
                </h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto mb-10 leading-relaxed">
                    Buktikan ketajaman logikamu dan taklukkan tantangan di platform Computer Based Test (CBT) terintegrasi paling mutakhir tahun ini.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl shadow-blue-500/30 transition-all transform hover:-translate-y-1">
                        Registrasi Peserta
                    </a>
                    <a href="#timeline" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 px-8 py-4 rounded-xl font-bold text-lg shadow-sm transition-all">
                        Lihat Jadwal
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Section -->
    <section id="tentang" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900">Tentang UPC 2026</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h3 class="text-2xl font-bold mb-4">Ajang Kompetisi Fisika Terbesar di Bali</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Udayana Physics Championship (UPC) 2026 adalah kompetisi fisika tingkat nasional yang diselenggarakan oleh Himpunan Mahasiswa Fisika (HIMAFI) Universitas Udayana. Kompetisi ini bertujuan untuk mengasah kemampuan berpikir kritis, analitis, dan kreativitas siswa SMA/SMK sederajat dalam bidang fisika.
                    </p>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Dengan mengusung tema "Synergizing Young Minds Through Physics Innovation", UPC 2026 menjadi wadah bagi generasi muda untuk menunjukkan bakat dan inovasi mereka dalam menyelesaikan berbagai permasalahan fisika.
                    </p>
                    <div class="flex items-center gap-4 mt-6">
                        <div class="flex -space-x-2">
                            <img src="https://ui-avatars.com/api/?name=Peserta+1&background=random&length=2&size=40" class="w-10 h-10 rounded-full border-2 border-white">
                            <img src="https://ui-avatars.com/api/?name=Peserta+2&background=random&length=2&size=40" class="w-10 h-10 rounded-full border-2 border-white">
                            <img src="https://ui-avatars.com/api/?name=Peserta+3&background=random&length=2&size=40" class="w-10 h-10 rounded-full border-2 border-white">
                        </div>
                        <p class="text-gray-500 text-sm"><span class="font-bold text-gray-900">500+</span> Peserta dari seluruh Indonesia</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4" data-aos="fade-left">
                    <div class="space-y-4">
                        <img src="https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Physics Lab" class="rounded-2xl shadow-lg h-48 w-full object-cover">
                        <img src="https://images.unsplash.com/photo-1581091226033-d5c48150dbaa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Students Competing" class="rounded-2xl shadow-lg h-64 w-full object-cover">
                    </div>
                    <div class="space-y-4 pt-8">
                        <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Award Ceremony" class="rounded-2xl shadow-lg h-64 w-full object-cover">
                        <img src="https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Team Discussion" class="rounded-2xl shadow-lg h-48 w-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cabang Lomba Section -->
    <section id="cabang" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900">Cabang Lomba</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
                <p class="mt-4 text-gray-500 max-w-2xl mx-auto">Pilih kategori lomba yang sesuai dengan minat dan bakatmu</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Olimpiade Fisika -->
                <div class="bg-white rounded-3xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="h-48 bg-gradient-to-r from-blue-600 to-blue-400 relative overflow-hidden">
                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold">Individual</span>
                        </div>
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <svg class="w-24 h-24 text-white/30" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-3">Olimpiade Fisika</h3>
                        <p class="text-gray-600 mb-4">Uji kemampuan analisis dan pemecahan masalah fisika melalui soal-soal teoritis yang menantang. Cocok untuk kamu yang suka dengan konsep dan rumus fisika.</p>
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>100 Soal Pilihan Ganda</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Waktu: 120 Menit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Tingkat Kesulitan: Tinggi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Eksperimen Fisika -->
                <div class="bg-white rounded-3xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="h-48 bg-gradient-to-r from-green-600 to-green-400 relative overflow-hidden">
                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold">Tim (2-3 orang)</span>
                        </div>
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <svg class="w-24 h-24 text-white/30" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4-3h2v13h-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-3">Eksperimen Fisika</h3>
                        <p class="text-gray-600 mb-4">Tunjukkan kreativitas dan keterampilan praktikum fisika melalui serangkaian percobaan menarik. Akan diuji ketelitian dan pemahaman konsep.</p>
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>3 Percobaan Praktikum</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Waktu: 180 Menit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Penilaian: Akurasi & Kreativitas</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Poster Fisika -->
                <div class="bg-white rounded-3xl shadow-lg overflow-hidden group hover:shadow-2xl transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                    <div class="h-48 bg-gradient-to-r from-purple-600 to-purple-400 relative overflow-hidden">
                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold">Individual/Tim</span>
                        </div>
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <svg class="w-24 h-24 text-white/30" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM5 5h14v14H5V5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-3">Poster Fisika</h3>
                        <p class="text-gray-600 mb-4">Kreasikan ide dan inovasi fisika dalam bentuk poster ilmiah yang menarik. Lomba ini menguji kemampuan komunikasi visual dan pemahaman konsep.</p>
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Tema: Fisika untuk Masa Depan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Presentasi: 10 Menit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Penilaian: Konten & Estetika</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Informasi Section -->
    <section id="informasi" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900">Mengapa Mengikuti UPC?</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                <!-- Card 1 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 text-center hover:shadow-xl transition-shadow" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl mx-auto flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Total Hadiah Jutaan Rupiah</h3>
                    <p class="text-gray-500">Raih kesempatan memenangkan uang pembinaan total belasan juta rupiah beserta piala bergilir untuk juara umum.</p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 text-center hover:shadow-xl transition-shadow" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-cyan-50 text-cyan-600 rounded-2xl mx-auto flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Sertifikat Tingkat Nasional</h3>
                    <p class="text-gray-500">Seluruh peserta dan finalis akan mendapatkan e-sertifikat bernilai tinggi yang dapat digunakan untuk pendaftaran SNBP.</p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 text-center hover:shadow-xl transition-shadow" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl mx-auto flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Sistem CBT Mutakhir</h3>
                    <p class="text-gray-500">Rasakan pengalaman ujian yang adil dengan sistem Anti-Contek, Auto-Save, dan UI/UX yang responsif.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section (Rapi) -->
    <section id="timeline" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-down">
                <h2 class="text-3xl font-bold text-gray-900">Jadwal Pelaksanaan</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
                <p class="mt-4 text-gray-500">Catat tanggal-tanggal penting berikut agar tidak tertinggal informasi.</p>
            </div>

            <!-- Timeline Cards -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-600" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Gelombang I & II</span>
                        <span class="text-3xl font-bold text-blue-600">01</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Pendaftaran</h3>
                    <p class="text-gray-500 text-sm mb-3">1 Maret - 20 April 2026</p>
                    <p class="text-gray-600 text-sm">Pendaftaran online melalui website. Lakukan pembayaran dan lengkapi profil.</p>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center text-xs text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>51 hari tersisa</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-cyan-500" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-semibold text-cyan-600 bg-cyan-50 px-3 py-1 rounded-full">Online</span>
                        <span class="text-3xl font-bold text-cyan-500">02</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Technical Meeting</h3>
                    <p class="text-gray-500 text-sm mb-3">25 - 26 April 2026</p>
                    <p class="text-gray-600 text-sm">Penjelasan regulasi via Zoom, dilanjutkan simulasi CBT.</p>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center text-xs text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Wajib diikuti</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-indigo-500" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">Online CBT</span>
                        <span class="text-3xl font-bold text-indigo-500">03</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Penyisihan</h3>
                    <p class="text-gray-500 text-sm mb-3">2 Mei 2026</p>
                    <p class="text-gray-600 text-sm">Ujian tahap pertama serentak menggunakan sistem CBT HIMAFI.</p>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center text-xs text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>120 menit</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500" data-aos="fade-up" data-aos-delay="400">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-semibold text-green-600 bg-green-50 px-3 py-1 rounded-full">Offline</span>
                        <span class="text-3xl font-bold text-green-500">04</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Semifinal & Final</h3>
                    <p class="text-gray-500 text-sm mb-3">10 Mei 2026</p>
                    <p class="text-gray-600 text-sm">Diundang ke Kampus Universitas Udayana untuk praktikum dan cerdas cermat.</p>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center text-xs text-gray-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Kampus Unud, Bukit</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Note -->
            <div class="mt-10 text-center text-sm text-gray-500 bg-white p-4 rounded-xl shadow-sm" data-aos="fade-up">
                <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Jadwal dapat berubah sewaktu-waktu. Pantau terus informasi terbaru melalui website dan media sosial kami.</span>
            </div>
        </div>
    </section>

    <!-- Galeri Section -->
    <section id="galeri" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900">Galeri UPC 2025</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
                <p class="mt-4 text-gray-500">Momen-momen seru dari perhelatan UPC tahun lalu</p>
            </div>

            <!-- Gallery Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="relative group overflow-hidden rounded-2xl aspect-square" data-aos="zoom-in" data-aos-delay="100">
                    <img src="https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gallery 1" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <p class="text-white text-sm font-semibold">Peserta sedang mengerjakan soal</p>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square" data-aos="zoom-in" data-aos-delay="150">
                    <img src="https://images.unsplash.com/photo-1581091226033-d5c48150dbaa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gallery 2" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <p class="text-white text-sm font-semibold">Praktikum fisika</p>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square" data-aos="zoom-in" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gallery 3" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <p class="text-white text-sm font-semibold">Sesi awarding</p>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square" data-aos="zoom-in" data-aos-delay="250">
                    <img src="https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gallery 4" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <p class="text-white text-sm font-semibold">Diskusi tim</p>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square" data-aos="zoom-in" data-aos-delay="300">
                    <img src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gallery 5" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <p class="text-white text-sm font-semibold">Suasana laboratorium</p>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square" data-aos="zoom-in" data-aos-delay="350">
                    <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gallery 6" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <p class="text-white text-sm font-semibold">Para finalis</p>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square" data-aos="zoom-in" data-aos-delay="400">
                    <img src="https://images.unsplash.com/photo-1567168544813-cc03465b4fa8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gallery 7" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <p class="text-white text-sm font-semibold">Presentasi poster</p>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl aspect-square" data-aos="zoom-in" data-aos-delay="450">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Gallery 8" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <p class="text-white text-sm font-semibold">Foto bersama</p>
                    </div>
                </div>
            </div>

            <!-- Video Highlight -->
            <div class="mt-12 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-3xl p-8 text-white" data-aos="fade-up">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="text-2xl font-bold mb-2">Highlight UPC 2025</h3>
                        <p class="text-blue-100">Tonton keseruan kompetisi tahun lalu dalam video berikut</p>
                    </div>
                    <a href="#" class="bg-white text-blue-600 px-6 py-3 rounded-xl font-bold hover:bg-blue-50 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        Putar Video
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer / CTA -->
    <footer class="bg-gray-900 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Siap untuk Berkompetisi?</h2>
            <p class="text-gray-400 mb-8 max-w-xl mx-auto">Jangan lewatkan kesempatan untuk mengasah kemampuan fisikamu di ajang paling bergengsi tahun ini.</p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-gray-900 px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-gray-100 transition-colors">
                Daftar Sekarang
            </a>

            <div class="mt-16 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center text-gray-500 text-sm">
                <p>&copy; 2026 HIMAFI Universitas Udayana. All rights reserved.</p>
                <div class="mt-4 md:mt-0 space-x-4">
                    <a href="#" class="hover:text-white transition">Bantuan</a>
                    <a href="#" class="hover:text-white transition">Kontak Panitia</a>
                    <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100,
                easing: 'ease-out-cubic'
            });
        });
    </script>
</body>
</html>