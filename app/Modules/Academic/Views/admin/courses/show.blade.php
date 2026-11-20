@extends('layouts.app')

@section('title', $course->name . ' (' . $course->code . ') · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Học phần</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $course->code }}</li>
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

    {{-- Course Detail Header Banner --}}
    <div class="hta-page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div class="d-flex align-items-start gap-3">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="hta-code-tag" style="font-size: 0.95rem; padding: 4px 10px;">{{ $course->code }}</span>
                    <span class="hta-badge hta-badge-amber">{{ $course->credits }} TÍN CHỈ</span>
                    @if ($course->is_active)
                        <span class="hta-badge hta-badge-teal">Đang giảng dạy</span>
                    @else
                        <span class="hta-badge hta-badge-rose">Tạm dừng</span>
                    @endif
                </div>
                <h1 class="hta-page-title mb-1">{{ $course->name }}</h1>
                <p class="hta-page-subtitle mb-0">
                    Khoa quản lý: <a href="{{ route('admin.departments.show', $course->department) }}" class="text-white text-decoration-underline">{{ $course->department->name }} ({{ $course->department->code }})</a>
                    · Lý thuyết: <strong>{{ $course->theory_periods }} tiết</strong>
                    · Thực hành: <strong>{{ $course->practice_periods }} tiết</strong>
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.courses.edit', $course) }}" class="hta-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                </svg>
                Chỉnh sửa môn học
            </a>
            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Bạn có chắc muốn xóa học phần {{ $course->name }}?');" class="d-inline">
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

    {{-- Nội dung chi tiết --}}
    <div class="row g-4 mb-4">
        {{-- Cột trái: Thông tin học phần --}}
        <div class="col-lg-5 col-xl-4">
            <div class="hta-card p-4 h-100">
                <h5 class="fw-bold mb-3 pb-2 border-bottom" style="color: #1b2559;">Thông tin học phần</h5>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">MÃ HỌC PHẦN</span>
                    <span class="hta-code-tag" style="font-size: 0.95rem;">{{ $course->code }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">TÊN HỌC PHẦN</span>
                    <span class="fw-bold fs-6" style="color: #1b2559;">{{ $course->name }}</span>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">KHOA CHUYÊN MÔN</span>
                    <a href="{{ route('admin.departments.show', $course->department) }}" class="fw-semibold text-decoration-none" style="color: #273873;">
                        {{ $course->department->name }} ({{ $course->department->code }})
                    </a>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <span class="hta-eyebrow d-block mb-1">TÍN CHỈ</span>
                        <span class="fw-bold fs-5 text-primary">{{ $course->credits }}</span>
                    </div>
                    <div class="col-4">
                        <span class="hta-eyebrow d-block mb-1">LÝ THUYẾT</span>
                        <span class="fw-semibold">{{ $course->theory_periods }} tiết</span>
                    </div>
                    <div class="col-4">
                        <span class="hta-eyebrow d-block mb-1">THỰC HÀNH</span>
                        <span class="fw-semibold">{{ $course->practice_periods }} tiết</span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">TRẠNG THÁI GIẢNG DẠY</span>
                    @if ($course->is_active)
                        <span class="hta-badge hta-badge-teal">Đang kích hoạt bình thường</span>
                    @else
                        <span class="hta-badge hta-badge-rose">Tạm dừng mở lớp</span>
                    @endif
                </div>

                <div class="mb-3">
                    <span class="hta-eyebrow d-block mb-1">MÔ TẢ HỌC PHẦN</span>
                    <p class="text-secondary small mb-0">{{ $course->description ?: 'Chưa cập nhật mô tả chi tiết cho môn học này.' }}</p>
                </div>

                <div class="small text-muted pt-3 border-top mt-auto">
                    Ngày tạo: {{ $course->created_at ? $course->created_at->format('d/m/Y H:i') : 'N/A' }}
                </div>
            </div>
        </div>

        {{-- Cột phải: Cây tiên quyết & Lớp học phần --}}
        <div class="col-lg-7 col-xl-8">
            {{-- Môn tiên quyết & Môn liên kết --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="hta-card p-3 h-100">
                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <h6 class="fw-bold mb-0" style="color: #1b2559;">Điều kiện Tiên Quyết</h6>
                        </div>
                        @if ($course->prerequisites->isNotEmpty())
                            <p class="small text-muted mb-2">Sinh viên phải hoàn thành các môn này trước khi đăng ký:</p>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($course->prerequisites as $prereq)
                                    <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="hta-code-tag me-1">{{ $prereq->code }}</span>
                                            <a href="{{ route('admin.courses.show', $prereq) }}" class="fw-semibold text-decoration-none text-dark small">
                                                {{ $prereq->name }}
                                            </a>
                                        </div>
                                        <span class="hta-badge hta-badge-amber">{{ $prereq->credits }} TC</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted small py-3 text-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" class="mb-1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <div>Môn học này không yêu cầu học phần tiên quyết.</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="hta-card p-3 h-100">
                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2">
                                <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                            </svg>
                            <h6 class="fw-bold mb-0" style="color: #1b2559;">Môn Phụ Thuộc (Là Tiên Quyết Cho)</h6>
                        </div>
                        @if ($course->requiredBy->isNotEmpty())
                            <p class="small text-muted mb-2">Học phần này là điều kiện tiên quyết cho các môn sau:</p>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($course->requiredBy as $dep)
                                    <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="hta-code-tag me-1">{{ $dep->code }}</span>
                                            <a href="{{ route('admin.courses.show', $dep) }}" class="fw-semibold text-decoration-none text-dark small">
                                                {{ $dep->name }}
                                            </a>
                                        </div>
                                        <span class="hta-badge hta-badge-teal">{{ $dep->credits }} TC</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted small py-3 text-center">
                                Chưa có học phần nào lấy môn này làm điều kiện tiên quyết.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Danh sách Lớp học phần đang mở --}}
            <div class="hta-card">
                <div class="hta-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                            <path d="m12 3 9 5-9 5-9-5zM3 12l9 5 9-5M3 16l9 5 9-5"/>
                        </svg>
                        <span>Các lớp học phần của môn này</span>
                    </div>
                    <span class="hta-badge hta-badge-teal">{{ $course->classSections->count() }} lớp</span>
                </div>

                <div class="table-responsive">
                    <table class="hta-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Mã LHP</th>
                                <th>Học kỳ</th>
                                <th>Giảng viên giảng dạy</th>
                                <th class="text-center" style="width: 120px;">Sĩ số / Tối đa</th>
                                <th class="text-center" style="width: 120px;">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($course->classSections as $cs)
                                <tr>
                                    <td>
                                        <span class="hta-code-tag">{{ $cs->code }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold" style="color: #1b2559;">{{ $cs->semester->name ?? 'N/A' }}</span>
                                        <div class="small text-muted">{{ $cs->semester->academic_year ?? '' }}</div>
                                    </td>
                                    <td>
                                        @if ($cs->lecturer)
                                            <div class="fw-semibold" style="color: #273873;">{{ $cs->lecturer->user->name ?? 'GV' }}</div>
                                            <div class="small text-muted">{{ $cs->lecturer->lecturer_code }}</div>
                                        @else
                                            <span class="text-muted fst-italic">Chưa gán giảng viên</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold">{{ $cs->enrolled_count }}</span> / <span class="text-muted">{{ $cs->capacity }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="hta-badge hta-badge-teal">{{ $cs->status?->value ?? $cs->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Chưa có lớp học phần nào được mở cho môn học này.
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
