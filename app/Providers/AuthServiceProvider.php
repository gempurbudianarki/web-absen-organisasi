<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Kegiatan;
use App\Models\Pengumuman; // <-- Tambahkan ini
use App\Policies\KegiatanPolicy;
use App\Policies\PengumumanPolicy; // <-- Tambahkan ini

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Kegiatan::class => KegiatanPolicy::class,
        Pengumuman::class => PengumumanPolicy::class, // <-- Tambahkan baris ini
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}