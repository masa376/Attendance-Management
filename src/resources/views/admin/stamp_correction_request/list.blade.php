@extends('layouts.app')

@section('title','申請承認一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/request.css') }}">
@endsection

@section('content')

@include('components.adminheader')
<h1>申請一覧</h1>

{{-- 承認待ち --}}
<h2>承認待ち</h2>
<table>
    <thead>
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pendingRequests as $reg)
        <tr>
            <td>承認待ち</td>
            <td>{{ $req->user->name }}</td>
            <td>{{ $req->attendance->date->isoFormat('YYYY年M月D日') }}</td>
            <td>{{ $req->note }}</td>
            <td>{{ $req->created_at->isoFormat('YYYY年M月D日') }}</td>
            <td>
                <a href="{{ route('admin.stamp_correction_request.show', $req->id) }}">詳細</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">承認待ちの申請はありません</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- 承認済み --}}
<h2>承認済み</h2>
<table>
    <thead>
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($approvedRequests as $req)
        <tr>
            <td>承認済み</td>
            <td>{{ $req->user->name }}</td>
            <td>{{ $req->attendance->date->isoFormat('YYYY年M月D日') }}</td>
            <td>{{ $req->note }}</td>
            <td>{{ $req->created_at->isoFormat('YYYY年M月D日') }}</td>
            <td>
                <a href="{{ route('admin.stamp_correction_request.show', $req->id) }}">詳細</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">承認済みの申請はありません</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection