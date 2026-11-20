@extends('layouts.app')

@section('title', 'Chỉnh sửa học phần ' . $course->code . ' · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 900px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Học phần</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}" class="text-decoration-none">{{ $course->code }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        {{-- Header Form --}}
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">CẬP NHẬT HỌC PHẦN ĐÀO TẠO</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Chỉnh sửa: {{ $course->name }}</h2>
                <p class="text-muted small mb-0">Mã học phần: <strong class="font-monospace text-primary">{{ $course->code }}</strong> · Khoa: <strong>{{ $course->department->name }}</strong></p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.courses.update', $course) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="code" class="hta-form-label">
                        Mã học phần <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control hta-input @error('code') is-invalid @enderror"
                        value="{{ old('code', $course->code) }}"
                        required
                        maxlength="20"
                        style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                    >
                    @error('code')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-8">
                    <label for="name" class="hta-form-label">
                        Tên học phần (môn học) <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control hta-input @error('name') is-invalid @enderror"
                        value="{{ old('name', $course->name) }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="department_id" class="hta-form-label">
                        Khoa chuyên môn phụ trách <span class="text-danger">*</span>
                    </label>
                    <select id="department_id" name="department_id" class="form-select hta-select @error('department_id') is-invalid @enderror" required>
                        <option value="">— Chọn khoa quản lý —</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $course->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="credits" class="hta-form-label">
                        Số tín chỉ <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="credits"
                        name="credits"
                        class="form-control hta-input @error('credits') is-invalid @enderror"
                        value="{{ old('credits', $course->credits) }}"
                        min="1"
                        max="15"
                        required
                    >
                    @error('credits')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="theory_periods" class="hta-form-label">
                        Tiết lý thuyết <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="theory_periods"
                        name="theory_periods"
                        class="form-control hta-input @error('theory_periods') is-invalid @enderror"
                        value="{{ old('theory_periods', $course->theory_periods) }}"
                        min="0"
                        max="150"
                        required
                    >
                    @error('theory_periods')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="practice_periods" class="hta-form-label">
                        Tiết thực hành <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="practice_periods"
                        name="practice_periods"
                        class="form-control hta-input @error('practice_periods') is-invalid @enderror"
                        value="{{ old('practice_periods', $course->practice_periods) }}"
                        min="0"
                        max="150"
                        required
                    >
                    @error('practice_periods')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="prerequisite_ids" class="hta-form-label">
                        Học phần tiên quyết
                    </label>
                    @php
                        $selectedPrereqIds = old('prerequisite_ids', $course->prerequisites->pluck('id')->toArray());
                    @endphp
                    <select id="prerequisite_ids" name="prerequisite_ids[]" class="form-select hta-select @error('prerequisite_ids') is-invalid @enderror" multiple size="4">
                        @foreach ($availableCourses as $ac)
                            <option value="{{ $ac->id }}" {{ in_array($ac->id, $selectedPrereqIds) ? 'selected' : '' }}>
                                [{{ $ac->code }}] {{ $ac->name }} ({{ $ac->credits }} TC)
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text small">Giữ phím <strong>Ctrl</strong> (Windows) hoặc <strong>Cmd</strong> (Mac) để chọn nhiều môn tiên quyết.</div>
                    @error('prerequisite_ids')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="hta-form-label">
                        Mô tả tóm tắt nội dung học phần
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="form-control hta-input @error('description') is-invalid @enderror"
                    >{{ old('description', $course->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active" style="color: #1b2559;">Kích hoạt mở học phần cho đăng ký giảng dạy</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('admin.courses.show', $course) }}" class="hta-btn-outline">
                    Quay lại chi tiết
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.courses.index') }}" class="hta-btn-outline">
                        Danh sách
                    </a>
                    <button type="submit" class="hta-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Lưu thay đổi
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
