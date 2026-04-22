<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminStampCorrectionRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// ゲスト専用（未ログイン）⇒ ログイン済なら HOME へ
Route::middleware(['guest'])->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])
        ->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'Login'])
        ->name('admin.login.post');
});



// 一般ユーザー専用（ログイン済 + メール認証済）
Route::middleware(['auth', 'verified'])->group(function () {
    // 打刻画面
    Route::get('/attendance',[AttendanceController::class, 'index'])
        ->name('attendance.index');

    // 各ステータスボタン
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])
        ->name('attendance.clockIn');

    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])
        ->name('attendance.clockOut');

    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])
        ->name('attendance.breakStart');

    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])
        ->name('attendance.breakEnd');


    // 勤怠一覧画面
    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');


    // 勤怠詳細画面
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])
        ->name('attendance.detail');

    // 勤怠詳細画面（編集）
    Route::post('/attendance/detail/{id}', [AttendanceController::class, 'update'])
        ->name('attendance.update');


    // 申請一覧画面
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'userIndex'])
        ->name('stamp_correction_request.list');

});



// 管理者専用（ログイン済 + role=1）
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {

    // 管理者 勤怠一覧
    Route::get('/attendance/list', [AdminAttendanceController::class, 'list'])
        ->name('attendance.list');

    // 管理者 勤怠詳細
    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'detail'])
        ->name('attendance.detail');

    // 管理者 勤怠更新
    Route::post('/attendance/{id}', [AdminAttendanceController::class, 'update'])
        ->name('attendance.update');

    // 管理者 スタッフ一覧
    Route::get('/staff/list', [AdminUserController::class, 'list'])
        ->name('staff.list');


    // ユーザー別月次勤怠一覧
    Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'staffList'])
        ->name('attendance.staff');

    // CSV出力
    Route::get('/attendance/staff/{id}/csv', [AdminAttendanceController::class, 'exportCsv'])
        ->name('attendance.staff.csv');


    // 管理者 申請一覧
    Route::get('/stamp_correction_request/list', [AdminStampCorrectionRequestController::class, 'list'])
        ->name('stamp_correction_request.list');

    // 申請詳細
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminStampCorrectionRequestController::class, 'show'])
        ->name('stamp_correction_request.show');

    // 承認処理
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminStampCorrectionRequestController::class, 'approve'])
        ->name('stamp_correction_request.approve');
});


// 管理者 申請承認ルート
Route::middleware(['auth', 'is_admin'])->prefix('stamp_correction_request')->name('stamp_correction_request.')->group(function () {

    // 申請一覧
    Route::get('/', [StampCorrectionRequestController::class, 'index'])
        ->name('index');

    // 申請詳細（管理者 承認画面）
    Route::get('approve/{attendance_correct_request_id}', [StampCorrectionRequestController::class, 'show'])
        ->name('show');

    // 承認処理
    Route::post('approve/{attendance_correct_request_id}', [StampCorrectionRequestController::class, 'approve'])
        ->name('approve');
});



// 管理者ログアウト（auth 必須）
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');