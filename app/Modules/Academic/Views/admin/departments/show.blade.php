@extends('layouts.app')

@section('title', $department->name . ' (' . $department->code . ') · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}" class="text-decoration-none">Quản lý khoa</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $department->code }}</li>
        </ol>
    </nav>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: #ecfdf5; color: #065f46;">
            <div class="d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="10"/></svg>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Header Banner Khoa --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="hta-avatar-circle hta-avatar-circle-lg bg-white">
                {{ mb_substr($department->code, 0, 2) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="hta-badge hta-badge-amber">{{ $department->code }}</span>
                    <span class="hta-eyebrow mb-0">ĐƠN VỊ ĐÀO TẠO</span>
                </div>
                <h1 class="hta-page-title mb-1">{{ $department->name }}</h1>
                <p class="hta-page-subtitle">{{ $department->description ?: 'Chưa có thông tin mô tả chi tiết.' }}</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.departments.edit', $department) }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                </svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.programs.create', ['department_id' => $department->id]) }}" class="btn btn-light rounded-pill px-3 fw-bold" style="color: #1b2559;">
                + Thêm ngành học
            </a>
        </div>
    </div>

    {{-- Thống kê quy mô khoa --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Ngành đào tạo trực thuộc</div>
                    <div class="hta-stat-value">{{ $department->programs->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-teal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Giảng viên cơ hữu</div>
                    <div class="hta-stat-value">{{ $department->lecturers->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10M6 10h10"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Môn học do khoa quản lý</div>
                    <div class="hta-stat-value">{{ $department->courses->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs nội dung: Ngành học, Giảng viên, Môn học --}}
    <div class="hta-card">
        <div class="hta-card-header bg-white border-bottom p-3">
            <ul class="nav nav-pills gap-2" id="deptTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-bold px-4" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs-pane" type="button" role="tab">
                        Các ngành học ({{ $department->programs->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold px-4" id="lecturers-tab" data-bs-toggle="tab" data-bs-target="#lecturers-pane" type="button" role="tab">
                        Đội ngũ giảng viên ({{ $department->lecturers->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold px-4" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses-pane" type="button" role="tab">
                        Môn học phụ trách ({{ $department->courses->count() }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="deptTabContent">
            {{-- Tab 1: Ngành học --}}
            <div class="tab-pane fade show active" id="programs-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="hta-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Mã ngành</th>
                                <th>Tên ngành đào tạo</th>
                                <th class="text-center" style="width: 140px;">Thời gian</th>
                                <th class="text-center" style="width: 140px;">Tín chỉ tích lũy</th>
                                <th class="text-center" style="width: 130px;">Lớp hành chính</th>
                                <th class="text-center" style="width: 120px;">Sinh viên</th>
                                <th class="text-end" style="width: 120px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($department->programs as $program)
                                <tr>
                                    <td><span class="hta-code-tag">{{ $program->code }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.programs.show', $program) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                            {{ $program->name }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $program->duration_years }} năm</td>
                                    <td class="text-center"><span class="badge bg-light text-dark fw-bold border">{{ $program->total_credits_required }} tín chỉ</span></td>
                                    <td class="text-center"><span class="hta-badge hta-badge-navy">{{ $program->administrative_classes_count }} lớp</span></td>
                                    <td class="text-center"><span class="hta-badge hta-badge-teal">{{ $program->students_count }} sinh viên</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chi tiết">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Sửa">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Khoa này chưa có ngành đào tạo nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 2: Giảng viên --}}
            <div class="tab-pane fade" id="lecturers-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="hta-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Mã GV</th>
                                <th>Họ và tên</th>
                                <th>Học hàm / Học vị</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Văn phòng</th>
                                <th class="text-end" style="width: 120px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($department->lecturers as $lecturer)
                                <tr>
                                    <td><span class="hta-code-tag">{{ $lecturer->lecturer_code }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="hta-avatar-circle">
                                                {{ mb_substr($lecturer->user->name ?? 'GV', 0, 1) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.lecturers.show', $lecturer) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                                    {{ $lecturer->user->name ?? '—' }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="hta-badge hta-badge-violet">{{ $lecturer->academic_title ?: 'Giảng viên' }}</span></td>
                                    <td>{{ $lecturer->user->email ?? '—' }}</td>
                                    <td>{{ $lecturer->phone ?: $lecturer->user->phone ?: '—' }}</td>
                                    <td>{{ $lecturer->office ?: '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.lecturers.show', $lecturer) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Xem hồ sơ">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Khoa này chưa có giảng viên nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 3: Môn học --}}
            <div class="tab-pane fade" id="courses-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="hta-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Mã môn</th>
                                <th>Tên môn học / Học phần</th>
                                <th class="text-center" style="width: 110px;">Số tín chỉ</th>
                                <th class="text-center" style="width: 120px;">Lý thuyết</th>
                                <th class="text-center" style="width: 120px;">Thực hành</th>
                                <th class="text-center" style="width: 130px;">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($department->courses as $course)
                                <tr>
                                    <td><span class="hta-code-tag">{{ $course->code }}</span></td>
                                    <td><span class="fw-bold" style="color: #1b2559;">{{ $course->name }}</span></td>
                                    <td class="text-center"><span class="badge bg-light text-dark fw-bold border">{{ $course->credits }} TC</span></td>
                                    <td class="text-center">{{ $course->theory_periods }} tiết</td>
                                    <td class="text-center">{{ $course->practice_periods }} tiết</td>
                                    <td class="text-center">
                                        @if ($course->is_active)
                                            <span class="hta-badge hta-badge-teal">Đang mở</span>
                                        @else
                                            <span class="hta-badge hta-badge-rose">Đã khóa</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Khoa này chưa quản lý môn học nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
