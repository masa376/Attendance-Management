<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    // 管理者ログイン画面表示
    public function showLoginForm()
    {
        return view('admin.login');
    }


    // 管理者ログイン処理
    public function login(AdminLoginRequest $request)
    {
        // ① FormRequest でバリデーション
        // ② email + password で認証を試みる
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            // ③ role が 1（管理者）かチェック
            if (Auth::user()->role !== 1) {

                // 管理者でなければ即ログアウト
                Auth::logout();
                return back()->withErrors([
                    'email' => 'ログイン情報が登録されていません',
                ]);
            }

            // ④ セッション固定化防止
            $request->session()->regenerate();

            // ⑤ 管理者トップへリダイレクト
            return redirect()->route('admin.attendance.list');
        }

        // ⑥ 認証失敗
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    // 管理者ログアウト処理
    public function logout()
    {
        Auth::logout();

        // セッションを無効化してCSRFトークンを再生成（セキュリティ）
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
