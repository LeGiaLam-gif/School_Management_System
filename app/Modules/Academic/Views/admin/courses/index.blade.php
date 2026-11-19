@extends('layouts.app')

@section('title', 'Quản lý học phần · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header theo nhận diện HTA --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · DANH MỤC MÔN HỌC & TÍN CHỈ · <span class="badge bg-white text-primary fw-semibold px-2 py-1">Giao diện cơ bản đã sẵn sàng</span></span>
            <h1 class="hta-page-title">Quản lý học phần</h1>
            <p class="hta-page-subtitle">Quản lý toàn bộ môn học, số lượng tín chỉ, số tiết lý thuyết/thực hành và cây điều kiện môn tiên quyết.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.departments.index') }}" class="hta-btn-outline text-white border-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 21V4h11v17M15 9h5v12M2 21h20M8 8h3M8 12h3M8 16h3M17 13h1M17 17h1"/>
                </svg>
                Khoa chuyên môn
            </a>
            <a href="{{ route('admin.courses.create') }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Thêm học phần mới
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
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng số môn học</div>
                    <div class="hta-stat-value">{{ $stats['total_courses'] }}</div>
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
                    <div class="hta-stat-label">Đang giảng dạy</div>
                    <div class="hta-stat-value">{{ $stats['active_courses'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng khối lượng tín chỉ</div>
                    <div class="hta-stat-value">{{ $stats['total_credits'] }} <span style="font-size: 0.9rem; font-weight: 600;">TC</span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Có môn tiên quyết</div>
                    <div class="hta-stat-value">{{ $stats['courses_with_prereq'] }} <span style="font-size: 0.9rem; font-weight: 600;">môn</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc đa tầng --}}
    <div class="hta-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.courses.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
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
                        placeholder="Tìm theo mã hoặc tên học phần..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select hta-select">
                    <option value="">— Khoa chuyên môn —</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="credits" class="form-select hta-select">
                    <option value="">— Số tín chỉ —</option>
                    @foreach ([1, 2, 3, 4, 5, 6] as $c)
                        <option value="{{ $c }}" {{ request('credits') == $c ? 'selected' : '' }}>{{ $c }} Tín chỉ</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_active" class="form-select hta-select">
                    <option value="">— Trạng thái —</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Đang mở</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="hta-btn-primary flex-fill justify-content-center px-3" title="Lọc dữ liệu">Lọc</button>
                @if(request()->hasAny(['search', 'department_id', 'credits', 'is_active']))
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" title="Xóa bộ lọc">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng danh sách học phần --}}
    <div class="hta-card">
        <div class="hta-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <span>Danh mục môn học & học phần</span>
            </div>
            <span class="hta-badge hta-badge-navy">{{ $courses->total() }} học phần</span>
        </div>

        <div class="table-responsive">
            <table class="hta-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 130px;">Mã HP</th>
                        <th>Tên học phần</th>
                        <th>Khoa chuyên môn</th>
                        <th class="text-center" style="width: 100px;">Tín chỉ</th>
                        <th class="text-center" style="width: 110px;">LT / TH</th>
                        <th>Tiên quyết</th>
                        <th class="text-center" style="width: 110px;">Trạng thái</th>
                        <th class="text-end" style="width: 130px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr>
                            <td>
                                <a href="{{ route('admin.courses.show', $course) }}" class="text-decoration-none">
                                    <span class="hta-code-tag">{{ $course->code }}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.courses.show', $course) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                    {{ $course->name }}
                                </a>
                                @if($course->class_sections_count > 0)
                                    <div class="small text-muted">{{ $course->class_sections_count }} lớp học phần đã mở</div>
                                @endif
                            </td>
                            <td>
                                <span class="small fw-semibold text-secondary">{{ $course->department->name ?? '—' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-amber fw-bold">{{ $course->credits }} TC</span>
                            </td>
                            <td class="text-center small text-secondary">
                                {{ $course->theory_periods }}t / {{ $course->practice_periods }}t
                            </td>
                            <td>
                                @if($course->prerequisites->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($course->prerequisites as $prereq)
                                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.75rem;">{{ $prereq->code }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($course->is_active)
                                    <span class="hta-badge hta-badge-teal">Đang mở</span>
                                @else
                                    <span class="hta-badge hta-badge-rose">Tạm dừng</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Xem chi tiết">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chỉnh sửa">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Bạn có chắc muốn xóa học phần {{ $course->code }}?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light rounded-pill px-2 text-danger" title="Xóa">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m5 4v6m4-6v6"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Không tìm thấy học phần nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($courses->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $courses->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
