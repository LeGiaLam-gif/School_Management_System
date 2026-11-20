@extends('layouts.app')

@section('title', 'Lớp ' . $classSection->code . ' (' . $classSection->course->name . ') · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.class-sections.index') }}" class="text-decoration-none">Lớp học phần</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $classSection->code }}</li>
        </ol>
    </nav>

    {{-- Thông báo phản hồi --}}
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
                <span class="hta-eyebrow mb-0">{{ $classSection->course->department->name ?? 'KHOA CHUYÊN MÔN' }}</span>
                @php
                    $statusVal = is_object($classSection->status) ? $classSection->status->value : $classSection->status;
                    $statusMap = [
                        'draft' => ['bg' => 'bg-secondary', 'text' => 'Bản nháp'],
                        'open' => ['bg' => 'bg-success', 'text' => 'Đang mở đăng ký'],
                        'in_progress' => ['bg' => 'bg-primary', 'text' => 'Đang học'],
                        'finished' => ['bg' => 'bg-info', 'text' => 'Đã hoàn thành'],
                        'grades_published' => ['bg' => 'bg-dark', 'text' => 'Công bố điểm'],
                    ];
                    $badge = $statusMap[$statusVal] ?? ['bg' => 'bg-secondary', 'text' => $statusVal];
                @endphp
                <span class="badge {{ $badge['bg'] }} text-white px-2 py-1 rounded-pill">
                    {{ $badge['text'] }}
                </span>
            </div>
            <h1 class="hta-page-title">{{ $classSection->code }} · {{ $classSection->course->name }}</h1>
            <p class="hta-page-subtitle">Học phần: {{ $classSection->course->code }} ({{ $classSection->course->credits }} tín chỉ) · Học kỳ: {{ $classSection->semester->name }} ({{ $classSection->semester->academic_year }})</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.class-sections.edit', $classSection) }}" class="hta-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
                Chỉnh sửa lớp
            </a>
            @if ($classSection->enrolled_count === 0)
                <form method="POST" action="{{ route('admin.class-sections.destroy', $classSection) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lớp học phần {{ $classSection->code }} không?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" style="border-radius: 12px; font-weight: 600;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Xóa lớp
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Hàng thông số tổng quan --}}
    <div class="row g-3 mb-4">
        {{-- Card Gán giảng viên phụ trách --}}
        <div class="col-lg-6">
            <div class="hta-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="hta-eyebrow mb-0">PHÂN CÔNG GIẢNG VIÊN PHỤ TRÁCH</span>
                    <span class="badge {{ $classSection->lecturer ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning' }}">
                        {{ $classSection->lecturer ? 'Đã gán' : 'Chưa phân công' }}
                    </span>
                </div>

                @if ($classSection->lecturer && $classSection->lecturer->user)
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3">
                        <div class="hta-avatar-circle" style="width: 48px; height: 48px; font-size: 1.1rem; background: linear-gradient(135deg, #1b2559, #273873); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            {{ strtoupper(substr($classSection->lecturer->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $classSection->lecturer->user->name }}</div>
                            <div class="text-muted small">
                                Mã GV: <span class="font-monospace fw-semibold">{{ $classSection->lecturer->lecturer_code }}</span>
                                · Email: <span class="font-monospace">{{ $classSection->lecturer->user->email }}</span>
                            </div>
                            <div class="text-muted small">
                                Học vị: {{ $classSection->lecturer->academic_degree ?? 'Chưa cập nhật' }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" style="border-radius: 12px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div>Lớp học phần này hiện <strong>chưa có giảng viên phụ trách</strong>. Vui lòng chọn giảng viên bên dưới để phân công.</div>
                    </div>
                @endif

                {{-- Form Phân công / Đổi GV trực tiếp --}}
                <form method="POST" action="{{ route('admin.class-sections.assign-lecturer', $classSection) }}" class="d-flex gap-2 align-items-center">
                    @csrf
                    @method('PUT')
                    <select name="lecturer_id" class="form-select form-select-sm hta-input" required>
                        <option value="">-- Chọn giảng viên mới để gán --</option>
                        @foreach ($availableLecturers as $lec)
                            <option value="{{ $lec->id }}" {{ $classSection->lecturer_id == $lec->id ? 'selected' : '' }}>
                                {{ $lec->user->name ?? 'GV #'.$lec->id }} ({{ $lec->lecturer_code }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="hta-btn-primary py-1 px-3 text-nowrap">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Lưu phân công
                    </button>
                </form>
            </div>
        </div>

        {{-- Sĩ số và chỉ số đăng ký --}}
        <div class="col-lg-6">
            <div class="hta-card p-4 h-100">
                <span class="hta-eyebrow">CHỈ SỐ SĨ SỐ & CÔNG SUẤT LỚP</span>
                <div class="d-flex justify-content-between align-items-baseline mt-2 mb-2">
                    <div>
                        <span class="display-6 fw-bold text-dark">{{ $classSection->enrolled_count }}</span>
                        <span class="text-muted fs-5">/ {{ $classSection->capacity }} sinh viên</span>
                    </div>
                    <div class="fw-bold fs-5 {{ $classSection->isFull() ? 'text-danger' : 'text-success' }}">
                        @if ($classSection->isFull())
                            ĐÃ ĐẦY (100%)
                        @else
                            Còn {{ $classSection->remainingSeats() }} chỗ trống
                        @endif
                    </div>
                </div>

                @php
                    $percent = $classSection->capacity > 0 ? min(100, round(($classSection->enrolled_count / $classSection->capacity) * 100)) : 0;
                    $barClass = $percent >= 100 ? 'bg-danger' : ($percent >= 80 ? 'bg-warning' : 'bg-success');
                @endphp
                <div class="progress mb-3" style="height: 10px; border-radius: 6px;">
                    <div class="progress-bar {{ $barClass }}" style="width: {{ $percent }}%"></div>
                </div>

                <div class="row g-2 text-muted small border-top pt-2">
                    <div class="col-6">
                        <strong>Số tín chỉ môn:</strong> {{ $classSection->course->credits }} TC ({{ $classSection->course->theory_hours ?? 0 }} LT / {{ $classSection->course->practical_hours ?? 0 }} TH)
                    </div>
                    <div class="col-6">
                        <strong>Khoa quản lý:</strong> {{ $classSection->course->department->name ?? '—' }}
                    </div>
                    <div class="col-6">
                        <strong>Thời gian học kỳ:</strong> {{ optional($classSection->semester->start_date)->format('d/m/Y') }} &rarr; {{ optional($classSection->semester->end_date)->format('d/m/Y') }}
                    </div>
                    <div class="col-6">
                        <strong>Hạn rút HP:</strong> {{ optional($classSection->semester->drop_deadline)->format('d/m/Y') ?? 'Chưa đặt' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách lịch học (Class Schedules) --}}
    <div class="hta-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 fw-bold mb-0" style="color: #1b2559;">Lịch Học & Phòng Học ({{ $classSection->schedules->count() }})</h2>
                <p class="text-muted small mb-0">Thời khóa biểu lặp hàng tuần của lớp học phần.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table hta-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 140px;">Thứ trong tuần</th>
                        <th>Tiết bắt đầu</th>
                        <th>Tiết kết thúc</th>
                        <th>Phòng học</th>
                        <th>Thời gian áp dụng</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $dayMap = [
                            1 => 'Thứ Hai',
                            2 => 'Thứ Ba',
                            3 => 'Thứ Tư',
                            4 => 'Thứ Năm',
                            5 => 'Thứ Sáu',
                            6 => 'Thứ Bảy',
                            7 => 'Chủ Nhật',
                        ];
                    @endphp
                    @forelse ($classSection->schedules as $sched)
                        <tr>
                            <td class="fw-bold" style="color: #273873;">
                                {{ $dayMap[$sched->day_of_week] ?? ('Thứ ' . $sched->day_of_week) }}
                            </td>
                            <td>Tiết {{ $sched->start_period }}</td>
                            <td>Tiết {{ $sched->end_period }}</td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">
                                    {{ $sched->room->code ?? ('Phòng #' . $sched->room_id) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ optional($sched->start_date)->format('d/m/Y') }} &rarr; {{ optional($sched->end_date)->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">
                                <div>Chưa có lịch học nào được sắp xếp cho lớp học phần này.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Danh sách sinh viên đăng ký học phần --}}
    <div class="hta-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 fw-bold mb-0" style="color: #1b2559;">Danh Sách Sinh Viên Đăng Ký ({{ $classSection->enrollments->count() }})</h2>
                <p class="text-muted small mb-0">Sinh viên đã đăng ký vào lớp học phần này.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table hta-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 140px;">Mã Sinh Viên</th>
                        <th>Họ và Tên</th>
                        <th>Email</th>
                        <th>Lớp Hành Chính</th>
                        <th>Ngày Đăng Ký</th>
                        <th class="text-center" style="width: 120px;">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classSection->enrollments as $idx => $enrollment)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <span class="font-monospace fw-bold" style="color: #273873;">
                                    {{ $enrollment->student->user->code ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $enrollment->student->user->name ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="text-muted small font-monospace">{{ $enrollment->student->user->email ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $enrollment->student->administrativeClass->code ?? '—' }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ optional($enrollment->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $enStatus = is_object($enrollment->status) ? $enrollment->status->value : $enrollment->status;
                                    $enBadge = match($enStatus) {
                                        'enrolled' => ['bg' => 'bg-success', 'text' => 'Đã ghi danh'],
                                        'dropped'  => ['bg' => 'bg-secondary', 'text' => 'Đã rút'],
                                        'cancelled'=> ['bg' => 'bg-danger', 'text' => 'Đã hủy'],
                                        default    => ['bg' => 'bg-info', 'text' => $enStatus],
                                    };
                                @endphp
                                <span class="badge {{ $enBadge['bg'] }} text-white px-2 py-1 rounded-pill">
                                    {{ $enBadge['text'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-2 opacity-50"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <div>Chưa có sinh viên nào đăng ký vào lớp học phần này.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
