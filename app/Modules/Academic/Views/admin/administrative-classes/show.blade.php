@extends('layouts.app')

@section('title', $administrativeClass->name . ' (' . $administrativeClass->code . ') · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.administrative-classes.index') }}" class="text-decoration-none">Lớp hành chính</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $administrativeClass->code }}</li>
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

    {{-- Class Detail Header Banner --}}
    <div class="hta-page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div class="d-flex align-items-start gap-3">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="hta-code-tag" style="font-size: 0.95rem; padding: 4px 10px;">{{ $administrativeClass->code }}</span>
                    <span class="hta-eyebrow mb-0">KHÓA {{ $administrativeClass->academic_year }} · NGÀNH {{ mb_strtoupper($administrativeClass->program->name) }}</span>
                </div>
                <h1 class="hta-page-title mb-1">{{ $administrativeClass->name }}</h1>
                <p class="hta-page-subtitle mb-0">
                    Chương trình: <a href="{{ route('admin.programs.show', $administrativeClass->program) }}" class="text-white text-decoration-underline">{{ $administrativeClass->program->name }} ({{ $administrativeClass->program->code }})</a>
                    · Khoa: <strong>{{ $administrativeClass->program->department->name ?? '' }}</strong>
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.administrative-classes.edit', $administrativeClass) }}" class="hta-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                </svg>
                Chỉnh sửa lớp
            </a>
            <form method="POST" action="{{ route('admin.administrative-classes.destroy', $administrativeClass) }}" onsubmit="return confirm('Bạn có chắc muốn xóa lớp {{ $administrativeClass->name }}?');" class="d-inline">
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

    {{-- Thông tin tóm tắt & Cố vấn học tập --}}
    <div class="row g-4 mb-4">
        {{-- Card Cố vấn học tập --}}
        <div class="col-lg-5 col-xl-4">
            <div class="hta-card h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <span class="hta-eyebrow mb-0">CỐ VẤN HỌC TẬP</span>
                    <span class="hta-badge {{ $administrativeClass->advisorLecturer ? 'hta-badge-teal' : 'hta-badge-amber' }}">
                        {{ $administrativeClass->advisorLecturer ? 'Đã phân công' : 'Chưa chỉ định' }}
                    </span>
                </div>

                @if ($administrativeClass->advisorLecturer)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="hta-avatar-circle" style="width: 52px; height: 52px; font-size: 1.3rem;">
                            {{ mb_substr($administrativeClass->advisorLecturer->user->name ?? 'GV', 0, 1) }}
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0" style="color: #1b2559;">{{ $administrativeClass->advisorLecturer->user->name }}</h5>
                            <span class="small text-muted">{{ $administrativeClass->advisorLecturer->academic_title ?? 'Giảng viên' }} · {{ $administrativeClass->advisorLecturer->lecturer_code }}</span>
                        </div>
                    </div>
                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-2 text-secondary">
                        <li class="d-flex align-items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <span>{{ $administrativeClass->advisorLecturer->user->email ?? 'Chưa cập nhật' }}</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>{{ $administrativeClass->advisorLecturer->phone ?? 'Chưa có SĐT' }}</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            <span>Văn phòng: {{ $administrativeClass->advisorLecturer->office ?? 'Khoa ' . ($administrativeClass->advisorLecturer->department->name ?? '') }}</span>
                        </li>
                    </ul>
                @else
                    <div class="text-center py-4 text-muted">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" class="mb-2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                        <p class="small mb-2">Lớp này hiện chưa được chỉ định Cố vấn học tập.</p>
                        <a href="{{ route('admin.administrative-classes.edit', $administrativeClass) }}" class="hta-btn-primary hta-btn-sm">Phân công ngay</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="col-lg-7 col-xl-8">
            <div class="row g-3 h-100">
                <div class="col-sm-6">
                    <div class="hta-stat-card h-100">
                        <div class="hta-stat-icon hta-stat-icon-teal">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Sĩ số sinh viên</div>
                            <div class="hta-stat-value">{{ $administrativeClass->students->count() }} <span style="font-size: 0.95rem; font-weight: 600;">SV</span></div>
                            <div class="small text-muted mt-1">Đang theo học chính khóa</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="hta-stat-card h-100">
                        <div class="hta-stat-icon hta-stat-icon-navy">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Khóa tuyển sinh</div>
                            <div class="hta-stat-value">Khóa {{ $administrativeClass->academic_year }}</div>
                            <div class="small text-muted mt-1">Năm nhập học: {{ $administrativeClass->academic_year }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="hta-stat-card h-100">
                        <div class="hta-stat-icon hta-stat-icon-amber">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Ngành đào tạo</div>
                            <div class="hta-stat-value" style="font-size: 1.25rem;">{{ $administrativeClass->program->code }}</div>
                            <div class="small text-muted mt-1">{{ $administrativeClass->program->name }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="hta-stat-card h-100">
                        <div class="hta-stat-icon hta-stat-icon-violet">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 21V4h11v17M15 9h5v12M2 21h20M8 8h3M8 12h3M8 16h3M17 13h1M17 17h1"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Khoa chủ quản</div>
                            <div class="hta-stat-value" style="font-size: 1.25rem;">{{ $administrativeClass->program->department->code ?? 'N/A' }}</div>
                            <div class="small text-muted mt-1">{{ $administrativeClass->program->department->name ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách Sinh viên trong lớp --}}
    <div class="hta-card">
        <div class="hta-card-header">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                    <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
                <span>Danh sách sinh viên lớp {{ $administrativeClass->name }}</span>
            </div>
            <span class="hta-badge hta-badge-teal">{{ $administrativeClass->students->count() }} sinh viên</span>
        </div>

        <div class="table-responsive">
            <table class="hta-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">STT</th>
                        <th style="width: 140px;">Mã sinh viên</th>
                        <th>Họ và tên</th>
                        <th>Email liên hệ</th>
                        <th class="text-center" style="width: 140px;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($administrativeClass->students as $index => $student)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <span class="hta-code-tag">{{ $student->student_code }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="hta-avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ mb_substr($student->user->name ?? 'SV', 0, 1) }}
                                    </div>
                                    <span class="fw-bold" style="color: #1b2559;">
                                        {{ $student->user->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-secondary small">
                                {{ $student->user->email ?? 'N/A' }}
                            </td>
                            <td class="text-center">
                                <span class="hta-badge {{ $student->status === 'studying' ? 'hta-badge-teal' : 'hta-badge-amber' }}">
                                    {{ $student->status === 'studying' ? 'Đang học' : $student->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="py-3">
                                    <p class="fw-bold mb-1" style="color: #1b2559;">Chưa có sinh viên nào trong lớp hành chính này</p>
                                    <p class="small text-muted mb-0">Sinh viên sẽ được xếp vào lớp này khi tiến hành nhập học hoặc phân lớp.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
