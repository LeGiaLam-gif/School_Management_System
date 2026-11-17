@extends('layouts.app')

@section('title', 'Chương trình đào tạo / Ngành học · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · CHƯƠNG TRÌNH ĐÀO TẠO</span>
            <h1 class="hta-page-title">Quản lý Ngành đào tạo</h1>
            <p class="hta-page-subtitle">Khai báo danh mục các ngành học, quy định số tín chỉ tốt nghiệp và thời lượng đào tạo chuẩn.</p>
        </div>
        <div>
            <a href="{{ route('admin.programs.create') }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Thêm ngành mới
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
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng ngành đào tạo</div>
                    <div class="hta-stat-value">{{ $stats['total_programs'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-navy">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Lớp hành chính</div>
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
                    <div class="hta-stat-label">Sinh viên đang học</div>
                    <div class="hta-stat-value">{{ $stats['total_students'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tín chỉ TB / Ngành</div>
                    <div class="hta-stat-value">{{ $stats['avg_credits'] }} <span style="font-size: 0.9rem; font-weight: 600;">TC</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tìm kiếm & Lọc theo Khoa --}}
    <div class="hta-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.programs.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5 col-lg-6">
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
                        placeholder="Tìm kiếm theo mã ngành, tên ngành đào tạo..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <select name="department_id" class="form-select hta-select">
                    <option value="">— Tất cả các khoa —</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="hta-btn-primary flex-fill justify-content-center">
                    Lọc dữ liệu
                </button>
                @if (request()->hasAny(['search', 'department_id']))
                    <a href="{{ route('admin.programs.index') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" title="Xóa bộ lọc">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng danh sách Ngành --}}
    <div class="hta-card">
        <div class="hta-card-header">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                    <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                </svg>
                <span>Danh mục Ngành & Chương trình đào tạo</span>
            </div>
            <span class="hta-badge hta-badge-navy">{{ $programs->total() }} ngành</span>
        </div>

        <div class="table-responsive">
            <table class="hta-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 120px;">Mã ngành</th>
                        <th>Tên ngành đào tạo</th>
                        <th>Khoa quản lý</th>
                        <th class="text-center" style="width: 130px;">Thời gian</th>
                        <th class="text-center" style="width: 140px;">Tín chỉ yêu cầu</th>
                        <th class="text-center" style="width: 120px;">Lớp HC</th>
                        <th class="text-center" style="width: 120px;">Sinh viên</th>
                        <th class="text-end" style="width: 160px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $prog)
                        <tr>
                            <td>
                                <span class="hta-code-tag">{{ $prog->code }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.programs.show', $prog) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                    {{ $prog->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.departments.show', $prog->department) }}" class="text-decoration-none small fw-semibold text-secondary">
                                    {{ $prog->department->name }} ({{ $prog->department->code }})
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold border">{{ $prog->duration_years }} năm</span>
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-amber">{{ $prog->total_credits_required }} tín chỉ</span>
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-navy">{{ $prog->administrative_classes_count }} lớp</span>
                            </td>
                            <td class="text-center">
                                <span class="hta-badge hta-badge-teal">{{ $prog->students_count }} SV</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.programs.show', $prog) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chi tiết">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.programs.edit', $prog) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chỉnh sửa">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.programs.destroy', $prog) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ngành {{ $prog->name }} ({{ $prog->code }})?');" class="d-inline">
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
                                    <p class="fw-bold mb-1" style="color: #1b2559;">Không tìm thấy ngành đào tạo nào</p>
                                    <p class="small text-muted mb-3">Vui lòng điều chỉnh tiêu chí lọc hoặc thêm ngành học mới.</p>
                                    <a href="{{ route('admin.programs.create') }}" class="hta-btn-primary hta-btn-sm">Thêm ngành mới</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($programs->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $programs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
