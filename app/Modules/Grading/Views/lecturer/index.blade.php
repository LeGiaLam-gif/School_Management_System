@extends('layouts.app')

@section('title', 'Nhập điểm & Công bố · Cổng Giảng Viên')

@section('content')
<div class="sms-workspace-page">
    <div class="sms-workspace-page-heading">
        <div>
            <span class="sms-eyebrow">HEXATECH ACADEMY · GIẢNG VIÊN</span>
            <h1>Nhập điểm & Công bố kết quả</h1>
            <p>
                Cấu hình cột điểm thành phần (tổng trọng số 100%), nhập điểm và công bố kết quả chính thức cho sinh viên.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if ($selectedClass)
                <a
                    href="{{ route('lecturer.attendance', ['class_section_id' => $selectedClass->id]) }}"
                    class="sms-workspace-back-link"
                >
                    Điểm danh lớp này
                </a>
            @endif
            <a
                href="{{ route('lecturer.classes', ['semester_id' => $filters['semester_id']]) }}"
                class="sms-workspace-back-link"
            >
                Lớp phụ trách
            </a>
        </div>
    </div>

    <div class="sms-workspace-card" style="min-height: auto;">
        <form method="GET" action="{{ route('lecturer.grades') }}" class="row g-3 align-items-end">
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
                <label for="class_section_id" class="form-label small fw-semibold text-muted">Lớp học phần</label>
                <select
                    name="class_section_id"
                    id="class_section_id"
                    class="form-select"
                    onchange="this.form.submit()"
                >
                    @if ($classes->isEmpty())
                        <option value="">-- Không có lớp học phần trong học kỳ này --</option>
                    @else
                        @foreach ($classes as $classItem)
                            <option value="{{ $classItem->id }}" @selected($filters['class_section_id'] === $classItem->id)>
                                {{ $classItem->code }} — {{ $classItem->course?->name }} ({{ $classItem->status->label() }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </form>
    </div>

    @if ($selectedClass === null)
        <div class="sms-workspace-card" style="min-height: auto;">
            <div class="sms-workspace-notice mx-auto my-2">
                <span class="sms-workspace-notice-icon">i</span>
                <div>
                    <h2>Chưa có lớp học phần</h2>
                    <p>Bạn chưa được phân công giảng dạy lớp học phần nào trong học kỳ này.</p>
                </div>
            </div>
        </div>
    @else
        @php
            $isPublished = $selectedClass->status === \App\Shared\Enums\ClassSectionStatus::GradesPublished;
        @endphp

        <div class="sms-workspace-card" style="min-height: auto;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span class="fw-bold font-monospace" style="color: var(--sms-teal-dark); font-size: 16px;">
                            {{ $selectedClass->code }}
                        </span>
                        <span class="text-muted">·</span>
                        <span class="fw-bold text-dark" style="font-size: 16px;">
                            {{ $selectedClass->course?->name }}
                        </span>
                        <span class="badge border px-2 py-1" style="{{ $isPublished ? 'background: #e8f5ee; color: #166534;' : 'background: #fef9c3; color: #854d0e;' }}">
                            {{ $selectedClass->status->label() }}
                        </span>
                    </div>
                    <div class="small text-muted">
                        Số tín chỉ: <strong class="text-dark">{{ $selectedClass->course?->credits ?? 0 }}</strong>
                        · Đủ điểm tổng kết: <strong class="text-dark">{{ $stats['completed'] }}/{{ $stats['total'] }}</strong> sinh viên
                        · Đạt môn: <strong class="text-dark">{{ $stats['passed'] }}/{{ $stats['completed'] }}</strong>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @if ($canManage)
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="collapse"
                            data-bs-target="#gradeComponentsPanel"
                        >
                            Cấu hình cột điểm ({{ $components->count() }})
                        </button>
                    @endif

                    @if ($canPublish)
                        <form
                            method="POST"
                            action="{{ route('lecturer.grades.publish', $selectedClass) }}"
                            onsubmit="return confirm('Sau khi công bố, bảng điểm sẽ bị khóa và tính vào GPA của sinh viên. Bạn có chắc chắn muốn công bố?');"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-sm btn-success fw-semibold"
                                @disabled(! $weightsValid || $stats['total'] === 0 || $stats['completed'] < $stats['total'])
                            >
                                Công bố & Khóa điểm
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if ($isPublished)
                <div class="alert alert-success small mb-0 mt-3 py-2">
                    Bảng điểm lớp học phần này đã được công bố và khóa. Nếu cần điều chỉnh sau công bố, vui lòng liên hệ Quản trị viên để mở khóa.
                </div>
            @endif
        </div>

        @if ($canManage)
            <div class="collapse @if($components->isEmpty() || $errors->has('components') || $errors->has('components.*')) show @endif" id="gradeComponentsPanel">
                <div class="sms-workspace-card" style="min-height: auto;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h6 fw-bold mb-1" style="color: var(--sms-teal-dark);">Cấu trúc cột điểm thành phần</h2>
                            <p class="small text-muted mb-0">Tổng trọng số các cột điểm phải bằng đúng 100%.</p>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success" id="addComponentRowBtn">
                            + Thêm cột điểm
                        </button>
                    </div>

                    <form method="POST" action="{{ route('lecturer.grades.components', $selectedClass) }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-3" id="componentsTable">
                                <thead class="small text-muted">
                                    <tr>
                                        <th>Tên cột điểm</th>
                                        <th style="width: 150px;">Trọng số (%)</th>
                                        <th style="width: 150px;">Điểm tối đa</th>
                                        <th style="width: 80px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $componentRows = old('components', $components->isNotEmpty()
                                            ? $components->map(fn ($c) => [
                                                'id' => $c->id,
                                                'name' => $c->name,
                                                'weight' => $c->weight,
                                                'max_score' => $c->max_score,
                                            ])->all()
                                            : [
                                                ['id' => null, 'name' => 'Giữa kỳ', 'weight' => 40, 'max_score' => 10],
                                                ['id' => null, 'name' => 'Cuối kỳ', 'weight' => 60, 'max_score' => 10],
                                            ]);
                                    @endphp
                                    @foreach ($componentRows as $idx => $comp)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="components[{{ $idx }}][id]" value="{{ $comp['id'] ?? '' }}">
                                                <input
                                                    type="text"
                                                    name="components[{{ $idx }}][name]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $comp['name'] }}"
                                                    required
                                                >
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0.01"
                                                    max="100"
                                                    name="components[{{ $idx }}][weight]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $comp['weight'] }}"
                                                    required
                                                >
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="1"
                                                    max="100"
                                                    name="components[{{ $idx }}][max_score]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $comp['max_score'] }}"
                                                    required
                                                >
                                            </td>
                                            <td class="text-end">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="if(document.querySelectorAll('#componentsTable tbody tr').length > 1) this.closest('tr').remove();"
                                                >
                                                    Xóa
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm text-white fw-semibold px-3" style="background-color: var(--sms-teal);">
                                Lưu cấu trúc điểm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if (! $weightsValid)
            <div class="sms-workspace-card" style="min-height: auto;">
                <div class="sms-workspace-notice mx-auto my-2">
                    <span class="sms-workspace-notice-icon">!</span>
                    <div>
                        <h2>Chưa cấu hình cột điểm hợp lệ</h2>
                        <p>Vui lòng mở mục "Cấu hình cột điểm" phía trên và lưu cấu trúc có tổng trọng số bằng 100% trước khi nhập điểm.</p>
                    </div>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('lecturer.grades.store', $selectedClass) }}">
                @csrf
                <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
                    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f7faf7;">
                        <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                            Bảng điểm sinh viên ({{ count($rows) }} sinh viên)
                        </span>
                        <span class="small text-muted">
                            Điểm tổng kết hệ 10, điểm chữ và GPA hệ 4 tự động tính sau khi lưu.
                        </span>
                    </div>

                    @if (count($rows) === 0)
                        <div class="p-4">
                            <div class="sms-workspace-notice mx-auto my-2">
                                <span class="sms-workspace-notice-icon">i</span>
                                <div>
                                    <h2>Lớp chưa có sinh viên</h2>
                                    <p>Chưa có sinh viên đăng ký học phần trong lớp này.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="small text-muted" style="background: #f8faf8;">
                                    <tr>
                                        <th class="ps-4" style="width: 55px;">STT</th>
                                        <th style="width: 120px;">Mã SV</th>
                                        <th>Họ và tên</th>
                                        @foreach ($components as $component)
                                            <th class="text-center" style="width: 140px;">
                                                <div>{{ $component->name }}</div>
                                                <div class="fw-normal text-muted">({{ number_format($component->weight, 0) }}% · tối đa {{ number_format($component->max_score, 0) }})</div>
                                            </th>
                                        @endforeach
                                        <th class="text-center" style="width: 110px;">Tổng kết</th>
                                        <th class="text-center" style="width: 90px;">Điểm chữ</th>
                                        <th class="text-center" style="width: 90px;">Hệ 4</th>
                                        <th class="text-end pe-4" style="width: 120px;">Kết quả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $index => $row)
                                        @php
                                            $enrollment = $row['enrollment'];
                                            $student = $enrollment->student;
                                            $finalResult = $row['finalResult'];
                                        @endphp
                                        <tr>
                                            <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                            <td class="fw-bold font-monospace" style="color: var(--sms-teal-dark);">
                                                {{ $student?->student_code ?? '—' }}
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $student?->user?->name ?? '—' }}</div>
                                                <div class="text-muted small">{{ $student?->administrativeClass?->code ?? '' }}</div>
                                            </td>
                                            @foreach ($components as $component)
                                                @php
                                                    $val = old("grades.{$enrollment->id}.{$component->id}", $row['scores'][$component->id] ?? '');
                                                @endphp
                                                <td class="text-center">
                                                    @if ($canManage)
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            min="0"
                                                            max="{{ $component->max_score }}"
                                                            name="grades[{{ $enrollment->id }}][{{ $component->id }}]"
                                                            class="form-control form-control-sm text-center mx-auto"
                                                            style="max-width: 95px;"
                                                            value="{{ $val }}"
                                                            placeholder="—"
                                                        >
                                                    @else
                                                        <span class="fw-semibold">{{ $val !== '' && $val !== null ? number_format((float) $val, 2) : '—' }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center fw-bold">
                                                {{ $finalResult !== null ? number_format($finalResult->total_score, 2) : '—' }}
                                            </td>
                                            <td class="text-center fw-bold" style="color: var(--sms-teal-dark);">
                                                {{ $finalResult?->letter_grade ?? '—' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $finalResult !== null ? number_format($finalResult->gpa_point, 2) : '—' }}
                                            </td>
                                            <td class="text-end pe-4">
                                                @if ($finalResult === null)
                                                    <span class="badge bg-light text-muted border">Chưa đủ điểm</span>
                                                @elseif ($finalResult->is_passed)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Đạt</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Không đạt</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($canManage)
                            <div class="px-4 py-3 border-top d-flex justify-content-end" style="background: #f8faf8;">
                                <button
                                    type="submit"
                                    class="btn fw-semibold text-white px-4"
                                    style="background-color: var(--sms-teal); border-color: var(--sms-teal);"
                                >
                                    Lưu bảng điểm
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            </form>
        @endif
    @endif
</div>

@if ($canManage)
<script>
    document.getElementById('addComponentRowBtn')?.addEventListener('click', function () {
        const tbody = document.querySelector('#componentsTable tbody');
        if (!tbody) return;
        const idx = Date.now();
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="hidden" name="components[${idx}][id]" value="">
                <input type="text" name="components[${idx}][name]" class="form-control form-control-sm" placeholder="VD: Chuyên cần" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" max="100" name="components[${idx}][weight]" class="form-control form-control-sm" value="10" required>
            </td>
            <td>
                <input type="number" step="0.01" min="1" max="100" name="components[${idx}][max_score]" class="form-control form-control-sm" value="10" required>
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(document.querySelectorAll('#componentsTable tbody tr').length > 1) this.closest('tr').remove();">Xóa</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
</script>
@endif
@endsection
