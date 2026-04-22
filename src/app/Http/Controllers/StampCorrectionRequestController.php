<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectRequest;

class StampCorrectionRequestController extends Controller
{
    public function userIndex()
    {
        // ログインユーザー自身の申請のみ取得
        $pendingRequests = AttendanceCorrectRequest::with('attendance')
            ->where('user_id', Auth::id())
            ->where('status', 0) // 承認待ちのみ
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedRequests = AttendanceCorrectRequest::with('attendance')
            ->where('user_id', Auth::id())
            ->where('status', 1) // 承認済
            ->orderBy('created_at', 'desc')
            ->get();

        return view('stamp_correction_request.list', [
            'pendingRequests'  => $pendingRequests,
            'approvedRequests' => $approvedRequests,
        ]);
    }
}
