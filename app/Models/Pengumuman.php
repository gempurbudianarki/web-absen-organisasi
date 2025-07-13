<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'user_id',
        'judul',
        'isi', // Konsisten menggunakan 'isi'
        'target',
        'devisi_id',
        'publish_at',
        'expires_at',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function devisi()
    {
        return $this->belongsTo(Devisi::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('publish_at', '<=', Carbon::now())
                     ->where(function ($q) {
                         $q->where('expires_at', '>=', Carbon::now())
                           ->orWhereNull('expires_at');
                     });
    }

    public function scopeRiwayat($query)
    {
        return $query->where('expires_at', '<', Carbon::now());
    }
}