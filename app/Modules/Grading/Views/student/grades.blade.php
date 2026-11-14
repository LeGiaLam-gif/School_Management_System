@extends('layouts.student')

@section('title', 'Bảng điểm & GPA · Cổng Sinh Viên')

@section('content')
<div class="row g-4">
    <!-- Header & Thống kê Tích lũy toàn khóa -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-2 rounded-pill fs-7">
                                📊 Kết quả học tập
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                Xếp loại tích lũy: {{ $report->cumulative_classification }}
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-dark">
                            Bảng Điểm & Điểm Trung Bình (GPA)
                        </h3>
                        <p class="text-muted small mb-0">
                            Tra cứu kết quả học tập chi tiết từng học phần và điểm trung bình tích lũy toàn khóa.
                        </p>
                    </div>

                    <!-- Bộ lọc theo học kỳ -->
                    <form method="GET" action="{{ route('student.grades') }}" class="d-flex align-items-center gap-2">
                        <select name="semester" class="form-select shadow-sm" onchange="this.form.submit()" style="min-width: 250px;">
                            @foreach ($report->semesters as $key => $name)
                                <option value="{{ $key }}" {{ $report->selected_semester === $key ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <!-- 4 Thẻ thống kê toàn khóa -->
                <div class="row g-3 text-center">
                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">GPA Tích lũy (Hệ 4)</span>
                            <div class="d-flex align-items-baseline justify-content-center gap-1 mt-1">
                                <h3 class="fw-bold text-primary mb-0">{{ number_format($report->cumulative_gpa_4, 2) }}</h3>
                                <span class="text-muted fs-6">/ 4.0</span>
                            </div>
                            <span class="badge bg-primary-subtle text-primary mt-1">{{ $report->cumulative_classification }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Điểm TB Tích lũy (Hệ 10)</span>
                            <div class="d-flex align-items-baseline justify-content-center gap-1 mt-1">
                                <h3 class="fw-bold text-success mb-0">{{ number_format($report->cumulative_gpa_10, 2) }}</h3>
                                <span class="text-muted fs-6">/ 10.0</span>
                            </div>
                            <span class="badge bg-success-subtle text-success mt-1">Thang 10</span>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Tín chỉ đã tích lũy</span>
                            <div class="d-flex align-items-baseline justify-content-center gap-1 mt-1">
                                <h3 class="fw-bold text-dark mb-0">{{ $report->total_passed_credits }}</h3>
                                <span class="text-muted fs-6">/ {{ $report->total_program_credits }} TC</span>
                            </div>
                            <span class="badge bg-info-subtle text-info mt-1">Đạt {{ $report->completion_percent }}% CTĐT</span>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Tín chỉ nợ / Cần học lại</span>
                            <div class="d-flex align-items-baseline justify-content-center gap-1 mt-1">
                                <h3 class="fw-bold {{ $report->total_failed_credits > 0 ? 'text-danger' : 'text-muted' }} mb-0">
                                    {{ $report->total_failed_credits }}
                                </h3>
                                <span class="text-muted fs-6">TC</span>
                            </div>
                            <span class="badge {{ $report->total_failed_credits > 0 ? 'bg-danger' : 'bg-success' }} mt-1">
                                {{ $report->total_failed_credits > 0 ? 'Có học phần nợ' : 'Không nợ môn' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Bảng điểm từng học kỳ -->
    <div class="col-12">
        <div class="d-flex flex-column gap-4">
            @forelse ($report->semester_groups as $sem)
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <!-- Header học kỳ -->
                    <div class="card-header bg-white py-3 px-4 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                <span>📚 {{ $sem->semester_name }}</span>
                            </h5>
                            <span class="text-muted small">
                                Tín chỉ học kỳ: <strong>{{ $sem->semester_credits }} TC</strong> · Xếp loại học lực: <strong class="text-primary">{{ $sem->classification }}</strong>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end">
                                <span class="text-muted small">ĐTB Học kỳ (Hệ 4):</span>
                                <span class="badge bg-primary fs-7 ms-1">{{ number_format($sem->semester_gpa_4, 2) }}</span>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small">Hệ 10:</span>
                                <span class="badge bg-success fs-7 ms-1">{{ number_format($sem->semester_gpa_10, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng điểm chi tiết học kỳ -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4" style="width: 50px;">STT</th>
                                    <th style="width: 120px;">Mã HP</th>
                                    <th>Tên học phần</th>
                                    <th class="text-center" style="width: 80px;">Số TC</th>
                                    <th class="text-center" style="width: 100px;">Chuyên cần (10%)</th>
                                    <th class="text-center" style="width: 100px;">Giữa kỳ (30%)</th>
                                    <th class="text-center" style="width: 100px;">Cuối kỳ (60%)</th>
                                    <th class="text-center" style="width: 100px;">Tổng kết (10)</th>
                                    <th class="text-center" style="width: 90px;">Điểm chữ</th>
                                    <th class="text-center" style="width: 90px;">Thang 4</th>
                                    <th class="text-center pe-4" style="width: 100px;">Kết quả</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sem->courses as $cIdx => $c)
                                    <tr>
                                        <td class="ps-4 text-muted small">{{ $cIdx + 1 }}</td>
                                        <td>
                                            <span class="fw-bold font-monospace text-primary">{{ $c->code }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $c->name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ $c->credits }}</span>
                                        </td>
                                        <td class="text-center small">{{ number_format($c->attendance_score, 1) }}</td>
                                        <td class="text-center small">{{ number_format($c->midterm_score, 1) }}</td>
                                        <td class="text-center small">{{ number_format($c->final_score, 1) }}</td>
                                        <td class="text-center fw-bold text-dark">{{ number_format($c->total_score, 1) }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ in_array($c->letter_grade, ['A', 'B+']) ? 'bg-success' : (in_array($c->letter_grade, ['B', 'C+']) ? 'bg-primary' : 'bg-warning text-dark') }} px-2 py-1">
                                                {{ $c->letter_grade }}
                                            </span>
                                        </td>
                                        <td class="text-center font-monospace fw-semibold">{{ number_format($c->gpa_point, 1) }}</td>
                                        <td class="text-center pe-4">
                                            @if ($c->is_passed)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    ✓ Đạt
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                    Học lại
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-5 text-center text-muted">
                        <div class="fs-1 mb-2">📑</div>
                        <h6 class="fw-bold">Chưa có dữ liệu điểm cho học kỳ đã chọn</h6>
                        <p class="small mb-0">Vui lòng chọn học kỳ khác hoặc liên hệ Phòng Đào tạo nếu có thắc mắc.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Hướng dẫn Quy chế Thang điểm -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 px-4 border-0">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span>ℹ️ Quy chế Thang điểm & Xếp loại đào tạo tín chỉ</span>
                </h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-center small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Thang điểm 10</th>
                                <th>Điểm chữ</th>
                                <th>Thang điểm 4</th>
                                <th>Đánh giá</th>
                                <th>Xếp loại học lực chung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>8.5 – 10.0</td>
                                <td><span class="badge bg-success">A</span></td>
                                <td class="font-monospace">4.0</td>
                                <td>Giỏi</td>
                                <td rowspan="2" class="align-middle fw-semibold text-primary">Xuất sắc: 3.60 – 4.00<br>Giỏi: 3.20 – 3.59</td>
                            </tr>
                            <tr>
                                <td>8.0 – 8.4</td>
                                <td><span class="badge bg-success">B+</span></td>
                                <td class="font-monospace">3.5</td>
                                <td>Khá giỏi</td>
                            </tr>
                            <tr>
                                <td>7.0 – 7.9</td>
                                <td><span class="badge bg-primary">B</span></td>
                                <td class="font-monospace">3.0</td>
                                <td>Khá</td>
                                <td rowspan="2" class="align-middle fw-semibold text-success">Khá: 2.50 – 3.19</td>
                            </tr>
                            <tr>
                                <td>6.5 – 6.9</td>
                                <td><span class="badge bg-primary">C+</span></td>
                                <td class="font-monospace">2.5</td>
                                <td>Trung bình khá</td>
                            </tr>
                            <tr>
                                <td>5.5 – 6.4</td>
                                <td><span class="badge bg-warning text-dark">C</span></td>
                                <td class="font-monospace">2.0</td>
                                <td>Trung bình</td>
                                <td class="align-middle fw-semibold text-warning">Trung bình: 2.00 – 2.49</td>
                            </tr>
                            <tr>
                                <td>4.0 – 5.4</td>
                                <td><span class="badge bg-secondary">D / D+</span></td>
                                <td class="font-monospace">1.0 – 1.5</td>
                                <td>Yếu / Đạt</td>
                                <td class="align-middle fw-semibold text-secondary">Yếu: 1.00 – 1.99</td>
                            </tr>
                            <tr>
                                <td>Dưới 4.0</td>
                                <td><span class="badge bg-danger">F</span></td>
                                <td class="font-monospace">0.0</td>
                                <td class="text-danger fw-semibold">Kém (Học lại)</td>
                                <td class="align-middle fw-semibold text-danger">Kém: < 1.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

