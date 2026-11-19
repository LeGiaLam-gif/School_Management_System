@extends('layouts.app')

@section('title', 'Lớp học phần · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header theo nhận diện HTA --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · PHÂN HỆ ĐÀO TẠO & GIẢNG DẠY · <span class="badge bg-white text-primary fw-semibold px-2 py-1">Giao diện cơ bản đã sẵn sàng</span></span>
            <h1 class="hta-page-title">Lớp học phần</h1>
            <p class="hta-page-subtitle">Quản lý mở lớp học phần theo từng học kỳ, quy định sĩ số tối đa và phân công giảng viên phụ trách giảng dạy.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.courses.index') }}" class="hta-btn-outline text-white border-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                Danh mục học phần
            </a>
            <a href="{{ route('admin.semesters.index') }}" class="hta-btn-outline text-white border-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Quản lý học kỳ
            </a>
            <a href="{{ route('admin.class-sections.create') }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Mở lớp học phần mới
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

    {{-- Thẻ thống kê nhanh --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-navy">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
                    </svg>
                </div>
                <div class="hta-stat-label">Tổng Lớp Học Phần</div>
                <div class="hta-stat-value">{{ number_format($stats['total_sections']) }}</div>
                <div class="hta-stat-sub">Toàn bộ các học kỳ đào tạo</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-green">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="hta-stat-label">Đang Mở Đăng Ký</div>
                <div class="hta-stat-value text-success">{{ number_format($stats['open_sections']) }}</div>
                <div class="hta-stat-sub">Trạng thái Open nhận SV ghi danh</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
                <div class="hta-stat-label">Chưa Gán Giảng Viên</div>
                <div class="hta-stat-value text-warning">{{ number_format($stats['unassigned_count']) }}</div>
                <div class="hta-stat-sub">Cần phân công phụ trách lớp</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-cyan">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="hta-stat-label">Lượt SV Đã Ghi Danh</div>
                <div class="hta-stat-value">{{ number_format($stats['total_enrolled']) }}</div>
                <div class="hta-stat-sub">Tổng lượt đăng ký toàn hệ thống</div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc tìm kiếm --}}
    <div class="hta-card p-3 mb-4">
        <form method="GET" action="{{ route('admin.class-sections.index') }}" class="row g-2 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Tìm kiếm</label>
                <input
                    type="text"
                    name="search"
                    class="form-control form-control-sm hta-input"
                    placeholder="Mã lớp HP, mã/tên môn, tên GV..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="col-lg-2 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Học kỳ</label>
                <select name="semester_id" class="form-select form-select-sm hta-input">
                    <option value="">Tất cả học kỳ</option>
                    @foreach ($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->name }} ({{ $sem->code }}) {{ $sem->is_current ? '★' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Học phần</label>
                <select name="course_id" class="form-select form-select-sm hta-input">
                    <option value="">Tất cả học phần</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->code }} - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Giảng viên</label>
                <select name="lecturer_id" class="form-select form-select-sm hta-input">
                    <option value="">Tất cả</option>
                    <option value="unassigned" {{ request('lecturer_id') === 'unassigned' ? 'selected' : '' }}>Chưa phân công</option>
                    @foreach ($lecturers as $lec)
                        <option value="{{ $lec->id }}" {{ request('lecturer_id') == $lec->id ? 'selected' : '' }}>
                            {{ $lec->user->name ?? 'GV #'.$lec->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Trạng thái</label>
                <select name="status" class="form-select form-select-sm hta-input">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Đang mở (Open)</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang học (In Progress)</option>
                    <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>Đã kết thúc (Finished)</option>
                    <option value="grades_published" {{ request('status') === 'grades_published' ? 'selected' : '' }}>Đã công bố điểm</option>
                </select>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
                <a href="{{ route('admin.class-sections.index') }}" class="btn btn-sm btn-light border">
                    Xóa lọc
                </a>
                <button type="submit" class="hta-btn-primary py-1 px-3">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>

    {{-- Bảng danh sách lớp học phần --}}
    <div class="hta-card p-0 overflow-hidden">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <span class="fw-bold" style="color: #1b2559;">
                Danh sách lớp học phần ({{ $classSections->total() }})
            </span>
            <span class="text-muted small">Hiển thị trang {{ $classSections->currentPage() }} / {{ $classSections->lastPage() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table hta-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 140px;">Mã Lớp HP</th>
                        <th>Học Phần</th>
                        <th>Học Kỳ</th>
                        <th>Giảng Viên Phụ Trách</th>
                        <th class="text-center" style="width: 110px;">Sĩ Số</th>
                        <th class="text-center" style="width: 130px;">Trạng Thái</th>
                        <th class="text-end" style="width: 130px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classSections as $section)
                        <tr>
                            <td>
                                <a href="{{ route('admin.class-sections.show', $section) }}" class="fw-bold font-monospace text-decoration-none" style="color: #273873;">
                                    {{ $section->code }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $section->course->name ?? '—' }}</div>
                                <div class="text-muted small font-monospace">
                                    {{ $section->course->code ?? '' }} · {{ $section->course->credits ?? 0 }} TC · Khoa {{ $section->course->department->code ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $section->semester->name ?? '—' }}
                                    @if ($section->semester?->is_current)
                                        <span class="text-success fw-bold">★</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if ($section->lecturer && $section->lecturer->user)
                                    <div class="fw-medium text-dark">{{ $section->lecturer->user->name }}</div>
                                    <div class="text-muted small font-monospace">{{ $section->lecturer->lecturer_code }}</div>
                                @else
                                    <span class="badge bg-warning-subtle text-warning fw-normal">
                                        Chưa phân công
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-bold {{ $section->isFull() ? 'text-danger' : 'text-dark' }}">
                                    {{ $section->enrolled_count }}
                                </span>
                                <span class="text-muted">/ {{ $section->capacity }}</span>
                                <div class="progress mt-1" style="height: 4px;">
                                    @php
                                        $pct = $section->capacity > 0 ? min(100, round(($section->enrolled_count / $section->capacity) * 100)) : 0;
                                        $barColor = $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <div class="progress-bar {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusVal = is_object($section->status) ? $section->status->value : $section->status;
                                    $statusMap = [
                                        'draft' => ['bg' => 'bg-secondary', 'text' => 'Bản nháp'],
                                        'open' => ['bg' => 'bg-success', 'text' => 'Đang mở'],
                                        'in_progress' => ['bg' => 'bg-primary', 'text' => 'Đang học'],
                                        'finished' => ['bg' => 'bg-info', 'text' => 'Đã kết thúc'],
                                        'grades_published' => ['bg' => 'bg-dark', 'text' => 'Công bố điểm'],
                                    ];
                                    $badge = $statusMap[$statusVal] ?? ['bg' => 'bg-secondary', 'text' => $statusVal];
                                @endphp
                                <span class="badge {{ $badge['bg'] }} text-white px-2 py-1 rounded-pill">
                                    {{ $badge['text'] }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.class-sections.show', $section) }}" class="btn btn-outline-secondary" title="Xem chi tiết">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.class-sections.edit', $section) }}" class="btn btn-outline-secondary" title="Chỉnh sửa">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-2 opacity-50">
                                    <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
                                </svg>
                                <div class="fw-semibold">Không tìm thấy lớp học phần nào</div>
                                <div class="small">Hãy thử thay đổi điều kiện lọc hoặc tạo lớp học phần mới.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($classSections->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $classSections->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
