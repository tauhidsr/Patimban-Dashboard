<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@patimban.test'], //kunci unik
            [
                'name' => 'Admin Desa Patimban',
                'password' => Hash::make('password123'),
                'role'=> 'admin',
            ]
        );
    }
}
