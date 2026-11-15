@extends('layouts.app')

@section('title', 'Báo cáo thống kê · Cổng Giảng Viên')

@section('content')
@php
    $overview = $report['overview'];
    $classSummaries = $report['classSummaries'];
    $gradeDistribution = $report['gradeDistribution'];
    $attendanceWarnings = $report['attendanceWarnings'];
    $maxDistCount = max(1, max($gradeDistribution));
@endphp

<div class="sms-workspace-page">
    <div class="sms-workspace-page-heading">
        <div>
            <span class="sms-eyebrow">HEXATECH ACADEMY · GIẢNG VIÊN</span>
            <h1>Báo cáo & Thống kê giảng dạy</h1>
            <p>
                Tổng hợp tiến độ giảng dạy, tỷ lệ chuyên cần, phổ điểm học phần và cảnh báo sinh viên vắng vượt ngưỡng {{ $report['thresholdPercent'] }}%.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a
                href="{{ route('lecturer.reports.export', array_filter(['semester_id' => $filters['semester_id'], 'class_section_id' => $filters['class_section_id']])) }}"
                class="sms-workspace-back-link"
            >
                Xuất báo cáo CSV
            </a>
            <a
                href="{{ route('lecturer.classes', ['semester_id' => $filters['semester_id']]) }}"
                class="sms-workspace-back-link"
            >
                Lớp phụ trách
            </a>
        </div>
    </div>

    <div class="sms-workspace-card" style="min-height: auto;">
        <form method="GET" action="{{ route('lecturer.reports') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="semester_id" class="form-label small fw-semibold text-muted">Học kỳ</label>
                <select
                    name="semester_id"
                    id="semester_id"
                    class="form-select"
                    onchange="document.getElementById('class_section_id').value=''; this.form.submit();"
                >
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected($filters['semester_id'] === $semester->id)>
                            {{ $semester->name }} ({{ $semester->academic_year }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-8">
                <label for="class_section_id" class="form-label small fw-semibold text-muted">Phạm vi lớp học phần</label>
                <select
                    name="class_section_id"
                    id="class_section_id"
                    class="form-select"
                    onchange="this.form.submit()"
                >
                    <option value="">Tất cả lớp phụ trách trong học kỳ ({{ $classes->count() }} lớp)</option>
                    @foreach ($classes as $classItem)
                        <option value="{{ $classItem->id }}" @selected($filters['class_section_id'] === $classItem->id)>
                            {{ $classItem->code }} — {{ $classItem->course?->name }} ({{ $classItem->status->label() }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <div class="sms-workspace-card h-100" style="min-height: auto;">
                <div class="small text-muted fw-semibold mb-1">Lớp & Sĩ số</div>
                <div class="h3 fw-bold mb-1" style="color: var(--sms-teal-dark);">{{ $overview['studentCount'] }} SV</div>
                <div class="small text-muted">Trên {{ $overview['classCount'] }} lớp học phần</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sms-workspace-card h-100" style="min-height: auto;">
                <div class="small text-muted fw-semibold mb-1">Chuyên cần trung bình</div>
                <div class="h3 fw-bold mb-1" style="color: var(--sms-teal-dark);">
                    {{ $overview['attendanceRate'] !== null ? number_format($overview['attendanceRate'], 1) . '%' : '—' }}
                </div>
                <div class="small text-muted">Đã dạy {{ $overview['heldSessions'] }}/{{ $overview['totalSessions'] }} buổi</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sms-workspace-card h-100" style="min-height: auto;">
                <div class="small text-muted fw-semibold mb-1">Tỷ lệ đạt & Điểm TB</div>
                <div class="h3 fw-bold mb-1" style="color: var(--sms-teal-dark);">
                    {{ $overview['passRate'] !== null ? number_format($overview['passRate'], 1) . '%' : '—' }}
                </div>
                <div class="small text-muted">
                    ĐTB: <strong class="text-dark">{{ $overview['averageScore'] !== null ? number_format($overview['averageScore'], 2) : '—' }}</strong>
                    ({{ $overview['gradedCount'] }} SV có điểm)
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sms-workspace-card h-100" style="min-height: auto;">
                <div class="small text-muted fw-semibold mb-1">Cảnh báo vắng (&gt;{{ $report['thresholdPercent'] }}%)</div>
                <div class="h3 fw-bold mb-1 {{ $overview['warningCount'] > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $overview['warningCount'] }} SV
                </div>
                <div class="small text-muted">Theo quy định chuyên cần BR-09</div>
            </div>
        </div>
    </div>

    <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f7faf7;">
            <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                Tổng hợp theo từng lớp học phần ({{ count($classSummaries) }} lớp)
            </span>
        </div>

        @if (count($classSummaries) === 0)
            <div class="p-4">
                <div class="sms-workspace-notice mx-auto my-2">
                    <span class="sms-workspace-notice-icon">i</span>
                    <div>
                        <h2>Chưa có dữ liệu báo cáo</h2>
                        <p>Bạn chưa được phân công lớp học phần nào trong học kỳ đang chọn.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted" style="background: #f8faf8;">
                        <tr>
                            <th class="ps-4">Mã lớp</th>
                            <th>Học phần</th>
                            <th class="text-center">Sĩ số</th>
                            <th class="text-center">Tiến độ buổi</th>
                            <th class="text-center">Chuyên cần</th>
                            <th class="text-center">Tỷ lệ đạt</th>
                            <th class="text-center">Điểm TB</th>
                            <th class="text-center">Cảnh báo vắng</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classSummaries as $item)
                            @php
                                $section = $item['section'];
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold font-monospace" style="color: var(--sms-teal-dark);">
                                    {{ $section->code }}
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $section->course?->name }}</div>
                                    <div class="small text-muted">{{ $section->status->label() }}</div>
                                </td>
                                <td class="text-center fw-semibold">{{ $item['studentCount'] }}/{{ $section->capacity }}</td>
                                <td class="text-center">{{ $item['heldSessions'] }}/{{ $item['totalSessions'] }}</td>
                                <td class="text-center">
                                    {{ $item['attendanceRate'] !== null ? number_format($item['attendanceRate'], 1) . '%' : '—' }}
                                </td>
                                <td class="text-center">
                                    @if ($item['passRate'] !== null)
                                        <span class="fw-semibold">{{ number_format($item['passRate'], 1) }}%</span>
                                        <span class="small text-muted">({{ $item['passedCount'] }}/{{ $item['gradedCount'] }})</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $item['averageScore'] !== null ? number_format($item['averageScore'], 2) : '—' }}
                                </td>
                                <td class="text-center">
                                    @if ($item['warningCount'] > 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            {{ $item['warningCount'] }} SV
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">0</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a
                                            href="{{ route('lecturer.attendance', ['class_section_id' => $section->id]) }}"
                                            class="btn btn-sm btn-outline-secondary py-0 px-2"
                                            style="font-size: 12px;"
                                        >
                                            Điểm danh
                                        </a>
                                        <a
                                            href="{{ route('lecturer.grades', ['class_section_id' => $section->id]) }}"
                                            class="btn btn-sm btn-outline-success py-0 px-2"
                                            style="font-size: 12px;"
                                        >
                                            Bảng điểm
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-5">
            <div class="sms-workspace-card" style="min-height: auto;">
                <h2 class="h6 fw-bold mb-1" style="color: var(--sms-teal-dark);">Phân bố phổ điểm chữ</h2>
                <p class="small text-muted mb-3">Thống kê trên {{ $overview['gradedCount'] }} sinh viên đã có điểm tổng kết.</p>

                <div class="d-flex flex-column gap-2">
                    @foreach ($gradeDistribution as $letter => $count)
                        @php
                            $percentWidth = $overview['gradedCount'] > 0 ? round(($count / $maxDistCount) * 100, 1) : 0;
                            $barColor = $letter === 'F' ? '#dc2626' : 'var(--sms-teal)';
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold font-monospace small" style="width: 28px; color: var(--sms-teal-dark);">{{ $letter }}</span>
                            <div class="progress flex-grow-1" style="height: 10px; background: #eef2ee;">
                                <div
                                    class="progress-bar"
                                    role="progressbar"
                                    style="width: {{ $percentWidth }}%; background-color: {{ $barColor }};"
                                ></div>
                            </div>
                            <span class="small fw-semibold text-end" style="width: 36px;">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f7faf7;">
                    <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                        Danh sách sinh viên cảnh báo chuyên cần (&gt; {{ $report['thresholdPercent'] }}%)
                    </span>
                </div>

                @if (count($attendanceWarnings) === 0)
                    <div class="p-4">
                        <div class="sms-workspace-notice mx-auto my-2">
                            <span class="sms-workspace-notice-icon">✓</span>
                            <div>
                                <h2>Không có sinh viên vi phạm ngưỡng vắng</h2>
                                <p>Tất cả sinh viên đang theo học đều đảm bảo tỷ lệ chuyên cần theo quy định.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="small text-muted" style="background: #f8faf8;">
                                <tr>
                                    <th class="ps-4">Mã SV</th>
                                    <th>Họ và tên</th>
                                    <th>Lớp HP</th>
                                    <th class="text-center">Số buổi vắng</th>
                                    <th class="text-center">Tỷ lệ vắng</th>
                                    <th class="text-end pe-4">Điểm TK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendanceWarnings as $warning)
                                    <tr>
                                        <td class="ps-4 fw-bold font-monospace" style="color: var(--sms-teal-dark);">
                                            {{ $warning['studentCode'] }}
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $warning['studentName'] }}</td>
                                        <td>
                                            <div class="font-monospace small fw-semibold">{{ $warning['classCode'] }}</div>
                                            <div class="small text-muted">{{ $warning['courseName'] }}</div>
                                        </td>
                                        <td class="text-center">{{ $warning['absentCount'] }}/{{ $warning['heldSessions'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                {{ number_format($warning['absenceRatePercent'], 1) }}%
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 fw-semibold">
                                            @if ($warning['totalScore'] !== null)
                                                {{ number_format($warning['totalScore'], 2) }} ({{ $warning['letterGrade'] }})
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
