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
        'poster', // Ini adalah path relatif ke file, contoh: 'kegiatan-posters/abc.jpg'
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
     * PERBAIKAN UTAMA: Accessor untuk mendapatkan URL penuh dari poster.
     * Ini adalah cara standar industri untuk menangani URL file.
     *
     * @return string
     */
    public function getPosterUrlAttribute(): string
    {
        // Jika ada path poster yang tersimpan di database
        if ($this->poster && Storage::disk('public')->exists($this->poster)) {
            // Kembalikan URL publik yang valid dari storage.
            return Storage::disk('public')->url($this->poster);
        }

        // Jika tidak ada poster, kembalikan URL placeholder yang dinamis.
        // Ini memastikan tampilan tidak rusak meskipun kegiatan tidak memiliki poster.
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->judul) . '&background=0D6EFD&color=fff&size=128';
    }
}