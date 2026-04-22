@extends('layouts.app')

@section('title','スタッフ別月次')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/staff.css') }}">
@endsection

@section('content')

@include('components.adminheader')
<h1>{{ $user->name }}の勤怠一覧</h1>

{{-- 月ナビゲーション --}}
<a href="{{ route('admin.attendance.staff', [$user->id, 'month' => $prevMonth]) }}">前月</a>
<span>{{ $currentMonth }}</span>
<a href="{{ route('admin.attendance.staff', [$user->id, 'month' => $nextMonth]) }}">次月</a>

<table>
    <thead>
        <tr>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($attendance as $attendance)
        @php
            $breakMin = $attendance->getTotalBreakMinutes();
            $breakH   = intdiv($breakMin, 60);
            $breakM   = $breakMin % 60;

            $workMin  = $attendance->getTotalWorkMinutes();
            $workH    = intdiv($workMin, 60);
            $workM    = $workMin % 60;
        @endphp
        <tr>
            <td>{{ $attendance->date->isoFormat('M月D日') }}</td>
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
                <a href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">この月の勤怠情報はありません</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- CSV出力ボタン --}}
<a href="{{ route('admin.attendance.staff.csv', [$user->id, 'month' => request('month')]) }}">
    CSV出力
</a>
@endsection