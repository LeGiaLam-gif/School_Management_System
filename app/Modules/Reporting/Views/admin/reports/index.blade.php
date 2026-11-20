@extends('layouts.app')

@section('title', 'Thống kê & báo cáo · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header theo nhận diện HTA --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · TRUNG TÂM BÁO CÁO & DỮ LIỆU ĐÀO TẠO · <span class="badge bg-white text-primary fw-semibold px-2 py-1">Giao diện cơ bản đã sẵn sàng</span></span>
            <h1 class="hta-page-title">Thống kê & báo cáo</h1>
            <p class="hta-page-subtitle">Tổng hợp chỉ số toàn trường, tình hình phân bổ sinh viên, giảng viên, chương trình đào tạo và các lớp học phần.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.reports.export', ['type' => 'students']) }}" class="hta-btn-outline text-white border-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Xuất DS Sinh Viên (CSV)
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'class_sections']) }}" class="hta-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Xuất DS Lớp Học Phần (CSV)
            </a>
        </div>
    </div>

    {{-- Nhóm 1: Thống kê Người dùng --}}
    <div class="mb-4">
        <h2 class="h6 fw-bold text-uppercase text-muted mb-3 d-flex align-items-center gap-2">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            1. Thống Kê Người Dùng & Tài Khoản
        </h2>
        <div class="row g-3">
            <div class="col-sm-6 col-xl-3">
                <div class="hta-stat-card">
                    <div class="hta-stat-icon hta-stat-icon-navy">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div class="hta-stat-label">Tổng Sinh Viên</div>
                    <div class="hta-stat-value">{{ number_format($userStats['total_students']) }}</div>
                    <div class="hta-stat-sub text-success">{{ $userStats['active_students'] }} đang học · {{ $userStats['locked_students'] }} bị khóa</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="hta-stat-card">
                    <div class="hta-stat-icon hta-stat-icon-cyan">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M5.5 21v-2a6.5 6.5 0 0 1 13 0v2"/></svg>
                    </div>
                    <div class="hta-stat-label">Tổng Giảng Viên</div>
                    <div class="hta-stat-value">{{ number_format($userStats['total_lecturers']) }}</div>
                    <div class="hta-stat-sub text-success">{{ $userStats['active_lecturers'] }} đang dạy · {{ $userStats['locked_lecturers'] }} bị khóa</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="hta-stat-card">
                    <div class="hta-stat-icon hta-stat-icon-amber">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="hta-stat-label">Quản Trị Viên (Admin)</div>
                    <div class="hta-stat-value">{{ number_format($userStats['total_admins']) }}</div>
                    <div class="hta-stat-sub">Tài khoản quản lý hệ thống</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="hta-stat-card">
                    <div class="hta-stat-icon hta-stat-icon-green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div class="hta-stat-label">Tỉ Lệ Kích Hoạt</div>
                    @php
                        $totalUsers = $userStats['total_students'] + $userStats['total_lecturers'];
                        $activeUsers = $userStats['active_students'] + $userStats['active_lecturers'];
                        $actRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 100;
                    @endphp
                    <div class="hta-stat-value text-success">{{ $actRate }}%</div>
                    <div class="hta-stat-sub">{{ $activeUsers }} / {{ $totalUsers }} tài khoản hoạt động</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nhóm 2: Thống kê Cơ cấu Đào tạo & Lớp học phần --}}
    <div class="mb-4">
        <h2 class="h6 fw-bold text-uppercase text-muted mb-3 d-flex align-items-center gap-2">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            2. Thống Kê Học Vụ & Giảng Dạy
        </h2>
        <div class="row g-3">
            <div class="col-sm-6 col-xl-3">
                <div class="hta-stat-card">
                    <div class="hta-stat-icon hta-stat-icon-navy">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V4h11v17M15 9h5v12M2 21h20M8 8h3M8 12h3M8 16h3M17 13h1M17 17h1"/></svg>
                    </div>
                    <div class="hta-stat-label">Khoa / Bộ Môn</div>
                    <div class="hta-stat-value">{{ $academicStats['total_departments'] }}</div>
                    <div class="hta-stat-sub">{{ $academicStats['total_programs'] }} Ngành đào tạo</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="hta-stat-card">
                    <div class="hta-stat-icon hta-stat-icon-cyan">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </div>
                    <div class="hta-stat-label">Học Phần Đào Tạo</div>
                    <div class="hta-stat-value">{{ $academicStats['total_courses'] }}</div>
                    <div class="hta-stat-sub text-success">{{ $academicStats['active_courses'] }} môn đang áp dụng</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="hta-stat-card">
                    <div class="hta-stat-icon hta-stat-icon-amber">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    </div>
                    <div class="hta-stat-label">Lớp Học Phần</div>
                    <div class="hta-stat-value">{{ number_format($teachingStats['total_sections']) }}</div>
                    <div class="hta-stat-sub">{{ $teachingStats['current_sections'] }} lớp trong kỳ hiện tại</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="hta-stat-card">
                    <div class="hta-stat-icon hta-stat-icon-green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><path d="M16 11l2 2 4-4"/></svg>
                    </div>
                    <div class="hta-stat-label">Lượt Đăng Ký HP</div>
                    <div class="hta-stat-value">{{ number_format($teachingStats['total_enrolled']) }}</div>
                    <div class="hta-stat-sub">{{ $teachingStats['unassigned_sections'] }} lớp chưa gán GV</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bảng 1: Cơ cấu Khoa & Ngành đào tạo --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="hta-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1b2559;">Cơ Cấu Khoa & Ngành Đào Tạo</h2>
                        <p class="text-muted small mb-0">Số lượng chương trình và học phần trực thuộc từng khoa.</p>
                    </div>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                        Quản lý khoa
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table hta-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Khoa / Đơn vị</th>
                                <th class="text-center" style="width: 100px;">Ngành</th>
                                <th class="text-center" style="width: 110px;">Học phần</th>
                                <th class="text-center" style="width: 100px;">Lớp HC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($departmentsReport as $dept)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $dept->name }}</div>
                                        <span class="badge bg-light text-muted border font-monospace">{{ $dept->code }}</span>
                                    </td>
                                    <td class="text-center fw-semibold">{{ $dept->programs_count }}</td>
                                    <td class="text-center fw-semibold text-primary">{{ $dept->courses_count }}</td>
                                    <td class="text-center">
                                        {{ $dept->programs->sum('administrative_classes_count') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Chưa có dữ liệu khoa đào tạo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Bảng 2: Tình hình mở lớp học phần theo kỳ --}}
        <div class="col-lg-6">
            <div class="hta-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1b2559;">Lớp Học Phần Theo Kỳ Học</h2>
                        <p class="text-muted small mb-0">Xem danh sách lớp mở trong kỳ được chọn.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.reports.index') }}" class="d-flex gap-2">
                        <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach ($allSemesters as $sem)
                                <option value="{{ $sem->id }}" {{ $selectedSemester && $selectedSemester->id == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->name }} ({{ $sem->code }}) {{ $sem->is_current ? '★' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table hta-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mã Lớp</th>
                                <th>Học phần</th>
                                <th>Giảng viên</th>
                                <th class="text-center" style="width: 100px;">Sĩ số</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sectionsInSemester as $sec)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.class-sections.show', $sec) }}" class="fw-bold font-monospace text-decoration-none" style="color: #273873;">
                                            {{ $sec->code }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;">{{ $sec->course->name ?? '—' }}</div>
                                        <span class="text-muted small font-monospace">{{ $sec->course->code ?? '' }}</span>
                                    </td>
                                    <td>
                                        @if ($sec->lecturer && $sec->lecturer->user)
                                            <span class="text-dark">{{ $sec->lecturer->user->name }}</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Chưa gán</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold">{{ $sec->enrolled_count }}</span>
                                        <span class="text-muted">/ {{ $sec->capacity }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Không có lớp học phần nào trong học kỳ này.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('admin.class-sections.index', ['semester_id' => $selectedSemester?->id]) }}" class="small text-decoration-none fw-semibold">
                        Xem tất cả lớp học phần trong kỳ &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
