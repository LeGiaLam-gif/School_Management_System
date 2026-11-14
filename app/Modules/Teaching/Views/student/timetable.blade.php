@extends('layouts.student')

@section('title', 'Thời khóa biểu sinh viên · Cổng Sinh Viên')

@section('content')
<div class="row g-4" x-data="{ viewMode: 'grid' }">
    <!-- Header Thời khóa biểu & Chuyển đổi chế độ xem -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-2 rounded-pill fs-7">
                                📅 Lịch học chính thức
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                {{ $timetable->total_courses }} Môn học · {{ $timetable->total_credits }} Tín chỉ
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-dark">
                            Thời Khóa Biểu Sinh Viên
                        </h3>
                        <p class="text-muted small mb-0">
                            Theo dõi lịch học các môn học phần đã đăng ký trong tuần và toàn học kỳ.
                        </p>
                    </div>

                    <!-- Nút đổi chế độ xem Grid / List -->
                    <div class="btn-group shadow-sm" role="group">
                        <button type="button" 
                                class="btn px-3 py-2 fw-semibold" 
                                :class="viewMode === 'grid' ? 'btn-primary' : 'btn-outline-primary'"
                                @click="viewMode = 'grid'">
                            📊 Lưới theo tuần
                        </button>
                        <button type="button" 
                                class="btn px-3 py-2 fw-semibold" 
                                :class="viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary'"
                                @click="viewMode = 'list'">
                            📋 Danh sách tổng hợp
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
                        <span>🗓️ {{ $timetable->week_label }}</span>
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
                <table class="table table-bordered align-middle mb-0 text-center" style="min-width: 1000px;">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 text-muted small" style="width: 140px;">Ca học / Tiết</th>
                            @foreach ($timetable->days as $d)
                                <th class="py-3 {{ $d['is_today'] ? 'bg-primary bg-opacity-10 text-primary border-primary' : '' }}" style="width: 12.5%;">
                                    <div class="fw-bold fs-6">{{ $d['name'] }}</div>
                                    <div class="small {{ $d['is_today'] ? 'text-primary fw-semibold' : 'text-muted' }}">
                                        {{ $d['date_formatted'] }}
                                        @if ($d['is_today'])
                                            <span class="badge bg-primary text-white ms-1">Hôm nay</span>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timetable->shifts as $shiftId => $shift)
                            <tr>
                                <!-- Cột thông tin Ca học -->
                                <td class="bg-light text-start p-3 align-top">
                                    <div class="fw-bold text-dark small">{{ $shift['label'] }}</div>
                                    <div class="text-muted small font-monospace mt-1">{{ $shift['time'] }}</div>
                                </td>

                                <!-- 7 Cột ngày trong tuần -->
                                @foreach ($timetable->days as $dayNum => $d)
                                    @php
                                        $items = $timetable->grid[$shiftId]['days'][$dayNum] ?? collect();
                                    @endphp
                                    <td class="p-2 align-top {{ $d['is_today'] ? 'bg-light bg-opacity-50' : '' }}" style="height: 125px;">
                                        @if ($items->isNotEmpty())
                                            <div class="d-flex flex-column gap-2">
                                                @foreach ($items as $item)
                                                    <div class="p-2 rounded-2 text-start shadow-sm border border-{{ $item->card_color }} bg-{{ $item->card_color }} bg-opacity-10">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="badge bg-{{ $item->card_color }} font-monospace fs-8">
                                                                {{ $item->class_section_code }}
                                                            </span>
                                                            <span class="small text-muted font-monospace">{{ $item->periods_text }}</span>
                                                        </div>
                                                        <div class="fw-bold text-dark small text-truncate" title="{{ $item->course_name }}">
                                                            {{ $item->course_name }}
                                                        </div>
                                                        <div class="small text-primary fw-semibold mt-1">
                                                            📍 {{ $item->room_name }}
                                                        </div>
                                                        <div class="small text-muted text-truncate" title="{{ $item->lecturer_name }}">
                                                            👨‍🏫 {{ $item->lecturer_name }}
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
                    📋 Danh sách lịch học tất cả các lớp trong kỳ
                </h5>
                <span class="text-muted small">Hiển thị lịch học cố định theo tuần</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4" style="width: 120px;">Thứ</th>
                            <th style="width: 160px;">Thời gian / Tiết</th>
                            <th style="width: 140px;">Mã lớp HP</th>
                            <th>Tên học phần</th>
                            <th style="width: 100px;" class="text-center">Số TC</th>
                            <th style="width: 160px;">Phòng học</th>
                            <th style="width: 180px;">Giảng viên</th>
                            <th style="width: 120px;" class="text-center">Hình thức</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($timetable->all_schedules as $sch)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">
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
                                    <span class="fw-bold font-monospace text-primary">{{ $sch->class_section_code }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $sch->course_name }}</span>
                                    <div class="small text-muted font-monospace">{{ $sch->course_code }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        {{ $sch->credits }} TC
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">📍 {{ $sch->room_name }}</span>
                                </td>
                                <td>
                                    <span class="small">{{ $sch->lecturer_name }}</span>
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
