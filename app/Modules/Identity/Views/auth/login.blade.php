@extends('layouts.app')

@section('title', match ($authMode ?? 'login') {
    'forgot' => 'Quên mật khẩu',
    'reset' => 'Đặt lại mật khẩu',
    default => 'Đăng nhập',
})

@section('content')
<style>
    * {
        box-sizing: border-box;
    }

    body {
        background: radial-gradient(circle at 10% 20%, #e0c3fc 0%, #8ec5fc 50%, #e0c3fc 90%),
                    linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: 0;
        padding: 30px 16px;
        position: relative;
        overflow-x: hidden;
    }

    /* Vibrant ambient glowing aura */
    body::before {
        content: "";
        position: fixed;
        width: 650px;
        height: 650px;
        top: -120px;
        left: -120px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 154, 162, 0.45) 0%, rgba(255, 183, 178, 0.15) 50%, transparent 70%);
        filter: blur(40px);
        pointer-events: none;
        z-index: 0;
    }

    body::after {
        content: "";
        position: fixed;
        width: 700px;
        height: 700px;
        bottom: -150px;
        right: -100px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, rgba(168, 85, 247, 0.2) 50%, transparent 70%);
        filter: blur(50px);
        pointer-events: none;
        z-index: 0;
    }

    body:has(.login-card--feature) {
        background:
            radial-gradient(ellipse at top left, #f3ebdc 0%, transparent 55%),
            linear-gradient(135deg, #e8eef2 0%, #d9e3ea 100%);
    }

    body:has(.login-card--feature)::before,
    body:has(.login-card--feature)::after {
        content: none;
    }

    /* Main Modal Card */
    .login-card {
        width: 100%;
        max-width: 980px;
        min-height: 560px;
        background: #ffffff;
        border-radius: 36px;
        box-shadow: 0 30px 70px rgba(60, 50, 120, 0.25), 0 10px 25px rgba(0, 0, 0, 0.08);
        display: flex;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .login-card--feature {
        width: min(1280px, calc(100vw - 64px));
        max-width: none;
        min-height: 0;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(340px, 37%);
        grid-template-areas: "news form";
        align-items: stretch;
        gap: clamp(20px, 3vw, 36px);
        background: transparent;
        border-radius: 0;
        box-shadow: none;
        overflow: visible;
    }

    .login-card--feature > .form-side,
    .login-card--feature > .art-side {
        border-radius: 24px;
        box-shadow: 0 16px 36px rgba(31, 39, 76, 0.2);
    }

    .login-card--feature > .form-side {
        grid-area: form;
        width: 100%;
        min-width: 0;
        min-height: 480px;
    }

    .login-card--feature > .art-side {
        grid-area: news;
        min-width: 0;
        padding: 0;
    }

    /* Left Form Side (White) */
    .form-side {
        width: 38%;
        min-width: 340px;
        background: #ffffff;
        padding: 44px 44px 34px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        z-index: 2;
    }

    /* Top Logo */
    .brand-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-img-logo {
        width: 38px;
        height: 38px;
        object-fit: contain;
        border-radius: 8px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
    }

    .brand-text .title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1b2559;
        letter-spacing: -0.2px;
    }

    .brand-text .subtitle {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1b2559;
    }

    /* Avatar Circle with HTA */
    .avatar-container {
        display: flex;
        justify-content: center;
        margin: 18px 0 22px;
    }

    .avatar-circle {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        border: 2.5px solid #273873;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #273873;
        font-weight: 800;
        font-size: 1.5rem;
        letter-spacing: 1.5px;
        background: #ffffff;
        transition: transform 0.25s ease, border-color 0.25s ease;
    }

    .avatar-circle:hover {
        transform: scale(1.05);
        border-color: #1e2b5e;
    }

    .auth-form-title {
        color: #1b2559;
        font-size: 1.05rem;
        font-weight: 800;
        margin: 0 0 6px;
        text-align: center;
    }

    .auth-form-description {
        color: #636b8a;
        font-size: 0.75rem;
        line-height: 1.45;
        margin: 0 0 16px;
        text-align: center;
    }

    /* Pill-Shaped Form Controls */
    .input-pill-wrapper {
        position: relative;
        margin-bottom: 15px;
        --input-height: 44px;
    }

    .input-icon {
        position: absolute;
        left: 18px;
        top: calc(var(--input-height) / 2);
        transform: translateY(-50%);
        color: #273873;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }

    .input-icon svg {
        width: 17px;
        height: 17px;
    }

    .pill-input {
        width: 100%;
        height: 44px;
        border-radius: 50px;
        border: 1.8px solid #273873;
        padding-left: 46px;
        padding-right: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.8px;
        color: #1e2850;
        outline: none;
        background: transparent;
        transition: all 0.2s ease;
    }

    .pill-input::placeholder {
        color: #636b8a;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .pill-input:focus {
        border-color: #1a2552;
        box-shadow: 0 0 0 3px rgba(39, 56, 115, 0.15);
    }

    .pill-input.is-invalid {
        border-color: #dc3545;
    }

    .login-password-input {
        padding-right: 52px;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: calc(var(--input-height) / 2);
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: transparent;
        color: #273873;
        cursor: pointer;
    }

    .password-toggle:focus-visible {
        outline: 2px solid #273873;
        outline-offset: 2px;
    }

    .password-toggle svg {
        width: 19px;
        height: 19px;
    }

    .password-toggle .eye-slash {
        display: none;
    }

    .password-toggle[aria-pressed="true"] .eye-slash {
        display: block;
    }

    /* Submit Button */
    .btn-submit-pill {
        width: 100%;
        height: 44px;
        border-radius: 50px;
        background: #273873;
        border: none;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.85rem;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-top: 5px;
        box-shadow: 0 6px 16px rgba(39, 56, 115, 0.35);
    }

    .btn-submit-pill:hover {
        background: #1d2b59;
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(39, 56, 115, 0.45);
    }

    /* Auxiliary Links */
    .aux-links {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 14px;
        padding: 0 4px;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        user-select: none;
        margin: 0;
    }

    .dot-check {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #273873;
        display: inline-block;
    }

    .remember-me span {
        font-size: 0.72rem;
        color: #4b526d;
        font-weight: 600;
    }

    .forgot-link {
        font-size: 0.72rem;
        color: #4b526d;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }

    .forgot-link:hover {
        color: #273873;
        text-decoration: underline;
    }

    /* 3 dots indicator at bottom */
    .dots-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 7px;
        margin-top: 18px;
    }

    .dot-item {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #273873;
    }

    /* Right Artwork Side (Fluid Gradient Mesh) */
    .art-side {
        flex: 1;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 34px 44px;
        overflow: hidden;
        color: #ffffff;
        background: #1e3a6f;
    }

    /* Fluid Glowing Mesh using layered CSS gradients matching the design */
    .fluid-background {
        position: absolute;
        inset: 0;
        transform: scale(1.1);
        z-index: 1;
        background:
            radial-gradient(circle at 75% 25%, #fedba2 0%, rgba(254, 219, 162, 0.9) 18%, rgba(254, 219, 162, 0) 45%),
            radial-gradient(circle at 35% 15%, #fbe7b8 0%, rgba(251, 231, 184, 0.8) 22%, rgba(251, 231, 184, 0) 50%),
            radial-gradient(circle at 15% 55%, #fce2a9 0%, rgba(252, 226, 169, 0.75) 20%, rgba(252, 226, 169, 0) 48%),
            radial-gradient(circle at 85% 75%, #fedfa6 0%, rgba(254, 223, 166, 0.7) 18%, rgba(254, 223, 166, 0) 44%),
            radial-gradient(circle at 55% 50%, #15295c 0%, #142756 35%, #0f1f45 70%),
            linear-gradient(135deg, #2b619e 0%, #1c3d70 50%, #0d1e44 100%);
        filter: blur(16px);
    }

    /* Ambient Silk Wave Overlays */
    .silk-wave {
        position: absolute;
        inset: -20px;
        z-index: 2;
        background:
            radial-gradient(ellipse at 40% 30%, rgba(254, 225, 170, 0.65) 0%, transparent 40%),
            radial-gradient(ellipse at 70% 40%, rgba(254, 215, 150, 0.5) 0%, transparent 45%),
            radial-gradient(ellipse at 50% 80%, rgba(13, 30, 68, 0.9) 0%, transparent 60%);
        filter: blur(28px);
    }

    .art-news {
        position: relative;
        z-index: 3;
        flex: 1;
        background: #f3f8f7;
        color: #18324b;
    }

    .art-news-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px 16px 12px;
        border-bottom: 1px solid #dce9e7;
    }

    .art-news-icon {
        display: grid;
        place-items: center;
        flex: 0 0 36px;
        height: 36px;
        border-radius: 10px;
        background: #e0f0ed;
        color: #176d70;
    }

    .art-news-header h2 {
        margin: 0;
        color: #176d70;
        font-size: clamp(1.05rem, 1.9vw, 1.22rem);
        font-weight: 800;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .art-news-item {
        display: grid;
        grid-template-columns: 36px minmax(0, 1fr);
        align-items: start;
        gap: 12px;
        margin: 12px;
        padding: 12px;
        border: 1px solid #e1eae9;
        border-radius: 13px;
        background: #fff;
    }

    .art-news-item--pinned {
        border-color: #dac49a;
        background: #fffbf2;
    }

    .art-news-item--pinned .art-news-icon {
        background: #f4e7cc;
        color: #735222;
    }

    .art-news-item h3 {
        margin: 0 0 5px;
        font-size: 1rem;
        font-weight: 800;
    }

    .art-news-item p {
        margin: 0;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    .art-news-item .art-news-scope {
        margin-bottom: 5px;
        color: #176d70;
        font-weight: 700;
    }

    /* Welcome Hero Text */
    .art-content {
    position: relative;
    z-index: 3;
    align-self: flex-end;
    width: fit-content;
    max-width: 100%;
    margin-top: auto;
    text-align: left;
    }

    .art-content h1 {
        margin: 0 0 12px;
        color: #fff;
        font-size: clamp(1.6rem, 3vw, 2.5rem);
        font-weight: 800;
        text-wrap: balance;
    }

    .art-content p {
        margin: 0;
        color: #f3f4f8;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .art-content p + p {
        margin-top: 8px;
    }

    /* Mobile Responsive */
    @media (max-width: 820px) {
        .login-card {
            flex-direction: column-reverse;
            max-width: 440px;
            border-radius: 28px;
        }

        .login-card--feature {
            width: min(440px, calc(100vw - 32px));
            grid-template-columns: minmax(0, 1fr);
            grid-template-areas: "form" "news";
            gap: 16px;
        }

        .login-card--feature > .form-side {
            min-height: 0;
        }

        .form-side {
            width: 100%;
            padding: 34px 28px;
        }

        .art-side {
            min-height: 250px;
            padding: 24px;
        }

        .art-content {
            width: 100%;
            margin-top: 16px;
            text-align: left;
        }

        .art-content h1 {
            font-size: clamp(1.25rem, 5vw, 1.65rem);
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .art-content p {
            font-size: 0.85rem;
            line-height: 1.5;
            text-wrap: pretty;
        }
    }

</style>

<div class="login-card{{ ($authMode ?? 'login') === 'login' ? ' login-card--feature' : '' }}">
    <!-- Left: Form side (White) -->
    <div class="form-side">
        <!-- Top Brand / Logo -->
        <div class="brand-header">
            <img src="{{ asset('images/logo.png') }}" alt="Hexa Tech Academy Logo" class="brand-img-logo">
            <div class="brand-text">
                <span class="title">Hexa Tech</span>
                <span class="subtitle">Academy</span>
            </div>
        </div>

        <!-- Center Avatar with HTA -->
        <div>
            <div class="avatar-container">
                <div class="avatar-circle">
                    HTA
                </div>
            </div>

            <!-- Form -->
            @if (($authMode ?? 'login') === 'forgot')
                <h1 class="auth-form-title">Quên mật khẩu</h1>
                <p class="auth-form-description">Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu.</p>
                <form method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf

                    <div class="input-pill-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <path d="m3 7 9 6 9-6"></path>
                            </svg>
                        </span>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               class="pill-input @error('email') is-invalid @enderror"
                               placeholder="Nhập email đã đăng ký"
                               autocomplete="email" autofocus required>
                        @error('email')
                            <div class="text-danger small mt-1 ps-3" style="font-size: 0.75rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit-pill">GỬI LIÊN KẾT ĐẶT LẠI MẬT KHẨU</button>
                    <div class="aux-links">
                        <a href="{{ route('login') }}" class="forgot-link">Quay lại</a>
                    </div>
                </form>
            @elseif (($authMode ?? 'login') === 'reset')
                <h1 class="auth-form-title">Đặt lại mật khẩu</h1>
                <p class="auth-form-description">Tạo mật khẩu mới để tiếp tục sử dụng tài khoản.</p>
                <form method="POST" action="{{ route('password.update') }}" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="input-pill-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <path d="m3 7 9 6 9-6"></path>
                            </svg>
                        </span>
                        <input id="email" name="email" type="email"
                               value="{{ old('email', $email ?? '') }}"
                               class="pill-input @error('email') is-invalid @enderror"
                               placeholder="Email đã đăng ký"
                               autocomplete="email" autofocus required>
                        @error('email')
                            <div class="text-danger small mt-1 ps-3" style="font-size: 0.75rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="input-pill-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input id="password" name="password" type="password"
                               class="pill-input @error('password') is-invalid @enderror"
                               placeholder="Mật khẩu mới (ít nhất 8 ký tự)"
                               autocomplete="new-password" required>
                        @error('password')
                            <div class="text-danger small mt-1 ps-3" style="font-size: 0.75rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="input-pill-wrapper">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               class="pill-input"
                               placeholder="Xác nhận mật khẩu mới"
                               autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn-submit-pill">ĐẶT LẠI MẬT KHẨU</button>
                    <div class="aux-links">
                        <a href="{{ route('login') }}" class="forgot-link">Quay lại</a>
                    </div>
                </form>
            @else
            <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                @csrf

                <!-- Username Input -->
                <div class="input-pill-wrapper">
                    <span class="input-icon">
                        <!-- User SVG Icon -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input id="login" name="login" type="text"
                           value="{{ old('login') }}"
                           class="pill-input @error('login') is-invalid @enderror"
                           placeholder="Nhập email hoặc mã tài khoản"
                           autocomplete="username" autofocus required>
                    @error('login')
                        <div class="text-danger small mt-1 ps-3" style="font-size: 0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="input-pill-wrapper">
                    <span class="input-icon">
                        <!-- Lock SVG Icon -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input id="password" name="password" type="password"
                           class="pill-input login-password-input @error('password') is-invalid @enderror"
                           placeholder="Nhập mật khẩu"
                           autocomplete="current-password" required>
                    <button type="button" class="password-toggle" aria-label="Hiện mật khẩu"
                            aria-controls="password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                            <path class="eye-slash" d="m3 3 18 18"></path>
                        </svg>
                    </button>
                    @error('password')
                        <div class="text-danger small mt-1 ps-3" style="font-size: 0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit-pill">LOGIN</button>

                <!-- Forgot password -->
                <div class="aux-links">
                    <a href="{{ route('password.request') }}" class="forgot-link">Quên mật khẩu?</a>
                </div>
            </form>
            @endif
        </div>

        <!-- 3 Dots Indicator -->
        <div class="dots-indicator">
            <span class="dot-item"></span>
            <span class="dot-item"></span>
            <span class="dot-item"></span>
        </div>
    </div>

    <!-- Right: Fluid Liquid Gradient Artwork (Blue & Cream) -->
    <div class="art-side">
        @if (($authMode ?? 'login') === 'login')
            <section class="art-news" aria-labelledby="art-news-title">
                <header class="art-news-header">
                    <span class="art-news-icon" aria-hidden="true">
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                            <path d="m3 10 13-5v14L3 14v-4Zm4 6 2 5h3l-2-4m9-5h3m-3-4 2-2m-2 10 2 2" />
                        </svg>
                    </span>
                    <h2 id="art-news-title">Tin tức &amp; Thông báo</h2>
                </header>

                @foreach ([
                    ['Cổng thông tin đào tạo', null, 'Dành cho sinh viên, giảng viên và quản trị viên của Hexa Tech Academy. Sử dụng tài khoản do nhà trường cấp để đăng nhập.'],
                    ['Chào mừng học kỳ mới', 'Toàn trường', 'Sinh viên đăng ký học phần trước hạn chót theo kế hoạch của phòng đào tạo.'],
                    ['Kiểm tra thời khóa biểu trước khi đến lớp', 'Nhắc sinh viên', 'Đăng nhập bằng tài khoản sinh viên và mở mục “Thời khóa biểu” để xem lịch học theo tuần.'],
                    ['Sử dụng email đã đăng ký để đặt lại mật khẩu', 'Hỗ trợ tài khoản', 'Chọn “Quên mật khẩu?” dưới nút đăng nhập và nhập email đã đăng ký.'],
                ] as [$title, $label, $body])
                    <article @class(['art-news-item', 'art-news-item--pinned' => $loop->first])>
                        <span class="art-news-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                                @if ($loop->first)
                                    <path d="M16 3 8 11l-4 1 8 8 1-4 8-8-5-5ZM8 16l-5 5" />
                                @else
                                    <path d="M6 3h8l4 4v14H6zM14 3v5h4M9 12h6m-6 4h6" />
                                @endif
                            </svg>
                        </span>
                        <div>
                            <h3>{{ $title }}</h3>
                            @if ($label)
                                <p class="art-news-scope">{{ $label }}</p>
                            @endif
                            <p>{{ $body }}</p>
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <div class="fluid-background"></div>
            <div class="silk-wave"></div>
            <div class="art-content">
                <h1>Cổng thông tin đào tạo</h1>
                <p>Dành cho sinh viên, giảng viên và quản trị viên của Hexa Tech Academy</p>
                <p>Sử dụng tài khoản do nhà trường cấp để đăng nhập</p>
            </div>
        @endif
    </div>
</div>
<script>
    document.querySelectorAll('.password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.getAttribute('aria-controls'));
            const showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(showPassword));
            button.setAttribute('aria-label', showPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
        });
    });
</script>
@endsection
