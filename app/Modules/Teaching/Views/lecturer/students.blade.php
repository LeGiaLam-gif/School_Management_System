@extends('layouts.app')

@section('title', 'Danh sách sinh viên · Cổng Giảng Viên')

@section('content')
<div class="sms-workspace-page">
    <div class="sms-workspace-page-heading">
        <div>
            <span class="sms-eyebrow">HEXATECH ACADEMY · GIẢNG VIÊN</span>
            <h1>Danh sách sinh viên</h1>
            <p>
                @if ($selectedClass)
                    Lớp <strong class="font-monospace" style="color: var(--sms-teal-dark);">{{ $selectedClass->code }}</strong> ·
                    {{ $selectedClass->course?->name }} ({{ $selectedClass->course?->code }})
                @else
                    Tra cứu sinh viên thuộc các lớp được phân công.
                @endif
            </p>
        </div>
        <a href="{{ route('lecturer.classes', ['semester_id' => $filters['semester_id']]) }}" class="sms-workspace-back-link">
            ← Về danh sách lớp
        </a>
    </div>

    <div class="sms-workspace-card" style="min-height: auto;">
        <form method="GET" action="{{ route('lecturer.students') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
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

            <div class="col-md-4">
                <label for="class_section_id" class="form-label small fw-semibold text-muted">Lớp học phần</label>
                <select name="class_section_id" id="class_section_id" class="form-select" onchange="this.form.submit()">
                    @forelse ($classes as $section)
                        <option value="{{ $section->id }}" @selected($selectedClass?->id === $section->id)>
                            {{ $section->code }} — {{ $section->course?->name }} ({{ $section->enrolled_count }} SV)
                        </option>
                    @empty
                        <option value="">Không có lớp trong học kỳ này</option>
                    @endforelse
                </select>
            </div>

            <div class="col-md-3">
                <label for="keyword" class="form-label small fw-semibold text-muted">Tìm sinh viên</label>
                <input
                    type="text"
                    name="keyword"
                    id="keyword"
                    class="form-control"
                    value="{{ $filters['keyword'] }}"
                    placeholder="MSSV, họ tên hoặc email..."
                >
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button
                    type="submit"
                    class="btn fw-semibold text-white flex-grow-1"
                    style="background-color: var(--sms-teal); border-color: var(--sms-teal);"
                >
                    Tìm
                </button>
                @if ($filters['keyword'] !== '' && $selectedClass)
                    <a href="{{ route('lecturer.students', ['class_section_id' => $selectedClass->id]) }}" class="btn btn-outline-secondary">
                        Xóa
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f7faf7;">
            <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                Sinh viên đăng ký học phần
            </span>
            @if ($selectedClass)
                <span class="badge rounded-pill px-3 py-2" style="background: #e8f4ef; color: var(--sms-teal-dark);">
                    Sĩ số: {{ $enrollments->count() }} / {{ $selectedClass->capacity }}
                </span>
            @endif
        </div>

        @if ($selectedClass === null)
            <div class="p-4">
                <div class="sms-workspace-notice mx-auto my-2">
                    <span class="sms-workspace-notice-icon">i</span>
                    <div>
                        <h2>Chưa chọn lớp học phần</h2>
                        <p>Bạn chưa được phân công lớp nào trong học kỳ đang chọn.</p>
                    </div>
                </div>
            </div>
        @elseif ($enrollments->isEmpty())
            <div class="p-4">
                <div class="sms-workspace-notice mx-auto my-2">
                    <span class="sms-workspace-notice-icon">i</span>
                    <div>
                        <h2>Không có sinh viên nào</h2>
                        <p>Chưa có sinh viên đăng ký hoặc không khớp từ khóa tìm kiếm.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted" style="background: #f8faf8;">
                        <tr>
                            <th class="ps-4" style="width: 60px;">STT</th>
                            <th style="width: 140px;">MSSV</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th style="width: 130px;">Số điện thoại</th>
                            <th style="width: 140px;">Lớp sinh hoạt</th>
                            <th class="text-center" style="width: 90px;">Khóa</th>
                            <th class="text-center pe-4" style="width: 130px;">Trạng thái ĐK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enrollments as $index => $enrollment)
                            @php
                                $student = $enrollment->student;
                                $studentUser = $student?->user;
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold font-monospace" style="color: var(--sms-teal-dark);">
                                        {{ $student?->student_code }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $studentUser?->name }}</span>
                                </td>
                                <td class="text-muted small">{{ $studentUser?->email }}</td>
                                <td class="text-muted small">{{ $student?->phone ?? $studentUser?->phone ?? '—' }}</td>
                                <td class="small">
                                    {{ $student?->administrativeClass?->code ?? $student?->administrativeClass?->name ?? '—' }}
                                </td>
                                <td class="text-center small">{{ $student?->cohort_year ?? '—' }}</td>
                                <td class="text-center pe-4">
                                    <span class="badge border px-2 py-1" style="background: #edf6f1; color: var(--sms-teal-dark);">
                                        {{ $enrollment->status->value }}
                                    </span>
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