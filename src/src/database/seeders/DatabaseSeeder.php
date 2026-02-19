<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@istore.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('admin');

        $this->call(SettingsSeeder::class);
        $this->call(ProductosSeeder::class);
    }
}
