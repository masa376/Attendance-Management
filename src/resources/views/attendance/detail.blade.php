@extends('layouts.app')

@section('title','勤怠詳細画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}?v={{ time() }}">
@endsection

@section('content')

@include('components.header')

<div class="detail-wrap">

    <h1 class="detail-title"><span class="detail-title__bar">|</span> 勤怠詳細</h1>

    <form method="POST" action="{{ route('attendance.update', $attendance->id) }}">
    @csrf
    <table class="detail-table">
        <tbody>

            {{-- 名前 --}}
            <tr>
                <td class="detail-table__label">名前</td>
                <td class="detail-table__value">{{ $userName }}</td>
            </tr>

            {{-- 日付 --}}
            <tr>
                <td class="detail-table__label">日付</td>
                <td class="detail-table__value">
                    {{ $attendance->date->format('Y年') }}
                    <span class="detail-table__date-gap"></span>
                    {{ $attendance->date->format('M月D日') }}
                </td>
            </tr>

            {{-- 出勤・退勤 --}}
            <tr>
                <td class="detail-table__label">出勤・退勤</td>
                <td class="detail-table__value detail-table__value--time">
                    <input type="time" class="detail-input" name="clock_in" value="{{ $attendance->clock_in->format('H:i') }}">
                    <span class="detail-table__tilde">～</span>
                    <input type="time" name="clock_out" value="{{ $attendance->clock_out?->format('H:i') ?? '' }}">
                    @error('clock_in')
                    <p class="detail-error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>


            {{-- 既存の休憩レコード（編集可）--}}
            @foreach ($restTimes as $index => $restTime)
            <tr>
                <td class="detail-table__label">休憩{{ $index + 1 }}</td>
                <td class="detail-table__value detail-table__value--time">
                    <input type="time" class="detail-input"
                    name="break_start[{{ $restTime->id }}]"
                    value="{{ $restTime->break_start->format('H:i') }}">
                    <span class="detail-table__tilde">～</span>
                    <input type="time" class="detail-input"
                    name="break_end[{{ $restTime->id }}]"
                    value="{{ $restTime->break_end?->format('H:i') ?? '' }}">
                    @error('break_start')
                    <p class="detail-error">{{ $message }}</p>
                    @enderror
                    @error('break_end')
                    <p class="detail-error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            @endforeach


            {{-- 追加の空入力フィールド --}}
            <tr>
                <td class="detail-table__label">休憩{{ count($restTimes) + 1 }}</td>
                <td class="detail-table__value detail-table__value--time">
                    <input type="time" class="detail-input" name="break_start_new">
                    <span class="detail-table__tilde">～</span>
                    <input type="time" class="detail-input" name="break_end_new">
                    @error('break_start_new')
                    <p class="detail-error">
                        {{ $message }}
                    </p>
                    @enderror
                    @error('break_end_new')
                    <p class="detail-error">
                        {{ $message }}
                    </p>
                    @enderror
                </td>
            </tr>


            {{-- 備考 --}}
            <tr>
                <td class="detail-table__label">備考</td>
                <td class="detail-table__value">
                    <textarea class="detail-textarea" name="note">{{ $attendance->note }}</textarea>
                    @error('note')
                    <p class="detail-error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>

        </tbody>
    </table>

    {{-- 修正ボタンの位置だけ切り替え --}}
    @if ($attendance->status === 4)
        <p class="detail-pending">＊承認待ちのため修正はできません。</p>
    @else
        <button type="submit" class="detail-btn">修正</button>
    @endif
    </form>
</div>

@endsection