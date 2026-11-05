@extends('layouts.app')

@section('title', $role->label().' · '.config('app.name'))

@section('content')
    <h1 class="h3 mb-3">Xin chào, {{ auth()->user()->name }}</h1>
    <p class="text-muted">Bạn đang đăng nhập với vai trò <strong>{{ $role->label() }}</strong>.</p>

    <div class="alert alert-info">
        Khung trang theo vai trò. Chức năng của từng module được bổ sung theo lộ trình trong
        <code>docs/04-team/progress.md</code>.
    </div>
@endsection
