@extends('layouts.student')

@section('title', 'Khung Chương trình đào tạo · Cổng Sinh Viên')

@section('content')
<div class="row g-4">
    <!-- Header thông tin chương trình đào tạo -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
                            {{ $curriculum->department_name }}
                        </span>
                        <h3 class="fw-bold mb-1 text-dark">
                            Khung Chương Trình Đào Tạo: {{ $curriculum->program_name }}
                        </h3>
                        <p class="text-muted mb-0">
                            Mã ngành: <strong class="text-dark">{{ $curriculum->program_code }}</strong> · 
                            Thời gian đào tạo: <strong class="text-dark">{{ $curriculum->duration_years }} năm</strong> (8 học kỳ chính)
                        </p>
                    </div>

                    <!-- Nút chuyển sang danh mục môn học toàn trường -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('student.courses') }}" class="btn btn-outline-primary px-3 py-2 fw-semibold">
                            🔍 Tra cứu môn học toàn trường →
                        </a>
                    </div>
                </div>

                <hr class="my-4">

                <!-- 3 Thống kê tín chỉ -->
                <div class="row g-3 text-center">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Tổng tín chỉ toàn khóa</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1">{{ $curriculum->total_credits }} <span class="fs-6 fw-normal text-muted">TC</span></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Tín chỉ Bắt buộc</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $curriculum->required_credits }} <span class="fs-6 fw-normal text-muted">TC</span></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-semibold">Tín chỉ Tự chọn gợi ý</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $curriculum->elective_credits }} <span class="fs-6 fw-normal text-muted">TC</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách môn học chia theo từng học kỳ -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span>📑 Kế hoạch giảng dạy theo học kỳ</span>
            </h5>
            <span class="text-muted small">Nhấp vào mã môn để xem chi tiết & điều kiện tiên quyết</span>
        </div>

        <div class="accordion d-flex flex-column gap-3" id="curriculumAccordion">
            @foreach ($curriculum->semesters as $index => $sem)
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center cursor-pointer"
                         data-bs-toggle="collapse" 
                         data-bs-target="#collapseSem{{ $sem['semester_no'] }}"
                         aria-expanded="{{ $index < 2 ? 'true' : 'false' }}">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary rounded-circle p-2 fs-7" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                {{ $sem['semester_no'] }}
                            </span>
                            <h6 class="fw-bold mb-0 text-dark">{{ $sem['semester_title'] }}</h6>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                                {{ $sem['total_credits'] }} Tín chỉ
                            </span>
                            <span class="text-muted small">▼</span>
                        </div>
                    </div>

                    <div id="collapseSem{{ $sem['semester_no'] }}" 
                         class="collapse {{ $index < 2 ? 'show' : '' }}" 
                         data-bs-parent="#curriculumAccordion">
                        <div class="card-body p-0 border-top">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted small">
                                        <tr>
                                            <th class="ps-4" style="width: 50px;">STT</th>
                                            <th style="width: 130px;">Mã HP</th>
                                            <th>Tên học phần</th>
                                            <th class="text-center" style="width: 100px;">Số TC</th>
                                            <th class="text-center" style="width: 140px;">Số tiết (LT/TH)</th>
                                            <th class="text-center" style="width: 120px;">Loại môn</th>
                                            <th style="width: 220px;">Học phần tiên quyết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sem['courses'] as $cIndex => $course)
                                            <tr>
                                                <td class="ps-4 text-muted small">{{ $cIndex + 1 }}</td>
                                                <td>
                                                    <a href="{{ route('student.courses.detail', $course->code) }}" 
                                                       class="fw-bold font-monospace text-primary text-decoration-none">
                                                        {{ $course->code }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-dark">{{ $course->name }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                                        {{ $course->credits }} TC
                                                    </span>
                                                </td>
                                                <td class="text-center small text-muted">
                                                    {{ $course->theory_periods }} LT / {{ $course->practice_periods }} TH
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $course->type_badge }} px-2 py-1">
                                                        {{ $course->type_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if (!empty($course->prerequisites))
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach ($course->prerequisites as $prereq)
                                                                <a href="{{ route('student.courses.detail', $prereq) }}" 
                                                                   class="badge bg-secondary-subtle text-secondary border text-decoration-none font-monospace">
                                                                    {{ $prereq }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

