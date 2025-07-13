<?php

namespace App\Policies;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KegiatanPolicy
{
    use HandlesAuthorization;

    /**
     * Otorisasi super-admin untuk bisa melakukan segalanya.
     * Jika user memiliki role 'admin', semua pengecekan lain akan di-bypass.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Menentukan apakah seorang user (PJ) dapat mengelola (edit, update, destroy)
     * sebuah kegiatan.
     *
     * @param  \App\Models\User  $user  User yang sedang login
     * @param  \App\Models\Kegiatan  $kegiatan  Kegiatan yang ingin dikelola
     * @return bool
     */
    public function manage(User $user, Kegiatan $kegiatan): bool
    {
        // Pengecekan hanya untuk PJ
        if ($user->hasRole('pj')) {
            // User boleh mengelola kegiatan JIKA ID devisi yang dia pimpin
            // sama dengan ID devisi pemilik kegiatan tersebut.
            return $user->devisiYangDipimpin?->id === $kegiatan->devisi_id;
        }

        // Jika bukan admin atau PJ, secara default tidak boleh.
        return false;
    }
}