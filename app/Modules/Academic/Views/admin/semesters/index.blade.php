@extends('layouts.app')

@section('title', 'Quản lý học kỳ · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header theo nhận diện HTA --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · NIÊN LỊCH ĐÀO TẠO & KHÓA TUYỂN SINH · <span class="badge bg-white text-primary fw-semibold px-2 py-1">Giao diện cơ bản đã sẵn sàng</span></span>
            <h1 class="hta-page-title">Quản lý học kỳ</h1>
            <p class="hta-page-subtitle">Quản lý các học kỳ, mốc thời gian đào tạo, khung thời gian đăng ký tín chỉ và kích hoạt học kỳ hiện tại.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.courses.index') }}" class="hta-btn-outline text-white border-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                Danh mục học phần
            </a>
            <a href="{{ route('admin.semesters.create') }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Thêm học kỳ mới
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
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng số học kỳ</div>
                    <div class="hta-stat-value">{{ $stats['total_semesters'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-teal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Học kỳ hiện tại</div>
                    <div class="hta-stat-value" style="font-size: 1.15rem; color: #0d9488;">{{ $stats['current_name'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Cổng ĐK tín chỉ đang mở</div>
                    <div class="hta-stat-value">{{ $stats['open_reg_count'] }} <span style="font-size: 0.9rem; font-weight: 600;">kỳ</span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 3 9 5-9 5-9-5zM3 12l9 5 9-5M3 16l9 5 9-5"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng lớp học phần</div>
                    <div class="hta-stat-value">{{ $stats['total_sections'] }} <span style="font-size: 0.9rem; font-weight: 600;">lớp</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="hta-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.semesters.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
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
                        placeholder="Tìm theo mã học kỳ hoặc tên..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-3">
                <select name="academic_year" class="form-select hta-select">
                    <option value="">— Năm học —</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                            Năm học {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_current" class="form-select hta-select">
                    <option value="">— Trạng thái —</option>
                    <option value="1" {{ request('is_current') === '1' ? 'selected' : '' }}>Học kỳ hiện tại</option>
                    <option value="0" {{ request('is_current') === '0' ? 'selected' : '' }}>Học kỳ khác</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="hta-btn-primary flex-fill justify-content-center">Lọc</button>
                @if(request()->hasAny(['search', 'academic_year', 'is_current']))
                    <a href="{{ route('admin.semesters.index') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" title="Xóa bộ lọc">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng danh sách học kỳ --}}
    <div class="hta-card">
        <div class="hta-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>Danh sách học kỳ đào tạo</span>
            </div>
            <span class="hta-badge hta-badge-navy">{{ $semesters->total() }} học kỳ</span>
        </div>

        <div class="table-responsive">
            <table class="hta-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 120px;">Mã HK</th>
                        <th>Tên học kỳ</th>
                        <th style="width: 130px;">Năm học</th>
                        <th>Thời gian đào tạo</th>
                        <th>Cổng đăng ký tín chỉ</th>
                        <th class="text-center" style="width: 110px;">Lớp mở</th>
                        <th class="text-center" style="width: 130px;">Trạng thái</th>
                        <th class="text-end" style="width: 170px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($semesters as $semester)
                        <tr>
                            <td>
                                <a href="{{ route('admin.semesters.show', $semester) }}" class="text-decoration-none">
                                    <span class="hta-code-tag">{{ $semester->code }}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.semesters.show', $semester) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                    {{ $semester->name }}
                                </a>
                                @if ($semester->is_current)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.75rem;">Học kỳ hiện tại</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-secondary">{{ $semester->academic_year }}</span>
                            </td>
                            <td class="small">
                                <div>{{ $semester->start_date ? $semester->start_date->format('d/m/Y') : '—' }} &rarr; {{ $semester->end_date ? $semester->end_date->format('d/m/Y') : '—' }}</div>
                            </td>
                            <td class="small">
                                @if ($semester->isRegistrationOpen())
                                    <span class="hta-badge hta-badge-teal mb-1">Đang mở cổng</span>
                                    <div class="text-muted small">Đóng: {{ $semester->registration_close_at->format('d/m/Y H:i') }}</div>
                                @elseif ($semester->registration_open_at && $semester->registration_open_at->isFuture())
                                    <span class="hta-badge hta-badge-amber mb-1">Chưa mở</span>
                                    <div class="text-muted small">Mở: {{ $semester->registration_open_at->format('d/m/Y H:i') }}</div>
                                @else
                                    <span class="text-muted">Đã đóng</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-violet">{{ $semester->class_sections_count }} lớp</span>
                            </td>
                            <td class="text-center">
                                @if ($semester->is_current)
                                    <span class="hta-badge hta-badge-teal">Kỳ hiện hành</span>
                                @else
                                    <form method="POST" action="{{ route('admin.semesters.set-current', $semester) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 0.75rem;" title="Đặt làm học kỳ hiện tại">
                                            Đặt hiện hành
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.semesters.show', $semester) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Xem chi tiết">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.semesters.edit', $semester) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chỉnh sửa">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/></svg>
                                    </a>
                                    @if (!$semester->is_current)
                                        <form method="POST" action="{{ route('admin.semesters.destroy', $semester) }}" onsubmit="return confirm('Bạn có chắc muốn xóa học kỳ {{ $semester->code }}?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light rounded-pill px-2 text-danger" title="Xóa">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m5 4v6m4-6v6"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Không tìm thấy học kỳ nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($semesters->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $semesters->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
