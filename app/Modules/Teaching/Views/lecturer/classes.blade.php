@extends('layouts.app')

@section('title', 'Lớp được phân công · Cổng Giảng Viên')

@section('content')
<div class="sms-workspace-page">
    <!-- Tiêu đề trang chuẩn Hexa Tech Academy -->
    <div class="sms-workspace-page-heading">
        <div>
            <span class="sms-eyebrow">HEXATECH ACADEMY · GIẢNG VIÊN</span>
            <h1>Lớp được phân công</h1>
            <p>Danh sách các lớp học phần bạn phụ trách giảng dạy theo từng học kỳ.</p>
        </div>
        <a href="{{ route('lecturer.home') }}" class="sms-workspace-back-link">
            ← Về bảng điều khiển
        </a>
    </div>

    <!-- Bộ lọc học kỳ & tìm kiếm -->
    <div class="sms-workspace-card" style="min-height: auto;">
        <form method="GET" action="{{ route('lecturer.classes') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="semester_id" class="form-label small fw-semibold text-muted">Học kỳ</label>
                <select name="semester_id" id="semester_id" class="form-select" onchange="this.form.submit()">
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected($filters['semester_id'] === $semester->id)>
                            {{ $semester->name }} ({{ $semester->academic_year }})
                            @if ($semester->is_current) — Hiện tại @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <label for="keyword" class="form-label small fw-semibold text-muted">Tìm kiếm lớp / học phần</label>
                <input
                    type="text"
                    name="keyword"
                    id="keyword"
                    class="form-control"
                    value="{{ $filters['keyword'] }}"
                    placeholder="Nhập mã lớp HP, mã môn hoặc tên môn học..."
                >
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button
                    type="submit"
                    class="btn fw-semibold text-white flex-grow-1"
                    style="background-color: var(--sms-teal); border-color: var(--sms-teal);"
                >
                    Lọc dữ liệu
                </button>
                @if ($filters['keyword'] !== '')
                    <a href="{{ route('lecturer.classes', ['semester_id' => $filters['semester_id']]) }}" class="btn btn-outline-secondary">
                        Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bảng danh sách lớp học phần -->
    <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f7faf7;">
            <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                Danh sách lớp học phần
            </span>
            <span class="badge rounded-pill px-3 py-2" style="background: #e8f4ef; color: var(--sms-teal-dark);">
                Tổng số: {{ $classes->count() }} lớp
            </span>
        </div>

        @if ($classes->isEmpty())
            <div class="p-4">
                <div class="sms-workspace-notice mx-auto my-2">
                    <span class="sms-workspace-notice-icon">i</span>
                    <div>
                        <h2>Chưa có lớp học phần nào</h2>
                        <p>Không tìm thấy lớp học phần nào được phân công cho bạn trong học kỳ này.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted" style="background: #f8faf8;">
                        <tr>
                            <th class="ps-4" style="width: 60px;">STT</th>
                            <th style="width: 150px;">Mã lớp HP</th>
                            <th style="width: 120px;">Mã môn</th>
                            <th>Tên học phần</th>
                            <th class="text-center" style="width: 90px;">Số TC</th>
                            <th class="text-center" style="width: 130px;">Sĩ số</th>
                            <th class="text-center" style="width: 140px;">Trạng thái</th>
                            <th class="text-end pe-4" style="width: 185px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classes as $index => $section)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold font-monospace" style="color: var(--sms-teal-dark);">
                                        {{ $section->code }}
                                    </span>
                                </td>
                                <td>
                                    <span class="font-monospace text-dark fw-semibold">{{ $section->course?->code }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $section->course?->name }}</div>
                                    @if ($section->course?->department)
                                        <div class="small text-muted">{{ $section->course->department->name }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        {{ $section->course?->credits ?? 0 }} TC
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-dark">{{ $section->enrolled_count }}</span>
                                    <span class="text-muted">/ {{ $section->capacity }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge border px-2 py-1" style="background: #edf6f1; color: var(--sms-teal-dark);">
                                        {{ $section->status->value }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a
                                        href="{{ route('lecturer.students', ['class_section_id' => $section->id]) }}"
                                        class="btn btn-sm fw-semibold"
                                        style="color: var(--sms-teal-dark); background: #e8f4ef; border: 1px solid #cfe2d8;"
                                    >
                                        Danh sách SV →
                                    </a>
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