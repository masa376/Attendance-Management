@extends('layouts.app')

@section('title','会員登録')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/register.css') }}">
@endsection

@section('content')

@include('components.header')
<form method="POST" action="{{ route('register') }}" class="authenticate">
    @csrf
    <h1 class="title">会員登録</h1>
    <label for="name" class="entry-name">名前</label>
    <input name="name" id="name" type="text" class="input" value="{{ old('name') }}">
    <div class="form-error">
        @error('name')
        {{ $message }}
        @enderror
    </div>

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

    <label for="password-confirm" class="entry-name">パスワード確認</label>
    <input name="password-confirmation" id="password-confirm" type="password" class="input">

    <button class="btn btn-big">登録する</button>
    <a href="{{ route('login') }}" class="link">ログインはこちら</a>
</form>
@endsection