<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'pengumumans'; // <-- BARIS PERBAIKAN DI SINI

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul',
        'isi',
        'user_id',
        'devisi_id',
        'waktu_publish',
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