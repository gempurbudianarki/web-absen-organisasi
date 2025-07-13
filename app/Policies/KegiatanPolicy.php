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
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Menentukan apakah seorang user (PJ) dapat mengelola (edit, update, destroy, absensi)
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
            // PERBAIKAN LOGIKA:
            // PJ boleh mengelola jika:
            // 1. Kegiatan tersebut adalah milik devisinya.
            // ATAU
            // 2. Kegiatan tersebut adalah kegiatan umum (devisi_id nya null).
            return $user->devisiYangDipimpin?->id === $kegiatan->devisi_id || is_null($kegiatan->devisi_id);
        }

        // Jika bukan admin atau PJ, secara default tidak boleh.
        return false;
    }
}