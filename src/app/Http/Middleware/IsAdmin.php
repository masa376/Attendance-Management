<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // ログイン済 かつ role = 1（管理者）のみ通す
        if (Auth::check() && Auth::user()->role === 1) {
            return $next($request);
        }

        // 管理者でない場合は管理者ログイン画面へ
        return redirect()->route('admin.login');
    }
}
