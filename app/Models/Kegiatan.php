<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'poster',
        'devisi_id',
        'kode_absensi',
        'status_absensi', // Kolom baru ditambahkan di sini
    ];

    /**
     * Casts untuk tipe data.
     */
    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /**
     * Relasi ke model Devisi.
     */
    public function devisi()
    {
        return $this->belongsTo(Devisi::class);
    }

    /**
     * Accessor untuk mendapatkan URL penuh dari poster.
     *
     * @return string
     */
    public function getPosterUrlAttribute(): string
    {
        if ($this->poster && Storage::disk('public')->exists($this->poster)) {
            return Storage::disk('public')->url($this->poster);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->judul) . '&background=0D6EFD&color=fff&size=128';
    }
}