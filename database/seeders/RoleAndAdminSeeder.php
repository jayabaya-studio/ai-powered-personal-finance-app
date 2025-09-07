<?php

namespace Database\Seeders;

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

        // Buat Roles
        $roleAdmin = Role::create(['name' => 'admin']);
        $rolePremium = Role::create(['name' => 'premium']);
        $roleUser = Role::create(['name' => 'user']);

        // Buat User Admin Pertama
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@finance.test',
            'password' => Hash::make('password'), // Ganti dengan password yang aman
        ]);
        $admin->assignRole($roleAdmin);

        // Buat Contoh User Premium
        $premiumUser = User::factory()->create([
            'name' => 'Premium User',
            'email' => 'premium@finance.test',
            'password' => Hash::make('password'),
        ]);
        $premiumUser->assignRole($rolePremium);

        // Buat Contoh User Biasa
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@finance.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole($roleUser);
    }
}
