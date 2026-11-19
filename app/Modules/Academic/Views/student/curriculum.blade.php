@extends('layouts.app')

@section('title', 'Chương trình khung · Hexa Tech Academy')

@section('content')
<div class="row g-4 sms-student-curriculum">
    <div class="col-12">
        <header class="sms-curriculum-banner p-3 p-md-4 px-lg-5">
            <h1 class="h3 fw-bold mb-2">Chương trình khung</h1>
            <div class="d-flex flex-wrap gap-2">
                <span class="sms-role-pill align-items-baseline gap-1 mt-0">
                    Ngành: <strong>{{ $curriculum->department_name }}</strong>
                </span>
                <span class="sms-role-pill align-items-baseline gap-1 mt-0">
                    Chuyên ngành: <strong>{{ $curriculum->program_name }}</strong>
                </span>
                <span class="sms-role-pill align-items-baseline gap-1 mt-0">
                    Mã ngành: <strong>{{ $curriculum->program_code }}</strong>
                </span>
                <span class="sms-role-pill align-items-baseline gap-1 mt-0">
                    Thời gian đào tạo: <strong>{{ $curriculum->duration_years }} năm</strong>
                </span>
            </div>
        </header>
    </div>

    <div class="col-12">
        <div class="row g-2 g-md-3">
            @foreach ([
                ['Tổng tín chỉ', $curriculum->total_credits, 'navy', 'M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20M8 7h8M8 11h8'],
                ['Bắt buộc', $curriculum->required_credits, 'teal', 'M9 4H5v17h14V4h-4M9 2h6v4H9zM8 13l3 3 5-6'],
                ['Tự chọn', $curriculum->elective_credits, 'amber', 'm12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2-5.5-2.9-5.5 2.9 1-6.2L3 9.6l6.2-.9z'],
            ] as [$label, $credits, $tone, $iconPath])
                <div class="col-12 col-sm-4">
                    <div class="hta-stat-card sms-curriculum-stat">
                        <span class="hta-stat-icon hta-stat-icon-{{ $tone }}" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
                                 stroke-linecap="round" stroke-linejoin="round" focusable="false">
                                <path d="{{ $iconPath }}" />
                            </svg>
                        </span>
                        <dl class="mb-0">
                            <dt class="small fw-semibold text-muted">{{ $label }}</dt>
                            <dd class="fs-4 fw-bold mb-0">
                                {{ $credits }}
                                @unless ($loop->first)
                                    <span class="fs-6 fw-normal text-muted">tín chỉ</span>
                                @endunless
                            </dd>
                        </dl>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Danh sách môn học chia theo từng học kỳ -->
    <div class="col-12">
        <div class="accordion d-flex flex-column gap-2" id="curriculumAccordion">
            @foreach ($curriculum->semesters as $sem)
                <div class="card shadow-sm rounded-3 overflow-hidden">
                    <h2 class="mb-0">
                        <button type="button"
                                class="sms-curriculum-semester w-100 border-0 text-start d-flex justify-content-between align-items-center gap-2 py-3 px-3 px-md-4"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseSem{{ $sem['semester_no'] }}"
                                aria-controls="collapseSem{{ $sem['semester_no'] }}"
                                aria-expanded="false">
                            <span class="fw-semibold">Học kỳ {{ $sem['semester_no'] }}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                {{ $sem['total_credits'] }} tín chỉ
                            </span>
                        </button>
                    </h2>

                    <div id="collapseSem{{ $sem['semester_no'] }}"
                         class="collapse"
                         data-bs-parent="#curriculumAccordion">
                        <div class="card-body p-0 border-top">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 sms-curriculum-table">
                                    <thead class="small">
                                        <tr>
                                            <th class="ps-4" style="width: 50px;">STT</th>
                                            <th style="width: 130px;">Mã HP</th>
                                            <th>Tên học phần</th>
                                            <th class="text-center text-nowrap" style="width: 100px;">Số tín chỉ</th>
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
                                                       class="sms-curriculum-course-code fw-semibold font-monospace text-decoration-none">
                                                        {{ $course->code }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-dark">{{ $course->name }}</span>
                                                </td>
                                                <td class="text-center">
                                                    {{ $course->credits }}
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

