@extends('layouts.app')

@section('title','管理者 勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/list.css') }}">
@endsection

@section('content')

@include('components.admin_header')
{{-- 日付ナビゲーション --}}
<a href="{{ route('admin.attendance.list', ['date' => $prevDate] )}}">前日</a>
<span>{{ $currentDate }}</span>
<a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}">翌日</a>

<table>
    <thead>
        <tr>
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($attendances as $attendance)
        @php
            $breakMin = $attendance->getTotalBreakMinutes();
            $breakH   = intdiv($breakMin, 60);
            $breakM   = $breakMin % 60;

            $workMin  = $attendance->getTotalWorkMinutes();
            $workH    = intdiv($workMin, 60);
            $workM    = $workMin % 60;
        @endphp

        <tr>
            <td>{{ $attendance->user->name }}</td>
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
            <td colspan="6">この日の勤怠情報はありません</td>
        </tr>
        @endforelse
    </tbody>
</table>