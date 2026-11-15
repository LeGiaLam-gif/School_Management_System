@extends('layouts.app')

@section('title', 'Lịch dạy · Cổng Giảng Viên')

@section('content')
@php
    $dayNames = [
        1 => 'Thứ 2',
        2 => 'Thứ 3',
        3 => 'Thứ 4',
        4 => 'Thứ 5',
        5 => 'Thứ 6',
        6 => 'Thứ 7',
        7 => 'Chủ nhật',
    ];
    $totalWeeklySessions = collect($weekDays)->sum(fn (array $day): int => $day['sessions']->count());
@endphp

<div class="sms-workspace-page">
    <div class="sms-workspace-page-heading">
        <div>
            <span class="sms-eyebrow">HEXATECH ACADEMY · GIẢNG VIÊN</span>
            <h1>Lịch dạy</h1>
            <p>
                Theo dõi lịch giảng dạy chi tiết từng tuần và thời khóa biểu cố định trong học kỳ.
            </p>
        </div>
        <a href="{{ route('lecturer.classes', ['semester_id' => $filters['semester_id']]) }}" class="sms-workspace-back-link">
            ← Về danh sách lớp
        </a>
    </div>

    <div class="sms-workspace-card" style="min-height: auto;">
        <form method="GET" action="{{ route('lecturer.timetable') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="semester_id" class="form-label small fw-semibold text-muted">Học kỳ</label>
                <select
                    name="semester_id"
                    id="semester_id"
                    class="form-select"
                    onchange="document.getElementById('week_date').value=''; this.form.submit();"
                >
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected($filters['semester_id'] === $semester->id)>
                            {{ $semester->name }} ({{ $semester->academic_year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="week_date" class="form-label small fw-semibold text-muted">Ngày trong tuần</label>
                <input
                    type="date"
                    name="week_date"
                    id="week_date"
                    class="form-control"
                    value="{{ $filters['week_date'] }}"
                    onchange="this.form.submit()"
                >
            </div>

            <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                <a
                    href="{{ route('lecturer.timetable', ['semester_id' => $filters['semester_id'], 'week_date' => $prevWeekDate]) }}"
                    class="btn btn-outline-secondary"
                >
                    ← Tuần trước
                </a>
                <a
                    href="{{ route('lecturer.timetable', ['semester_id' => $filters['semester_id'], 'week_date' => $todayDate]) }}"
                    class="btn fw-semibold text-white"
                    style="background-color: var(--sms-teal); border-color: var(--sms-teal);"
                >
                    Tuần này
                </a>
                <a
                    href="{{ route('lecturer.timetable', ['semester_id' => $filters['semester_id'], 'week_date' => $nextWeekDate]) }}"
                    class="btn btn-outline-secondary"
                >
                    Tuần sau →
                </a>
            </div>
        </form>
    </div>

    <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
        <div class="px-4 py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: #f7faf7;">
            <div>
                <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                    Lịch giảng dạy tuần ({{ $weekStart->format('d/m/Y') }} – {{ $weekEnd->format('d/m/Y') }})
                </span>
            </div>
            <span class="badge rounded-pill px-3 py-2" style="background: #e8f4ef; color: var(--sms-teal-dark);">
                {{ $totalWeeklySessions }} buổi dạy trong tuần
            </span>
        </div>

        @if ($totalWeeklySessions === 0)
            <div class="p-4">
                <div class="sms-workspace-notice mx-auto my-2">
                    <span class="sms-workspace-notice-icon">i</span>
                    <div>
                        <h2>Không có buổi dạy trong tuần này</h2>
                        <p>Chuyển sang tuần khác bằng thanh điều hướng phía trên hoặc xem lịch cố định bên dưới.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted" style="background: #f8faf8;">
                        <tr>
                            <th class="ps-4" style="width: 150px;">Ngày / Thứ</th>
                            <th style="width: 120px;">Tiết học</th>
                            <th style="width: 140px;">Mã lớp HP</th>
                            <th>Học phần</th>
                            <th style="width: 130px;">Phòng học</th>
                            <th class="text-center" style="width: 90px;">Buổi</th>
                            <th class="text-center" style="width: 130px;">Trạng thái</th>
                            <th class="text-end pe-4" style="width: 130px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weekDays as $day)
                            @foreach ($day['sessions'] as $session)
                                @php
                                    $section = $session->classSection;
                                    $statusValue = $session->status->value;
                                    $statusLabel = match ($statusValue) {
                                        'scheduled' => 'Chưa diễn ra',
                                        'completed' => 'Đã dạy',
                                        'cancelled' => 'Đã hủy',
                                        'makeup' => 'Dạy bù',
                                        default => $statusValue,
                                    };
                                @endphp
                                <tr @style(['background-color: #f3faf6;' => $day['is_today']])>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark">
                                            {{ $dayNames[$day['iso_day']] ?? '' }}
                                            @if ($day['is_today'])
                                                <span class="badge ms-1" style="background: var(--sms-teal); color: #fff; font-size: 10px;">
                                                    Hôm nay
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">{{ $day['date']->format('d/m/Y') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge border px-2 py-1" style="background: #edf6f1; color: var(--sms-teal-dark);">
                                            Tiết {{ $session->start_period }}–{{ $session->end_period }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace" style="color: var(--sms-teal-dark);">
                                            {{ $section?->code }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $section?->course?->name }}</div>
                                        <div class="text-muted small">{{ $section?->course?->code }}</div>
                                    </td>
                                    <td class="small fw-semibold">
                                        {{ $session->room?->code ?? $session->room?->name ?? '—' }}
                                    </td>
                                    <td class="text-center small text-muted">
                                        #{{ $session->session_no }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge border px-2 py-1" style="background: #f8faf8; color: #334155;">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if ($section)
                                            <a
                                                href="{{ route('lecturer.students', ['class_section_id' => $section->id]) }}"
                                                class="btn btn-sm btn-outline-success"
                                            >
                                                DS sinh viên
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f7faf7;">
            <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                Thời khóa biểu cố định trong học kỳ
            </span>
            <span class="text-muted small">
                {{ $schedules->count() }} khung giờ
            </span>
        </div>

        @if ($schedules->isEmpty())
            <div class="p-4">
                <div class="sms-workspace-notice mx-auto my-2">
                    <span class="sms-workspace-notice-icon">i</span>
                    <div>
                        <h2>Chưa có lịch cố định</h2>
                        <p>Các lớp trong học kỳ này chưa được xếp khung giờ cố định.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted" style="background: #f8faf8;">
                        <tr>
                            <th class="ps-4" style="width: 120px;">Thứ</th>
                            <th style="width: 120px;">Tiết học</th>
                            <th style="width: 140px;">Mã lớp HP</th>
                            <th>Học phần</th>
                            <th style="width: 130px;">Phòng học</th>
                            <th style="width: 200px;">Thời gian áp dụng</th>
                            <th class="text-center pe-4" style="width: 110px;">Sĩ số</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            @php
                                $section = $schedule->classSection;
                            @endphp
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">
                                    {{ $dayNames[$schedule->day_of_week] ?? '—' }}
                                </td>
                                <td>
                                    <span class="badge border px-2 py-1" style="background: #edf6f1; color: var(--sms-teal-dark);">
                                        Tiết {{ $schedule->start_period }}–{{ $schedule->end_period }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold font-monospace" style="color: var(--sms-teal-dark);">
                                        {{ $section?->code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $section?->course?->name }}</div>
                                    <div class="text-muted small">{{ $section?->course?->code }}</div>
                                </td>
                                <td class="small fw-semibold">
                                    {{ $schedule->room?->code ?? $schedule->room?->name ?? '—' }}
                                </td>
                                <td class="small text-muted">
                                    {{ $schedule->start_date?->format('d/m/Y') }} – {{ $schedule->end_date?->format('d/m/Y') }}
                                </td>
                                <td class="text-center pe-4 small">
                                    {{ $section?->enrolled_count ?? 0 }} / {{ $section?->capacity ?? 0 }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
