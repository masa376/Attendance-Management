<header class="header">
    <div class="header_logo">
        <a href="/"><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
    </div>

    @if (!in_array(Route::currentRouteName(), ['register', 'admin/login', 'verification.notice']) )


    <nav class="header_nav">
        <ul>
            @if (Auth::check())
            <li><a href="{{ route('admin.attendance.list') }}" class="header_button">勤怠一覧</a></li>
            <li><a href="{{ route('admin.staff.list') }}" class="header_button">スタッフ一覧</a></li>
            <li><a href="{{ route('admin.stamp_correction_request.list') }}" class="header_button">申請一覧</a></li>
            <li>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button class="header_button">ログアウト</button>
                </form>
            </li>
            @else
            <li><a href="{{ route('login') }}">ログイン</a></li>
            <li><a href="{{ route('register') }}">会員登録</a></li>
            @endif
        </ul>
    </nav>
    @endif
</header>