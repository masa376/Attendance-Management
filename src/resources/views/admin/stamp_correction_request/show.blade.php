@extends('layouts.app')

@section('title','申請承認詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/show.css') }}">
@endsection

@section('content')

@include('components.adminheader')
<h1>修正申請詳細</h1>

@if (session('message'))
    <p>{{ session('message') }}</p>
@endif

<table>
    <tbody>
        {{-- 申請者名 --}}
        <tr>
            <td>名前</td>
            <td>{{ $correctRequest->user->name }}</td>
        </tr>

        {{-- 対象日付 --}}
        <tr>
            <td>日付</td>
            <td>{{ $correctRequest->attendance->date->isoFormat('YYYY年M月D日') }}</td>
        </tr>

        {{-- 修正後の出勤時刻 --}}
        <tr>
            <td>出勤</td>
            <td>{{ $correctRequest->clock_in->format('H:i') }}</td>
        </tr>

        {{-- 修正後の退勤時刻 --}}
        <tr>
            <td>退勤</td>
            <td>{{ $correctRequest->clock_out?->format('H:i') ?? '' }}</td>
        </tr>

        {{-- 元の休憩レコード --}}
        @foreach ($correctRequest->attendance->restTimes as $index => $restTime)
        <tr>
            <td>休憩{{ $index + 1 }}（開始）</td>
            <td>{{ $restTime->break_start->format('H:i') }}</td>
        </tr>
        <tr>
            <td>休憩{{ $index + 1 }}（終了）</td>
            <td>{{ $restTime->break_end?->format('H:i') ?? '' }}</td>
        </tr>
        @endforeach

        {{-- 申請理由（備考）--}}
        <tr>
            <td>備考</td>
            <td>{{ $correctRequest->note }}</td>
        </tr>

        {{-- 申請日時 --}}
        <tr>
            <td>申請日時</td>
            <td>{{ $correctRequest->created_at->isoFormat('YYYY年M月D日') }}</td>
        </tr>
    </tbody>
</table>

{{-- 承認ボタン（承認待ちのときのみ表示）--}}
@if ($correctRequest->status === 0)
    <form method="POST"
            action="{{ route('admin.stamp_correction_request.approve', $correctRequest->id) }}">
        @csrf
        <button type="submit">承認</button>
    </form>
@else
    <p>承認済み</p>
@endif

@endsection