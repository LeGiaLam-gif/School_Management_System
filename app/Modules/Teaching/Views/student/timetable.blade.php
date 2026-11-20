@extends('layouts.app')

@section('title', 'Thời khóa biểu sinh viên')

@section('content')
<style>
    .sms-student-timetable {
        --bs-primary-rgb: 33, 134, 110;
    }
    .sms-student-timetable .card {
        border: 1px solid #d1eadf !important;
        border-radius: 15px !important;
        box-shadow: 0 6px 18px rgba(43, 62, 54, .045) !important;
    }
    .sms-student-timetable .card-body h3,
    .sms-student-timetable .text-primary {
        color: #21866e !important;
    }
    .sms-student-timetable .bg-primary {
        background-color: #21866e !important;
    }
    .sms-student-timetable .btn-primary {
        --bs-btn-bg: #21866e;
        --bs-btn-border-color: #21866e;
        --bs-btn-hover-bg: #196b58;
        --bs-btn-hover-border-color: #196b58;
    }
    .sms-student-timetable .btn-outline-primary {
        --bs-btn-color: #21866e;
        --bs-btn-border-color: #8bcdb6;
        --bs-btn-hover-bg: #21866e;
        --bs-btn-hover-border-color: #21866e;
    }
    .sms-student-timetable .table {
        --bs-table-border-color: #dce9e3;
    }
    .sms-student-timetable .table > thead > tr > th {
        --bs-table-bg: #f4f6f7;
        --bs-table-color: #53616b;
    }
    .sms-student-timetable .table > thead > tr > .sms-today {
        --bs-table-bg: #e1f5eb;
        border-color: #a5d7c2 !important;
    }
    .sms-student-timetable .card-header {
        background: linear-gradient(110deg, #d5f4e5, #effbf5) !important;
    }
    .sms-student-timetable .sms-timetable-lesson {
        background: #f1f8ff;
        border: 1px solid #c7dff3;
        border-left: 3px solid #79acd3;
    }
    .sms-student-timetable .sms-timetable-lesson:nth-child(3n + 2) {
        background: #fff9ed;
        border-color: #f0dfb6;
        border-left-color: #d8b15b;
    }
    .sms-student-timetable .sms-timetable-lesson:nth-child(3n) {
        background: #f5f0fc;
        border-color: #e0d3ee;
        border-left-color: #b295cf;
    }
    .sms-student-timetable .sms-section-code {
        display: inline-block;
        flex-shrink: 0;
        padding: .15rem .5rem;
        border-radius: .375rem;
        background: #e3f1ea;
        color: #21866e;
        font-weight: 600;
    }
    .sms-timetable-lesson > .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 4px;
    }
    .sms-student-timetable .sms-timetable-lesson {
        min-height: 168px;
        display: flex;
        flex-direction: column;
        gap: .25rem;
        overflow-wrap: anywhere;
    }
    .sms-student-timetable > .col-12:first-child > .card {
        background: linear-gradient(112deg, #d6a36e 0%, #a9a574 52%, #498f76 100%);
        color: #fff;
    }
    .sms-student-timetable > .col-12:first-child .card-body h3,
    .sms-student-timetable > .col-12:first-child .text-primary {
        color: #fff !important;
    }
    .sms-student-timetable > .col-12:first-child hr {
        border-color: #fff;
    }
    .sms-student-timetable .sms-shift-column {
        width: 120px;
        min-width: 120px;
        white-space: nowrap;
    }
    .sms-timetable-icon {
        width: 17px;
        height: 17px;
        vertical-align: -3px;
        margin-right: 5px;
    }
    .sms-student-timetable .sms-weekday-label {
        background: #e3f1ea;
        color: #21866e;
    }
    .sms-student-timetable > .col-12:first-child .btn-outline-secondary {
        background: #fff;
        color: #53616b;
        border-color: #dce9e3;
    }
</style>
<div class="row g-4 sms-student-timetable" x-data="{ viewMode: 'grid' }">
    <!-- Header Thời khóa biểu & Chuyển đổi chế độ xem -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">
                            <svg class="sms-timetable-icon" style="width: 25px; height: 25px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>Thời khóa biểu
                        </h3>
                    </div>

                    <!-- Nút đổi chế độ xem Grid / List -->
                    <div class="btn-group shadow-sm" role="group">
                        <button type="button" 
                                class="btn px-3 py-2 fw-semibold" 
                                :class="viewMode === 'grid' ? 'btn-primary' : 'btn-outline-primary'"
                                @click="viewMode = 'grid'">
                            <svg class="sms-timetable-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 9v12M15 9v12"/></svg>Lịch tuần
                        </button>
                        <button type="button" 
                                class="btn px-3 py-2 fw-semibold" 
                                :class="viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary'"
                                @click="viewMode = 'list'">
                            <svg class="sms-timetable-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M8 6h13M8 12h13M8 18h13M3 6h1M3 12h1M3 18h1"/></svg>Danh sách lịch học
                        </button>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Bộ điều hướng Tuần (Chỉ hiển thị ở chế độ Grid) -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" x-show="viewMode === 'grid'">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('student.timetable', ['week' => $timetable->week_offset - 1]) }}" 
                           class="btn btn-sm btn-outline-secondary px-3">
                            ← Tuần trước
                        </a>

                        @if ($timetable->week_offset !== 0)
                            <a href="{{ route('student.timetable', ['week' => 0]) }}" 
                               class="btn btn-sm btn-light border px-3">
                                Hôm nay
                            </a>
                        @endif

                        <a href="{{ route('student.timetable', ['week' => $timetable->week_offset + 1]) }}" 
                           class="btn btn-sm btn-outline-secondary px-3">
                            Tuần sau →
                        </a>
                    </div>

                    <div class="fw-semibold text-primary d-flex align-items-center gap-2">
                        <span><svg class="sms-timetable-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>{{ str_replace(['Tuần từ ', ' đến '], ['', ' – '], $timetable->week_label) }}</span>
                        @if ($timetable->week_offset === 0)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Tuần hiện tại</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHẾ ĐỘ 1: LƯỚI THỜI KHÓA BIỂU THEO TUẦN (WEEKLY GRID) -->
    <div class="col-12" x-show="viewMode === 'grid'">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 text-center" style="min-width: 1900px;">
                    <thead class="table-light">
                        <tr>
                            <th class="sms-shift-column py-3 text-muted small">Ca học</th>
                            @foreach ($timetable->days as $d)
                                <th class="py-3 {{ $d['is_today'] ? 'sms-today text-primary' : '' }}" style="width: 12.5%;">
                                    <div class="fw-bold fs-6">{{ $d['name'] }}</div>
                                    <div class="small {{ $d['is_today'] ? 'text-primary fw-semibold' : 'text-muted' }}">
                                        {{ $d['date_formatted'] }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timetable->shifts as $shiftId => $shift)
                            <tr>
                                <!-- Cột thông tin Ca học -->
                                <td class="sms-shift-column bg-light text-start p-3 align-top">
                                    <div class="fw-bold text-dark small">{{ trim(preg_replace('/\s*\(.*\)/u', '', $shift['label'])) }}</div>
                                    <div class="text-muted small font-monospace mt-1">{{ $shift['time'] }}</div>
                                </td>

                                <!-- 7 Cột ngày trong tuần -->
                                @foreach ($timetable->days as $dayNum => $d)
                                    @php
                                        $items = $timetable->grid[$shiftId]['days'][$dayNum] ?? collect();
                                    @endphp
                                    <td class="p-2 align-top {{ $d['is_today'] ? 'bg-light bg-opacity-50' : '' }}" style="height: 125px;">
                                        @if ($items->isNotEmpty())
                                            <div class="d-flex flex-column gap-3">
                                                @foreach ($items as $item)
                                                    <div class="sms-timetable-lesson p-2 rounded-2 text-start shadow-sm">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="sms-section-code font-monospace small">
                                                                {{ $item->class_section_code }}
                                                            </span>
                                                            <span class="small text-muted font-monospace">{{ $item->periods_text }}</span>
                                                        </div>
                                                        <div class="fw-bold text-dark small text-nowrap" title="{{ $item->course_name }}">
                                                            {{ $item->course_name }}
                                                        </div>
                                                        <div class="small text-primary fw-semibold mt-1">
                                                            <svg class="sms-timetable-icon" style="color: #338cb2;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>{{ $item->room_name }}
                                                        </div>
                                                        <div class="small text-muted fw-bold text-nowrap" title="GV: {{ $item->lecturer_name }}">
                                                            <svg class="sms-timetable-icon" style="color: #9471b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg>GV: {{ $item->lecturer_name }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted opacity-25 small pt-4">—</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CHẾ ĐỘ 2: DANH SÁCH LỊCH HỌC TỔNG HỢP (LIST VIEW) -->
    <div class="col-12" x-show="viewMode === 'list'">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <svg class="sms-timetable-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M8 6h13M8 12h13M8 18h13M3 6h1M3 12h1M3 18h1"/></svg>Danh sách lịch học tất cả các lớp trong kỳ
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="text-center px-3" style="width: 120px;">Thứ</th>
                            <th style="width: 160px;">Thời gian</th>
                            <th style="width: 140px;">Mã lớp HP</th>
                            <th>Tên học phần</th>
                            <th style="width: 100px;" class="text-center">Số tín chỉ</th>
                            <th style="width: 160px;">Phòng học</th>
                            <th style="width: 180px;">Giảng viên</th>
                            <th style="width: 120px;" class="text-center">Hình thức</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($timetable->all_schedules as $sch)
                            <tr>
                                <td class="text-center px-3">
                                    <span class="badge sms-weekday-label fw-bold px-2 py-1">
                                        {{ match ((int) $sch->day_of_week) {
                                            1 => 'Thứ Hai',
                                            2 => 'Thứ Ba',
                                            3 => 'Thứ Tư',
                                            4 => 'Thứ Năm',
                                            5 => 'Thứ Sáu',
                                            6 => 'Thứ Bảy',
                                            7 => 'Chủ Nhật',
                                            default => 'N/A',
                                        } }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $sch->time_text }}</div>
                                    <div class="text-muted small font-monospace">{{ $sch->periods_text }}</div>
                                </td>
                                <td>
                                    <span class="sms-section-code font-monospace">{{ $sch->class_section_code }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $sch->course_name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        {{ $sch->credits }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark"><svg class="sms-timetable-icon" style="color: #338cb2;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>{{ $sch->room_name }}</span>
                                </td>
                                <td>
                                    <span class="small fw-bold">{{ $sch->lecturer_name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border px-2 py-1">
                                        {{ $sch->type ?? 'Lý thuyết' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="fs-1 mb-2">📅</div>
                                    <h6 class="fw-bold">Chưa có lịch học nào trong học kỳ này</h6>
                                    <p class="small mb-3">Bạn chưa đăng ký lớp học phần hoặc các lớp chưa được xếp lịch học.</p>
                                    <a href="{{ route('student.enrollment') }}" class="btn btn-sm btn-primary px-3">
                                        Đăng ký học phần ngay →
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

