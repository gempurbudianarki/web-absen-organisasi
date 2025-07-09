<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus cache permission sebelum membuat role baru
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat role Admin, PJ, dan Anggota
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'pj']);
        Role::create(['name' => 'anggota']);
    }
}