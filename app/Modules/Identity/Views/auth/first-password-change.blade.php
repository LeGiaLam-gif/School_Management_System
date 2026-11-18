@extends('layouts.app')

@section('title', 'Đổi mật khẩu lần đầu · Hexa Tech Academy')

@section('content')
<style>
    .first-password-page {
        min-height: 100dvh;
        padding: clamp(24px, 6vh, 64px) 16px;
        background: linear-gradient(120deg, var(--sms-shell-surface), #f8f2e9);
    }

    .first-password-page .card-header.text-danger {
        color: var(--sms-teal-dark) !important;
    }

    .first-password-page .btn-outline-danger {
        --bs-btn-color: var(--sms-teal-dark);
        --bs-btn-border-color: var(--sms-teal-dark);
        --bs-btn-hover-bg: var(--sms-teal-dark);
        --bs-btn-hover-border-color: var(--sms-teal-dark);
    }
</style>

    <div class="first-password-page">
        <div class="mx-auto sms-profile-page" style="max-width: 32rem;">
            <h1 class="h3 sms-profile-title">Đổi mật khẩu lần đầu</h1>
            <p class="text-body-secondary mb-4">
                Đổi mật khẩu để tiếp tục vào cổng thông tin đào tạo.
            </p>

            @include('identity::partials.change-password')

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary">Đăng xuất</button>
            </form>
        </div>
    </div>
@endsection