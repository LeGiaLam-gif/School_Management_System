@php
    $user = auth()->user();
    $dashboard = match (true) {
        $user->isAdmin() => ['route' => 'admin.home', 'label' => 'Trang quản trị'],
        $user->isLecturer() => ['route' => 'lecturer.home', 'label' => 'Trang giảng viên'],
        $user->isStudent() => ['route' => 'student.home', 'label' => 'Trang sinh viên'],
        default => null,
    };
@endphp

<nav class="border rounded bg-white p-3" aria-label="Điều hướng chính">
    <div class="nav nav-pills flex-column gap-1">
        @if ($dashboard)
            <a
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs($dashboard['route']) ? 'active' : 'text-dark' }}"
                href="{{ route($dashboard['route']) }}"
            >
                <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M3 10.5 12 3l9 7.5" />
                    <path d="M5.5 9.5V21h13V9.5M9 21v-7h6v7" />
                </svg>
                {{ $dashboard['label'] }}
            </a>
        @endif
        <a
            class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('profile.show') ? 'active' : 'text-dark' }}"
            href="{{ route('profile.show') }}"
        >
            <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 21a8 8 0 0 1 16 0" />
            </svg>
            Hồ sơ cá nhân
        </a>
    </div>
</nav>
