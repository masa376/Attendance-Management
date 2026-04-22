<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrectRequest;

class StampCorrectionRequestController extends Controller
{
    // 申請一覧
    public function index()
    {
        $requests = AttendanceCorrectRequest::with(['user', 'attendance'])
            ->where('status', 0) // 承認待ちのみ
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.stamp_correction_request.index', [
            'requests' => $requests,
        ]);
    }

    // 申請詳細（承認画面）
    public function show($attendanceCorrectionRequestId)
    {
        $correctRequest = AttendanceCorrectRequest::with(['user', 'attendance'])
            ->findOrFail($attendanceCorrectionRequestId);

        return view('admin.stamp_correction_request.show', [
            'correctRequest' => $correctRequest,
        ]);
    }

    public function approve($attendanceCorrectionRequestId)
    {
        $correctRequest = AttendanceCorrectRequest::with('attendance')
            ->findOrFail($attendanceCorrectionRequestId);

        // ① attendance_correct_request.status を承認済（1）に更新
        $correctRequest->update(['status' => 1]);

        // ② attendance.status を承認済（5）に更新
        $correctRequest->attendance->update(['status' => 5]);

        return redirect()
            ->route('stamp_correction_request.index');
    }
}
