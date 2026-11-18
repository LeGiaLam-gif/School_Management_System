@extends('layouts.app')

@section('title', $program->name . ' (' . $program->code . ') · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.programs.index') }}" class="text-decoration-none">Chương trình đào tạo</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $program->code }}</li>
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

    {{-- Program Detail Header Banner --}}
    <div class="hta-page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div class="d-flex align-items-start gap-3">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                </svg>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="hta-code-tag" style="font-size: 0.95rem; padding: 4px 10px;">{{ $program->code }}</span>
                    <span class="hta-eyebrow mb-0">KHOA {{ mb_strtoupper($program->department->name) }}</span>
                </div>
                <h1 class="hta-page-title mb-1">{{ $program->name }}</h1>
                <p class="hta-page-subtitle mb-0">
                    Khoa chủ quản: <a href="{{ route('admin.departments.show', $program->department) }}" class="text-white text-decoration-underline">{{ $program->department->name }} ({{ $program->department->code }})</a>
                    · Chuẩn đào tạo: <strong>{{ $program->duration_years }} năm</strong> ({{ $program->total_credits_required }} tín chỉ tích lũy)
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.programs.edit', $program) }}" class="hta-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                </svg>
                Chỉnh sửa ngành
            </a>
            <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" onsubmit="return confirm('Bạn có chắc muốn xóa ngành {{ $program->name }}?');" class="d-inline">
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

    {{-- Thống kê nhanh của Ngành --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Lớp hành chính</div>
                    <div class="hta-stat-value">{{ $program->administrativeClasses->count() }}</div>
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
                    <div class="hta-stat-label">Sinh viên ghi danh</div>
                    <div class="hta-stat-value">{{ $program->students->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-navy">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Môn học CTĐT</div>
                    <div class="hta-stat-value">{{ $program->courses->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tín chỉ yêu cầu</div>
                    <div class="hta-stat-value">{{ $program->total_credits_required }} <span style="font-size: 0.9rem; font-weight: 600;">TC</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs nội dung --}}
    <div class="hta-card">
        <div class="hta-card-header p-0">
            <ul class="nav nav-tabs border-0 px-3 pt-2" id="programTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold py-3 px-4 border-0" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes-pane" type="button" role="tab" style="color: #1b2559;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        </svg>
                        Lớp hành chính ({{ $program->administrativeClasses->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-3 px-4 border-0" id="students-tab" data-bs-toggle="tab" data-bs-target="#students-pane" type="button" role="tab" style="color: #1b2559;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                            <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>
                        Sinh viên ({{ $program->students->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-3 px-4 border-0" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses-pane" type="button" role="tab" style="color: #1b2559;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        </svg>
                        Khung môn học ({{ $program->courses->count() }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="programTabsContent">
            {{-- Tab 1: Lớp hành chính --}}
            <div class="tab-pane fade show active p-3" id="classes-pane" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="small text-muted">Các lớp niên chế thuộc ngành đào tạo</span>
                    <a href="{{ route('admin.administrative-classes.create', ['program_id' => $program->id]) }}" class="hta-btn-primary hta-btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Mở lớp hành chính mới
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="hta-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 130px;">Mã lớp</th>
                                <th>Tên lớp hành chính</th>
                                <th class="text-center" style="width: 140px;">Khóa tuyển sinh</th>
                                <th>Cố vấn học tập</th>
                                <th class="text-center" style="width: 120px;">Sĩ số</th>
                                <th class="text-end" style="width: 140px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($program->administrativeClasses as $class)
                                <tr>
                                    <td>
                                        <span class="hta-code-tag">{{ $class->code }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.administrative-classes.show', $class) }}" class="fw-bold text-decoration-none" style="color: #1b2559;">
                                            {{ $class->name }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark fw-bold border">Khóa {{ $class->academic_year }}</span>
                                    </td>
                                    <td>
                                        @if ($class->advisorLecturer)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="hta-avatar-circle" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                    {{ mb_substr($class->advisorLecturer->user->name ?? 'GV', 0, 1) }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold small" style="color: #1b2559;">{{ $class->advisorLecturer->user->name }}</span>
                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $class->advisorLecturer->academic_title ?? 'Giảng viên' }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small fst-italic">Chưa phân công</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="hta-badge hta-badge-teal">{{ $class->students_count }} SV</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.administrative-classes.show', $class) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chi tiết">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.administrative-classes.edit', $class) }}" class="btn btn-sm btn-light rounded-pill px-2" title="Chỉnh sửa">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                                                <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Chưa có lớp hành chính nào thuộc ngành này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 2: Sinh viên --}}
            <div class="tab-pane fade p-3" id="students-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="hta-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 130px;">MSSV</th>
                                <th>Họ và tên</th>
                                <th>Email</th>
                                <th class="text-center" style="width: 140px;">Khóa học</th>
                                <th class="text-center" style="width: 140px;">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($program->students as $student)
                                <tr>
                                    <td>
                                        <span class="hta-code-tag">{{ $student->student_code }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold" style="color: #1b2559;">{{ $student->user->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $student->user->email ?? 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">K{{ $student->enrollment_year }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="hta-badge {{ $student->status === 'studying' ? 'hta-badge-teal' : 'hta-badge-amber' }}">
                                            {{ $student->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Chưa có sinh viên nào đăng ký ngành học này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 3: Khung môn học --}}
            <div class="tab-pane fade p-3" id="courses-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="hta-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 130px;">Mã HP</th>
                                <th>Tên môn học / Học phần</th>
                                <th class="text-center" style="width: 130px;">Số tín chỉ</th>
                                <th class="text-center" style="width: 140px;">LT / TH</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($program->courses as $course)
                                <tr>
                                    <td>
                                        <span class="hta-code-tag">{{ $course->code }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold" style="color: #1b2559;">{{ $course->name }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="hta-badge hta-badge-amber">{{ $course->credits }} TC</span>
                                    </td>
                                    <td class="text-center small text-muted">
                                        {{ $course->theory_hours ?? 0 }} LT / {{ $course->practice_hours ?? 0 }} TH
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Chưa có học phần nào được gắn vào chương trình đào tạo này.
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
@endsection
