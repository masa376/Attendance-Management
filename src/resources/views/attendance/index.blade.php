@extends('layouts.app')

@section('title','勤怠打刻画面')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css') }}?V={{ time() }}">
@endsection

@section('content')

@include('components.header')

<div class="attendance-wrap">
    @if (session('message'))
        <p class="flash-message">{{ session('message') }}</p>
    @endif

    {{-- ステータス表示 --}}
    @switch($status)
        @case('勤務外')
            <span class="status status--off">勤務外</span>
            @break
        @case('出勤中')
            <span class="status status--in">出勤中</span>
            @break
        @case('休憩中')
            <span class="status status--break">休憩中</span>
            @break
        @case('退勤済')
            <span class="status status--out">退勤済</span>
            @break
    @endswitch

    {{-- 日時表示 --}}
    <p class="attendance-date">{{ $date }} {{ $dow }}</p>
    <p class="attendance-time">{{ $time }}</p>


    {{-- ステータス別ボタン表示 --}}
    @if ($status === '勤務外')
        <form method="POST" action="{{ route('attendance.clockIn')}}">
            @csrf
            <button type="submit" class="attendance-btn">出勤</button>
        </form>
    @endif

    @if ($status === '出勤中')
        <div class="attendance-btn-wrap">
            <form method="POST" action="{{ route('attendance.clockOut') }}">
                @csrf
                <button type="submit" class="attendance-btn">退勤</button>
            </form>

            <form method="POST" action="{{ route('attendance.breakStart') }}">
                @csrf
                <button type="submit" class="attendance-btn attendance-btn--sub">休憩入</button>
            </form>
        </div>
    @endif

    @if ($status === '休憩中')
        <form method="POST" action="{{ route('attendance.breakEnd') }}">
            @csrf
            <button type="submit" class="attendance-btn attendance-btn--sub">休憩戻</button>
        </form>
    @endif

    @if ($status === '退勤済')
        @if (session('message'))
            <p class="attendance-finish">{{ session('message') }}</p>
        @endif
    @endif
</div>
@endsection