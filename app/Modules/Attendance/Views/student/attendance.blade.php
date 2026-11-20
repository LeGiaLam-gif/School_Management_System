@extends('layouts.app')

@section('title', 'Theo dõi Chuyên cần & Điểm danh · Cổng Sinh Viên')

@section('content')
<style>
    .sms-student-attendance .sms-attendance-course {
        background: #edf4f0;
        color: #35564c;
    }

    .sms-student-attendance .sms-curriculum-table thead th {
        --bs-table-bg: #f6f8f7;
        --bs-table-color: #465850;
        font-weight: 600;
    }

    .sms-student-attendance .sms-curriculum-table > tbody > tr > td {
        background: #fff;
        box-shadow: none;
    }

    .sms-student-attendance .sms-attendance-toggle[aria-expanded="true"] svg {
        transform: rotate(180deg);
    }
    .sms-student-attendance .sms-attendance-status.bg-success {
        background-color: #e4f3eb !important;
        color: #236347;
    }

    .sms-student-attendance .sms-attendance-status.bg-warning {
        background-color: #fff3d9 !important;
        color: #755312 !important;
    }

    .sms-student-attendance .sms-attendance-status.bg-danger {
        background-color: #fbe8ea !important;
        color: #8d3440;
    }
</style>
<div class="row g-4 sms-student-attendance">
    <div class="col-12">
        <header class="sms-curriculum-banner p-3 p-md-4 px-lg-5 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 fw-bold d-flex align-items-center gap-2 mb-2">
                    <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg>
                    Chuyên cần
                </h1>
                <span class="sms-role-pill mt-0">{{ $attendance->semester_name }}</span>
            </div>
            <div class="align-self-start align-self-md-center rounded-3 bg-white text-dark px-3 py-2">
                <span class="text-muted small">Tỷ lệ vắng trung bình</span>
                <div>
                    <strong class="fs-5 {{ $attendance->overall_rate >= 20.0 ? 'text-danger-emphasis' : ($attendance->overall_rate >= 10.0 ? 'text-warning-emphasis' : 'text-success-emphasis') }}">
                        {{ number_format($attendance->overall_rate, 1) }}%
                    </strong>
                </div>
            </div>
        </header>
    </div>

    <div class="col-12">
        <div class="row g-2 g-md-3">
            @foreach ([
                ['Buổi đã diễn ra', $attendance->overall_held, 'navy', 'M4 6h16v15H4zM8 3v6m8-6v6M4 11h16'],
                ['Buổi có mặt', $attendance->overall_present, 'teal', 'm5 12 4 4L19 6'],
                ['Buổi đi muộn', $attendance->overall_late, 'amber', 'M12 7v5l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                ['Buổi vắng', $attendance->overall_absent.' / '.$attendance->overall_held, 'navy', 'm8 8 8 8m0-8-8 8m13-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
            ] as [$label, $value, $tone, $iconPath])
                <div class="col-6 col-xl-3">
                    <div class="hta-stat-card sms-curriculum-stat h-100 py-2">
                        <span class="hta-stat-icon hta-stat-icon-{{ $tone }} flex-shrink-0" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $iconPath }}"/></svg>
                        </span>
                        <dl class="mb-0">
                            <dt class="small fw-semibold text-muted">{{ $label }}</dt>
                            <dd class="fs-4 fw-bold mb-0">{{ $value }}</dd>
                        </dl>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Bảng danh sách Chuyên cần từng môn học -->
    <div class="col-12">
        <div class="d-flex flex-column gap-4">
            @foreach ($attendance->courses as $cIdx => $c)
                <div class="card border shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header sms-attendance-course py-3 px-4 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <span class="sms-curriculum-course-code small fw-semibold font-monospace">{{ $c->class_section_code }}</span>
                                <h5 class="fw-semibold mb-0">{{ $c->course_name }}</h5>
                                <span class="badge bg-light text-dark border">{{ $c->credits }} tín chỉ</span>
                            </div>
                            <span class="text-muted small">
                                Giảng viên phụ trách: <strong>{{ $c->lecturer_name }}</strong>
                            </span>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="text-end">
                                <span class="text-muted small">Tỷ lệ vắng:</span>
                                <span class="fw-bold {{ $c->absence_rate >= 20.0 ? 'text-danger-emphasis' : ($c->absence_rate >= 10.0 ? 'text-warning-emphasis' : 'text-success-emphasis') }} ms-1">
                                    {{ number_format($c->absence_rate, 1) }}%
                                </span>
                            </div>
                            <span class="badge sms-attendance-status {{ $c->status_badge }} px-3 py-2 rounded-pill">
                                {{ $c->is_banned ? 'Cấm thi' : $c->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Tiến trình chuyên cần của môn -->
                    <div class="px-4 py-2 bg-light border-top border-bottom small d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-wrap gap-3">
                            <span>Đã học: <strong>{{ $c->held_sessions }}/{{ $c->total_sessions }} buổi</strong></span>
                            <span>Có mặt: <span class="text-success-emphasis fw-semibold">{{ $c->present_count }}</span></span>
                            <span>Muộn: <span class="text-warning-emphasis fw-semibold">{{ $c->late_count }}</span></span>
                            <span>Vắng: <span class="text-danger-emphasis fw-semibold">{{ $c->absent_count }}</span></span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary sms-attendance-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSessions{{ $cIdx }}" aria-expanded="true" aria-controls="collapseSessions{{ $cIdx }}">
                            Xem nhật ký buổi học <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                    </div>

                    <!-- Nhật ký từng buổi học (Collapse) -->
                    <div class="collapse show" id="collapseSessions{{ $cIdx }}">
                        <div class="table-responsive">
                            <table class="table sms-curriculum-table align-middle mb-0 text-center">
                                <thead class="small text-nowrap">
                                    <tr>
                                        <th class="ps-4 text-start" style="width: 80px;">Buổi</th>
                                        <th style="width: 140px;">Ngày học</th>
                                        <th style="width: 160px;">Thời gian</th>
                                        <th style="width: 120px;">Phòng</th>
                                        <th style="width: 140px;">Trạng thái</th>
                                        <th class="text-start ps-3 pe-4">Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($c->sessions as $s)
                                        <tr>
                                            <td class="ps-4 text-start fw-bold text-muted">{{ $s->session_no }}</td>
                                            <td class="font-monospace fw-semibold">{{ $s->date }}</td>
                                            <td class="small">{{ $s->time }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $s->room }}</span></td>
                                            <td>
                                                <span class="badge sms-attendance-status {{ $s->badge }} px-3 py-1">
                                                    {{ $s->status }}
                                                </span>
                                            </td>
                                            <td class="text-start ps-3 pe-4 small text-muted">{{ $s->note }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-3 text-muted small">
                                                Chưa có dữ liệu điểm danh chi tiết.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

