<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil RoleSeeder yang baru kita buat
        $this->call(RoleSeeder::class);

        // Membuat satu user admin default untuk testing
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ])->assignRole('admin');

        // (Opsional) Membuat user PJ dan Anggota untuk testing
        User::factory()->create([
            'name' => 'PJ User',
            'email' => 'pj@example.com',
        ])->assignRole('pj');

        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('anggota');
        });
    }
}