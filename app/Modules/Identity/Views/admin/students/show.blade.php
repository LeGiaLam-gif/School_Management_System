@extends('layouts.app')

@section('title', ($student->user->name ?? $student->student_code) . ' · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}" class="text-decoration-none">Sinh viên</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $student->student_code }}</li>
        </ol>
    </nav>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 14px; background: #ecfdf5; color: #065f46;">
            <div class="d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="10"/></svg>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 14px; background: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <span class="fw-semibold">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Student Detail Header Banner --}}
    <div class="hta-page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div class="d-flex align-items-start gap-3">
            <div class="hta-avatar-circle hta-avatar-circle-lg" style="width: 58px; height: 58px; font-size: 1.4rem;">
                {{ mb_substr($student->user->name ?? $student->student_code, 0, 1) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="hta-code-tag" style="font-size: 0.95rem; padding: 4px 10px;">{{ $student->student_code }}</span>
                    <span class="hta-eyebrow mb-0">KHÓA {{ $student->cohort_year }}</span>

                    @php
                        $statusVal = $student->status?->value ?? $student->status;
                    @endphp
                    @if ($statusVal === 'studying')
                        <span class="hta-badge hta-badge-teal">Đang học</span>
                    @elseif ($statusVal === 'suspended')
                        <span class="hta-badge hta-badge-amber">Bảo lưu</span>
                    @elseif ($statusVal === 'graduated')
                        <span class="hta-badge hta-badge-violet">Đã tốt nghiệp</span>
                    @else
                        <span class="hta-badge hta-badge-rose">Thôi học</span>
                    @endif

                    @if ($student->user && $student->user->isActive())
                        <span class="hta-badge hta-badge-teal">Tài khoản hoạt động</span>
                    @else
                        <span class="hta-badge hta-badge-rose">Tài khoản bị khóa</span>
                    @endif
                </div>

                <h1 class="hta-page-title mb-1">{{ $student->user->name ?? 'Sinh viên chưa cập nhật tên' }}</h1>
                <p class="hta-page-subtitle mb-0">
                    Ngành: <strong>{{ $student->program->name ?? 'N/A' }}</strong>
                    @if ($student->administrativeClass)
                        · Lớp: <strong>{{ $student->administrativeClass->name }} ({{ $student->administrativeClass->code }})</strong>
                    @endif
                    · Email: <strong>{{ $student->user->email ?? 'N/A' }}</strong>
                    @if ($student->phone)
                        · SĐT: <strong>{{ $student->phone }}</strong>
                    @endif
                </p>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.students.edit', $student) }}" class="hta-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                </svg>
                Chỉnh sửa hồ sơ
            </a>
            <form method="POST" action="{{ route('admin.students.toggle-status', $student) }}" class="d-inline">
                @csrf
                <button type="submit" class="hta-btn-outline text-white border-white">
                    @if ($student->user && $student->user->isActive())
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Khóa tài khoản
                    @else
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                        Mở khóa
                    @endif
                </button>
            </form>
            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hồ sơ sinh viên {{ $student->user->name ?? $student->student_code }}? Thao tác này sẽ xóa vĩnh viễn cả tài khoản đăng nhập!');" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="hta-btn-outline text-white border-white">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m5 4v6m4-6v6"/>
                    </svg>
                    Xóa
                </button>
            </form>
        </div>
    </div>

    {{-- Nội dung chi tiết --}}
    <div class="row g-4 mb-4">
        {{-- Cột trái: Hồ sơ cá nhân & Thông tin tài khoản --}}
        <div class="col-lg-5 col-xl-4">
            <div class="hta-card p-4 h-100">
                <h5 class="fw-bold mb-3 pb-2 border-bottom" style="color: #1b2559;">Hồ sơ sinh viên</h5>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">MÃ SỐ SINH VIÊN</span>
                    <span class="hta-code-tag" style="font-size: 0.95rem;">{{ $student->student_code }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">HỌ VÀ TÊN</span>
                    <span class="fw-bold fs-6" style="color: #1b2559;">{{ $student->user->name ?? 'N/A' }}</span>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <span class="hta-eyebrow d-block mb-1">NGÀY SINH</span>
                        <span class="text-secondary small fw-semibold">
                            {{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : 'Chưa cập nhật' }}
                        </span>
                    </div>
                    <div class="col-6">
                        <span class="hta-eyebrow d-block mb-1">GIỚI TÍNH</span>
                        <span class="text-secondary small fw-semibold">
                            {{ $student->gender?->label() ?? 'Chưa xác định' }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">EMAIL LIÊN HỆ</span>
                    <span class="text-secondary small font-monospace">{{ $student->user->email ?? 'N/A' }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">SỐ ĐIỆN THOẠI</span>
                    <span class="text-secondary small">{{ $student->phone ?: 'Chưa cập nhật' }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">ĐỊA CHỈ THƯỜNG TRÚ</span>
                    <span class="text-secondary small">{{ $student->address ?: 'Chưa cập nhật' }}</span>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">TRẠNG THÁI HỌC TẬP</span>
                    @if ($statusVal === 'studying')
                        <span class="hta-badge hta-badge-teal">Đang học chính khóa</span>
                    @elseif ($statusVal === 'suspended')
                        <span class="hta-badge hta-badge-amber">Bảo lưu kết quả học tập</span>
                    @elseif ($statusVal === 'graduated')
                        <span class="hta-badge hta-badge-violet">Đã công nhận tốt nghiệp</span>
                    @else
                        <span class="hta-badge hta-badge-rose">Đã thôi học / Xóa tên</span>
                    @endif
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">TRẠNG THÁI TÀI KHOẢN PORTAL</span>
                    @if ($student->user && $student->user->isActive())
                        <span class="hta-badge hta-badge-teal">Đang kích hoạt bình thường</span>
                    @else
                        <span class="hta-badge hta-badge-rose">Đang bị khóa đăng nhập</span>
                    @endif
                </div>

                <div class="small text-muted pt-2 border-top">
                    Khởi tạo hồ sơ: {{ $student->created_at ? $student->created_at->format('d/m/Y H:i') : 'N/A' }}
                </div>
            </div>
        </div>

        {{-- Cột phải: Thông tin đào tạo & Tabs học phần, cảnh báo --}}
        <div class="col-lg-7 col-xl-8">
            {{-- Quick Academic Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="hta-stat-card">
                        <div class="hta-stat-icon hta-stat-icon-navy">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Khóa tuyển sinh</div>
                            <div class="hta-stat-value" style="font-size: 1.25rem;">K{{ $student->cohort_year }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="hta-stat-card">
                        <div class="hta-stat-icon hta-stat-icon-teal">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Học phần đã ĐK</div>
                            <div class="hta-stat-value" style="font-size: 1.25rem;">{{ $student->enrollments->count() }} <span style="font-size: 0.85rem; font-weight: 500;">môn</span></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="hta-stat-card">
                        <div class="hta-stat-icon hta-stat-icon-amber">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Lớp hành chính</div>
                            <div class="hta-stat-value" style="font-size: 1.1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $student->administrativeClass ? $student->administrativeClass->code : 'Chưa gán' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="hta-stat-card">
                        <div class="hta-stat-icon {{ $student->academicWarnings->count() > 0 ? 'hta-stat-icon-rose' : 'hta-stat-icon-violet' }}" style="{{ $student->academicWarnings->count() > 0 ? 'background: #fee2e2; color: #dc2626;' : '' }}">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Cảnh báo học vụ</div>
                            <div class="hta-stat-value" style="font-size: 1.25rem; {{ $student->academicWarnings->count() > 0 ? 'color: #dc2626;' : '' }}">
                                {{ $student->academicWarnings->count() }} <span style="font-size: 0.85rem; font-weight: 500;">lần</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thông tin đào tạo chi tiết --}}
            <div class="hta-card p-4 mb-4">
                <h5 class="fw-bold mb-3 pb-2 border-bottom" style="color: #1b2559;">Thông tin học vụ & Quản lý</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="hta-eyebrow d-block mb-1">NGÀNH ĐÀO TẠO</span>
                        <div class="fw-bold fs-6" style="color: #273873;">
                            {{ $student->program->name ?? 'Chưa phân ngành' }}
                            @if ($student->program)
                                <span class="hta-code-tag ms-1">{{ $student->program->code }}</span>
                            @endif
                        </div>
                        <div class="small text-muted mt-1">
                            Khoa: <strong>{{ $student->program->department->name ?? 'N/A' }}</strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <span class="hta-eyebrow d-block mb-1">LỚP HÀNH CHÍNH & CỐ VẤN HỌC TẬP</span>
                        @if ($student->administrativeClass)
                            <div class="fw-bold fs-6" style="color: #1b2559;">
                                {{ $student->administrativeClass->name }} ({{ $student->administrativeClass->code }})
                            </div>
                            <div class="small text-muted mt-1">
                                Cố vấn học tập:
                                @if ($student->administrativeClass->advisorLecturer)
                                    <strong style="color: #273873;">{{ $student->administrativeClass->advisorLecturer->user->name ?? 'N/A' }}</strong>
                                    ({{ $student->administrativeClass->advisorLecturer->lecturer_code }})
                                @else
                                    <span class="fst-italic">Chưa phân công CVHT</span>
                                @endif
                            </div>
                        @else
                            <div class="text-muted fst-italic">Sinh viên chưa được phân vào lớp sinh hoạt hành chính nào.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tabs: Học phần đăng ký & Cảnh báo học vụ --}}
            <div class="hta-card">
                <div class="hta-card-header p-0">
                    <ul class="nav nav-tabs border-0 px-3 pt-2" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold py-3 px-4 border-0" id="enrollments-tab" data-bs-toggle="tab" data-bs-target="#enrollments-pane" type="button" role="tab" style="color: #1b2559;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                </svg>
                                Lịch sử đăng ký học phần ({{ $student->enrollments->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-3 px-4 border-0" id="warnings-tab" data-bs-toggle="tab" data-bs-target="#warnings-pane" type="button" role="tab" style="color: #1b2559;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                Cảnh báo học vụ ({{ $student->academicWarnings->count() }})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="studentTabsContent">
                    {{-- Tab 1: Enrollments --}}
                    <div class="tab-pane fade show active p-3" id="enrollments-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="hta-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 130px;">Mã LHP</th>
                                        <th>Học phần</th>
                                        <th class="text-center" style="width: 90px;">Tín chỉ</th>
                                        <th style="width: 140px;">Thời gian ĐK</th>
                                        <th class="text-center" style="width: 120px;">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($student->enrollments as $enr)
                                        <tr>
                                            <td>
                                                <span class="hta-code-tag">{{ $enr->classSection->code ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold" style="color: #1b2559;">{{ $enr->classSection->course->name ?? 'Học phần' }}</span>
                                                <div class="text-muted small">{{ $enr->classSection->course->code ?? '' }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="hta-badge hta-badge-amber">{{ $enr->classSection->course->credits ?? 0 }} TC</span>
                                            </td>
                                            <td class="small text-secondary">
                                                {{ $enr->registered_at ? $enr->registered_at->format('d/m/Y H:i') : ($enr->created_at ? $enr->created_at->format('d/m/Y H:i') : '—') }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $enrStatus = is_object($enr->status) ? $enr->status->value : $enr->status;
                                                @endphp
                                                @if ($enrStatus === 'registered')
                                                    <span class="hta-badge hta-badge-teal">Đã đăng ký</span>
                                                @elseif ($enrStatus === 'completed')
                                                    <span class="hta-badge hta-badge-navy">Hoàn thành</span>
                                                @elseif ($enrStatus === 'dropped')
                                                    <span class="hta-badge hta-badge-rose">Đã hủy</span>
                                                @else
                                                    <span class="badge bg-light text-dark border">{{ $enrStatus }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                Sinh viên chưa đăng ký lớp học phần nào trong hệ thống.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 2: Academic Warnings --}}
                    <div class="tab-pane fade p-3" id="warnings-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="hta-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 120px;">Học kỳ</th>
                                        <th class="text-center" style="width: 110px;">Mức cảnh báo</th>
                                        <th class="text-center" style="width: 90px;">ĐTBHK</th>
                                        <th class="text-center" style="width: 90px;">ĐTBCTL</th>
                                        <th class="text-center" style="width: 90px;">Nợ tín chỉ</th>
                                        <th>Lý do vi phạm</th>
                                        <th class="text-center" style="width: 100px;">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($student->academicWarnings as $warn)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold" style="color: #1b2559;">
                                                    {{ $warn->semester->name ?? ('Học kỳ ' . $warn->semester_id) }}
                                                </span>
                                                <div class="text-muted small">Năm thứ {{ $warn->student_year }}</div>
                                            </td>
                                            <td class="text-center">
                                                @if ($warn->warning_level == 1)
                                                    <span class="hta-badge hta-badge-amber">Mức 1</span>
                                                @elseif ($warn->warning_level == 2)
                                                    <span class="hta-badge hta-badge-rose">Mức 2</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Thôi học</span>
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold {{ $warn->semester_gpa < 2.0 ? 'text-danger' : 'text-dark' }}">
                                                {{ number_format($warn->semester_gpa, 2) }}
                                            </td>
                                            <td class="text-center fw-bold {{ $warn->cumulative_gpa < 2.0 ? 'text-danger' : 'text-dark' }}">
                                                {{ number_format($warn->cumulative_gpa, 2) }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-danger border border-danger-subtle">{{ $warn->total_debt_credits }} TC</span>
                                            </td>
                                            <td class="small">
                                                {{ $warn->reason }}
                                            </td>
                                            <td class="text-center">
                                                @if ($warn->status === 'active')
                                                    <span class="hta-badge hta-badge-rose">Hiệu lực</span>
                                                @elseif ($warn->status === 'resolved')
                                                    <span class="hta-badge hta-badge-teal">Đã khắc phục</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">{{ $warn->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" class="mb-1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                <div class="fw-semibold text-success mt-1">Không có cảnh báo học vụ</div>
                                                <small class="text-muted">Sinh viên duy trì tiến độ học tập bình thường và không vi phạm quy chế đào tạo.</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
