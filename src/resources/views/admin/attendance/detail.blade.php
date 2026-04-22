@extends('layouts.app')

@section('title','勤怠詳細1')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css') }}">
@endsection

@section('content')

<h1>勤怠詳細</h1>
<p>{{ $attendance->user->name }}</p>
<p>{{ $attendance->date->isoFormat('YYYY年M月D日') }}</p>

@if (session('message'))
    <p>{{ session('message') }}</p>
@endif

<form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
@csrf

<table>
    <tbody>

        {{-- 出勤時刻 --}}
        <tr>
            <td>出勤</td>
            <td>
                <input type="time" name="clock_in"
                value="{{ $attendance->clock_in->format('H:i') }}">
                @error('clock_in')
                    <p>{{ $message }}</p>
                @enderror
            </td>
        </tr>

        {{-- 退勤時刻 --}}
        <tr>
            <td>退勤</td>
            <td>
                <input type="time" name="clock_out"
                value="{{ $attendance->clock_out?->format('H:i') ?? '' }}">
            </td>
        </tr>

        {{-- 既存の休憩レコード --}}
        @foreach ($restTimes as $index => $restTime)
        <tr>
            <td>休憩{{ $index + 1 }}（開始）</td>
            <td>
                <input type="time"
                name="break_start[{{ $restTime->id }}]"
                value="{{ $restTime->break_start->format('H:i') }}">
                @error('break_start')
                    <p>{{ $message }}</p>
                @enderror
            </td>
        </tr>
        <tr>
            <td>休憩{{ $index + 1 }}（終了）</td>
            <td>
                <input type="time"
                name="break_end[{{ $restTime->id }}]"
                value="{{ $restTime->break_end?->format('H:i') ?? '' }}">
                @error('break_end')
                    <p>{{ $message }}</p>
                @enderror
            </td>
        </tr>
        @endforeach

        {{-- 追加の空入力フィールド --}}
        <tr>
            <td>休憩{{ count($restTimes) + 1 }}（開始）</td>
            <td>
                <input type="time"          name="break_start_new">
                @error('break_start_new')
                    <p>{{ $message }}</p>
                @enderror
            </td>
        </tr>
        <tr>
            <td>休憩{{ count($restTimes) + 1 }}（終了）</td>
            <td>
                <input type="time"          name="break_end_new">
                @error('break_end_new')
                    <p>{{ $message }}</p>
                @enderror
            </td>
        </tr>

        {{-- 備考 --}}
        <tr>
            <td>備考</td>
            <td>
                <textarea name="note">{{ $attendance->note }}</textarea>
                @error('note')
                    <p>{{ $message }}</p>
                @enderror
            </td>
        </tr>

    </tbody>
</table>

<button type="submit">修正</button>
</form>
@endsection