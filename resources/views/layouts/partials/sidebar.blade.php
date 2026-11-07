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
    <h2 class="h6 text-uppercase text-muted mb-3">Menu</h2>
    <div class="nav nav-pills flex-column gap-1">
        @if ($dashboard)
            <a
                class="nav-link {{ request()->routeIs($dashboard['route']) ? 'active' : 'text-dark' }}"
                href="{{ route($dashboard['route']) }}"
            >
                {{ $dashboard['label'] }}
            </a>
        @endif
        <a
            class="nav-link {{ request()->routeIs('profile.show') ? 'active' : 'text-dark' }}"
            href="{{ route('profile.show') }}"
        >
            Hồ sơ cá nhân
        </a>
    </div>
</nav>
