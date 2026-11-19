@extends('layouts.app')

@section('title', 'Thêm học phần mới · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 900px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Học phần</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        {{-- Header Form --}}
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">DANH MỤC MÔN HỌC & ĐÀO TẠO</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Thêm Học Phần Mới</h2>
                <p class="text-muted small mb-0">Khai báo môn học mới vào hệ thống cùng thông tin khối lượng tín chỉ và môn tiên quyết.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.courses.store') }}" novalidate>
            @csrf

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
                        placeholder="VD: INT101, MAT102"
                        value="{{ old('code') }}"
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
                        placeholder="VD: Lập trình Cơ bản C/C++, Giải tích 1"
                        value="{{ old('name') }}"
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
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
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
                        value="{{ old('credits', 3) }}"
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
                        value="{{ old('theory_periods', 30) }}"
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
                        value="{{ old('practice_periods', 15) }}"
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
                        Học phần tiên quyết (nếu có)
                    </label>
                    <select id="prerequisite_ids" name="prerequisite_ids[]" class="form-select hta-select @error('prerequisite_ids') is-invalid @enderror" multiple size="4">
                        @foreach ($availableCourses as $ac)
                            <option value="{{ $ac->id }}" {{ in_array($ac->id, (array) old('prerequisite_ids', [])) ? 'selected' : '' }}>
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
                        placeholder="Nội dung kiến thức, kỹ năng đầu ra của học phần..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active" style="color: #1b2559;">Kích hoạt mở học phần cho đăng ký giảng dạy</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.courses.index') }}" class="hta-btn-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="hta-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Lưu học phần
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
