<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminUserSeeder::class, // 管理者（先に実行）
            UserSeeder::class, // 一般ユーザー
            AttendanceSeeder::class, // 勤怠データ
        ]);
    }
}
