@php
    $user = auth()->user();
    $dashboard = match (true) {
        $user->isAdmin() => ['route' => 'admin.home', 'label' => 'Trang quản trị'],
        $user->isLecturer() => ['route' => 'lecturer.home', 'label' => 'Trang giảng viên'],
        $user->isStudent() => ['route' => 'student.home', 'label' => 'Trang sinh viên'],
        default => null,
    };
    $navigation = match (true) {
        $user->isAdmin() => [
            ['route' => 'admin.users.index', 'active' => 'admin.users.*', 'label' => 'Người dùng', 'icon' => 'users'],
            ['route' => 'admin.departments.index', 'active' => 'admin.departments.*', 'label' => 'Khoa', 'icon' => 'building'],
            ['route' => 'admin.programs.index', 'active' => 'admin.programs.*', 'label' => 'Chương trình đào tạo', 'icon' => 'organization'],
            ['route' => 'admin.courses.index', 'active' => 'admin.courses.*', 'label' => 'Học phần', 'icon' => 'book'],
            ['route' => 'admin.semesters.index', 'active' => 'admin.semesters.*', 'label' => 'Học kỳ', 'icon' => 'calendar'],
            ['route' => 'admin.class-sections.index', 'active' => 'admin.class-sections.*', 'label' => 'Lớp học phần', 'icon' => 'layers'],
            ['route' => 'admin.reports.index', 'active' => 'admin.reports.*', 'label' => 'Thống kê & báo cáo', 'icon' => 'chart'],
        ],
        $user->isLecturer() => [
            ['route' => 'lecturer.classes', 'active' => 'lecturer.classes*', 'label' => 'Lớp được phân công', 'icon' => 'book'],
            ['route' => 'lecturer.students', 'active' => 'lecturer.students*', 'label' => 'Danh sách sinh viên', 'icon' => 'users'],
            ['route' => 'lecturer.timetable', 'active' => 'lecturer.timetable*', 'label' => 'Lịch dạy', 'icon' => 'calendar'],
            ['route' => 'lecturer.attendance', 'active' => 'lecturer.attendance*', 'label' => 'Điểm danh', 'icon' => 'check'],
            ['route' => 'lecturer.grades', 'active' => 'lecturer.grades*', 'label' => 'Quản lý điểm', 'icon' => 'chart'],
            ['route' => 'lecturer.announcements', 'active' => 'lecturer.announcements*', 'label' => 'Thông báo', 'icon' => 'bell'],
            ['route' => 'lecturer.reports', 'active' => 'lecturer.reports*', 'label' => 'Báo cáo & xuất dữ liệu', 'icon' => 'report'],
        ],
        $user->isStudent() => [
            ['route' => 'student.curriculum', 'active' => 'student.curriculum*', 'label' => 'Chương trình đào tạo', 'icon' => 'book'],
            ['route' => 'student.enrollment', 'active' => 'student.enrollment*', 'label' => 'Đăng ký học phần', 'icon' => 'edit'],
            ['route' => 'student.timetable', 'active' => 'student.timetable*', 'label' => 'Thời khóa biểu', 'icon' => 'calendar'],
            ['route' => 'student.grades', 'active' => 'student.grades*', 'label' => 'Kết quả học tập', 'icon' => 'chart'],
            ['route' => 'student.attendance', 'active' => 'student.attendance*', 'label' => 'Chuyên cần', 'icon' => 'check'],
            ['route' => 'student.announcements', 'active' => 'student.announcements*', 'label' => 'Thông báo', 'icon' => 'bell'],
        ],
        default => [],
    };
    $profileRoute = $user->isStudent() ? 'student.profile' : 'profile.show';
    $profileActive = $user->isStudent() ? 'student.profile*' : 'profile.show';
@endphp

<aside id="app-sidebar" class="sms-sidebar" x-show="sidebarOpen" x-cloak>
    <a class="sms-sidebar-brand" href="{{ route('home') }}" aria-label="Hexa Tech Academy - trang chủ">
        <img src="{{ asset('images/logo.png') }}" alt="" class="sms-logo">
        <span>Hexa Tech <strong>Academy</strong></span>
    </a>
    <nav class="sms-sidebar-nav" aria-label="Điều hướng chính">
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
            class="sms-sidebar-link {{ request()->routeIs($profileActive) ? 'active' : '' }}"
            href="{{ route($profileRoute) }}"
        >
            <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 21a8 8 0 0 1 16 0" />
            </svg>
            Hồ sơ cá nhân
        </a>
        @foreach ($navigation as $item)
            @php
                $routeAvailable = Route::has($item['route']);
                $isAdminUsers = $user->isAdmin() && $item['route'] === 'admin.users.index';
                $userSubmenuActive = $isAdminUsers && request()->routeIs(
                    'admin.students.*',
                    'admin.lecturers.*',
                    'admin.administrative-classes.*',
                    'admin.users.*'
                );
            @endphp
            @if ($isAdminUsers)
                <div x-data="{ userMenuOpen: {{ $userSubmenuActive ? 'true' : 'false' }} }" class="d-flex flex-column">
                    <a
                        class="sms-sidebar-link justify-content-between {{ request()->routeIs($item['active']) ? 'active' : '' }}"
                        href="{{ route($item['route']) }}"
                        @click.prevent="userMenuOpen = !userMenuOpen"
                        :aria-expanded="userMenuOpen.toString()"
                        role="button"
                    >
                        <span class="d-flex align-items-center" style="gap: 12px;">
                            <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <circle cx="9" cy="8" r="3" />
                                <path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v2" />
                            </svg>
                            {{ $item['label'] }}
                        </span>
                        <svg
                            class="sms-sidebar-chevron ms-auto"
                            style="width: 15px; height: 15px; flex-shrink: 0; transition: transform 0.25s ease-in-out;"
                            :class="{ 'is-open': userMenuOpen }"
                            :style="userMenuOpen ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                            focusable="false"
                        >
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </a>

                    <div
                        x-show="userMenuOpen"
                        x-transition.opacity.duration.150ms
                        style="{{ $userSubmenuActive ? '' : 'display: none;' }}"
                        :aria-hidden="(!userMenuOpen).toString()"
                    >
                        <div class="ps-3 ms-2 my-1 border-start d-flex flex-column gap-1" style="border-color: rgba(39, 56, 115, 0.2) !important;">
                            <a
                                class="sms-sidebar-link py-1 px-2 {{ request()->routeIs('admin.students.*') ? 'active' : '' }}"
                                href="{{ route('admin.students.index') }}"
                                style="min-height: 32px; font-size: 12px;"
                            >
                                <svg class="sms-sidebar-icon" style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                                Sinh viên
                            </a>
                            <a
                                class="sms-sidebar-link py-1 px-2 {{ request()->routeIs('admin.lecturers.*') ? 'active' : '' }}"
                                href="{{ route('admin.lecturers.index') }}"
                                style="min-height: 32px; font-size: 12px;"
                            >
                                <svg class="sms-sidebar-icon" style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v2"/></svg>
                                Giảng viên
                            </a>
                            <a
                                class="sms-sidebar-link py-1 px-2 {{ request()->routeIs('admin.administrative-classes.*') ? 'active' : '' }}"
                                href="{{ route('admin.administrative-classes.index') }}"
                                style="min-height: 32px; font-size: 12px;"
                            >
                                <svg class="sms-sidebar-icon" style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                Lớp hành chính
                            </a>
                        </div>
                    </div>
                </div>
            @else
                @if ($routeAvailable)
                    <a
                        class="sms-sidebar-link {{ request()->routeIs($item['active']) ? 'active' : '' }}"
                        href="{{ route($item['route']) }}"
                    >
                @else
                    <span class="sms-sidebar-link sms-sidebar-link-disabled" aria-disabled="true" title="Chức năng chưa khả dụng">
                @endif
                    <svg class="sms-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        @switch($item['icon'])
                            @case('users')
                                <circle cx="9" cy="8" r="3" />
                                <path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v2" />
                                @break
                            @case('lecturer')
                                <circle cx="12" cy="8" r="4" />
                                <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                @break
                            @case('class')
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                @break
                            @case('building')
                                <path d="M4 21V4h11v17M15 9h5v12M2 21h20M8 8h3M8 12h3M8 16h3M17 13h1M17 17h1" />
                                @break
                            @case('organization')
                                <rect x="8" y="3" width="8" height="5" rx="1" />
                                <rect x="2" y="16" width="8" height="5" rx="1" />
                                <rect x="14" y="16" width="8" height="5" rx="1" />
                                <path d="M12 8v4M6 16v-4h12v4" />
                                @break
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
                            @case('check')
                                <circle cx="12" cy="12" r="9" />
                                <path d="m8 12 3 3 5-6" />
                                @break
                            @case('layers')
                                <path d="m12 3 9 5-9 5-9-5zM3 12l9 5 9-5M3 16l9 5 9-5" />
                                @break
                            @case('chart')
                                <path d="M4 20V10M10 20V4M16 20v-7M22 20H2" />
                                @break
                            @case('bell')
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
                                @break
                            @case('report')
                                <path d="M5 3h10l4 4v14H5zM14 3v5h5M8 12h8M8 16h8" />
                                @break
                        @endswitch
                    </svg>
                    {{ $item['label'] }}
                @if ($routeAvailable)
                    </a>
                @else
                    </span>
                @endif
            @endif
        @endforeach
    </nav>
    <div class="sms-sidebar-footer">
        <span>Hệ thống quản lý đào tạo</span>
        <strong>Hexa Tech Academy</strong>
    </div>
</aside>
