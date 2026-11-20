@extends('layouts.app')

@section('title', $semester->name . ' (' . $semester->code . ') · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.semesters.index') }}" class="text-decoration-none">Học kỳ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $semester->code }}</li>
        </ol>
    </nav>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: #ecfdf5; color: #065f46;">
            <div class="d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="10"/></svg>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px; background: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <span class="fw-semibold">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Header chi tiết --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="hta-eyebrow mb-0">HỌC KỲ NIÊN KHÓA {{ $semester->academic_year }}</span>
                @if ($semester->is_current)
                    <span class="badge bg-success text-white px-2 py-1 rounded-pill fw-semibold">
                        ★ Học kỳ hiện hành
                    </span>
                @endif
                @if ($semester->isRegistrationOpen())
                    <span class="badge bg-info text-dark px-2 py-1 rounded-pill fw-semibold">
                        Đang mở ĐK tín chỉ
                    </span>
                @endif
            </div>
            <h1 class="hta-page-title">{{ $semester->name }} ({{ $semester->code }})</h1>
            <p class="hta-page-subtitle">Niên khóa: {{ $semester->academic_year }} · Diễn ra từ {{ optional($semester->start_date)->format('d/m/Y') }} đến {{ optional($semester->end_date)->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if (! $semester->is_current)
                <form method="POST" action="{{ route('admin.semesters.set-current', $semester) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="hta-btn-outline text-white border-white">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        Đặt làm học kỳ hiện hành
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.semesters.edit', $semester) }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
                Chỉnh sửa
            </a>
            @if (! $semester->is_current && $semester->classSections->isEmpty())
                <form method="POST" action="{{ route('admin.semesters.destroy', $semester) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa học kỳ này không?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" style="border-radius: 12px; font-weight: 600;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Xóa
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Thông số tổng quan --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="hta-card p-3 h-100">
                <span class="text-muted small fw-semibold text-uppercase">Thời gian học tập</span>
                <div class="fw-bold fs-6 mt-1 text-dark">
                    {{ optional($semester->start_date)->format('d/m/Y') }} &rarr; {{ optional($semester->end_date)->format('d/m/Y') }}
                </div>
                <div class="text-muted small mt-1">
                    @php
                        $weeks = $semester->start_date && $semester->end_date ? ceil($semester->start_date->diffInDays($semester->end_date) / 7) : 0;
                    @endphp
                    Thời lượng ước tính: {{ $weeks }} tuần
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="hta-card p-3 h-100">
                <span class="text-muted small fw-semibold text-uppercase">Đợt mở đăng ký học phần</span>
                <div class="fw-bold fs-6 mt-1 text-dark">
                    @if ($semester->registration_open_at && $semester->registration_close_at)
                        {{ $semester->registration_open_at->format('d/m/Y H:i') }}<br>
                        &rarr; {{ $semester->registration_close_at->format('d/m/Y H:i') }}
                    @else
                        <span class="text-muted fst-italic">Chưa thiết lập</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="hta-card p-3 h-100">
                <span class="text-muted small fw-semibold text-uppercase">Hạn chót rút học phần</span>
                <div class="fw-bold fs-6 mt-1 text-dark">
                    @if ($semester->drop_deadline)
                        {{ $semester->drop_deadline->format('d/m/Y') }}
                    @else
                        <span class="text-muted fst-italic">Chưa thiết lập</span>
                    @endif
                </div>
                <div class="text-muted small mt-1">Không tính điểm F khi rút trước hạn</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="hta-card p-3 h-100">
                <span class="text-muted small fw-semibold text-uppercase">Giới hạn tín chỉ</span>
                <div class="fw-bold fs-5 mt-1 text-primary">
                    {{ $semester->min_credits }} - {{ $semester->max_credits }} <span class="fs-6 text-muted fw-normal">tín chỉ / SV</span>
                </div>
                <div class="text-muted small mt-1">Quy định tối thiểu và tối đa</div>
            </div>
        </div>
    </div>

    {{-- Danh sách lớp học phần trong học kỳ --}}
    <div class="hta-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold mb-0" style="color: #1b2559;">Danh Sách Lớp Học Phần Trong Kỳ ({{ $semester->classSections->count() }})</h2>
                <p class="text-muted small mb-0">Các lớp học phần mở trong kỳ {{ $semester->code }}.</p>
            </div>
            <a href="{{ route('admin.class-sections.create', ['semester_id' => $semester->id]) }}" class="hta-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Mở lớp học phần mới
            </a>
        </div>

        <div class="table-responsive">
            <table class="table hta-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 140px;">Mã lớp HP</th>
                        <th>Học phần</th>
                        <th>Khoa bộ môn</th>
                        <th>Giảng viên</th>
                        <th class="text-center" style="width: 110px;">Sĩ số</th>
                        <th class="text-center" style="width: 130px;">Trạng thái</th>
                        <th class="text-end" style="width: 130px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($semester->classSections as $section)
                        <tr>
                            <td>
                                <a href="{{ route('admin.class-sections.show', $section) }}" class="fw-bold font-monospace text-decoration-none" style="color: #273873;">
                                    {{ $section->code }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $section->course->name ?? '—' }}</div>
                                <div class="text-muted small font-monospace">{{ $section->course->code ?? '' }} · {{ $section->course->credits ?? 0 }} TC</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $section->course->department->code ?? '—' }}</span>
                            </td>
                            <td>
                                @if ($section->lecturer && $section->lecturer->user)
                                    <div class="fw-medium text-dark">{{ $section->lecturer->user->name }}</div>
                                    <div class="text-muted small font-monospace">{{ $section->lecturer->lecturer_code }}</div>
                                @else
                                    <span class="badge bg-warning-subtle text-warning fw-normal">Chưa phân công</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold">{{ $section->enrollments_count ?? $section->current_enrollment ?? 0 }}</span>
                                <span class="text-muted">/ {{ $section->max_capacity }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusVal = is_object($section->status) ? $section->status->value : $section->status;
                                    $statusMap = [
                                        'open' => ['bg' => 'bg-success', 'text' => 'Đang mở'],
                                        'closed' => ['bg' => 'bg-secondary', 'text' => 'Đã đóng'],
                                        'cancelled' => ['bg' => 'bg-danger', 'text' => 'Đã hủy'],
                                        'completed' => ['bg' => 'bg-info', 'text' => 'Hoàn thành'],
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
                            <td colspan="7" class="text-center py-4 text-muted">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-2 opacity-50"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                                <div>Chưa có lớp học phần nào được mở trong học kỳ này.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
