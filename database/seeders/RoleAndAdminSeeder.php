<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'pj']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'anggota']);

        // Create default admin user
        $admin = User::create([
            'name' => 'Admin LEMS',
            'email' => 'admin@lems.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // default password
        ]);

        // Assign 'admin' role to the user
        $admin->assignRole('admin');
    }
}