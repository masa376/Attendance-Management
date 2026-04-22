<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['name' => '山田太郎', 'email' => 'yamada@example.com'],
            ['name' => '鈴木花子', 'email' => 'suzuki@example.com'],
            ['name' => '田中次郎', 'email' => 'tanaka@example.com'],
        ];

        foreach ($users as $user) {
                User::firstOrCreate(
                ['email' => $user['email']], // 検索条件
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => 0, // 一般ユーザー
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
