<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // <-- Hanya 'name' yang dipertahankan untuk nama lengkap.
        'email',
        'password',
        'devisi_id',
        
        // --- Kolom yang sudah tidak relevan dihapus dari sini ---
        'grade_level',
        'section',
        'qr_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mendefinisikan relasi bahwa User ini milik satu Devisi.
     */
    public function devisi()
    {
        return $this->belongsTo(Devisi::class);
    }

    /**
     * Mendefinisikan relasi jika User ini adalah PJ untuk sebuah Devisi.
     */
    public function devisiYangDipimpin()
    {
        return $this->hasOne(Devisi::class, 'pj_id');
    }

    /**
     * Define the relationship to attendance records.
     * A user can have many attendance records.
     */
    public function attendance()
    {
        return $this->hasMany(LearnerAttendance::class);
    }
}