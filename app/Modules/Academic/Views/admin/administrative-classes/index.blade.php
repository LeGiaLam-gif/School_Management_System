@extends('layouts.app')

@section('title', 'Lớp hành chính · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · TỔ CHỨC LỚP HỌC</span>
            <h1 class="hta-page-title">Quản lý Lớp hành chính</h1>
            <p class="hta-page-subtitle">Quản lý các lớp niên chế sinh viên theo ngành, khóa tuyển sinh và phân công cố vấn học tập.</p>
        </div>
        <div>
            <a href="{{ route('admin.administrative-classes.create') }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Mở lớp mới
            </a>
        </div>
    </div>

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

    {{-- Thống kê nhanh --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-navy">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng lớp hành chính</div>
                    <div class="hta-stat-value">{{ $stats['total_classes'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-teal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng sinh viên</div>
                    <div class="hta-stat-value">{{ $stats['total_students'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Có Cố vấn học tập</div>
                    <div class="hta-stat-value">{{ $stats['with_advisor'] }} <span style="font-size: 0.9rem; font-weight: 600;">/ {{ $stats['total_classes'] }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Ngành đào tạo</div>
                    <div class="hta-stat-value">{{ $stats['total_programs'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc tìm kiếm --}}
    <div class="hta-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.administrative-classes.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4 col-lg-5">
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
                        placeholder="Tìm theo mã lớp, tên lớp..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <select name="program_id" class="form-select hta-select">
                    <option value="">— Tất cả các ngành —</option>
                    @foreach ($programs as $prog)
                        <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                            {{ $prog->name }} ({{ $prog->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <select name="academic_year" class="form-select hta-select">
                    <option value="">— Khóa tuyển sinh —</option>
                    @foreach ($years as $yr)
                        <option value="{{ $yr }}" {{ request('academic_year') == $yr ? 'selected' : '' }}>
                            Khóa {{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="hta-btn-primary flex-fill justify-content-center">
                    Lọc
                </button>
                @if (request()->hasAny(['search', 'program_id', 'academic_year']))
                    <a href="{{ route('admin.administrative-classes.index') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" title="Xóa bộ lọc">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng danh sách Lớp --}}
    <div class="hta-card">
        <div class="hta-card-header">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
                <span>Danh mục Lớp sinh viên hành chính</span>
            </div>
            <span class="hta-badge hta-badge-navy">{{ $classes->total() }} lớp</span>
        </div>

        <div class="table-responsive">
            <table class="hta-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 130px;">Mã lớp</th>
                        <th>Tên lớp</th>
                        <th>Ngành & Khoa</th>
                        <th class="text-center" style="width: 120px;">Khóa</th>
                        <th>Cố vấn học tập</th>
                        <th class="text-center" style="width: 110px;">Sĩ số</th>
                        <th class="text-end" style="width: 150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $item)
                        <tr>
                            <td>
                                <span class="hta-code-tag">{{ $item->code }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.administrative-classes.show', $item) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                    {{ $item->name }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold small" style="color: #1b2559;">
                                    {{ $item->program->name }}
                                </div>
                                <div class="text-muted" style="font-size: 0.78rem;">
                                    {{ $item->program->department->name ?? '' }} ({{ $item->program->code }})
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold border">K{{ $item->academic_year }}</span>
                            </td>
                            <td>
                                @if ($item->advisorLecturer)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="hta-avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ mb_substr($item->advisorLecturer->user->name ?? 'GV', 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.lecturers.show', $item->advisorLecturer) }}" class="fw-bold text-decoration-none small" style="color: #1b2559;">
                                                {{ $item->advisorLecturer->user->name }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                {{ $item->advisorLecturer->academic_title ?? 'GV' }} · {{ $item->advisorLecturer->lecturer_code }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Chưa có cố vấn</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-teal">{{ $item->students_count }} SV</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.administrative-classes.show', $item) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chi tiết">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.administrative-classes.edit', $item) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chỉnh sửa">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.administrative-classes.destroy', $item) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lớp {{ $item->name }} ({{ $item->code }})?');" class="d-inline">
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <p class="fw-bold mb-1" style="color: #1b2559;">Không tìm thấy lớp hành chính nào</p>
                                    <p class="small text-muted mb-3">Vui lòng điều chỉnh tiêu chí lọc hoặc mở lớp mới.</p>
                                    <a href="{{ route('admin.administrative-classes.create') }}" class="hta-btn-primary hta-btn-sm">Mở lớp mới</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($classes->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $classes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
