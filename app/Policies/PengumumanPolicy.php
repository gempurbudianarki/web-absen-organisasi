<?php

namespace App\Policies;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PengumumanPolicy
{
    /**
     * Memberikan akses penuh kepada admin untuk semua aksi.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pengumuman $pengumuman): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Semua PJ dan Admin bisa membuat pengumuman
        return $user->hasRole(['admin', 'pj']);
    }

    /**
     * Determine whether the user can update the model.
     * User hanya bisa update jika dia adalah pemilik pengumuman tersebut.
     */
    public function update(User $user, Pengumuman $pengumuman): bool
    {
        return $user->id === $pengumuman->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * User hanya bisa delete jika dia adalah pemilik pengumuman tersebut.
     */
    public function delete(User $user, Pengumuman $pengumuman): bool
    {
        return $user->id === $pengumuman->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pengumuman $pengumuman): bool
    {
        return $user->id === $pengumuman->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pengumuman $pengumuman): bool
    {
        return $user->id === $pengumuman->user_id;
    }
}