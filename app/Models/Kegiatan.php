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
     * Jika tidak ada poster, kembalikan URL placeholder.
     */
    public function getPosterUrlAttribute()
    {
        if ($this->poster) {
            // Mengembalikan URL publik dari storage
            return Storage::url($this->poster);
        }

        // URL placeholder jika tidak ada gambar
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->judul) . '&background=random&size=128';
    }
}