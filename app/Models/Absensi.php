<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'kegiatan_id',
        'status',
        'waktu_absen',
        'keterangan',
    ];

    /**
     * Mendefinisikan relasi bahwa Absensi ini milik seorang User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi bahwa Absensi ini tercatat untuk sebuah Kegiatan.
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}