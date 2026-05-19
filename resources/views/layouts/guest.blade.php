<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HIMAFI UPC 2026') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-figtree antialiased text-gray-900">
    <div class="min-h-screen flex bg-white">
        
        <div class="w-full lg:w-1/2 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-md">
                
                <div class="flex items-center justify-between mb-8">
                    <a href="/" class="flex items-center gap-2 text-2xl font-bold text-blue-700">
                        HIMAFI <span class="text-gray-900">UPC</span>
                    </a>
                    <a href="/" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                        &larr; Kembali ke Beranda
                    </a>
                </div>

                <div class="bg-white">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 h-full w-full bg-gradient-to-br from-blue-900 via-blue-800 to-cyan-700 flex flex-col justify-center items-center text-center p-16 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
                
                <div class="relative z-10 space-y-6">
                    <div class="w-24 h-24 bg-white/10 backdrop-blur-md rounded-2xl mx-auto flex items-center justify-center border border-white/20 mb-8">
                        <svg class="w-12 h-12 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h2 class="text-4xl font-extrabold text-white tracking-tight">Computer Based Test</h2>
                    <p class="text-lg text-blue-100 max-w-md mx-auto leading-relaxed">
                        Platform ujian terintegrasi yang dirancang khusus untuk memastikan integritas dan kenyamanan selama Udayana Physics Championship berlangsung.
                    </p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>