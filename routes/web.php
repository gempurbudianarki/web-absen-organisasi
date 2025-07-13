<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
// Hapus 'use App\Http\Controllers\LearnerController;' karena akan kita hilangkan
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\DevisiController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;
use App\Http\Controllers\PJ\KegiatanController as PJKegiatanController;
use App\Http\Controllers\PJ\AbsensiController as PJAbsensiController;
use App\Http\Controllers\PJ\PengumumanController as PJPengumumanController;
use App\Http\Controllers\Anggota\DashboardController as AnggotaDashboardController;
use App\Http\Controllers\Anggota\AbsensiController as AnggotaAbsensiController;
use App\Http\Controllers\QrScanController; // <-- Menambahkan QrScanController

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
        if ($user->hasRole('pj')) { return redirect()->route('pj.dashboard'); }
        if ($user->hasRole('anggota')) { return redirect()->route('anggota.dashboard'); }
        if ($user->hasRole('employee')) { return redirect()->route('employee.dashboard'); }
        return abort(403);
    })->name('dashboard');

    // RUTE-RUTE YANG BERLAKU UNTUK SEMUA ROLE YANG LOGIN
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RUTE-RUTE KHUSUS ADMIN
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // --- PERBAIKAN: Menggunakan Route::resource untuk standarisasi ---
        Route::resource('users', UserController::class)->except(['create', 'store']); // Create/Store ditangani RegisterController
        Route::get('/users/{id}/qrcode', [UserController::class, 'generateQrCode'])->name('users.qrcode');
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset_password'); // Mengganti nama reset_password
        Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk_action');

        // Manajemen Devisi
        Route::post('devisi/{devisi}/add-member', [DevisiController::class, 'addMember'])->name('devisi.addMember');
        Route::post('devisi/{devisi}/remove-member/{user}', [DevisiController::class, 'removeMember'])->name('devisi.removeMember');
        Route::resource('devisi', DevisiController::class);

        // Manajemen Kegiatan
        Route::resource('kegiatan', AdminKegiatanController::class);

        // Manajemen Absensi
        Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/create', [AdminAbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/absensi', [AdminAbsensiController::class, 'store'])->name('absensi.store');
        Route::get('/absensi/export', [AdminAbsensiController::class, 'export'])->name('absensi.export');
        Route::get('/absensi/kegiatan/{kegiatan}', [AdminAbsensiController::class, 'show'])->name('absensi.show');
        Route::get('/absensi/kegiatan/{kegiatan}/qr', [AdminAbsensiController::class, 'qr'])->name('absensi.qr');
        Route::delete('/absensi/{absensi}', [AdminAbsensiController::class, 'destroy'])->name('absensi.destroy');

        // Manajemen Pengumuman
        Route::resource('pengumuman', AdminPengumumanController::class)->only(['index', 'store', 'destroy']);

        // Profile Admin
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        
        // Registrasi oleh Admin
        Route::get('/register-user', [RegisterController::class, 'showAdminRegisterForm'])->name('register.form');
        Route::post('/register-user', [RegisterController::class, 'registerByAdmin'])->name('register.user');
        Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('otp.verify.form');
        Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('otp.verify.submit');

        // Fitur Email
        Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email.logs');
        Route::get('/custom-email', [UserController::class, 'customEmailForm'])->name('email.custom.form');
        Route::post('/custom-email/send', [UserController::class, 'sendCustomEmail'])->name('email.custom.send');
    });

    // RUTE-RUTE KHUSUS PJ
    Route::prefix('pj')->name('pj.')->middleware('role:pj')->group(function () {
        // --- PERBAIKAN: Menambahkan dashboard untuk PJ ---
        Route::get('/dashboard', [\App\Http\Controllers\PJ\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('kegiatan', PJKegiatanController::class);
        Route::get('absensi/{kegiatan_id}', [PJAbsensiController::class, 'show'])->name('absensi.show');
        Route::post('absensi', [PJAbsensiController::class, 'store'])->name('absensi.store');
        Route::delete('absensi/{absensi}', [PJAbsensiController::class, 'destroy'])->name('absensi.destroy');
        Route::post('kegiatan/{kegiatan}/generate-code', [PJAbsensiController::class, 'generateCode'])->name('absensi.generate_code');
        Route::post('absensi/{kegiatan}/close', [PJAbsensiController::class, 'closeAndMarkAbsentees'])->name('absensi.close');
        Route::get('anggota', [\App\Http\Controllers\PJ\AnggotaController::class, 'index'])->name('anggota.index');
        Route::resource('pengumuman', PJPengumumanController::class)->only(['index', 'store', 'destroy']);
    });

    // RUTE-RUTE KHUSUS ANGGOTA
    Route::prefix('anggota')->name('anggota.')->middleware('role:anggota')->group(function () {
        Route::get('/dashboard', [AnggotaDashboardController::class, 'index'])->name('dashboard');
        // --- PERBAIKAN: Memisahkan logika QR dan Kode Absensi ---
        Route::get('/absensi/scan', [QrScanController::class, 'scan'])->name('absensi.scan');
        Route::post('/absensi/scan/process', [QrScanController::class, 'process'])->name('absensi.process');
        Route::get('/absensi/kode', [QrScanController::class, 'showCodeForm'])->name('absensi.kode.form');
        Route::post('/absensi/kode/process', [QrScanController::class, 'processCode'])->name('absensi.kode.process');
    });

    // Rute untuk role lain jika ada
    Route::get('/employee/dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard')->middleware('role:employee');
});

require __DIR__.'/auth.php';