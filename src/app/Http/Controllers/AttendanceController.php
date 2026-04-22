<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\RestTime;
use App\Models\AttendanceCorrectRequest;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // 本日の勤怠レコード取得 （なければ null）
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $now->toDateString())
            ->first();

        // ステータス判定
        if ($attendance === null) {
            $status = '勤務外';  // 本日レコードなし
        } elseif ($attendance->clock_out !== null) {
            $status = '退勤済';  // 退勤時刻入力済
        } elseif ($attendance->restTimes()
                ->whereNull('break_end')
                ->exists()) {

            $status = '休憩中';
            // 終了していない休憩レコードあり
        } else {
            $status = '出勤中';  // 上記以外 ⇒ 出勤中
        }

        return view('attendance.index', [
            'date'   => $now->isoFormat('YYYY年M月D日'),
            // 例：2026年3月10日

            'dow'    => $now->isoFormat('(ddd)'),
            // 例: (火)

            'time'   => $now->format('H:i'),
            // 例: 14：30

            'status' => $status,  //ステータス文字列
        ]);
    }


    public function clockIn()
    {
        $today = Carbon::now()->toDateString();

        // 本日すでにレコードがあれば何もしない
        $exists = Attendance::where('user_id', Auth::id())
                            ->where('date', $today)
                            ->exists();

        if ($exists) {
            return redirect()->route('attendance.index');
        }

        // レコード作成
        Attendance::create([
            'user_id'  => Auth::id(),
            'clock_in' => Carbon::now(),
            'date'     => $today,
        ]);

        return redirect()->route('attendance.index');
    }


    public function breakStart()
    {
        $today = Carbon::now()->toDateString();

        // 本日の勤怠レコード取得
        $attendance = Attendance::where('user_id', Auth::id())
                                ->where('date', $today)
                                ->first();

        // 出勤中でなければ何もしない（直接POSTへの防御）
        if (!$attendance || $attendance->clock_out !== null) {
            return redirect()->route('attendance.index');
        }

        // すでに休憩中なら何もしない
        if ($attendance->restTimes()->whereNull('break_end')->exists()) {
            return redirect()->route('attendance.index');
        }

        // 休憩レコードを新規作成（何回でも作れる）
        RestTime::create([
            'attendance_id' => $attendance->id,
            'break_start'   => Carbon::now(),
        ]);

        return redirect()->route('attendance.index');
    }


    public function breakEnd()
    {
        $today = Carbon::now()->toDateString();

        $attendance = Attendance::where('user_id', Auth::id())
                                ->where('date', $today)
                                ->first();

        // 休憩中でなければ何もしない（直接POSTへの防御）
        $restTime = $attendance?->restTimes()
                                ->whereNull('break_end')
                                ->first();

        if (!$restTime) {
            return redirect()->route('attendance.index');
        }

        // 対象の休憩レコードに break_end を記録
        $restTime->update([
            'break_end' => Carbon::now(),
        ]);

        return redirect()->route('attendance.index');
    }


    public function clockOut()
    {
        $today = Carbon::now()->toDateString();

        $attendance = Attendance::where('user_id', Auth::id())
                                ->where('date', $today)
                                ->first();

        // すでに退勤済、または出勤レコードなければ弾く
        if (!$attendance || $attendance->clock_out !== null) {
            return redirect()->route('attendance.index');
        }

        $attendance->update([
            'clock_out' => Carbon::now(),
        ]);

        return redirect()->route('attendance.index')
            ->with('message', 'お疲れ様でした。');
    }



    public function list(Request $request)
    {
        // month パラメータがあればその月、なければ当月
        $current = $request->filled('month')
            ? Carbon::parse($request->month)
            : Carbon::now();

        $attendances = Attendance::where('user_id', Auth::id())
            ->whereYear('date', $current->year)
            ->whereMonth('date', $current->month)
            ->orderBy('date', 'asc')
            // 日付昇順
            ->with('restTimes')
            ->get();

        return view('attendance.list', [
            'attendances'  => $attendances,
            'currentMonth' => $current->isoFormat('YYYY年M月'),
            'prevMonth'    => $current->copy()->subMonth()->format('Y-m'),
            'nextMonth'    => $current->copy()->addMonth()->format('Y-m'),
        ]);
    }


    public function detail($id)
    {
        // 自分のレコードのみ取得
        $attendance = Attendance::where('id', $id)
                                ->where('user_id', Auth::id())
                                ->firstOrFail();

        // 紐づく休憩レコードを全件取得（作成順）
        $restTimes = $attendance->restTimes()
                                ->orderBy('break_start', 'asc')
                                ->get();

        return view('attendance.detail', [
            'attendance' => $attendance,
            'restTimes'  => $restTimes,
            'userName'   => Auth::user()->name,
        ]);
    }

    public function update(AttendanceUpdateRequest $request, $id)
    {
        // 自分のレコードのみ更新可
        $attendance = Attendance::where('id', $id)
                                ->where('user_id', Auth::id())
                                ->firstOrFail();

        // 承認待ち（status=4）なら修正不可
        if ($attendance->status === 4) {
            return redirect()->route('attendance.detail', $id)
                ->with('error', '承認待ちのため修正はできません。');
        }

        // 修正レコード作成
        AttendanceCorrectRequest::updateOrCreate(
            ['attendance_id' => $attendance->id],

            [
                'user_id' => Auth::id(),
                'clock_in' => $attendance->date->format('Y-m-d') . ' ' . $request->clock_in,
                'clock_out' => $request->clock_out ? $attendance->date->format('Y-m-d') . ' ' . $request->clock_out : null,
                'note' => $request->note,
                'status' => 0, // 承認待ち
            ]
        );

        // 出勤・退勤・備考の修正保存 + status を「承認待ち」
        $attendance->update([
            'clock_in'  => $request->clock_in,
            'clock_out' => $request->clock_out,
            'note'      => $request->note,
            'status'    => 4,
        ]);

        // 既存の休憩レコード更新
        foreach ($request->break_start ?? [] as $restId => $breakStart) {
            RestTime::where('id', $restId)
                    ->where('attendance_id', $attendance->id)
                    ->update([
                        'break_start' => $breakStart,
                        'break_end'   => $request->break_end[$restId] ?? null,
                    ]);
        }

        // ③ 新規休憩レコードを追加
        if ($request->filled('break_start_new')) {
            RestTime::create([
                'attendance_id' => $attendance->id,
                'break_start'   => $request->break_start_new,
                'break_end'     => $request->break_end_new ?? null,
            ]);
        }

        return redirect()->route('attendance.detail', $id);
    }
}
