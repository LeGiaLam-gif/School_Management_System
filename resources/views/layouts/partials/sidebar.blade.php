@php
    $user = auth()->user();
    $dashboard = match (true) {
        $user->isAdmin() => ['route' => 'admin.home', 'label' => 'Trang quản trị'],
        $user->isLecturer() => ['route' => 'lecturer.home', 'label' => 'Trang giảng viên'],
        $user->isStudent() => ['route' => 'student.home', 'label' => 'Trang sinh viên'],
        default => null,
    };
@endphp

<aside id="app-sidebar" class="sms-sidebar" x-show="sidebarOpen" x-cloak>
    <a class="sms-sidebar-brand" href="{{ route('home') }}" aria-label="Hexa Tech Academy - trang chủ">
        <img src="{{ asset('images/logo.png') }}" alt="" class="sms-logo">
        <span>Hexa Tech <strong>Academy</strong></span>
    </a>
    <nav class="sms-sidebar-nav" aria-label="Điều hướng chính">
        <div class="sms-sidebar-nav-heading">
            <button
                class="sms-sidebar-toggle"
                type="button"
                @click="sidebarOpen = !sidebarOpen"
                :aria-expanded="sidebarOpen.toString()"
                aria-controls="app-sidebar"
                aria-label="Ẩn hoặc hiện thanh điều hướng"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M3 6h18M3 12h18M3 18h18" />
                </svg>
            </button>
        </div>
        @if ($dashboard)
            <a
                class="sms-sidebar-link {{ request()->routeIs($dashboard['route']) ? 'active' : '' }}"
                href="{{ route($dashboard['route']) }}"
            >
                <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <rect x="3" y="3" width="8" height="8" rx="2" />
                    <rect x="13" y="3" width="8" height="5" rx="2" />
                    <rect x="13" y="10" width="8" height="11" rx="2" />
                    <rect x="3" y="13" width="8" height="8" rx="2" />
                </svg>
                {{ $dashboard['label'] }}
            </a>
        @endif
        <a
            class="sms-sidebar-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"
            href="{{ route('profile.show') }}"
        >
            <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 21a8 8 0 0 1 16 0" />
            </svg>
            Hồ sơ cá nhân
        </a>
    </nav>
    <div class="sms-sidebar-footer">
        <span>Hệ thống quản lý đào tạo</span>
        <strong>Hexa Tech Academy</strong>
    </div>
</aside>
