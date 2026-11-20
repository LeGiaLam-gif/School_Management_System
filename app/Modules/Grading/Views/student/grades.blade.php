@extends('layouts.app')

@section('title', 'Kết quả học tập')

@section('content')
<style>
    .sms-student-grades .sms-grades-semester {
        background: #edf4f0;
        color: #35564c;
    }

    .sms-student-grades .sms-grades-columns th {
        --bs-table-bg: #f6f8f7;
        --bs-table-color: #465850;
        font-weight: 600;
    }

    .sms-student-grades .sms-grades-metric {
        background: #e1ebe6;
        color: #35564c;
        font-weight: 600;
    }

    .sms-student-grades .sms-curriculum-table > tbody > tr > td {
        background: #fff;
        box-shadow: none;
    }
</style>
<div class="row g-4 sms-student-grades">
    <div class="col-12">
        <header class="sms-curriculum-banner p-3 p-md-4 px-lg-5 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <h1 class="h3 fw-bold mb-0 d-flex align-items-center gap-2">
                <svg class="flex-shrink-0" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
                Kết quả học tập
            </h1>
            <form method="GET" action="{{ route('student.grades') }}">
                <select name="semester" class="form-select" onchange="this.form.submit()" aria-label="Chọn học kỳ">
                    @foreach ($report->semesters as $key => $name)
                        <option value="{{ $key }}" {{ $report->selected_semester === $key ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </header>
    </div>

    <div class="col-12">
        <div class="row g-2 g-md-3">
            @foreach ([
                ['GPA tích lũy', number_format($report->cumulative_gpa_4, 2), '/ 4.0', 'navy', 'M4 20V10M10 20V4M16 20v-7M22 20H2'],
                ['Điểm trung bình tích lũy', number_format($report->cumulative_gpa_10, 2), '/ 10.0', 'teal', 'M3 17l6-6 4 4 7-8m-5 0h5v5'],
                ['Tín chỉ đã tích lũy', $report->total_passed_credits, '/ '.$report->total_program_credits.' tín chỉ', 'amber', 'M9 4H5v17h14V4h-4M9 2h6v4H9zM8 13l3 3 5-6'],
                ['Tín chỉ nợ', $report->total_failed_credits, 'tín chỉ', 'navy', 'M12 8v5m0 3h.01M10.3 4.5 3 18h18L13.7 4.5a2 2 0 0 0-3.4 0Z'],
            ] as [$label, $value, $unit, $tone, $iconPath])
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="hta-stat-card sms-curriculum-stat h-100 py-2">
                        <span class="hta-stat-icon hta-stat-icon-{{ $tone }} flex-shrink-0" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iconPath }}"/>
                            </svg>
                        </span>
                        <dl class="mb-0">
                            <dt class="small fw-semibold text-muted">{{ $label }}</dt>
                            <dd class="fs-4 fw-bold mb-0">
                                {{ $value }}
                                <span class="fs-6 fw-normal text-muted">{{ $unit }}</span>
                            </dd>
                            @if ($loop->first)
                                <dd class="small text-muted mb-0">Xếp loại: <strong>{{ $report->cumulative_classification }}</strong></dd>
                            @endif
                        </dl>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <!-- Danh sách Bảng điểm từng học kỳ -->
    <div class="col-12">
        <div class="d-flex flex-column gap-4">
            @forelse ($report->semester_groups as $sem)
                <div class="card border shadow-sm rounded-4 overflow-hidden">
                    <!-- Header học kỳ -->
                    <div class="card-header sms-grades-semester py-3 px-4 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h5 class="fw-semibold mb-1 d-flex align-items-center gap-2">
                                <span><svg class="flex-shrink-0 align-middle me-1" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg> {{ $sem->semester_name }}</span>
                            </h5>
                            <span class="text-muted small">
                                Tín chỉ học kỳ: <strong>{{ $sem->semester_credits }}</strong> · Xếp loại học lực: <strong class="fw-semibold">{{ $sem->classification }}</strong>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end">
                                <span class="text-muted small">GPA học kỳ:</span>
                                <span class="badge rounded-pill sms-grades-metric ms-1">{{ number_format($sem->semester_gpa_4, 2) }}</span>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small">Điểm trung bình học kỳ:</span>
                                <span class="badge rounded-pill sms-grades-metric ms-1">{{ number_format($sem->semester_gpa_10, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng điểm chi tiết học kỳ -->
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 sms-curriculum-table">
                            <thead class="sms-grades-columns small text-nowrap">
                                <tr>
                                    <th class="ps-4" style="width: 50px;">STT</th>
                                    <th style="width: 90px;">Mã HP</th>
                                    <th>Tên học phần</th>
                                    <th class="text-center" style="width: 80px;">Tín chỉ</th>
                                    <th class="text-center" style="width: 1%;">Chuyên cần (10%)</th>
                                    <th class="text-center" style="width: 1%;">Giữa kỳ (30%)</th>
                                    <th class="text-center" style="width: 1%;">Cuối kỳ (60%)</th>
                                    <th class="text-center" style="width: 1%;">Tổng kết (10)</th>
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
                                            <span class="sms-curriculum-course-code fw-semibold font-monospace">{{ $c->code }}</span>
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
                                            <span class="badge {{ in_array($c->letter_grade, ['A', 'B+']) ? 'bg-success-subtle text-success-emphasis' : (in_array($c->letter_grade, ['B', 'C+']) ? 'bg-primary-subtle text-primary-emphasis' : 'bg-warning-subtle text-warning-emphasis') }} rounded-pill px-2 py-1">
                                                {{ $c->letter_grade }}
                                            </span>
                                        </td>
                                        <td class="text-center font-monospace fw-semibold">{{ number_format($c->gpa_point, 1) }}</td>
                                        <td class="text-center pe-4">
                                            @if ($c->is_passed)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    Đạt
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
                <div class="card border shadow-sm rounded-4 overflow-hidden">
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
        <div class="card border shadow-sm rounded-4 overflow-hidden">
            <div class="card-header sms-curriculum-semester py-3 px-4 border-0">
                <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                    <span><svg class="flex-shrink-0 align-middle me-1 text-primary" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg> Quy chế xếp loại</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle text-center small mb-0 sms-curriculum-table">
                        <thead class="small">
                            <tr>
                                <th>Thang điểm 10</th>
                                <th>Điểm chữ</th>
                                <th>Thang điểm 4</th>
                                <th>Đánh giá</th>
                                <th>Xếp loại học lực</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>8.5 – 10.0</td>
                                <td><span class="badge rounded-pill bg-success-subtle text-success-emphasis">A</span></td>
                                <td class="font-monospace">4.0</td>
                                <td>Giỏi</td>
                                <td rowspan="2" class="align-middle fw-semibold text-primary">Xuất sắc: 3.60 – 4.00<br>Giỏi: 3.20 – 3.59</td>
                            </tr>
                            <tr>
                                <td>8.0 – 8.4</td>
                                <td><span class="badge rounded-pill bg-success-subtle text-success-emphasis">B+</span></td>
                                <td class="font-monospace">3.5</td>
                                <td>Khá giỏi</td>
                            </tr>
                            <tr>
                                <td>7.0 – 7.9</td>
                                <td><span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">B</span></td>
                                <td class="font-monospace">3.0</td>
                                <td>Khá</td>
                                <td rowspan="2" class="align-middle fw-semibold text-success">Khá: 2.50 – 3.19</td>
                            </tr>
                            <tr>
                                <td>6.5 – 6.9</td>
                                <td><span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">C+</span></td>
                                <td class="font-monospace">2.5</td>
                                <td>Trung bình khá</td>
                            </tr>
                            <tr>
                                <td>5.5 – 6.4</td>
                                <td><span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">C</span></td>
                                <td class="font-monospace">2.0</td>
                                <td>Trung bình</td>
                                <td class="align-middle fw-semibold text-warning">Trung bình: 2.00 – 2.49</td>
                            </tr>
                            <tr>
                                <td>4.0 – 5.4</td>
                                <td><span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">D / D+</span></td>
                                <td class="font-monospace">1.0 – 1.5</td>
                                <td>Yếu / Đạt</td>
                                <td class="align-middle fw-semibold text-secondary">Yếu: 1.00 – 1.99</td>
                            </tr>
                            <tr>
                                <td>Dưới 4.0</td>
                                <td><span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">F</span></td>
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

