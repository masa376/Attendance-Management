<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceUpdateRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function list(Request $request)
    {
        // date パラメータがあればその日、なければ今日
        $current = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::now();

        $attendances = Attendance::with(['user', 'restTimes'])
            ->whereDate('date', $current->toDateString())
            ->orderBy('user_id', 'asc')
            ->get();

        return view('admin.attendance.list', [
            'attendances' => $attendances,
            'currentDate' => $current->isoFormat('YYYY年M月D日'),
            'prevDate' => $current->copy()->subDay()->format('Y-m-d'),
            'nextDate' => $current->copy()->addDay()->format('Y-m-d'),
        ]);
    }

    public function detail($id)
    {
        $attendance = Attendance::with(['user', 'restTimes'])
            ->findOrFail($id);

        $restTimes = $attendance->restTimes()
            ->orderBy('break_start', 'asc')
            ->get();

        return view('admin.attendance.detail', [
            'attendance' => $attendance,
            'restTimes'  => $restTimes,
        ]);
    }

    public function update(AttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // 出勤・退勤・備考を更新
        $attendance->update([
            'clock_in'  => $attendance->date->format('Y-m-d') . ' ' . $request->clock_in,
            'clock_out' => $request->clock_out
                            ? $attendance->date->format('Y-m-d') . ' ' . $request->clock_out
                            : null,
            'note'      => $request->note,
        ]);

        // 既存の休憩レコードを更新
        foreach ($request->break_start ?? [] as $restId => $breakStart) {
            RestTime::where('id', $restId)
                    ->where('attendance_id', $attendance->id)
                    ->update([
                        'break_start' => $attendance->date->format('Y-m-d') . ' ' . $breakStart,
                        'break_end'   => $request->break_end[$restId]
                                            ? $attendance->date->format('Y-m-d') . ' ' . $request->break_end[$restId]
                                            : null,
                    ]);
        }

        // 新規休憩レコードを追加
        if ($request->filled('break_start_new')) {
            RestTime::create([
                'attendance_id' => $attendance->id,
                'break_start'   => $attendance->date->format('Y-m-d') . ' ' . $request->break_start_new,
                'break_end'     => $request->break_end_new
                                    ? $attendance->date->format('Y-m-d') . ' ' . $request->break_end_new
                                    : null,
            ]);
        }

        return redirect()->route('admin.attendance.detail', $id);
    }


    public function staffList(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $current = $request->filled('month')
            ? Carbon::parse($request->month)
            : Carbon::now();

        $attendance = Attendance::with('restTimes')
            ->where('user_id', $id)
            ->whereYear('date', $current->year)
            ->whereMonth('date', $current->month)
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.attendance.staff', [
            'user'         => $user,
            'attendance'  => $attendances,
            'currentMonth' => $current->isoFormat('YYYY年M月'),
            'prevMonth'    => $current->copy()->subMonth()->format('Y-m'),
            'nextMonth'    => $current->copy()->addMonth()->format('Y-m'),
        ]);
    }


    public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $current = $request->filled('month')
            ? Carbon::parse($request->month)
            : Carbon::now();

        $attendances = Attendance::with('restTimes')
            ->where('user_id', $id)
            ->whereYear('date', $current->year)
            ->whereMonth('date', $current->month)
            ->orderBy('date', 'asc')
            ->get();

        // CSV ファイル名
        $fileName = $user->name . '_' . $current->format(Y年m月) . '_勤怠.csv';

        // レスポンスヘッダー
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filleName . '"',
        ];

        // CSV本体作成
        $callback = function () use ($attendance, $user) {
            $file = fopen('php://output', 'w');

            // BOM (Excelで文字化け防止)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ヘッダー行
            fputcsv($file, ['日付', '出勤', '退勤', '休憩', '合計']);

            // データ行
            foreach ($attendances as $attendance) {
                $breakMin = $attendance->getTotalBreakMinutes();
                $breakH   = intdiv($breakMin, 60);
                $breakM   = $breakMin % 60;

                $workMin  = $attendance->getTotalWorkMinutes();
                $workH    = intdiv($workMin, 60);
                $workM    = $workMin % 60;

                fputcsv($file, [
                    $attendance->date->isoFormat('M月D日'),
                    $attendance->clock_in->format('H:i'),
                    $attendance->clock_out?->format('H:i') ?? '',
                    $breakMin > 0 ? $breakH . ':' . sprintf('%02d', $breakM) : '',
                    $workMin  > 0 ? $workH  . ':' . sprintf('%02d', $workM)  : '',
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
