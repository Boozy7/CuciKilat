<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun petugas demo untuk pengembangan lokal & pengujian.
        User::updateOrCreate(
            ['email' => 'admin@cucikilat.local'],
            [
                'name' => 'Petugas CuciKilat',
                'password' => 'password',
            ],
        );
    }
}
