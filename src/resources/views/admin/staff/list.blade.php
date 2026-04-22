@extends('layouts.app')

@section('title','スタッフ一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/stafflist.css') }}">
@endsection

@section('content')

@include('components.adminheader')
<h1>スタッフ一覧</h1>

<table>
    <thead>
        <tr>
            <th>氏名</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <a href="{{ route('admin.attendance.staff', $user->id) }}">詳細</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3">スタッフが登録されていません</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection