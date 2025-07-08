<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function devisi()
    {
        return $this->belongsTo(Devisi::class);
    }
}