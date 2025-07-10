<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\DevisiController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\PJ\KegiatanController as PJKegiatanController;
use App\Http\Controllers\PJ\AbsensiController as PJAbsensiController;
use App\Http\Controllers\PJ\AnggotaController as PJAnggotaController; // <-- Tambahkan ini
use App\Http\Controllers\QrScanController;

/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->middleware('guest');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('admin')) { return redirect()->route('admin.dashboard'); }
        if ($user->hasRole('pj')) { return redirect()->route('pj.kegiatan.index'); }
        if ($user->hasRole('anggota')) { return redirect()->route('anggota.dashboard'); }
        return abort(403);
    })->name('dashboard');

    // RUTE-RUTE YANG BERLAKU UNTUK BANYAK ROLE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rute untuk dashboard anggota
    Route::get('/anggota/dashboard', function() {
        return view('learner.dashboard');
    })->name('anggota.dashboard');

    // --- RUTE UNTUK ABSENSI ANGGOTA ---
    Route::get('/scan-qr', [QrScanController::class, 'scan'])->name('absensi.scan');
    Route::post('/process-scan', [QrScanController::class, 'process'])->name('absensi.process');
    Route::get('/kode-absensi', [QrScanController::class, 'showCodeForm'])->name('absensi.kode.form');
    Route::post('/process-kode', [QrScanController::class, 'processCode'])->name('absensi.kode.process');


    // --- GRUP KHUSUS ADMIN ---
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        Route::get('absensi/{kegiatan}/qr', [AdminAbsensiController::class, 'showQr'])->name('absensi.qr');

        Route::resource('devisi', DevisiController::class);
        Route::resource('kegiatan', AdminKegiatanController::class);
        Route::resource('absensi', AdminAbsensiController::class);
        Route::resource('pengumuman', PengumumanController::class);
        
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/register-user', [RegisterController::class, 'showAdminRegisterForm'])->name('register.form');
        Route::post('/register-user', [RegisterController::class, 'registerByAdmin'])->name('register.user');
        
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/sendmail', [UserController::class, 'sendMail'])->name('users.sendmail');
        Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email.logs');
        Route::get('/custom-email', [UserController::class, 'customEmailForm'])->name('email.custom.form');
        Route::post('/custom-email/send', [UserController::class, 'sendCustomEmail'])->name('email.custom.send');
    });
    
    // --- GRUP KHUSUS PJ ---
    Route::prefix('pj')->name('pj.')->middleware('role:pj')->group(function () {
        Route::resource('kegiatan', PJKegiatanController::class);
        Route::resource('absensi', PJAbsensiController::class);
        Route::post('absensi/{kegiatan}/generate-code', [PJAbsensiController::class, 'generateCode'])->name('absensi.generate_code');
        Route::get('anggota', [PJAnggotaController::class, 'index'])->name('anggota.index'); // <-- BARIS BARU DI SINI
    });

    // --- RUTE UNTUK PROSES OTP (TIDAK PERLU PREFIX) ---
    Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('admin.otp.verify.form');
    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('admin.otp.verify.submit');

});


require __DIR__.'/auth.php';