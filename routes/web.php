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
use App\Http\Controllers\PJ\AnggotaController as PJAnggotaController;
use App\Http\Controllers\PJ\LaporanController as PJLaporanController;
use App\Http\Controllers\Anggota\DashboardController as AnggotaDashboardController;
use App\Http\Controllers\Anggota\AbsensiController as AnggotaAbsensiController;
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
        Route::resource('users', UserController::class)->except(['create', 'store']); 
        Route::get('/users/{id}/qrcode', [UserController::class, 'generateQrCode'])->name('users.qrcode');
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset_password'); 
        Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk_action');
        Route::post('devisi/{devisi}/add-member', [DevisiController::class, 'addMember'])->name('devisi.addMember');
        Route::post('devisi/{devisi}/remove-member/{user}', [DevisiController::class, 'removeMember'])->name('devisi.removeMember');
        Route::resource('devisi', DevisiController::class);
        Route::resource('kegiatan', AdminKegiatanController::class);
        Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/create', [AdminAbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/absensi', [AdminAbsensiController::class, 'store'])->name('absensi.store');
        Route::get('/absensi/export', [AdminAbsensiController::class, 'export'])->name('absensi.export');
        Route::get('/absensi/kegiatan/{kegiatan}', [AdminAbsensiController::class, 'show'])->name('absensi.show');
        Route::get('/absensi/kegiatan/{kegiatan}/qr', [AdminAbsensiController::class, 'qr'])->name('absensi.qr');
        Route::delete('/absensi/{absensi}', [AdminAbsensiController::class, 'destroy'])->name('absensi.destroy');
        Route::resource('pengumuman', AdminPengumumanController::class)->only(['index', 'store', 'destroy']);
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/register-user', [RegisterController::class, 'showAdminRegisterForm'])->name('register.form');
        Route::post('/register-user', [RegisterController::class, 'registerByAdmin'])->name('register.user');
        Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('otp.verify.form');
        Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('otp.verify.submit');
        Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email.logs');
        Route::get('/custom-email', [UserController::class, 'customEmailForm'])->name('email.custom.form');
        Route::post('/custom-email/send', [UserController::class, 'sendCustomEmail'])->name('email.custom.send');
    });

    // RUTE-RUTE KHUSUS PJ
    Route::prefix('pj')->name('pj.')->middleware('role:pj')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\PJ\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('kegiatan', PJKegiatanController::class);
        Route::get('absensi/{kegiatan}', [PJAbsensiController::class, 'show'])->name('absensi.show');
        Route::post('absensi', [PJAbsensiController::class, 'store'])->name('absensi.store');
        Route::delete('absensi/{absensi}', [PJAbsensiController::class, 'destroy'])->name('absensi.destroy');
        Route::post('absensi/{kegiatan}/buka', [PJAbsensiController::class, 'bukaSesi'])->name('absensi.buka');
        Route::post('absensi/{kegiatan}/tutup', [PJAbsensiController::class, 'tutupSesi'])->name('absensi.tutup');
        Route::get('anggota', [PJAnggotaController::class, 'index'])->name('anggota.index');
        Route::get('anggota/register', [PJAnggotaController::class, 'showRegisterForm'])->name('anggota.register');
        Route::post('anggota/register', [PJAnggotaController::class, 'registerMember'])->name('anggota.store');
        Route::post('anggota/remove/{user}', [PJAnggotaController::class, 'removeMember'])->name('anggota.remove');
        Route::resource('pengumuman', PJPengumumanController::class)->only(['index', 'store', 'destroy']);
        Route::get('laporan-absensi', [PJLaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan-absensi/export', [PJLaporanController::class, 'export'])->name('laporan.export');
    });

    // RUTE-RUTE KHUSUS ANGGOTA
    Route::prefix('anggota')->name('anggota.')->middleware('role:anggota')->group(function () {
        Route::get('/dashboard', [AnggotaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/absensi/scan', [QrScanController::class, 'scan'])->name('absensi.scan');
        Route::post('/absensi/scan/process', [QrScanController::class, 'process'])->name('absensi.process');
        Route::get('/absensi/kode', [AnggotaAbsensiController::class, 'showCodeForm'])->name('absensi.form');
        Route::post('/absensi/kode/process', [AnggotaAbsensiController::class, 'processCode'])->name('absensi.process.code');
    });

    // Rute untuk role lain jika ada
    Route::get('/employee/dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard')->middleware('role:employee');
});

require __DIR__.'/auth.php';