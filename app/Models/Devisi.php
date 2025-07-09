<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devisi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_devisi',
        'deskripsi',
        'pj_id',
    ];

    /**
     * Mendefinisikan relasi bahwa Devisi ini memiliki satu Penanggung Jawab (PJ).
     * Relasi ini ke model User.
     */
    public function pj()
    {
        return $this->belongsTo(User::class, 'pj_id');
    }

    /**
     * Mendefinisikan relasi bahwa Devisi ini memiliki banyak Anggota.
     * Relasi ini ke model User.
     */
    public function anggota()
    {
        return $this->hasMany(User::class, 'devisi_id');
    }

    /**
     * Mendefinisikan relasi bahwa Devisi ini memiliki banyak Kegiatan.
     */
    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'devisi_id');
    }
}