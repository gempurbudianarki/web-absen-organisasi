<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\LearnerAttendanceController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Admin\DevisiController;
use App\Http\Controllers\PJ\KegiatanController; // <-- 1. Tambahkan impor ini

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
        if ($user->hasRole('pj')) { return redirect()->route('pj.kegiatan.index'); } // Diarahkan ke daftar kegiatan
        if ($user->hasRole('employee')) { return redirect()->route('employee.dashboard'); }
        if ($user->hasRole('anggota') || $user->hasRole('learner')) { return redirect()->route('learner.dashboard'); }
        return abort(403);
    })->name('dashboard');

    // RUTE-RUTE YANG BERLAKU UNTUK BANYAK ROLE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Employee & Learner Dashboards
    Route::get('/employee/dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard');
    Route::get('/learner/dashboard', [LearnerController::class, 'index'])->name('learner.dashboard');

    // --- GRUP KHUSUS ADMIN ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        Route::resource('devisi', DevisiController::class);
        
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/register-user', [RegisterController::class, 'showAdminRegisterForm'])->name('register.form');
        Route::post('/register-user', [RegisterController::class, 'registerByAdmin'])->name('register.user');
    });
    
    // --- GRUP KHUSUS PJ ---
    Route::prefix('pj')->name('pj.')->group(function () {
        Route::resource('kegiatan', KegiatanController::class);
    });

    // --- GRUP UNTUK FITUR BERSAMA (DIAKSES ADMIN) ---
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/sendmail', [UserController::class, 'sendMail'])->name('users.sendmail');
    Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email.logs');
    Route::get('/custom-email', [UserController::class, 'customEmailForm'])->name('email.custom.form');
    Route::post('/custom-email/send', [UserController::class, 'sendCustomEmail'])->name('email.custom.send');

    // --- RUTE UNTUK PROSES OTP (TIDAK PERLU PREFIX) ---
    Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('admin.otp.verify.form');
    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('admin.otp.verify.submit');

    // Fitur lain yang mungkin akan dipakai oleh banyak role
    Route::get('attendance', [LearnerAttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::post('attendance/store', [LearnerAttendanceController::class, 'store'])->name('admin.attendance.store');
    Route::post('attendance/lookup-learner', [LearnerAttendanceController::class, 'lookupLearner'])->name('admin.attendance.lookup-learner');
    
    Route::prefix('admin/announcements')->name('admin.announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::get('/send', [AnnouncementController::class, 'sendForm'])->name('sendForm');
        Route::post('/send', [AnnouncementController::class, 'processSend'])->name('processSend');
        Route::get('/{id}/send', [AnnouncementController::class, 'send'])->name('send');
        Route::get('/logs', [AnnouncementController::class, 'logs'])->name('logs');
    });

    Route::resource('learners', LearnerController::class)->names('admin.learners');
    Route::delete('/learners/{id}', [LearnerController::class, 'destroy'])->name('learners.destroy');
});


require __DIR__.'/auth.php';