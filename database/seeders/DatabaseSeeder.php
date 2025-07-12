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
        // Panggil seeder untuk Role dan Admin
        $this->call(RoleAndAdminSeeder::class);

        // Kamu bisa menambahkan factory untuk data dummy lain di sini jika perlu
        // User::factory(10)->create();
    }
}