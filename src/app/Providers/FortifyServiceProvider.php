<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ログアウト後のリダイレクト先を /login に上書き
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                return redirect('/login');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);


        // 会員登録画面の View を登録
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログイン画面のViewを登録
        Fortify::loginView(function () {
            return view('auth.login');
        });


        // 認証誘導画面の View を登録
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });


        Fortify::authenticateUsing(function (Request $request) {

            // ① メールアドレスで User 検索
            $user = User::where('email', $request->email)->first();

            // ② パスワードを照合して一致すれば User を返す（認証成功）
            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            // ③ 一致しない場合 ⇒ カスタムメッセージで ValidationException を投げる
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません'],
            ]);
        });
    }
}
