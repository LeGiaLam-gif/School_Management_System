@extends('layouts.app')

@section('title', 'Cổng Sinh Viên · Trang chủ')

@section('content')
    @php($studentActions = [
        ['route' => 'student.profile', 'title' => 'Hồ sơ cá nhân', 'icon' => 'user', 'color' => 'teal'],
        ['route' => 'student.curriculum', 'title' => 'Chương trình đào tạo', 'icon' => 'book', 'color' => 'orange'],
        ['route' => 'student.enrollment', 'title' => 'Đăng ký học phần', 'icon' => 'edit', 'color' => 'violet'],
        ['route' => 'student.timetable', 'title' => 'Thời khóa biểu', 'icon' => 'calendar', 'color' => 'orange'],
        ['route' => 'student.grades', 'title' => 'Điểm & GPA', 'icon' => 'chart', 'color' => 'blue'],
        ['route' => 'student.attendance', 'title' => 'Chuyên cần', 'icon' => 'check', 'color' => 'green'],
        ['route' => 'student.announcements', 'title' => 'Thông báo', 'icon' => 'bell', 'color' => 'teal'],
    ])

    <section class="sms-welcome-card">
        <div class="sms-welcome-copy">
            <span class="sms-eyebrow">HEXA TECH ACADEMY · HỆ THỐNG QUẢN LÝ ĐÀO TẠO</span>
            <h2>Chào mừng, {{ $student->name ?? $user?->name ?? 'Sinh viên' }}</h2>
            <p>Không gian học tập dành cho sinh viên, tập trung thông tin và hoạt động học vụ trên cùng một nền tảng.</p>
            <span class="sms-role-pill">Sinh viên</span>
        </div>
        <div class="sms-welcome-art" aria-hidden="true">
            <svg viewBox="0 0 180 150" focusable="false">
                <path class="art-orbit" d="M18 115c26-71 106-95 145-56 34 35-1 75-44 71-36-3-66-25-91-15" />
                <path class="art-book" d="M46 51c18-8 33-5 45 4v66c-12-9-27-12-45-4z" />
                <path class="art-book" d="M91 55c14-9 29-12 46-4v66c-17-8-32-5-46 4z" />
                <path class="art-line" d="M56 67c11-4 20-2 27 3M56 79c11-4 20-2 27 3M102 70c9-5 19-7 28-5M102 82c9-5 19-7 28-5" />
                <path class="art-cap" d="m31 43 60-23 60 23-60 24z" />
                <path class="art-cap-tail" d="M48 51v24c24 17 70 17 86 0V51" />
                <circle class="art-spark" cx="148" cy="29" r="5" />
                <circle class="art-spark" cx="30" cy="95" r="4" />
            </svg>
        </div>
    </section>

    <section class="sms-summary-grid" aria-label="Thông tin sinh viên">
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-teal" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM8 9h8M8 13h5" /></svg>
            </span>
            <span class="sms-summary-label">Mã sinh viên</span>
            <strong>{{ $student->student_code ?? $user?->code ?? 'Chưa cập nhật' }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-orange" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM8 9h8M8 13h8M8 17h5" /></svg>
            </span>
            <span class="sms-summary-label">Ngành học</span>
            <strong>{{ $student->program_name ?? 'Chưa cập nhật' }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-violet" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></svg>
            </span>
            <span class="sms-summary-label">Khóa tuyển sinh</span>
            <strong>{{ $student->cohort_year ?? 'Chưa cập nhật' }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-green" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6" /><circle cx="12" cy="12" r="10" /></svg>
            </span>
            <span class="sms-summary-label">Trạng thái</span>
            <strong>{{ $student->status_label ?? 'Chưa cập nhật' }}</strong>
        </article>
    </section>

    <section class="sms-workspace-card mt-4">
        <div class="sms-section-heading">
            <h2>Bảng điều khiển</h2>
            <span class="sms-current-role">Sinh viên</span>
        </div>
        <div class="sms-student-action-grid">
            @foreach ($studentActions as $action)
                @php($routeAvailable = Route::has($action['route']))
                <article class="sms-student-action-card {{ $routeAvailable ? 'is-available' : '' }}">
                    <span class="sms-student-action-icon sms-action-icon-{{ $action['color'] }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false">
                            @switch($action['icon'])
                                @case('user')
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 21a8 8 0 0 1 16 0" />
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
                    </span>
                    <h3>
                        @if ($routeAvailable)
                            <a href="{{ route($action['route']) }}">{{ $action['title'] }}</a>
                        @else
                            <span aria-disabled="true">{{ $action['title'] }}</span>
                        @endif
                    </h3>
                </article>
            @endforeach
        </div>
    </section>

    <!-- Hướng dẫn sinh viên -->
    <div class="mt-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="text-primary fs-3">ℹ️</div>
                    <div>
                        <h6 class="fw-bold mb-1">Hướng dẫn dành cho sinh viên</h6>
                        <p class="text-muted small mb-0">
                            Sinh viên cần thường xuyên kiểm tra thông tin cá nhân và thông tin học vấn. Nếu phát hiện sai sót về
                            Khoa, Ngành hoặc Khóa học, vui lòng liên hệ trực tiếp với Phòng Đào tạo để được hỗ trợ điều chỉnh.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
