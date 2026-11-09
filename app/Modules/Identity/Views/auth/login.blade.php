@extends('layouts.app')

@section('title', match ($authMode ?? 'login') {
    'forgot' => 'Quên mật khẩu · Hexa Tech Academy',
    'reset' => 'Đặt lại mật khẩu · Hexa Tech Academy',
    default => 'Đăng nhập · Hexa Tech Academy',
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
    }

    .input-icon {
        position: absolute;
        left: 18px;
        top: 50%;
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
        background:
            radial-gradient(circle at 75% 25%, #fedba2 0%, rgba(254, 219, 162, 0.9) 18%, rgba(254, 219, 162, 0) 45%),
            radial-gradient(circle at 35% 15%, #fbe7b8 0%, rgba(251, 231, 184, 0.8) 22%, rgba(251, 231, 184, 0) 50%),
            radial-gradient(circle at 15% 55%, #fce2a9 0%, rgba(252, 226, 169, 0.75) 20%, rgba(252, 226, 169, 0) 48%),
            radial-gradient(circle at 85% 75%, #fedfa6 0%, rgba(254, 223, 166, 0.7) 18%, rgba(254, 223, 166, 0) 44%),
            radial-gradient(circle at 55% 50%, #15295c 0%, #142756 35%, #0f1f45 70%),
            linear-gradient(135deg, #2b619e 0%, #1c3d70 50%, #0d1e44 100%);
        filter: blur(16px);
        transform: scale(1.1);
        z-index: 1;
    }

    /* Ambient Silk Wave Overlays */
    .silk-wave {
        position: absolute;
        inset: -20px;
        background:
            radial-gradient(ellipse at 40% 30%, rgba(254, 225, 170, 0.65) 0%, transparent 40%),
            radial-gradient(ellipse at 70% 40%, rgba(254, 215, 150, 0.5) 0%, transparent 45%),
            radial-gradient(ellipse at 50% 80%, rgba(13, 30, 68, 0.9) 0%, transparent 60%);
        filter: blur(28px);
        z-index: 2;
    }

    /* Header Nav Scrollable Bar */
    .art-nav-wrapper {
        position: relative;
        z-index: 3;
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        background: rgba(18, 32, 72, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 8px 14px 6px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    .art-nav-scroll {
        display: flex;
        align-items: center;
        gap: 10px;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        padding-bottom: 6px;
        flex: 1;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }

    /* Tinh chỉnh thanh cuộn thanh mảnh dạ quang */
    .art-nav-scroll::-webkit-scrollbar {
        height: 3px;
    }

    .art-nav-scroll::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 10px;
    }

    .art-nav-scroll::-webkit-scrollbar-thumb {
        background: #00b4d8;
        border-radius: 10px;
        box-shadow: 0 0 8px rgba(0, 180, 216, 0.8);
    }

    .art-nav-scroll a {
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        text-decoration: none;
        padding: 6px 14px;
        border-radius: 8px;
        transition: all 0.25s ease;
        flex-shrink: 0;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
        background: rgba(255, 255, 255, 0.06);
    }

    .art-nav-scroll a:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.22);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .nav-tab-active {
        background: #ffffff !important;
        color: #1a2a5e !important;
        font-weight: 800 !important;
        text-shadow: none !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
    }

    .menu-bars {
        display: flex;
        flex-direction: column;
        gap: 3.5px;
        cursor: pointer;
        padding: 6px;
        flex-shrink: 0;
    }

    .menu-bars span {
        display: block;
        width: 17px;
        height: 2px;
        background-color: #ffffff;
        border-radius: 2px;
    }

    /* Welcome Hero Text */
    .art-content {
        position: relative;
        z-index: 3;
        align-self: flex-end;
        text-align: right;
        margin-top: auto;
        margin-bottom: 25px;
        max-width: 320px;
    }

    .art-content h1 {
        font-size: 3.2rem;
        font-weight: 800;
        margin: 0 0 10px;
        letter-spacing: -0.8px;
        color: #ffffff;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .art-content p {
        font-size: 0.72rem;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.78);
        margin: 0 0 16px;
    }

    .signup-invite {
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.85);
        font-weight: 500;
    }

    .signup-invite a {
        color: #ffffff;
        font-weight: 800;
        text-decoration: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.6);
        padding-bottom: 1px;
    }

    /* Mobile Responsive */
    @media (max-width: 820px) {
        .login-card {
            flex-direction: column-reverse;
            max-width: 440px;
            border-radius: 28px;
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
            align-self: flex-start;
            text-align: left;
            margin-bottom: 10px;
        }

        .art-content h1 {
            font-size: 2.2rem;
        }
    }
</style>

<div class="login-card">
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
                           placeholder="Nhập mã sinh viên"
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
                           class="pill-input @error('password') is-invalid @enderror"
                           placeholder="Nhập mật khẩu"
                           autocomplete="current-password" required>
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
        <div class="fluid-background"></div>
        <div class="silk-wave"></div>

        <!-- Top Navigation (Academic Scrollable Tabs) -->
        <div class="art-nav-wrapper">
            <nav class="art-nav-scroll">
                <a href="#">THÔNG BÁO</a>
                <a href="#" class="nav-tab-active">ĐÀO TẠO</a>
                <a href="#">HƯỚNG DẪN</a>
                <a href="#">QUY TRÌNH - QUY ĐỊNH</a>
            </nav>
            <div class="menu-bars">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Bottom Right Welcome Hero -->
        <div class="art-content">
            <h1>Welcome.</h1>
            <p>Hệ thống Quản lý Đào tạo Hexa Tech Academy. Nền tảng học tập số thông minh và đồng bộ.</p>
            <div class="signup-invite">
                Not a member? <a href="#">Sign up now</a>
            </div>
        </div>
    </div>
</div>
@endsection
