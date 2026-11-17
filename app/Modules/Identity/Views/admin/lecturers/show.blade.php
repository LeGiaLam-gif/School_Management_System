@extends('layouts.app')

@section('title', ($lecturer->user->name ?? $lecturer->lecturer_code) . ' · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.lecturers.index') }}" class="text-decoration-none">Giảng viên</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $lecturer->lecturer_code }}</li>
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

    {{-- Lecturer Detail Header Banner --}}
    <div class="hta-page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div class="d-flex align-items-start gap-3">
            <div class="hta-avatar-circle hta-avatar-circle-lg" style="width: 56px; height: 56px; font-size: 1.4rem;">
                {{ mb_substr($lecturer->user->name ?? 'GV', 0, 1) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="hta-code-tag" style="font-size: 0.95rem; padding: 4px 10px;">{{ $lecturer->lecturer_code }}</span>
                    <span class="hta-eyebrow mb-0">{{ mb_strtoupper($lecturer->academic_title ?: 'GIẢNG VIÊN') }}</span>
                    @if ($lecturer->user->isActive())
                        <span class="hta-badge hta-badge-teal">Đang công tác</span>
                    @else
                        <span class="hta-badge hta-badge-amber">Tài khoản bị khóa</span>
                    @endif
                </div>
                <h1 class="hta-page-title mb-1">{{ $lecturer->user->name ?? 'N/A' }}</h1>
                <p class="hta-page-subtitle mb-0">
                    Khoa: <a href="{{ route('admin.departments.show', $lecturer->department) }}" class="text-white text-decoration-underline">{{ $lecturer->department->name }} ({{ $lecturer->department->code }})</a>
                    · Email: <strong>{{ $lecturer->user->email }}</strong>
                    @if ($lecturer->phone)
                        · Điện thoại: <strong>{{ $lecturer->phone }}</strong>
                    @endif
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.lecturers.edit', $lecturer) }}" class="hta-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                </svg>
                Chỉnh sửa hồ sơ
            </a>
            <form method="POST" action="{{ route('admin.lecturers.toggle-status', $lecturer) }}" class="d-inline">
                @csrf
                <button type="submit" class="hta-btn-outline text-white border-white">
                    @if ($lecturer->user->isActive())
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Khóa tài khoản
                    @else
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                        Mở khóa
                    @endif
                </button>
            </form>
            <form method="POST" action="{{ route('admin.lecturers.destroy', $lecturer) }}" onsubmit="return confirm('Bạn có chắc muốn xóa giảng viên {{ $lecturer->user->name }}?');" class="d-inline">
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

    {{-- Thông tin tóm tắt & Hồ sơ chi tiết --}}
    <div class="row g-4 mb-4">
        {{-- Cột trái: Thông tin học thuật & công tác --}}
        <div class="col-lg-5 col-xl-4">
            <div class="hta-card p-4 h-100">
                <h5 class="fw-bold mb-3 pb-2 border-bottom" style="color: #1b2559;">Hồ sơ cán bộ</h5>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">MÃ SỐ ĐỊNH DANH</span>
                    <span class="hta-code-tag" style="font-size: 0.95rem;">{{ $lecturer->lecturer_code }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">HỌC HÀM / HỌC VỊ</span>
                    <span class="fw-bold" style="color: #1b2559;">{{ $lecturer->academic_title ?: 'Chưa cập nhật' }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">ĐƠN VỊ CÔNG TÁC</span>
                    <a href="{{ route('admin.departments.show', $lecturer->department) }}" class="fw-semibold text-decoration-none" style="color: #273873;">
                        {{ $lecturer->department->name }} ({{ $lecturer->department->code }})
                    </a>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">VĂN PHÒNG LÀM VIỆC</span>
                    <span class="text-secondary small">{{ $lecturer->office ?: 'Chưa cập nhật' }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">SỐ ĐIỆN THOẠI</span>
                    <span class="text-secondary small">{{ $lecturer->phone ?: 'Chưa cập nhật' }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">ĐỊA CHỈ LIÊN LẠC</span>
                    <span class="text-secondary small">{{ $lecturer->address ?: 'Chưa cập nhật' }}</span>
                </div>

                <hr class="my-3">

                <div class="mb-2">
                    <span class="hta-eyebrow d-block mb-1">TRẠNG THÁI HỆ THỐNG</span>
                    @if ($lecturer->user->isActive())
                        <span class="hta-badge hta-badge-teal">Đang kích hoạt</span>
                    @else
                        <span class="hta-badge hta-badge-amber">Đang tạm khóa</span>
                    @endif
                </div>

                <div class="small text-muted">
                    Tham gia: {{ $lecturer->created_at ? $lecturer->created_at->format('d/m/Y') : 'N/A' }}
                </div>
            </div>
        </div>

        {{-- Cột phải: Thống kê & Tabs lớp giảng dạy, lớp cố vấn --}}
        <div class="col-lg-7 col-xl-8">
            {{-- Stat Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="hta-stat-card">
                        <div class="hta-stat-icon hta-stat-icon-amber">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Lớp làm Cố vấn học tập</div>
                            <div class="hta-stat-value">{{ $lecturer->advisedClasses->count() }} <span style="font-size: 0.95rem; font-weight: 600;">lớp</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="hta-stat-card">
                        <div class="hta-stat-icon hta-stat-icon-teal">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="hta-stat-label">Lớp học phần phụ trách</div>
                            <div class="hta-stat-value">{{ $lecturer->classSections->count() }} <span style="font-size: 0.95rem; font-weight: 600;">lớp HP</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="hta-card">
                <div class="hta-card-header p-0">
                    <ul class="nav nav-tabs border-0 px-3 pt-2" id="lecturerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold py-3 px-4 border-0" id="advised-tab" data-bs-toggle="tab" data-bs-target="#advised-pane" type="button" role="tab" style="color: #1b2559;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                </svg>
                                Lớp Cố vấn học tập ({{ $lecturer->advisedClasses->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-3 px-4 border-0" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections-pane" type="button" role="tab" style="color: #1b2559;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                </svg>
                                Lớp học phần phân công ({{ $lecturer->classSections->count() }})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="lecturerTabsContent">
                    {{-- Tab 1: Lớp cố vấn --}}
                    <div class="tab-pane fade show active p-3" id="advised-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="hta-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 130px;">Mã lớp</th>
                                        <th>Tên lớp</th>
                                        <th>Ngành đào tạo</th>
                                        <th class="text-center" style="width: 120px;">Khóa</th>
                                        <th class="text-end" style="width: 100px;">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lecturer->advisedClasses as $c)
                                        <tr>
                                            <td>
                                                <span class="hta-code-tag">{{ $c->code }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.administrative-classes.show', $c) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                                    {{ $c->name }}
                                                </a>
                                            </td>
                                            <td class="small text-secondary">
                                                {{ $c->program->name ?? '' }} ({{ $c->program->code ?? '' }})
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">K{{ $c->academic_year }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.administrative-classes.show', $c) }}" class="btn btn-sm btn-light rounded-pill px-2">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                Giảng viên hiện chưa được phân công làm Cố vấn học tập cho lớp nào.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 2: Lớp học phần --}}
                    <div class="tab-pane fade p-3" id="sections-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="hta-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 130px;">Mã LHP</th>
                                        <th>Học phần</th>
                                        <th class="text-center" style="width: 100px;">Tín chỉ</th>
                                        <th class="text-center" style="width: 120px;">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lecturer->classSections as $cs)
                                        <tr>
                                            <td>
                                                <span class="hta-code-tag">{{ $cs->code }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold" style="color: #1b2559;">{{ $cs->course->name ?? 'N/A' }}</span>
                                                <div class="text-muted small">{{ $cs->course->code ?? '' }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="hta-badge hta-badge-amber">{{ $cs->course->credits ?? 0 }} TC</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="hta-badge hta-badge-teal">{{ $cs->status ?? 'Active' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                Giảng viên hiện chưa được phân công giảng dạy lớp học phần nào.
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
