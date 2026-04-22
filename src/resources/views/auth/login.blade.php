@extends('layouts.app')

@section('title','ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/login.css') }}">
@endsection

@section('content')

@include('components.header')
<form action="/login" method="post" class="authenticate">
    @csrf
    <h1 class="title">ログイン</h1>

    {{-- 認証失敗エラー --}}
    @if ($errors->has('email'))
        <div class="form-error">
            {{ $errors->first('email') }}
        </div>
    @endif

    <label for="mail" class="entry-name">メールアドレス</label>
    <input name="email" id="mail" type="email" class="input" value="{{ old('email') }}">
    <div class="form-error">
        @error('email')
        {{ $message }}
        @enderror
    </div>

    <label for="password" class="entry-name">パスワード</label>
    <input name="password" id="password" type="password" class="input">
    <div class="form-error">
        @error('password')
        {{ $message }}
        @enderror
    </div>
    <button class="btn btn--big">ログインする</button>
    <a href="{{ route('register') }}" class="link">会員登録はこちら</a>
</form>
@endsection