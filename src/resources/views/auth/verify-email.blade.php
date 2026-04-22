@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/verify.css') }}">
@endsection

@section('content')
@include('components.header')
    <h1>メールアドレスの確認</h1>

    <p>
        ご登録いただいたメールアドレスに確認メールを送信しました。
    </p>

    {{-- 再送信成功メッセージ --}}
    @if (session('status') === 'verification-link-sent')
        <p>メール認証を再送信しました。</p>
    @endif

    {{-- 認証メール再送信ボタン --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">
            認証メールを再送信する
        </button>
    </form>
@endsection

