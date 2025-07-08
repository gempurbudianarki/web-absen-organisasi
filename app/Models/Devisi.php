<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_devisi',
        'deskripsi',
        'pj_id',
    ];

    public function pj()
    {
        return $this->belongsTo(User::class, 'pj_id');
    }

    public function anggota()
    {
        return $this->hasMany(User::class, 'devisi_id');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'devisi_id');
    }
}