@extends('layouts.app')

@section('title', 'Quản lý khoa · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header theo nhận diện HTA --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · CẤU TRÚC ĐÀO TẠO</span>
            <h1 class="hta-page-title">Quản lý Khoa & Đơn vị đào tạo</h1>
            <p class="hta-page-subtitle">Thiết lập cơ cấu các khoa chuyên môn, quản lý ngành học và phân bổ giảng viên trực thuộc.</p>
        </div>
        <div>
            <a href="{{ route('admin.departments.create') }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Thêm khoa mới
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

    {{-- Thẻ số liệu thống kê nhanh --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-navy">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 21V4h11v17M15 9h5v12M2 21h20M8 8h3M8 12h3M8 16h3M17 13h1M17 17h1"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng số khoa</div>
                    <div class="hta-stat-value">{{ $stats['total_departments'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
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
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-teal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Giảng viên</div>
                    <div class="hta-stat-value">{{ $stats['total_lecturers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10M6 10h10"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Học phần phụ trách</div>
                    <div class="hta-stat-value">{{ $stats['total_courses'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="hta-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.departments.index') }}" class="row g-2 align-items-center">
            <div class="col-md-9 col-lg-10">
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
                        placeholder="Tìm kiếm theo mã khoa, tên khoa hoặc mô tả..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="hta-btn-primary flex-fill justify-content-center">
                    Tìm kiếm
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" title="Xóa tìm kiếm">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng danh sách Khoa --}}
    <div class="hta-card">
        <div class="hta-card-header">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                    <path d="M4 21V4h11v17M15 9h5v12M2 21h20M8 8h3M8 12h3M8 16h3M17 13h1M17 17h1"/>
                </svg>
                <span>Danh mục Khoa chuyên môn</span>
            </div>
            <span class="hta-badge hta-badge-navy">{{ $departments->total() }} khoa</span>
        </div>

        <div class="table-responsive">
            <table class="hta-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 120px;">Mã khoa</th>
                        <th>Tên khoa</th>
                        <th>Mô tả / Chức năng</th>
                        <th class="text-center" style="width: 130px;">Số ngành</th>
                        <th class="text-center" style="width: 140px;">Giảng viên</th>
                        <th class="text-center" style="width: 130px;">Học phần</th>
                        <th class="text-end" style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $dept)
                        <tr>
                            <td>
                                <span class="hta-code-tag">{{ $dept->code }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="hta-avatar-circle">
                                        {{ mb_substr($dept->code, 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.departments.show', $dept) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                            {{ $dept->name }}
                                        </a>
                                        <div class="text-muted small">Cập nhật: {{ $dept->updated_at?->format('d/m/Y') ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ Str::limit($dept->description, 75, '...') ?: 'Chưa có mô tả' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-amber">
                                    {{ $dept->programs_count }} ngành
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-teal">
                                    {{ $dept->lecturers_count }} giảng viên
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-violet">
                                    {{ $dept->courses_count }} môn
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.departments.show', $dept) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chi tiết">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chỉnh sửa">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khoa {{ $dept->name }} ({{ $dept->code }})?');" class="d-inline">
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
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#636b8a" stroke-width="1.5" class="opacity-50 mb-3">
                                        <path d="M4 21V4h11v17M15 9h5v12M2 21h20M8 8h3M8 12h3M8 16h3M17 13h1M17 17h1"/>
                                    </svg>
                                    <p class="fw-bold mb-1" style="color: #1b2559;">Chưa có khoa nào trong hệ thống</p>
                                    <p class="small text-muted mb-3">Hãy tạo khoa đầu tiên để phân bổ các ngành đào tạo và môn học.</p>
                                    <a href="{{ route('admin.departments.create') }}" class="hta-btn-primary hta-btn-sm">Thêm khoa mới</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($departments->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $departments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
