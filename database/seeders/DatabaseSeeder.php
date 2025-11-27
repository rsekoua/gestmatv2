<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();



        $this->call([
            MaterielTypeSeeder::class,
            AccessorySeeder::class,
            RolePermissionSeeder::class,
            // ServiceSeeder::class,
            // EmployeeSeeder::class,
            // MaterielSeeder::class,
        ]);
        $user = User::create([
            'name' => 'Administrateur Principal',
            'email' => 'admin@dap-ci.org',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('super_admin');
    }
}
