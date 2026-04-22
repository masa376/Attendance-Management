<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrectRequest;

class AdminStampCorrectionRequestController extends Controller
{
    public function list()
    {
        // 全ユーザーの未承認申請取得
        $pendingRequests = AttendanceCorrectRequest::with(['user', 'attendance'])
            ->where('status', 0) // 承認待ちのみ
            ->orderBy('created_at', 'desc')
            ->get();

        // 承認済み
        $approvedRequests = AttendanceCorrectRequest::with(['user', 'attendance'])
            ->where('status', 1) // 承認済みのみ
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.stamp_correction_request.list', [
            'pendingRequests'  => $pendingRequests,
            'approvedRequests' => $approvedRequests,
        ]);
    }


    public function show($attendanceCorrectRequestId)
    {
        $correctRequest = AttendanceCorrectRequest::with(['user', 'attendance.restTimes'])
            ->findOrFail($attendanceCorrectRequestId);

        return view('admin.stamp_correction_request.show', [
            'correctRequest' => $correctRequest,
        ]);
    }

    public function approve($attendanceCorrectRequestId)
    {
        $correctRequest = AttendanceCorrectRequest::with('attendance')
            ->findOrFail($attendanceCorrectRequestId);

        // ① 申請レコード承認済 (1) に更新
        $correctRequest->update(['status' => 1]);

        // ② 勤怠レコードを承認済 (5) に更新
        $correctRequest->attendance->update(['status' => 5]);

        return redirect()->route('admin.stamp_correction_request.list');
    }
}
