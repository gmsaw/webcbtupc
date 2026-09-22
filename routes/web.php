<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

// ─── Models ───
use App\Models\User;
use App\Models\Registration;
use App\Models\Competition;
use App\Models\Announcement;
use App\Models\Merchandise;

// ─── Middleware ───
use App\Http\Middleware\IsAdmin;

// ─── Controllers: Breeze ───
use App\Http\Controllers\ProfileController;

// ─── Controllers: Admin ───
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\MerchandiseController;
use App\Http\Controllers\Admin\MerchandiseVerificationController;
use App\Http\Controllers\Admin\QuestionController;

// ─── Controllers: User ───
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\User\AnnouncementController as UserAnnouncementController;
use App\Http\Controllers\User\CbtController;
use App\Http\Controllers\User\LibraryController;
use App\Http\Controllers\User\MerchandiseTransactionController;

// ─── Controllers: Lainnya ───
use App\Http\Controllers\PaymentCallbackController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// ROUTE PUBLIK (Tanpa Login)
// =========================================================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/api/payment-callback', [PaymentCallbackController::class, 'receive'])
    ->name('payment.callback');

Route::get('/link-storage', function () {
    Artisan::call('storage:link');
    return "Storage link has been created!";
})->name('link-storage');

// =========================================================================
// AREA WAJIB LOGIN (AUTH + VERIFIED)
// =========================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ---------------------------------------------------------------------
    // 1. DASHBOARD
    // ---------------------------------------------------------------------
    Route::get('/dashboard', function () {
        $admin_emails = [
            'admin@upc.com',
            'aridaniswara6@gmail.com',
            'tambun.24008@student.unud.ac.id',
            'alvinandaa26@gmail.com',
            'dinanti.har03@gmail.com',
            'widnyana.24013@student.unud.ac.id',
            'nandana.2508521055@student.unud.ac.id',
        ];

        // Jika admin
        if (in_array(Auth::user()->email, $admin_emails)) {
            $total_peserta       = User::whereNotIn('email', $admin_emails)->count();
            $pending_verifikasi  = Registration::where('status_pendaftaran', 'pending')->count();
            $terverifikasi       = Registration::where('status_pendaftaran', 'verified')->count();

            return view('admin.dashboard', compact(
                'total_peserta', 'pending_verifikasi', 'terverifikasi'
            ));
        }

        // Jika peserta
        $user = Auth::user();

        $my_registrations        = Registration::with('competition')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $registered_comp_ids     = $my_registrations->pluck('competition_id')->toArray();
        $available_competitions  = Competition::where('is_active', true)
            ->whereNotIn('id', $registered_comp_ids)
            ->get();

        $announcements = Announcement::where('is_active', true)->latest()->take(5)->get();
        $merchandises  = Merchandise::where('is_active', true)->latest()->take(3)->get();

        return view('user.dashboard', compact(
            'my_registrations', 'available_competitions', 'announcements', 'merchandises'
        ));
    })->name('dashboard');

    // ---------------------------------------------------------------------
    // 2. ADMIN — Dilindungi Middleware IsAdmin
    // ---------------------------------------------------------------------
    Route::middleware([IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {

        // ─── (A) Verifikasi Pendaftaran ───
        Route::get('/verifikasi', [VerificationController::class, 'index'])
            ->name('verifikasi.index');
        Route::get('/verifikasi/{competition}', [VerificationController::class, 'show'])
            ->name('verifikasi.show');
        Route::post('/verifikasi/update/{registration}', [VerificationController::class, 'updateStatus'])
            ->name('verifikasi.updateStatus');
        Route::put('/verifikasi/{registration}', [VerificationController::class, 'update'])
            ->name('verifikasi.update');
        Route::delete('/verifikasi/{registration}', [VerificationController::class, 'destroy'])
            ->name('verifikasi.destroy');

        // ─── (B) Manajemen Peserta ───
        Route::get('/peserta', [ParticipantController::class, 'index'])
            ->name('peserta.index');
        Route::get('/peserta/export', [ParticipantController::class, 'export'])
            ->name('peserta.export');
        Route::get('/peserta/{user}/edit', [ParticipantController::class, 'edit'])
            ->name('peserta.edit');
        Route::put('/peserta/{user}', [ParticipantController::class, 'update'])
            ->name('peserta.update');
        Route::delete('/peserta/{user}', [ParticipantController::class, 'destroy'])
            ->name('peserta.destroy');
        Route::delete('/peserta/{user}/reset', [ParticipantController::class, 'resetRegistrations'])
            ->name('peserta.reset');

        // ─── (C) Manajemen Kompetisi ───
        Route::get('/kompetisi', [CompetitionController::class, 'index'])
            ->name('kompetisi.index');
        Route::get('/kompetisi/create', [CompetitionController::class, 'create'])
            ->name('kompetisi.create');
        Route::post('/kompetisi', [CompetitionController::class, 'store'])
            ->name('kompetisi.store');
        Route::get('/kompetisi/{competition}/edit', [CompetitionController::class, 'edit'])
            ->name('kompetisi.edit');
        Route::put('/kompetisi/{competition}', [CompetitionController::class, 'update'])
            ->name('kompetisi.update');
        Route::delete('/kompetisi/{competition}', [CompetitionController::class, 'destroy'])
            ->name('kompetisi.destroy');

        // ─── (D) Bank Soal ───
        Route::get('/kompetisi/{competition}/soal', [QuestionController::class, 'index'])
            ->name('kompetisi.soal.index');
        Route::post('/kompetisi/{competition}/soal', [QuestionController::class, 'store'])
            ->name('kompetisi.soal.store');
        Route::delete('/soal/{question}', [QuestionController::class, 'destroy'])
            ->name('kompetisi.soal.destroy');

        // ─── (E) Export & Ranking ───
        Route::get('/kompetisi/{competition}/export', [ParticipantController::class, 'exportByCompetition'])
            ->name('kompetisi.export');
        Route::get('/kompetisi/{competition}/ranking', [ParticipantController::class, 'ranking'])
            ->name('kompetisi.ranking');

        // ─── (F) Jawaban Mentah ───
        Route::get('/kompetisi/{competition}/jawaban-export', [ParticipantController::class, 'exportAnswersCsv'])
            ->name('kompetisi.jawaban.export');
        Route::get('/kompetisi/{competition}/peserta/{registration}/jawaban', [ParticipantController::class, 'showAnswers'])
            ->name('kompetisi.peserta.jawaban');
        Route::get('/kompetisi/{competition}/peserta/{registration}/jawaban-export', [ParticipantController::class, 'exportSingleAnswersCsv'])
            ->name('kompetisi.peserta.jawaban.export');

        // ─── (G) Pengumuman & Merchandise ───
        Route::resource('/pengumuman', AnnouncementController::class)
            ->except(['show'])
            ->names('pengumuman');

        Route::resource('/merchandise', MerchandiseController::class)
            ->except(['show'])
            ->names('merchandise');

        // ─── (H) Verifikasi Merchandise ───
        Route::get('/verifikasi-merchandise', [MerchandiseVerificationController::class, 'index'])
            ->name('merchandise.verifikasi');
        Route::put('/verifikasi-merchandise/{transaction}', [MerchandiseVerificationController::class, 'update'])
            ->name('merchandise.verifikasi.update');
    });

    // ---------------------------------------------------------------------
    // 3. PESERTA (USER)
    // ---------------------------------------------------------------------
    Route::prefix('user')->name('user.')->group(function () {

        // ─── (A) Pendaftaran Lomba ───
        Route::post('/daftar-kompetisi', [UserRegistrationController::class, 'store'])
            ->name('kompetisi.daftar');

        // ─── (B) Checkout Midtrans ───
        Route::get('/checkout-lomba/{registration}', function (Registration $registration) {
            if ($registration->user_id !== Auth::id()
                || $registration->status_pendaftaran !== 'pending') {
                abort(403, 'Akses tidak valid.');
            }
            return view('user.checkout-lomba-midtrans', compact('registration'));
        })->name('kompetisi.checkout');

        // ─── (C) Sistem CBT ───
        Route::prefix('ujian/{registration}')->name('ujian.')->group(function () {
            // Waiting room
            Route::get('/persiapan', [CbtController::class, 'prepare'])
                ->name('prepare');

            // API polling antrian
            Route::get('/queue-status', [CbtController::class, 'queueStatus'])
                ->name('queue-status');

            // Halaman ujian — dilindungi middleware
            Route::get('/', [CbtController::class, 'show'])
                ->middleware('check.exam.access')
                ->name('show');

            // Submit & autosave
            Route::post('/submit', [CbtController::class, 'submit'])
                ->name('submit');
            Route::post('/autosave', [CbtController::class, 'autosave'])
                ->name('autosave');
        });

        // ─── (D) Pengumuman ───
        Route::get('/pengumuman', [UserAnnouncementController::class, 'index'])
            ->name('pengumuman');
        Route::get('/pengumuman/{announcement}', [UserAnnouncementController::class, 'show'])
            ->name('pengumuman.show');

        // ─── (E) Pustaka E-Book ───
        Route::get('/pustaka', [LibraryController::class, 'index'])
            ->name('pustaka');
        Route::get('/pustaka/{id}/read', [LibraryController::class, 'read'])
            ->name('pustaka.read');
        Route::get('/pustaka/{id}/stream', [LibraryController::class, 'stream'])
            ->name('pustaka.stream');
        Route::post('/pustaka/log-security', [LibraryController::class, 'logSecurity'])
            ->name('pustaka.log');

        // ─── (F) Merchandise ───
        Route::post('/beli-merchandise', [MerchandiseTransactionController::class, 'store'])
            ->name('merchandise.beli');
    });
});

// =========================================================================
// ROUTE PROFIL (BREEZE)
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================================================================
// ROUTE AUTENTIKASI (BREEZE)
// =========================================================================
require __DIR__ . '/auth.php';