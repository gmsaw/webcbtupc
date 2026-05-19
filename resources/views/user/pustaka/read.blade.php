<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('user.pustaka') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-bold text-xl md:text-2xl text-gray-800 leading-tight line-clamp-1">
                    {{ $ebook->nama_produk }}
                </h2>
            </div>
            <div class="hidden md:flex items-center gap-2 bg-red-50 text-red-700 px-3 py-1.5 rounded-lg border border-red-200 text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Proteksi DRM Aktif
            </div>
        </div>
    </x-slot>

    <style>
        /* Mencegah seleksi teks dan drag */
        .noselect {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Mencegah print */
        @media print {
            body { 
                display: none !important; 
                visibility: hidden !important;
            }
        }

        /* Overlay untuk mencegah screenshot di beberapa browser */
        .screenshot-protection {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            z-index: 9999;
            pointer-events: none;
        }

        /* Blur effect saat window tidak aktif */
        .pdf-container.blur {
            filter: blur(10px);
            transition: filter 0.3s ease;
        }

        /* Watermark overlay */
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            color: rgba(255, 255, 255, 0.15);
            font-size: 12px;
            pointer-events: none;
            z-index: 10000;
            transform: rotate(-15deg);
            user-select: none;
        }
    </style>

    <div class="noselect" id="app">
        <!-- Peringatan Hak Cipta -->
        <div class="bg-red-600 text-white px-6 py-3 text-center text-xs md:text-sm font-medium flex items-center justify-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span>
                <b>⚠️ PERINGATAN HAK CIPTA:</b> Konten ini dilindungi undang-undang. Segala bentuk pengunduhan, penggandaan, dan penyebaran tanpa izin adalah tindakan pidana.
            </span>
        </div>

        <!-- PDF Reader dengan Proteksi -->
        <div class="w-full h-[85vh] bg-gray-900 relative pdf-container" id="pdfContainer">
            <!-- PDF Viewer dengan parameter keamanan -->
            <iframe 
                src="{{ route('user.pustaka.stream', $ebook->id) }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH&zoom=100&statusbar=0&messages=0&download=0&print=0" 
                class="w-full h-full border-0" 
                id="pdfViewer"
                title="E-Book Reader - Dilindungi"
                sandbox="allow-scripts allow-same-origin allow-forms"
                loading="lazy"
                importance="high"
                referrerpolicy="no-referrer"
            ></iframe>

            <!-- Watermark -->
            <div class="watermark" id="dynamicWatermark">
                {{ Auth::user()->email }} • {{ now()->format('Y-m-d H:i:s') }} • HIMAFI
            </div>

            <!-- Overlay proteksi -->
            <div class="screenshot-protection" id="protectionOverlay"></div>

            <!-- Loading indicator -->
            <div id="loadingIndicator" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-3"></div>
                <p class="text-sm opacity-75">Memuat dokumen dengan aman...</p>
            </div>
        </div>

        <!-- Footer Informasi -->
        <div class="bg-gray-800 text-white/70 px-6 py-2 text-xs flex flex-wrap items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    </svg>
                    DRM Active
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Secure Connection
                </span>
            </div>
            <div>
                IP: <span id="userIP">{{ request()->ip() }}</span> • Session: {{ session()->getId() }}
            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            // ==================== KONFIGURASI KEAMANAN ====================
            const CONFIG = {
                BLOCK_KEYS: ['F12', 'PrintScreen', 'PrtScn'],
                BLOCK_COMBINATIONS: [
                    { ctrl: true, shift: true, keys: ['I', 'J', 'C', 'i', 'j', 'c'] }, // DevTools
                    { ctrl: true, keys: ['U', 'u', 'S', 's', 'P', 'p', 'C', 'c'] }, // View Source, Save, Print, Copy
                    { meta: true, keys: ['S', 's', 'P', 'p', 'C', 'c'] } // Mac shortcuts
                ],
                WATERMARK_UPDATE_INTERVAL: 30000 // 30 detik
            };

            // ==================== ELEMEN DOM ====================
            const elements = {
                container: document.getElementById('pdfContainer'),
                viewer: document.getElementById('pdfViewer'),
                overlay: document.getElementById('protectionOverlay'),
                watermark: document.getElementById('dynamicWatermark'),
                loading: document.getElementById('loadingIndicator')
            };

            // ==================== FUNGSI KEAMANAN ====================

            // 1. Blokir semua event yang mencurigakan
            function blockSecurityEvents() {
                // Blokir klik kanan
                document.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                    showSecurityAlert('Fitur ini tidak tersedia');
                    return false;
                });

                // Blokir drag & drop
                document.addEventListener('dragstart', (e) => e.preventDefault());
                document.addEventListener('selectstart', (e) => e.preventDefault());

                // Blokir copy/cut/paste
                ['copy', 'cut', 'paste'].forEach(eventType => {
                    document.addEventListener(eventType, (e) => {
                        e.preventDefault();
                        showSecurityAlert('Tindakan menyalin diblokir oleh sistem keamanan');
                    });
                });

                // Blokir save as
                document.addEventListener('save', (e) => e.preventDefault());
            }

            // 2. Blokir kombinasi keyboard berbahaya
            function blockKeyboardShortcuts() {
                document.addEventListener('keydown', (e) => {
                    // Blokir F12 dan Print Screen
                    if (CONFIG.BLOCK_KEYS.includes(e.key) || e.keyCode === 44 || e.keyCode === 123) {
                        e.preventDefault();
                        showSecurityAlert('Akses ditolak: Tombol ini diblokir');
                        return false;
                    }

                    // Blokir kombinasi Ctrl/Command + sesuatu
                    const isCtrlPressed = e.ctrlKey || e.metaKey;
                    
                    // Blokir kombinasi Ctrl+Shift+I/J/C
                    if (isCtrlPressed && e.shiftKey && ['I', 'J', 'C', 'i', 'j', 'c'].includes(e.key)) {
                        e.preventDefault();
                        showSecurityAlert('Developer Tools tidak dapat diakses');
                        return false;
                    }

                    // Blokir Ctrl+U (View Source), Ctrl+S (Save), Ctrl+P (Print), Ctrl+C (Copy)
                    if (isCtrlPressed && ['u', 'U', 's', 'S', 'p', 'P', 'c', 'C'].includes(e.key)) {
                        e.preventDefault();
                        showSecurityAlert('Tindakan ini diblokir untuk melindungi hak cipta');
                        return false;
                    }

                    // Blokir Alt + tombol tertentu
                    if (e.altKey && ['d', 'D', 'f', 'F'].includes(e.key)) {
                        e.preventDefault();
                    }
                });
            }

            // 3. Deteksi dan blokir screenshot (metode lebih agresif)
            function blockScreenshot() {
                let screenshotAttempts = 0;
                
                // Deteksi tombol Print Screen
                document.addEventListener('keyup', (e) => {
                    if (e.key === 'PrintScreen' || e.keyCode === 44) {
                        screenshotAttempts++;
                        
                        // Clear clipboard
                        try {
                            navigator.clipboard.writeText('').catch(() => {});
                        } catch (err) {}

                        // Tampilkan peringatan
                        showSecurityAlert('⚠️ PERINGATAN: Screenshot terdeteksi! Tindakan ini dilaporkan.');
                        
                        // Tambah efek blur sesaat
                        elements.container.classList.add('blur');
                        setTimeout(() => elements.container.classList.remove('blur'), 1000);

                        // Log attempt (bisa dikirim ke server)
                        logSecurityEvent('screenshot_attempt', screenshotAttempts);
                    }
                });

                // Deteksi kombinasi yang sering digunakan untuk screenshot
                document.addEventListener('keydown', (e) => {
                    // Windows + Shift + S (Snipping Tool)
                    if (e.metaKey && e.shiftKey && e.key === 'S') {
                        e.preventDefault();
                        showSecurityAlert('Snipping Tool diblokir');
                    }

                    // Command + Shift + 3/4 (Mac screenshot)
                    if (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4')) {
                        e.preventDefault();
                        showSecurityAlert('Mac screenshot diblokir');
                    }
                });
            }

            // 4. Proteksi iframe dari ekstraksi konten
            function protectIframe() {
                if (elements.viewer) {
                    // Blokir akses ke konten iframe dari parent
                    elements.viewer.addEventListener('load', () => {
                        try {
                            // Coba blokir akses ke iframe contentWindow
                            Object.defineProperty(elements.viewer, 'contentWindow', {
                                get: function() {
                                    showSecurityAlert('Akses ke konten internal diblokir');
                                    return null;
                                }
                            });
                        } catch (err) {
                            // Silent fail
                        }
                    });
                }
            }

            // 5. Dynamic watermark yang bergerak
            function initDynamicWatermark() {
                const watermark = elements.watermark;
                if (!watermark) return;

                // Update watermark setiap 30 detik
                setInterval(() => {
                    const now = new Date();
                    watermark.textContent = `{{ Auth::user()->email }} • ${now.toLocaleString('id-ID')} • HIMAFI`;
                }, CONFIG.WATERMARK_UPDATE_INTERVAL);

                // Watermark bergerak random
                setInterval(() => {
                    if (!watermark) return;
                    
                    const positions = [
                        { bottom: '20px', right: '20px' },
                        { bottom: '50px', left: '20px' },
                        { top: '20px', right: '20px' },
                        { top: '50px', left: '20px' }
                    ];
                    
                    const randomPos = positions[Math.floor(Math.random() * positions.length)];
                    Object.assign(watermark.style, randomPos);
                }, 10000);
            }

            // 6. Deteksi Developer Tools
            function detectDevTools() {
                let devToolsOpen = false;
                
                // Metode 1: Deteksi perubahan ukuran
                const threshold = 160;
                setInterval(() => {
                    if (window.outerWidth - window.innerWidth > threshold || 
                        window.outerHeight - window.innerHeight > threshold) {
                        if (!devToolsOpen) {
                            devToolsOpen = true;
                            showSecurityAlert('Developer Tools terdeteksi! Akses akan dibatasi.');
                            logSecurityEvent('devtools_opened');
                            
                            // Refresh halaman sebagai tindakan keamanan
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                        }
                    } else {
                        devToolsOpen = false;
                    }
                }, 1000);
            }

            // 7. Anti-debugging
            function antiDebugging() {
                // Override console methods
                const originalConsole = window.console;
                window.console = {
                    ...originalConsole,
                    log: function() {},
                    info: function() {},
                    debug: function() {},
                    table: function() {}
                };

                // Deteksi debugger
                setInterval(() => {
                    const start = performance.now();
                    debugger;
                    const end = performance.now();
                    
                    if (end - start > 100) {
                        showSecurityAlert('Debugger terdeteksi!');
                        logSecurityEvent('debugger_detected');
                    }
                }, 2000);
            }

            // 8. Blokir akses ke file PDF langsung
            function blockDirectAccess() {
                // Cek apakah halaman diakses langsung
                if (window.location.href.includes('.pdf')) {
                    window.location.href = '{{ route("user.pustaka") }}';
                }
            }

            // ==================== FUNGSI UTILITY ====================

            function showSecurityAlert(message) {
                // Tampilkan alert non-intrusive
                const alertDiv = document.createElement('div');
                alertDiv.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold z-[99999] animate-fade-in-down';
                alertDiv.textContent = message;
                document.body.appendChild(alertDiv);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            }

            function logSecurityEvent(eventType, details = null) {
                // Kirim log ke server (implementasi AJAX)
                fetch('{{ route("user.pustaka.log") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        event: eventType,
                        details: details,
                        url: window.location.href,
                        timestamp: new Date().toISOString()
                    })
                }).catch(() => {});
            }

            // ==================== INITIALIZATION ====================

            function init() {
                // Hapus loading indicator
                if (elements.loading) {
                    setTimeout(() => {
                        elements.loading.style.opacity = '0';
                        setTimeout(() => {
                            elements.loading.style.display = 'none';
                        }, 500);
                    }, 1500);
                }

                // Inisialisasi semua proteksi
                blockSecurityEvents();
                blockKeyboardShortcuts();
                blockScreenshot();
                protectIframe();
                initDynamicWatermark();
                detectDevTools();
                antiDebugging();
                blockDirectAccess();

                // Disable right click pada iframe
                if (elements.viewer) {
                    elements.viewer.addEventListener('load', () => {
                        try {
                            const iframeDoc = elements.viewer.contentDocument || elements.viewer.contentWindow.document;
                            iframeDoc.addEventListener('contextmenu', (e) => e.preventDefault());
                        } catch (err) {
                            // Cross-origin error, ignore
                        }
                    });
                }

                // Blur saat window tidak aktif
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        elements.container.classList.add('blur');
                    } else {
                        elements.container.classList.remove('blur');
                    }
                });
            }

            // Jalankan setelah DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

        })();
    </script>

    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate(-50%, -20px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }
        
        .animate-fade-in-down {
            animation: fadeInDown 0.3s ease-out;
        }
    </style>
</x-app-layout>