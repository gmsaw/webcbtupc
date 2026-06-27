<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import Model
use App\Models\User;
use App\Models\Registration;
use App\Models\Competition;
use App\Models\Announcement;
use App\Models\Merchandise;

// Import Controller Breeze
use App\Http\Controllers\ProfileController;

// Import Controller Admin
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\MerchandiseController;
use App\Http\Controllers\Admin\MerchandiseVerificationController;

// Import Controller User
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\User\AnnouncementController as UserAnnouncementController;
use App\Http\Controllers\User\LibraryController;
use App\Http\Controllers\User\MerchandiseTransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/payment-callback', [\App\Http\Controllers\PaymentCallbackController::class, 'receive']);

// =========================================================================
// AREA WAJIB LOGIN (AUTH)
// =========================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ------------------------------------------
    // 1. ROUTE DASHBOARD (Gerbang Utama)
    // ------------------------------------------
    Route::get('/dashboard', function () {
        // Jika yang login adalah Admin HIMAFI
        if (Auth::user()->email === 'admin@upc.com') {
            $total_peserta = User::where('email', '!=', 'admin@upc.com')->count();
            $pending_verifikasi = Registration::where('status_pendaftaran', 'pending')->count();
            $terverifikasi = Registration::where('status_pendaftaran', 'verified')->count();
            return view('admin.dashboard', compact('total_peserta', 'pending_verifikasi', 'terverifikasi'));
        }

        // Jika yang login adalah Peserta (User)
        $user = Auth::user();
        
        $my_registrations = Registration::with('competition')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $registered_comp_ids = $my_registrations->pluck('competition_id')->toArray();
        $available_competitions = Competition::where('is_active', true)->whereNotIn('id', $registered_comp_ids)->get();
        $announcements = Announcement::where('is_active', true)->latest()->take(5)->get();
        $merchandises = Merchandise::where('is_active', true)->latest()->take(3)->get();

        return view('user.dashboard', compact('my_registrations', 'available_competitions', 'announcements', 'merchandises'));
    })->name('dashboard');


    // ------------------------------------------
    // 2. ROUTE KHUSUS ADMIN (Dilindungi Middleware IsAdmin)
    // ------------------------------------------
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        
        // (A) Manajemen Verifikasi Pendaftaran
        Route::get('/admin/verifikasi', [VerificationController::class, 'index'])->name('admin.verifikasi.index');
        Route::put('/admin/verifikasi/{registration}', [VerificationController::class, 'update'])->name('admin.verifikasi.update');
        Route::delete('/admin/verifikasi/{registration}', [VerificationController::class, 'destroy'])->name('admin.verifikasi.destroy');
        // Menu daftar peserta spesifik lomba
        Route::get('/admin/verifikasi/{competition}', [VerificationController::class, 'show'])->name('admin.verifikasi.show');
        // Aksi verifikasi
        Route::post('/admin/verifikasi/update/{registration}', [VerificationController::class, 'updateStatus'])->name('admin.verifikasi.update');

        // (B) Manajemen Data Akun Peserta
        Route::get('/admin/peserta', [ParticipantController::class, 'index'])->name('admin.peserta.index');
        Route::get('/admin/peserta/{user}/edit', [ParticipantController::class, 'edit'])->name('admin.peserta.edit');
        Route::put('/admin/peserta/{user}', [ParticipantController::class, 'update'])->name('admin.peserta.update');
        Route::delete('/admin/peserta/{user}', [ParticipantController::class, 'destroy'])->name('admin.peserta.destroy');
        //DERBUG
        Route::delete('/admin/peserta/{user}/reset', [ParticipantController::class, 'resetRegistrations'])->name('admin.peserta.reset');

        // (C) Manajemen Kompetisi / Lomba
        Route::get('/admin/kompetisi', [CompetitionController::class, 'index'])->name('admin.kompetisi.index');
        Route::get('/admin/kompetisi/create', [CompetitionController::class, 'create'])->name('admin.kompetisi.create');
        Route::post('/admin/kompetisi', [CompetitionController::class, 'store'])->name('admin.kompetisi.store');
        Route::get('/admin/kompetisi/{competition}/edit', [CompetitionController::class, 'edit'])->name('admin.kompetisi.edit');
        Route::put('/admin/kompetisi/{competition}', [CompetitionController::class, 'update'])->name('admin.kompetisi.update');
        Route::delete('/admin/kompetisi/{competition}', [CompetitionController::class, 'destroy'])->name('admin.kompetisi.destroy');

        // (D) Manajemen Pengumuman & Merchandise
        Route::resource('/admin/pengumuman', AnnouncementController::class)->except(['show'])->names('admin.pengumuman');
        Route::resource('/admin/merchandise', MerchandiseController::class)->except(['show'])->names('admin.merchandise');

        // (E) Verifikasi Pembelian Merchandise
        Route::get('/admin/verifikasi-merchandise', [MerchandiseVerificationController::class, 'index'])->name('admin.merchandise.verifikasi');
        Route::put('/admin/verifikasi-merchandise/{transaction}', [MerchandiseVerificationController::class, 'update'])->name('admin.merchandise.verifikasi.update');

        // Manajemen Bank Soal Kompetisi
        Route::get('/admin/kompetisi/{competition}/soal', [\App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('admin.kompetisi.soal.index');
        Route::post('/admin/kompetisi/{competition}/soal', [\App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('admin.kompetisi.soal.store');
        Route::delete('/admin/soal/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('admin.kompetisi.soal.destroy');

        Route::get('/peserta/export', [App\Http\Controllers\Admin\ParticipantController::class, 'export'])->name('admin.peserta.export');

        // Download CSV spesifik per lomba
        Route::get('/admin/kompetisi/{competition}/export', [App\Http\Controllers\Admin\ParticipantController::class, 'exportByCompetition'])->name('admin.kompetisi.export');

        // Lihat Ranking per lomba
        Route::get('/admin/kompetisi/{competition}/ranking', [App\Http\Controllers\Admin\ParticipantController::class, 'ranking'])->name('admin.kompetisi.ranking');
    });


    // ------------------------------------------
    // 3. ROUTE KHUSUS PESERTA (USER)
    // ------------------------------------------
    // Pendaftaran Lomba
    Route::post('/user/daftar-kompetisi', [UserRegistrationController::class, 'store'])->name('user.kompetisi.daftar');

    // Checkout Midtrans Lomba
    Route::get('/user/checkout-lomba/{registration}', function (Registration $registration) {
        if ($registration->user_id !== Auth::id() || $registration->status_pendaftaran !== 'pending') {
            abort(403, 'Akses tidak valid.');
        }
        return view('user.checkout-lomba-midtrans', compact('registration'));
    })->name('user.kompetisi.checkout');

    // Sistem CBT
    Route::get('/user/ujian/{registration}/persiapan', [\App\Http\Controllers\User\CbtController::class, 'prepare'])->name('user.ujian.prepare');
    Route::get('/user/ujian/{registration}', [\App\Http\Controllers\User\CbtController::class, 'show'])->name('user.ujian.show');
    Route::post('/user/ujian/{registration}/submit', [\App\Http\Controllers\User\CbtController::class, 'submit'])->name('user.ujian.submit');
    // AUTO SAVE
    Route::post('/user/ujian/{registration}/autosave', [\App\Http\Controllers\User\CbtController::class, 'autosave'])->name('user.ujian.autosave');
    
    // Pusat Informasi & Pengumuman
    Route::get('/user/pengumuman', [UserAnnouncementController::class, 'index'])->name('user.pengumuman');
    Route::get('/user/pengumuman/{announcement}', [UserAnnouncementController::class, 'show'])->name('user.pengumuman.show');

    // Pustaka E-Book (Rak Buku Peserta)
    Route::get('/user/pustaka', [LibraryController::class, 'index'])->name('user.pustaka');
    Route::get('/user/pustaka/{id}/read', [LibraryController::class, 'read'])->name('user.pustaka.read');
    Route::get('/user/pustaka/{id}/stream', [LibraryController::class, 'stream'])->name('user.pustaka.stream');

    // Pembelian Merchandise / E-Book
    Route::post('/user/beli-merchandise', [MerchandiseTransactionController::class, 'store'])->name('user.merchandise.beli');
    
    // API Log Keamanan DRM
    Route::post('/user/pustaka/log-security', [LibraryController::class, 'logSecurity'])->name('user.pustaka.log');
});

// =========================================================================
// ROUTE PROFIL BAWAAN BREEZE
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Import rute autentikasi Breeze
require __DIR__.'/auth.php';