<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\RestTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 一般ユーザー全員分の勤怠データ作成
        $users = User::where('role', 0)->get();

        foreach ($users as $user) {
            // 過去2週間分データ
            for ($i = 13; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                // 土日スキップ
                if ($date->isWeekend()) continue;

                $clockIn = $date->copy()->setTime(9, 0);
                $clockOut = $date->copy()->setTime(18, 0);

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'date' => $date->toDateString(),
                    'status' => 3, // 退勤済
                    'note' => '',
                ]);

                // 休憩レコード追加
                RestTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $date->copy()->setTime(12, 0),
                    'break_end' => $date->copy()->setTime(13, 0),
                ]);
            }
        }
    }
}
