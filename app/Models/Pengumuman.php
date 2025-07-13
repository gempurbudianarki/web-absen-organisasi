<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'isi',
        'user_id',
        'devisi_id',
        'waktu_publish',
    ];

    /**
     * --- PERBAIKAN DI SINI ---
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waktu_publish' => 'datetime',
    ];

    /**
     * Mendefinisikan relasi bahwa Pengumuman ini dibuat oleh seorang User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi bahwa Pengumuman ini bisa ditujukan untuk sebuah Devisi.
     */
    public function devisi()
    {
        return $this->belongsTo(Devisi::class);
    }
}