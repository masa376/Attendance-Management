@extends('layouts.app')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}?v={{ time() }}">
@endsection

@section('content')

@include('components.header')

<div class="list-wrap">

    <h1 class="list-title"><span class="list-title__bar">|</span>勤怠一覧</h1>

    {{-- 月ナビゲーション --}}
    <div class="list-nav">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="list-nav__prev">← 前月</a>
        <span class="list-nav__current">
            <img src="{{ asset('img/calendar.png') }}" alt=""> {{-- カレンダーアイコン --}}
            {{ $currentMonth }}
        </span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="list-nav__next">翌月 →</a>
    </div>

    {{-- テーブル --}}
    <table class="list-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>実働</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($attendances as $attendance)
            @php
                $breakMin = $attendance->getTotalBreakMinutes();
                $breakH   = intdiv($breakMin, 60);
                $breakM   = $breakMin % 60;

                $workMin  = $attendance->getTotalWorkMinutes();
                $workH    = intdiv($workMin, 60);
                $workM    = $workMin % 60;
            @endphp
            <tr>
                <td>{{ $attendance->date->isoFormat('MM/DD(ddd)') }}</td>
                <td>{{ $attendance->clock_in->format('H:i') }}</td>
                <td>{{ $attendance->clock_out?->format('H:i') ?? '' }}</td>

                <td>
                    @if ($breakMin > 0)
                        {{ $breakH }}:{{ sprintf('%02d', $breakM) }}
                    @endif
                </td>
                <td>
                    @if ($workMin > 0)
                        {{ $workH }}:{{ sprintf('%02d', $workM) }}
                    @endif
                </td>
                <td>
                    <a href="{{ route('attendance.detail', $attendance->id) }}" class="list-table__link">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection