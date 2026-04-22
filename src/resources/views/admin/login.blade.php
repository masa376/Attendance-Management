@extends('layouts.app')

@section('title','管理者 ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin.login.css') }}">
@endsection

@section('content')

@include('components.adminheader')
    {{-- 認証失敗エラー --}}
    @if ($errors->has('email'))
        <p>{{ $errors->first('email') }}</p>
    @endif

<form action="{{ route('admin.login.post') }}" method="POST">
    @csrf
    <h1 class="title">管理者ログイン</h1>

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
        {{ @message }}
        @enderror
    </div>
    <button class="btn btn--big">管理者ログインする</button>
</form>