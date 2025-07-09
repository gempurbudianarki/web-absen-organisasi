<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
     * Mendefinisikan relasi bahwa Kegiatan ini milik satu Devisi.
     */
    public function devisi()
    {
        return $this->belongsTo(Devisi::class);
    }
}