@extends('layouts.app')

@section('title', 'Điểm danh · Cổng Giảng Viên')

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
    $statusBadgeStyles = [
        'present' => 'background: #e8f5ee; color: #166534; border-color: #bbf7d0;',
        'late' => 'background: #fef9c3; color: #854d0e; border-color: #fde047;',
        'excused' => 'background: #e0f2fe; color: #075985; border-color: #bae6fd;',
        'absent' => 'background: #fee2e2; color: #991b1b; border-color: #fecaca;',
    ];
@endphp

<div class="sms-workspace-page">
    <div class="sms-workspace-page-heading">
        <div>
            <span class="sms-eyebrow">HEXATECH ACADEMY · GIẢNG VIÊN</span>
            <h1>Điểm danh buổi học</h1>
            <p>
                Ghi nhận chuyên cần sinh viên theo từng buổi học và theo dõi cảnh báo tỷ lệ vắng.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if ($selectedClass)
                <a
                    href="{{ route('lecturer.students', ['class_section_id' => $selectedClass->id]) }}"
                    class="sms-workspace-back-link"
                >
                    Danh sách sinh viên
                </a>
            @endif
            <a
                href="{{ route('lecturer.timetable', ['semester_id' => $filters['semester_id']]) }}"
                class="sms-workspace-back-link"
            >
                Lịch dạy
            </a>
        </div>
    </div>

    <div class="sms-workspace-card" style="min-height: auto;">
        <form method="GET" action="{{ route('lecturer.attendance') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="semester_id" class="form-label small fw-semibold text-muted">Học kỳ</label>
                <select
                    name="semester_id"
                    id="semester_id"
                    class="form-select"
                    onchange="document.getElementById('class_section_id').value=''; document.getElementById('class_session_id').value=''; this.form.submit();"
                >
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected($filters['semester_id'] === $semester->id)>
                            {{ $semester->name }} ({{ $semester->academic_year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <label for="class_section_id" class="form-label small fw-semibold text-muted">Lớp học phần</label>
                <select
                    name="class_section_id"
                    id="class_section_id"
                    class="form-select"
                    onchange="document.getElementById('class_session_id').value=''; this.form.submit();"
                >
                    @if ($classes->isEmpty())
                        <option value="">-- Không có lớp học phần --</option>
                    @else
                        @foreach ($classes as $classItem)
                            <option value="{{ $classItem->id }}" @selected($filters['class_section_id'] === $classItem->id)>
                                {{ $classItem->code }} — {{ $classItem->course?->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-md-4">
                <label for="class_session_id" class="form-label small fw-semibold text-muted">Buổi học</label>
                <select
                    name="class_session_id"
                    id="class_session_id"
                    class="form-select"
                    onchange="this.form.submit()"
                    @disabled($sessions->isEmpty())
                >
                    @if ($sessions->isEmpty())
                        <option value="">-- Chưa có buổi học --</option>
                    @else
                        @foreach ($sessions as $sessionItem)
                            @php
                                $sessionDate = \Carbon\CarbonImmutable::parse($sessionItem->session_date);
                                $markedTag = $sessionItem->attendance_records_count > 0 ? 'Đã điểm danh' : 'Chưa điểm danh';
                            @endphp
                            <option value="{{ $sessionItem->id }}" @selected($filters['class_session_id'] === $sessionItem->id)>
                                Buổi #{{ $sessionItem->session_no }} ({{ $sessionDate->format('d/m/Y') }} · Tiết {{ $sessionItem->start_period }}–{{ $sessionItem->end_period }}) · {{ $markedTag }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </form>
    </div>

    @if ($selectedClass === null || $selectedSession === null)
        <div class="sms-workspace-card" style="min-height: auto;">
            <div class="sms-workspace-notice mx-auto my-2">
                <span class="sms-workspace-notice-icon">i</span>
                <div>
                    <h2>Chưa có buổi học để điểm danh</h2>
                    <p>Lớp học phần hiện tại chưa được khởi tạo lịch các buổi giảng dạy trong học kỳ này.</p>
                </div>
            </div>
        </div>
    @else
        @php
            $activeDate = \Carbon\CarbonImmutable::parse($selectedSession->session_date);
            $hasExistingAttendance = $selectedSession->attendance_records_count > 0;
        @endphp

        <div class="sms-workspace-card" style="min-height: auto;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span class="fw-bold font-monospace" style="color: var(--sms-teal-dark); font-size: 16px;">
                            {{ $selectedClass->code }}
                        </span>
                        <span class="text-muted">·</span>
                        <span class="fw-bold text-dark" style="font-size: 16px;">
                            {{ $selectedClass->course?->name }}
                        </span>
                        <span class="badge border px-2 py-1" style="background: #edf6f1; color: var(--sms-teal-dark);">
                            Buổi #{{ $selectedSession->session_no }}
                        </span>
                        <span class="badge border px-2 py-1" style="background: #f8faf8; color: #334155;">
                            {{ $selectedSession->status->label() }}
                        </span>
                    </div>
                    <div class="small text-muted">
                        {{ $dayNames[$activeDate->dayOfWeekIso] ?? '' }}, ngày {{ $activeDate->format('d/m/Y') }}
                        · Tiết {{ $selectedSession->start_period }}–{{ $selectedSession->end_period }}
                        · Phòng: <strong class="text-dark">{{ $selectedSession->room?->code ?? $selectedSession->room?->name ?? '—' }}</strong>
                        · Trạng thái dữ liệu:
                        @if ($hasExistingAttendance)
                            <span class="fw-semibold" style="color: var(--sms-teal-dark);">Đã có dữ liệu điểm danh</span>
                        @else
                            <span class="fw-semibold text-warning-emphasis">Chưa lưu điểm danh</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @foreach ($statuses as $statusOption)
                        <span class="badge border px-3 py-2" style="{{ $statusBadgeStyles[$statusOption->value] ?? '' }}">
                            {{ $statusOption->label() }}: {{ $statusCounts[$statusOption->value] ?? 0 }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('lecturer.attendance.store', $selectedSession) }}">
            @csrf
            <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
                <div class="px-4 py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: #f7faf7;">
                    <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                        Bảng điểm danh sinh viên ({{ count($rows) }} sinh viên)
                    </span>
                    <div class="d-flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-success"
                            onclick="document.querySelectorAll('input[type=radio][value=present]').forEach(el => el.checked = true);"
                        >
                            Đánh dấu tất cả Có mặt
                        </button>
                    </div>
                </div>

                @if (count($rows) === 0)
                    <div class="p-4">
                        <div class="sms-workspace-notice mx-auto my-2">
                            <span class="sms-workspace-notice-icon">i</span>
                            <div>
                                <h2>Lớp chưa có sinh viên đăng ký</h2>
                                <p>Hiện chưa có sinh viên nào ở trạng thái đang học trong lớp học phần này.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="small text-muted" style="background: #f8faf8;">
                                <tr>
                                    <th class="ps-4" style="width: 55px;">STT</th>
                                    <th style="width: 125px;">Mã SV</th>
                                    <th>Họ và tên</th>
                                    <th style="width: 120px;">Lớp SH</th>
                                    <th style="width: 340px;">Trạng thái điểm danh</th>
                                    <th style="width: 220px;">Ghi chú</th>
                                    <th class="text-end pe-4" style="width: 150px;">Vắng tích lũy</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $index => $row)
                                    @php
                                        $enrollment = $row['enrollment'];
                                        $student = $enrollment->student;
                                        $currentStatus = old("records.{$enrollment->id}.status", $row['status']->value);
                                        $currentNote = old("records.{$enrollment->id}.note", $row['note']);
                                        $summary = $row['summary'];
                                        $ratePercent = number_format($summary['rate'] * 100, 1);
                                    @endphp
                                    <tr @style(['background-color: #fff8f8;' => $summary['exceeds_threshold']])>
                                        <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                        <td class="fw-bold font-monospace" style="color: var(--sms-teal-dark);">
                                            {{ $student?->student_code ?? '—' }}
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $student?->user?->name ?? '—' }}</div>
                                            <div class="text-muted small">{{ $student?->user?->email ?? '' }}</div>
                                        </td>
                                        <td class="small text-muted">
                                            {{ $student?->administrativeClass?->code ?? '—' }}
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($statuses as $statusOption)
                                                    @php
                                                        $inputId = "att_{$enrollment->id}_{$statusOption->value}";
                                                    @endphp
                                                    <div class="form-check form-check-inline m-0">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="records[{{ $enrollment->id }}][status]"
                                                            id="{{ $inputId }}"
                                                            value="{{ $statusOption->value }}"
                                                            @checked($currentStatus === $statusOption->value)
                                                            required
                                                        >
                                                        <label class="form-check-label small" for="{{ $inputId }}">
                                                            {{ $statusOption->label() }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                name="records[{{ $enrollment->id }}][note]"
                                                class="form-control form-control-sm"
                                                value="{{ $currentNote }}"
                                                placeholder="Ghi chú (nếu có)..."
                                                maxlength="255"
                                            >
                                        </td>
                                        <td class="text-end pe-4 small">
                                            <div>
                                                <span class="fw-semibold">{{ $summary['absent'] }}/{{ $summary['held'] }}</span>
                                                <span class="text-muted">({{ $ratePercent }}%)</span>
                                            </div>
                                            @if ($summary['exceeds_threshold'])
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                    Vượt ngưỡng vắng
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: #f8faf8;">
                        <div class="d-flex align-items-center gap-2">
                            <label for="session_status" class="small fw-semibold text-muted mb-0">
                                Trạng thái buổi học sau khi lưu:
                            </label>
                            <select name="session_status" id="session_status" class="form-select form-select-sm" style="width: auto;">
                                @foreach ($sessionStatuses as $sessionStatusOption)
                                    @php
                                        $defaultTarget = $selectedSession->status === \App\Shared\Enums\SessionStatus::Scheduled
                                            ? \App\Shared\Enums\SessionStatus::Held->value
                                            : $selectedSession->status->value;
                                    @endphp
                                    <option
                                        value="{{ $sessionStatusOption->value }}"
                                        @selected(old('session_status', $defaultTarget) === $sessionStatusOption->value)
                                    >
                                        {{ $sessionStatusOption->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button
                            type="submit"
                            class="btn fw-semibold text-white px-4"
                            style="background-color: var(--sms-teal); border-color: var(--sms-teal);"
                        >
                            Lưu điểm danh
                        </button>
                    </div>
                @endif
            </div>
        </form>
    @endif
</div>
@endsection
