@extends('layouts.app')

@section('title', 'Trang sinh viên · Hexa Tech Academy')

@section('content')
    @php
        $studentActions = [
            ['route' => 'student.profile', 'title' => 'Hồ sơ cá nhân', 'icon' => 'user'],
            ['route' => 'student.curriculum', 'title' => 'Chương trình', 'second_line' => 'đào tạo', 'icon' => 'book'],
            ['route' => 'student.enrollment', 'title' => 'Đăng ký học phần', 'icon' => 'edit'],
            ['route' => 'student.timetable', 'title' => 'Thời khóa biểu', 'icon' => 'calendar'],
            ['route' => 'student.grades', 'title' => 'Điểm & GPA', 'icon' => 'chart'],
            ['route' => 'student.attendance', 'title' => 'Chuyên cần', 'icon' => 'check'],
            ['route' => 'student.announcements', 'title' => 'Thông báo', 'icon' => 'bell'],
        ];
        $progress = $stats->progress;
        $creditPercent = min(100, max(0, (float) $progress->progress_percentage));
        $gpa = (float) $stats->gpa->gpa_4;
        $attendanceRate = (float) $stats->attendance->overall_rate;
        $hasWarning = $stats->warning->has_warning;
        $announcementCount = count($stats->recent_announcements ?? []);
        $learningMetrics = [
            ['label' => 'GPA', 'value' => $gpa, 'max' => 4, 'display' => number_format($gpa, 2) . ' / 4.00', 'color' => 'var(--sms-teal-dark)'],
            ['label' => 'Chuyên cần', 'value' => $attendanceRate, 'max' => 100, 'display' => number_format($attendanceRate, 1) . '%', 'color' => 'var(--sms-teal)'],
        ];
    @endphp

    <div class="sms-student-dashboard d-flex flex-column gap-4">
        <!-- Welcome Banner -->
        <section class="sms-welcome-card">
            <div class="sms-welcome-copy">
                <span class="sms-eyebrow">HEXA TECH ACADEMY · HỆ THỐNG QUẢN LÝ ĐÀO TẠO</span>
                <h2>Chào mừng trở lại, {{ $student->name ?? $user?->name ?? 'Sinh viên' }}</h2>
                <p class="mb-2 mw-100">
                    Không gian học tập dành cho sinh viên, tập trung thông tin và hoạt động học vụ trên cùng một nền&nbsp;tảng.
                </p>
                <div class="d-flex align-items-center gap-2 mt-3">
                    <span class="sms-role-pill mt-0">Sinh viên</span>
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

        <nav class="sms-student-action-grid d-lg-flex gap-2 pt-0" aria-label="Chức năng sinh viên">
            @foreach ($studentActions as $action)
                @php($routeAvailable = Route::has($action['route']))
                <article class="sms-student-action-card position-relative flex-lg-grow-1 justify-content-start gap-2 px-2 py-3 {{ $routeAvailable ? 'is-available' : '' }}"
                    style="flex-basis: 0; min-width: 0; min-height: 110px;">
                    <span class="sms-student-action-icon sms-action-icon-teal rounded-2" style="width: 28px; height: 28px; flex-basis: 28px;" aria-hidden="true">
                        <svg viewBox="0 0 24 24" style="width: 17px; height: 17px;" focusable="false">
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
                            <a class="stretched-link" href="{{ route($action['route']) }}">
                                {{ $action['title'] }}@isset($action['second_line'])<br>{{ $action['second_line'] }}@endisset
                            </a>
                        @else
                            <span aria-disabled="true">
                                {{ $action['title'] }}@isset($action['second_line'])<br>{{ $action['second_line'] }}@endisset
                            </span>
                        @endif
                    </h3>
                </article>
            @endforeach
        </nav>

        <div class="row g-3">
            <section class="col-12 col-lg-6" aria-labelledby="student-stats">
                <div class="card h-100">
                    <div class="card-header bg-light d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                        <h2 id="student-stats" class="h6 mb-0">Thống kê học tập</h2>
                        <a href="{{ route('student.curriculum') }}" class="btn btn-sm btn-outline-success"
                            style="--bs-btn-color: var(--sms-teal-dark); --bs-btn-border-color: var(--sms-teal);
                                   --bs-btn-hover-bg: var(--sms-teal-dark); --bs-btn-hover-border-color: var(--sms-teal-dark);
                                   --bs-btn-active-bg: var(--sms-teal-dark); --bs-btn-active-border-color: var(--sms-teal-dark);">
                            Chương trình đào tạo
                        </a>
                    </div>
                    <div class="card-body p-3 p-xl-4">
                        <div class="d-flex justify-content-center mb-4">
                            <svg class="sms-credit-ring" viewBox="0 0 136 136" width="136" height="136" role="img"
                                aria-label="{{ $progress->earned_credits }} trên {{ $progress->total_credits_required }} tín chỉ">
                                <circle cx="68" cy="68" r="60" fill="none" stroke="#e5eeea" stroke-width="12" />
                                <circle cx="68" cy="68" r="60" fill="none" stroke="#378b78" stroke-width="12"
                                    pathLength="100" stroke-dasharray="{{ $creditPercent }} 100" transform="rotate(-90 68 68)" />
                                <text x="68" y="66" text-anchor="middle" font-size="20" font-weight="700" fill="#293638">
                                    {{ $progress->earned_credits }} / {{ $progress->total_credits_required }}
                                </text>
                                <text x="68" y="87" text-anchor="middle" font-size="12" fill="#536566">Tín chỉ</text>
                            </svg>
                        </div>

                        <dl class="d-grid gap-3 mb-0">
                            @foreach ($learningMetrics as $metric)
                                @php($value = min($metric['max'], max(0, $metric['value'])))
                                <div class="bg-light border rounded-3 p-2">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <dt class="small fw-semibold">{{ $metric['label'] }}</dt>
                                        <dd class="small fw-semibold mb-0">{{ $metric['display'] }}</dd>
                                    </div>
                                    <div class="progress" style="height: 8px; --bs-progress-bg: #e5eeea;"
                                        role="meter" aria-label="{{ $metric['label'] }}" aria-valuemin="0"
                                        aria-valuemax="{{ $metric['max'] }}" aria-valuenow="{{ $value }}" aria-valuetext="{{ $metric['display'] }}">
                                        <div class="progress-bar" style="width: {{ 100 * $value / $metric['max'] }}%; background-color: {{ $metric['color'] }};"></div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 bg-light border rounded-3 p-2">
                                <dt class="small fw-semibold">Học vụ</dt>
                                <dd class="mb-0">
                                    <span class="badge rounded-pill {{ $hasWarning ? 'text-danger-emphasis bg-danger-subtle' : 'text-success-emphasis bg-success-subtle' }}">
                                        {{ $stats->warning->status_label }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>

            <div class="col-12 col-lg-6 d-flex flex-column gap-3">
                <a href="{{ route('student.announcements') }}"
                    class="card text-decoration-none {{ $announcementCount > 0 ? 'bg-danger-subtle text-danger-emphasis border-danger-subtle' : 'text-body' }}">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h2 class="h6 mb-0">Thông báo mới</h2>
                        <span class="small fw-semibold">
                            {{ $announcementCount > 0 ? "Có $announcementCount thông báo" : 'Không có thông báo' }}
                        </span>
                    </div>
                </a>

                <section class="card" aria-labelledby="student-schedule" style="--bs-border-color: #d4e9e0;">
                    <div class="card-header bg-light d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                        <h2 id="student-schedule" class="h6 mb-0">Lịch học trong ngày</h2>
                        <a href="{{ route('student.timetable') }}" class="btn btn-sm btn-outline-success"
                            style="--bs-btn-color: var(--sms-teal-dark); --bs-btn-border-color: var(--sms-teal);
                                   --bs-btn-hover-bg: var(--sms-teal-dark); --bs-btn-hover-border-color: var(--sms-teal-dark);
                                   --bs-btn-active-bg: var(--sms-teal-dark); --bs-btn-active-border-color: var(--sms-teal-dark);
                                   --bs-btn-focus-shadow-rgb: 55, 139, 120;">
                            Thời khóa biểu
                        </a>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled d-grid gap-2 mb-0">
                            @forelse ($stats->today_schedules as $schedule)
                                <li class="sms-today-lesson d-flex flex-wrap align-items-center justify-content-between gap-2 border bg-light rounded-3 p-2">
                                    <div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <span class="badge" style="background-color: var(--sms-teal-dark);">{{ $schedule->course_code }}</span>
                                            <span class="badge" style="color: var(--sms-teal-dark); background-color: #e5eeea;">{{ $schedule->shift }}</span>
                                            <h3 class="h6 fw-semibold mb-0">{{ $schedule->course_name }}</h3>
                                        </div>
                                        <div class="small text-muted d-flex flex-wrap gap-2">
                                            <span>{{ $schedule->periods }}</span>
                                            <span>{{ preg_replace('/^Lab máy(?=\s|$)/u', 'Phòng lab', $schedule->room ?? '') }}</span>
                                            <span>{{ $schedule->lecturer }}</span>
                                        </div>
                                    </div>
                                    @if (filled($schedule->status ?? null))
                                        <span class="sms-lesson-status badge" style="color: var(--sms-teal-dark); background-color: #e5eeea;">{{ $schedule->status }}</span>
                                    @endif
                                </li>
                            @empty
                                <li class="text-muted text-center py-3">Hôm nay bạn không có tiết học.</li>
                            @endforelse
                        </ul>
                    </div>
                </section>
                <section class="card" aria-labelledby="student-support">
                    <div class="card-body d-flex align-items-start gap-2 p-3">
                        <span class="fs-4" aria-hidden="true">ℹ️</span>
                        <div>
                            <h2 id="student-support" class="h6 mb-2">Hỗ trợ</h2>
                            <div class="d-grid gap-1 small text-muted">
                                <div>Liên hệ Phòng Đào tạo (P.201 - Nhà A)</div>
                                <div>Hotline <a class="fw-semibold text-decoration-none" style="color: var(--sms-teal-dark);" href="tel:+842838221234">(028) 3822 1234</a></div>
                                <div>Giờ làm việc: Thứ 2 - Thứ 6, 08:00 - 17:00</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
