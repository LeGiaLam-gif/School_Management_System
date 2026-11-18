@extends('layouts.app')

@section('title', 'Quản lý Sinh viên · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header theo nhận diện HTA --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · QUẢN LÝ HỒ SƠ HỌC VỤ</span>
            <h1 class="hta-page-title">Quản lý Sinh viên</h1>
            <p class="hta-page-subtitle">Quản lý toàn bộ danh sách sinh viên, lớp hành chính, ngành đào tạo và tài khoản đăng nhập.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.administrative-classes.index') }}" class="hta-btn-outline text-white border-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
                Lớp hành chính
            </a>
            <a href="{{ route('admin.students.create') }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Thêm sinh viên mới
            </a>
        </div>
    </div>

    {{-- Thông báo phản hồi --}}
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

    {{-- Thống kê nhanh --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-navy">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng số sinh viên</div>
                    <div class="hta-stat-value">{{ $stats['total_students'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-teal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Đang theo học</div>
                    <div class="hta-stat-value">{{ $stats['studying_students'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Bảo lưu / Tạm dừng</div>
                    <div class="hta-stat-value">{{ $stats['suspended_students'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Đã tốt nghiệp</div>
                    <div class="hta-stat-value">{{ $stats['graduated_students'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc & Tìm kiếm đa tầng --}}
    <div class="hta-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px; border: 1.8px solid #cbd5e1; border-right: none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2.2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </span>
                    <input
                        type="search"
                        name="search"
                        class="form-control border-start-0 hta-input"
                        style="border-radius: 0 12px 12px 0; border-left: none;"
                        placeholder="Tìm theo MSSV, họ tên, email, SĐT..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-2 col-lg-2">
                <select name="program_id" class="form-select hta-select">
                    <option value="">— Ngành học —</option>
                    @foreach ($programs as $prog)
                        <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                            {{ $prog->name }} ({{ $prog->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <select name="class_id" class="form-select hta-select">
                    <option value="">— Lớp hành chính —</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <select name="cohort_year" class="form-select hta-select">
                    <option value="">— Khóa tuyển sinh —</option>
                    @foreach ($cohortYears as $yr)
                        <option value="{{ $yr }}" {{ request('cohort_year') == $yr ? 'selected' : '' }}>
                            Khóa {{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-1">
                <select name="status" class="form-select hta-select">
                    <option value="">— Trạng thái —</option>
                    <option value="studying" {{ request('status') === 'studying' ? 'selected' : '' }}>Đang học</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Bảo lưu</option>
                    <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Tốt nghiệp</option>
                    <option value="dropped_out" {{ request('status') === 'dropped_out' ? 'selected' : '' }}>Thôi học</option>
                </select>
            </div>
            <div class="col-md-12 col-lg-1 d-flex gap-1">
                <button type="submit" class="hta-btn-primary flex-fill justify-content-center px-3" title="Lọc dữ liệu">
                    Lọc
                </button>
                @if (request()->hasAny(['search', 'program_id', 'class_id', 'cohort_year', 'status']))
                    <a href="{{ route('admin.students.index') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" title="Xóa bộ lọc">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng danh sách Sinh viên --}}
    <div class="hta-card">
        <div class="hta-card-header">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                    <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
                <span>Danh mục Hồ sơ Sinh viên</span>
            </div>
            <span class="hta-badge hta-badge-navy">{{ $students->total() }} sinh viên</span>
        </div>

        <div class="table-responsive">
            <table class="hta-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 120px;">MSSV</th>
                        <th>Họ và tên & Email</th>
                        <th>Lớp hành chính</th>
                        <th>Ngành đào tạo</th>
                        <th class="text-center" style="width: 110px;">Khóa</th>
                        <th class="text-center" style="width: 130px;">Tình trạng học</th>
                        <th class="text-center" style="width: 120px;">Tài khoản</th>
                        <th class="text-end" style="width: 160px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $item)
                        <tr>
                            <td>
                                <span class="hta-code-tag">{{ $item->student_code }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="hta-avatar-circle" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                        {{ mb_substr($item->user->name ?? 'SV', 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.students.show', $item) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                            {{ $item->user->name ?? 'N/A' }}
                                        </a>
                                        <div class="text-muted small">
                                            {{ $item->user->email ?? 'N/A' }}
                                            @if ($item->phone)
                                                · <span class="text-secondary">{{ $item->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($item->administrativeClass)
                                    <a href="{{ route('admin.administrative-classes.show', $item->administrativeClass) }}" class="fw-semibold text-decoration-none small" style="color: #1b2559;">
                                        {{ $item->administrativeClass->name }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ $item->administrativeClass->code }}
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Chưa xếp lớp</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.programs.show', $item->program) }}" class="text-decoration-none small fw-semibold text-secondary">
                                    {{ $item->program->name }}
                                </a>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    Khoa {{ $item->program->department->code ?? '' }} ({{ $item->program->code }})
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold border">K{{ $item->cohort_year }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = match ($item->status?->value ?? $item->status) {
                                        'studying' => 'hta-badge-teal',
                                        'suspended' => 'hta-badge-amber',
                                        'graduated' => 'hta-badge-navy',
                                        'dropped_out' => 'hta-badge-danger',
                                        default => 'hta-badge-secondary',
                                    };
                                    $statusLabel = match ($item->status?->value ?? $item->status) {
                                        'studying' => 'Đang học',
                                        'suspended' => 'Bảo lưu',
                                        'graduated' => 'Đã tốt nghiệp',
                                        'dropped_out' => 'Thôi học',
                                        default => $item->status,
                                    };
                                @endphp
                                <span class="hta-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($item->user->isActive())
                                    <span class="hta-badge hta-badge-teal">Hoạt động</span>
                                @else
                                    <span class="hta-badge hta-badge-amber">Đã khóa</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.students.show', $item) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chi tiết">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $item) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chỉnh sửa">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.students.toggle-status', $item) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light rounded-pill px-2 {{ $item->user->isActive() ? 'text-warning' : 'text-success' }}" title="{{ $item->user->isActive() ? 'Khóa tài khoản' : 'Mở khóa' }}">
                                            @if ($item->user->isActive())
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                            @else
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.students.destroy', $item) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sinh viên {{ $item->user->name }} ({{ $item->student_code }})?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light rounded-pill px-2 text-danger" title="Xóa">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m5 4v6m4-6v6"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <p class="fw-bold mb-1" style="color: #1b2559;">Không tìm thấy sinh viên nào</p>
                                    <p class="small text-muted mb-3">Vui lòng điều chỉnh tiêu chí lọc hoặc thêm hồ sơ sinh viên mới.</p>
                                    <a href="{{ route('admin.students.create') }}" class="hta-btn-primary hta-btn-sm">Thêm sinh viên mới</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
