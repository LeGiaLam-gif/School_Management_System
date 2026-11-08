@php
    $user = auth()->user();
    $dashboard = match (true) {
        $user->isAdmin() => ['route' => 'admin.home', 'label' => 'Trang quản trị'],
        $user->isLecturer() => ['route' => 'lecturer.home', 'label' => 'Trang giảng viên'],
        $user->isStudent() => ['route' => 'student.home', 'label' => 'Trang sinh viên'],
        default => null,
    };
    $studentNavigation = [
        ['route' => 'student.curriculum', 'label' => 'Chương trình đào tạo', 'active' => 'student.curriculum*', 'icon' => 'book'],
        ['route' => 'student.enrollment', 'label' => 'Đăng ký học phần', 'active' => 'student.enrollment*', 'icon' => 'edit'],
        ['route' => 'student.timetable', 'label' => 'Thời khóa biểu', 'active' => 'student.timetable*', 'icon' => 'calendar'],
        ['route' => 'student.grades', 'label' => 'Điểm & GPA', 'active' => 'student.grades*', 'icon' => 'chart'],
        ['route' => 'student.attendance', 'label' => 'Chuyên cần', 'active' => 'student.attendance*', 'icon' => 'check'],
        ['route' => 'student.announcements', 'label' => 'Thông báo', 'active' => 'student.announcements*', 'icon' => 'bell'],
    ];
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
        @if ($user->isStudent())
            @if (Route::has('student.profile'))
                <a
                    class="sms-sidebar-link {{ request()->routeIs('student.profile*') ? 'active' : '' }}"
                    href="{{ route('student.profile') }}"
                >
                    <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <circle cx="12" cy="8" r="4" />
                        <path d="M4 21a8 8 0 0 1 16 0" />
                    </svg>
                    Hồ sơ cá nhân
                </a>
            @endif

            @foreach ($studentNavigation as $item)
                @php($routeAvailable = Route::has($item['route']))
                @if ($routeAvailable)
                    <a
                        class="sms-sidebar-link {{ request()->routeIs($item['active']) ? 'active' : '' }}"
                        href="{{ route($item['route']) }}"
                    >
                @else
                    <span class="sms-sidebar-link sms-sidebar-link-disabled" aria-disabled="true">
                @endif
                        <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            @switch($item['icon'])
                                @case('book')
                                    <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 0 4 22zM4 5.5v16M8 7h8M8 11h8" />
                                    @break
                                @case('edit')
                                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z" />
                                    @break
                                @case('calendar')
                                    <rect x="3" y="5" width="18" height="16" rx="2" />
                                    <path d="M16 3v4M8 3v4M3 10h18" />
                                    @break
                                @case('chart')
                                    <path d="M4 20V10M10 20V4M16 20v-7M22 20H2" />
                                    @break
                                @case('check')
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="m8 12 3 3 5-6" />
                                    @break
                                @case('bell')
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
                                    @break
                            @endswitch
                        </svg>
                        {{ $item['label'] }}
                @if ($routeAvailable)
                    </a>
                @else
                    </span>
                @endif
            @endforeach
        @else
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
        @endif
    </nav>
    <div class="sms-sidebar-footer">
        <span>Hệ thống quản lý đào tạo</span>
        <strong>Hexa Tech Academy</strong>
    </div>
</aside>
