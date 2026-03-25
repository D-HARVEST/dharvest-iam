<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RoleSeeder::class);

        $admin = User::updateOrCreate([

            'email' => 'admin@admin.com',
        ], [
            'email_verified_at' => now(),
            'name' => 'Admin',
            'password' => bcrypt('p@ssw0rd'),
            "uid" => Str::uuid()->toString(),
        ]);

        $admin->assignRole('Super-admin');
        setPermissionsTeamId(null);
    }
}
