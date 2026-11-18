@extends('layouts.app')

@section('title', 'Cổng Sinh Viên · Bảng điều khiển & Thống kê học tập')

@section('content')
    @php($studentActions = [
        ['route' => 'student.enrollment', 'title' => 'Đăng ký học phần', 'desc' => 'Đăng ký môn học HK2', 'icon' => 'edit', 'color' => 'violet'],
        ['route' => 'student.timetable', 'title' => 'Thời khóa biểu', 'desc' => 'Xem lịch học tuần & hôm nay', 'icon' => 'calendar', 'color' => 'orange'],
        ['route' => 'student.grades', 'title' => 'Điểm & GPA', 'desc' => 'Tra cứu bảng điểm chi tiết', 'icon' => 'chart', 'color' => 'blue'],
        ['route' => 'student.attendance', 'title' => 'Chuyên cần', 'desc' => 'Tỷ lệ điểm danh & cảnh báo vắng', 'icon' => 'check', 'color' => 'green'],
        ['route' => 'student.curriculum', 'title' => 'Chương trình đào tạo', 'desc' => 'Khung CTĐT & môn học', 'icon' => 'book', 'color' => 'orange'],
        ['route' => 'student.announcements', 'title' => 'Thông báo', 'desc' => 'Tin tức từ nhà trường & lớp', 'icon' => 'bell', 'color' => 'teal'],
        ['route' => 'student.profile', 'title' => 'Hồ sơ cá nhân', 'desc' => 'Thông tin cá nhân & mật khẩu', 'icon' => 'user', 'color' => 'teal'],
    ])

    <!-- Welcome Banner -->
    <section class="sms-welcome-card mb-4">
        <div class="sms-welcome-copy">
            <span class="sms-eyebrow">HEXA TECH ACADEMY · HỆ THỐNG QUẢN LÝ ĐÀO TẠO</span>
            <h2>Chào mừng trở lại, {{ $student->name ?? $user?->name ?? 'Sinh viên' }}</h2>
            <p class="mb-2">
                Mã SV: <strong class="text-white">{{ $student->student_code ?? $user?->code ?? 'SV20230001' }}</strong> · 
                Ngành: <strong class="text-white">{{ $student->program_name ?? 'Kỹ thuật Phần mềm' }}</strong> · 
                Lớp SH: <strong class="text-white">{{ $student->class_code ?? 'SE23.01' }}</strong>
            </p>
            <div class="d-flex align-items-center gap-2">
                <span class="sms-role-pill">Sinh viên chính quy</span>
                <span class="badge {{ $student->status_badge ?? 'bg-success' }} px-2 py-1">{{ $student->status_label ?? 'Đang học' }}</span>
            </div>
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

    <!-- Thẻ Thống kê Trọng yếu (Stat Cards) -->
    <div class="row g-3 mb-4">
        <!-- 1. Tiến độ tín chỉ -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Tiến độ tín chỉ</span>
                    <span class="badge bg-teal-subtle text-teal fw-bold" style="background-color: #e6f4f1; color: #226b5c;">
                        {{ $stats->progress->progress_percentage }}%
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats->progress->earned_credits }}</h3>
                    <span class="text-muted small">/ {{ $stats->progress->total_credits_required }} TC tích lũy</span>
                </div>
                <!-- Progress bar -->
                <div class="progress mb-2" style="height: 6px;" role="progressbar" aria-valuenow="{{ $stats->progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: {{ $stats->progress->progress_percentage }}%; background-color: #226b5c;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-auto pt-1">
                    <span>Đang học: <strong>{{ $stats->progress->enrolled_credits }} TC</strong></span>
                    <span>Còn lại: <strong>{{ $stats->progress->remaining_credits }} TC</strong></span>
                </div>
            </div>
        </div>

        <!-- 2. Điểm trung bình GPA -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Điểm GPA tích lũy</span>
                    <span class="badge bg-primary-subtle text-primary fw-bold">
                        {{ $stats->gpa->classification }}
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($stats->gpa->gpa_4, 2) }}</h3>
                    <span class="text-muted small">/ 4.0 (Thang 10: {{ number_format($stats->gpa->gpa_10, 2) }})</span>
                </div>
                <p class="text-muted small mb-0 mt-auto">
                    Xếp loại học lực: <strong class="text-dark">{{ $stats->gpa->classification }}</strong> · <span class="text-success fw-semibold">Tích cực</span>
                </p>
            </div>
        </div>

        <!-- 3. Tình trạng học vụ (Academic Warning) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Tình trạng học vụ</span>
                    <span class="badge {{ $stats->warning->status_badge }} px-2 py-1">
                        {{ $stats->warning->status_label }}
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    @if ($stats->warning->has_warning)
                        <h4 class="fw-bold mb-0 text-danger">Có cảnh báo</h4>
                    @else
                        <h4 class="fw-bold mb-0 text-success">An toàn</h4>
                    @endif
                </div>
                <p class="text-muted small mb-0 mt-auto line-clamp-2" title="{{ $stats->warning->note }}">
                    {{ $stats->warning->note }}
                </p>
            </div>
        </div>

        <!-- 4. Chuyên cần tổng quan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Tỷ lệ chuyên cần</span>
                    <span class="badge {{ $stats->attendance->status_badge }} px-2 py-1">
                        {{ $stats->attendance->status_label }}
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($stats->attendance->overall_rate, 1) }}%</h3>
                    <span class="text-muted small">tham gia lớp học</span>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-auto pt-1">
                    <span>Có mặt: <strong>{{ $stats->attendance->present_count }}/{{ $stats->attendance->total_sessions }}</strong></span>
                    <span>Vắng KP: <strong class="{{ $stats->attendance->unexcused_count > 0 ? 'text-danger' : 'text-success' }}">{{ $stats->attendance->unexcused_count }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Nội dung 2 Cột -->
    <div class="row g-4">
        <!-- Cột Trái (Col-lg-8): Lịch học hôm nay & Lối tắt chức năng -->
        <div class="col-12 col-lg-8">
            <!-- 1. Lịch học hôm nay (Today's Schedule) -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-primary fs-5">📅</span>
                        <h5 class="fw-bold mb-0">Lịch học trong ngày</h5>
                    </div>
                    <a href="{{ route('student.timetable') }}" class="btn btn-sm btn-outline-primary text-decoration-none">
                        Xem thời khóa biểu tuần &rarr;
                    </a>
                </div>
                <div class="card-body p-3">
                    @if (empty($stats->today_schedules))
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">Hôm nay bạn không có tiết học nào. Chúc bạn có một ngày tự học và nghỉ ngơi hiệu quả!</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach ($stats->today_schedules as $schedule)
                                <div class="p-3 rounded-3 border bg-light d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-secondary">{{ $schedule->course_code }}</span>
                                            <span class="badge bg-primary-subtle text-primary">{{ $schedule->shift }}</span>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $schedule->course_name }}</h6>
                                        </div>
                                        <div class="text-muted small d-flex flex-wrap gap-3">
                                            <span>🕒 {{ $schedule->periods }}</span>
                                            <span>📍 {{ $schedule->room }}</span>
                                            <span>👨‍🏫 {{ $schedule->lecturer }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-teal-subtle text-teal px-3 py-2" style="background-color: #e6f4f1; color: #226b5c;">
                                        {{ $schedule->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. Thống kê tiến độ môn học & CTĐT -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-warning fs-5">📊</span>
                        <h5 class="fw-bold mb-0">Tiến độ tích lũy CTĐT ({{ $student->program_name ?? 'Kỹ thuật Phần mềm' }})</h5>
                    </div>
                    <a href="{{ route('student.curriculum') }}" class="btn btn-sm btn-outline-secondary text-decoration-none">
                        Chi tiết CTĐT &rarr;
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 text-center mb-3">
                        <div class="col-4 border-end">
                            <span class="text-muted small d-block">Môn đã hoàn thành</span>
                            <h4 class="fw-bold text-success mb-0">{{ $stats->progress->passed_courses_count }}</h4>
                            <span class="text-muted small">môn học</span>
                        </div>
                        <div class="col-4 border-end">
                            <span class="text-muted small d-block">Môn đang học (HK2)</span>
                            <h4 class="fw-bold text-primary mb-0">{{ $stats->progress->enrolled_courses_count }}</h4>
                            <span class="text-muted small">môn học ({{ $stats->progress->enrolled_credits }} TC)</span>
                        </div>
                        <div class="col-4">
                            <span class="text-muted small d-block">Tín chỉ cần đạt thêm</span>
                            <h4 class="fw-bold text-danger mb-0">{{ $stats->progress->remaining_credits }}</h4>
                            <span class="text-muted small">tín chỉ còn lại</span>
                        </div>
                    </div>
                    <div class="alert alert-light border small text-muted mb-0">
                        <strong>💡 Ghi chú học vụ:</strong> Sinh viên cần hoàn thành tối thiểu <strong>{{ $stats->progress->total_credits_required }} tín chỉ</strong> và đạt chuẩn đầu ra ngoại ngữ/tin học để đủ điều kiện xét tốt nghiệp đúng hạn.
                    </div>
                </div>
            </div>

            <!-- 3. Lối tắt Chức năng (Student Workspace Actions) -->
            <section class="sms-workspace-card">
                <div class="sms-section-heading mb-3">
                    <h5 class="fw-bold mb-0">Lối tắt tính năng sinh viên</h5>
                    <span class="sms-current-role">Student Portal</span>
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
        </div>

        <!-- Cột Phải (Col-lg-4): Thông báo mới nhất & Thông tin Cố vấn học tập -->
        <div class="col-12 col-lg-4">
            <!-- 1. Thông báo mới nhất -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-danger fs-5">🔔</span>
                        <h5 class="fw-bold mb-0">Thông báo mới</h5>
                    </div>
                    <a href="{{ route('student.announcements') }}" class="small text-decoration-none">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($stats->recent_announcements as $announcement)
                            <div class="border-bottom pb-3 last-no-border">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    @if ($announcement->is_pinned)
                                        <span class="badge bg-danger" title="Ghim ưu tiên">Ghim</span>
                                    @endif
                                    <span class="badge {{ $announcement->badge }}">{{ $announcement->category }}</span>
                                    <span class="text-muted small ms-auto">{{ $announcement->published_at }}</span>
                                </div>
                                <h6 class="fw-semibold mb-1">
                                    <a href="{{ route('student.announcements.show', $announcement->id) }}" class="text-dark text-decoration-none hover-primary">
                                        {{ $announcement->title }}
                                    </a>
                                </h6>
                                <span class="text-muted small">Đăng bởi: {{ $announcement->author }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 2. Thông tin Lớp sinh hoạt & Cố vấn học tập -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-teal fs-5" style="color: #226b5c;">🎓</span>
                        <h5 class="fw-bold mb-0">Lớp sinh hoạt & Cố vấn</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="text-muted small d-block">Lớp hành chính sinh hoạt</span>
                        <strong class="text-dark fs-6">{{ $student->class_name ?? 'Lớp Kỹ thuật Phần mềm 2023-01' }}</strong>
                        <div class="text-muted small mt-1">Mã lớp: <code>{{ $student->class_code ?? 'SE23.01' }}</code></div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="text-muted small d-block">Cố vấn học tập (CVHT)</span>
                        <strong class="text-dark fs-6">{{ $student->advisor_name ?? 'ThS. Hoàng Tuấn Anh' }}</strong>
                        <div class="text-muted small mt-1">
                            ✉️ <a href="mailto:tuananh.hoang@sms.edu.vn" class="text-decoration-none">tuananh.hoang@sms.edu.vn</a>
                        </div>
                    </div>
                    <div class="mb-0">
                        <span class="text-muted small d-block">Khoa phụ trách</span>
                        <strong class="text-dark">{{ $student->department_name ?? 'Khoa Công nghệ Thông tin' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Hướng dẫn & Đường dây nóng hỗ trợ -->
            <div class="card border-0 shadow-sm rounded-3 bg-light">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-2">
                        <span class="fs-4">ℹ️</span>
                        <div>
                            <h6 class="fw-bold mb-1">Cần hỗ trợ học vụ?</h6>
                            <p class="text-muted small mb-2">
                                Nếu gặp vướng mắc về điểm số, đăng ký học phần hoặc tiến độ tốt nghiệp, sinh viên liên hệ Phòng Đào tạo (P.201 - Nhà A).
                            </p>
                            <span class="text-muted small">Hotline: <strong>(028) 3822 1234</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
