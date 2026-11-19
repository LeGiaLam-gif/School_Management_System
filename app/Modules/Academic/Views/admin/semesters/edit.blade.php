@extends('layouts.app')

@section('title', 'Chỉnh sửa học kỳ ' . $semester->code . ' · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 900px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.semesters.index') }}" class="text-decoration-none">Học kỳ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.semesters.show', $semester) }}" class="text-decoration-none">{{ $semester->code }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        {{-- Header Form --}}
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">CẬP NHẬT NIÊN LỊCH & ĐÀO TẠO</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Chỉnh sửa học kỳ: {{ $semester->name }}</h2>
                <p class="text-muted small mb-0">Cập nhật khung thời gian đào tạo, đợt mở đăng ký học phần và trạng thái kích hoạt.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.semesters.update', $semester) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="code" class="hta-form-label">
                        Mã học kỳ <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control hta-input @error('code') is-invalid @enderror"
                        placeholder="VD: 2026-HK1, 2026-HK2"
                        value="{{ old('code', $semester->code) }}"
                        required
                        maxlength="20"
                        style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                    >
                    @error('code')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="name" class="hta-form-label">
                        Tên học kỳ <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control hta-input @error('name') is-invalid @enderror"
                        placeholder="VD: Học kỳ 1, Học kỳ hè"
                        value="{{ old('name', $semester->name) }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="academic_year" class="hta-form-label">
                        Năm học <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="academic_year"
                        name="academic_year"
                        class="form-control hta-input @error('academic_year') is-invalid @enderror"
                        placeholder="VD: 2026-2027"
                        value="{{ old('academic_year', $semester->academic_year) }}"
                        required
                    >
                    @error('academic_year')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="start_date" class="hta-form-label">
                        Ngày bắt đầu học kỳ <span class="text-danger">*</span>
                    </label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        class="form-control hta-input @error('start_date') is-invalid @enderror"
                        value="{{ old('start_date', $semester->start_date ? $semester->start_date->format('Y-m-d') : '') }}"
                        required
                    >
                    @error('start_date')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="end_date" class="hta-form-label">
                        Ngày kết thúc học kỳ <span class="text-danger">*</span>
                    </label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        class="form-control hta-input @error('end_date') is-invalid @enderror"
                        value="{{ old('end_date', $semester->end_date ? $semester->end_date->format('Y-m-d') : '') }}"
                        required
                    >
                    @error('end_date')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Khung đăng ký tín chỉ --}}
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="fw-bold mb-2 text-dark">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2" class="me-1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Khung thời gian mở cổng đăng ký tín chỉ (ĐK học phần)
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="registration_open_at" class="hta-form-label">Thời điểm mở đăng ký</label>
                                <input
                                    type="datetime-local"
                                    id="registration_open_at"
                                    name="registration_open_at"
                                    class="form-control hta-input @error('registration_open_at') is-invalid @enderror"
                                    value="{{ old('registration_open_at', $semester->registration_open_at ? $semester->registration_open_at->format('Y-m-d\TH:i') : '') }}"
                                >
                                @error('registration_open_at')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="registration_close_at" class="hta-form-label">Thời điểm đóng đăng ký</label>
                                <input
                                    type="datetime-local"
                                    id="registration_close_at"
                                    name="registration_close_at"
                                    class="form-control hta-input @error('registration_close_at') is-invalid @enderror"
                                    value="{{ old('registration_close_at', $semester->registration_close_at ? $semester->registration_close_at->format('Y-m-d\TH:i') : '') }}"
                                >
                                @error('registration_close_at')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="drop_deadline" class="hta-form-label">Hạn chót rút học phần</label>
                                <input
                                    type="date"
                                    id="drop_deadline"
                                    name="drop_deadline"
                                    class="form-control hta-input @error('drop_deadline') is-invalid @enderror"
                                    value="{{ old('drop_deadline', $semester->drop_deadline ? $semester->drop_deadline->format('Y-m-d') : '') }}"
                                >
                                @error('drop_deadline')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="min_credits" class="hta-form-label">
                        Số tín chỉ tối thiểu / sinh viên <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="min_credits"
                        name="min_credits"
                        class="form-control hta-input @error('min_credits') is-invalid @enderror"
                        value="{{ old('min_credits', $semester->min_credits) }}"
                        min="1"
                        max="30"
                        required
                    >
                    @error('min_credits')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="max_credits" class="hta-form-label">
                        Số tín chỉ tối đa / sinh viên <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="max_credits"
                        name="max_credits"
                        class="form-control hta-input @error('max_credits') is-invalid @enderror"
                        value="{{ old('max_credits', $semester->max_credits) }}"
                        min="1"
                        max="50"
                        required
                    >
                    @error('max_credits')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_current" name="is_current" value="1" {{ old('is_current', $semester->is_current) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_current" style="color: #1b2559;">
                            Đặt học kỳ này làm Học kỳ hiện tại của hệ thống (Active Semester)
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('admin.semesters.show', $semester) }}" class="hta-btn-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="hta-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Lưu cập nhật
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
