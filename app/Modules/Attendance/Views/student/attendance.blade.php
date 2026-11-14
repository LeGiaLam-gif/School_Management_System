@extends('layouts.student')

@section('title', 'Theo dõi Chuyên cần & Điểm danh · Cổng Sinh Viên')

@section('content')
<div class="row g-4">
    <!-- Header Thống kê Chuyên cần -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-2 rounded-pill fs-7">
                                📋 Điểm danh học kỳ
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                {{ $attendance->semester_name }}
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-dark">
                            Theo Dõi Điểm Danh & Chuyên Cần
                        </h3>
                        <p class="text-muted small mb-0">
                            Quy chế đào tạo: Sinh viên vắng từ <strong>20% số buổi</strong> trở lên sẽ bị <strong>cấm thi kết thúc học phần</strong> (BR-09).
                        </p>
                    </div>

                    <div class="text-md-end">
                        <span class="text-muted small fw-semibold">Tỷ lệ vắng trung bình:</span>
                        <div class="d-flex align-items-baseline justify-content-md-end gap-1 mt-1">
                            <h2 class="fw-bold {{ $attendance->overall_rate >= 20.0 ? 'text-danger' : ($attendance->overall_rate >= 10.0 ? 'text-warning' : 'text-success') }} mb-0">
                                {{ number_format($attendance->overall_rate, 1) }}%
                            </h2>
                            <span class="text-muted fs-6">/ Max 20%</span>
                        </div>
                    </div>
                </div>

                <!-- 4 Thẻ thống kê buổi học -->
                <div class="row g-3 text-center">
                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Tổng số buổi đã diễn ra</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $attendance->overall_held }}</h3>
                            <span class="badge bg-secondary-subtle text-secondary mt-1">Buổi học</span>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Số buổi có mặt</span>
                            <h3 class="fw-bold text-success mb-0 mt-1">{{ $attendance->overall_present }}</h3>
                            <span class="badge bg-success-subtle text-success mt-1">Đúng giờ</span>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Số buổi vào muộn</span>
                            <h3 class="fw-bold text-warning mb-0 mt-1">{{ $attendance->overall_late }}</h3>
                            <span class="badge bg-warning-subtle text-warning mt-1">Đi muộn</span>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Số buổi vắng mặt</span>
                            <h3 class="fw-bold {{ $attendance->overall_absent > 0 ? 'text-danger' : 'text-success' }} mb-0 mt-1">
                                {{ $attendance->overall_absent }}
                            </h3>
                            <span class="badge {{ $attendance->overall_absent > 0 ? 'bg-danger' : 'bg-success' }} mt-1">
                                {{ $attendance->overall_absent > 0 ? 'Có vắng học' : 'Nghiêm túc 100%' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách Chuyên cần từng môn học -->
    <div class="col-12">
        <div class="d-flex flex-column gap-4">
            @foreach ($attendance->courses as $cIdx => $c)
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary font-monospace">{{ $c->class_section_code }}</span>
                                <h5 class="fw-bold mb-0 text-dark">{{ $c->course_name }}</h5>
                                <span class="badge bg-light text-dark border">{{ $c->credits }} TC</span>
                            </div>
                            <span class="text-muted small">
                                Giảng viên phụ trách: <strong>{{ $c->lecturer_name }}</strong>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end">
                                <span class="text-muted small">Tỷ lệ vắng:</span>
                                <span class="fw-bold {{ $c->absence_rate >= 20.0 ? 'text-danger' : ($c->absence_rate >= 10.0 ? 'text-warning' : 'text-success') }} ms-1">
                                    {{ number_format($c->absence_rate, 1) }}%
                                </span>
                            </div>
                            <span class="badge {{ $c->status_badge }} px-3 py-2 rounded-pill">
                                {{ $c->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Tiến trình chuyên cần của môn -->
                    <div class="px-4 py-2 bg-light border-top border-bottom small d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            Đã học: <strong>{{ $c->held_sessions }}/{{ $c->total_sessions }} buổi</strong> · 
                            Có mặt: <span class="text-success fw-bold">{{ $c->present_count }}</span> · 
                            Muộn: <span class="text-warning fw-bold">{{ $c->late_count }}</span> · 
                            Vắng: <span class="text-danger fw-bold">{{ $c->absent_count }}</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSessions{{ $cIdx }}" aria-expanded="false">
                            Xem nhật ký buổi học ▼
                        </button>
                    </div>

                    <!-- Nhật ký từng buổi học (Collapse) -->
                    <div class="collapse show" id="collapseSessions{{ $cIdx }}">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="ps-4 text-start" style="width: 80px;">Buổi</th>
                                        <th style="width: 140px;">Ngày học</th>
                                        <th style="width: 160px;">Thời gian</th>
                                        <th style="width: 120px;">Phòng</th>
                                        <th style="width: 140px;">Trạng thái</th>
                                        <th class="text-end pe-4">Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($c->sessions as $s)
                                        <tr>
                                            <td class="ps-4 text-start fw-bold text-muted">#{{ $s->session_no }}</td>
                                            <td class="font-monospace fw-semibold">{{ $s->date }}</td>
                                            <td class="small">{{ $s->time }}</td>
                                            <td><span class="badge bg-light text-dark border">📍 {{ $s->room }}</span></td>
                                            <td>
                                                <span class="badge {{ $s->badge }} px-3 py-1">
                                                    {{ $s->status }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 small text-muted">{{ $s->note }}</td>
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
