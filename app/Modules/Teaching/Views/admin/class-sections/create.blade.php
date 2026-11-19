@extends('layouts.app')

@section('title', 'Mở lớp học phần mới · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 900px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.class-sections.index') }}" class="text-decoration-none">Lớp học phần</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mở lớp mới</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        {{-- Header Form --}}
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">PHÂN HỆ ĐÀO TẠO & GIẢNG DẠY</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Mở Lớp Học Phần Mới</h2>
                <p class="text-muted small mb-0">Tổ chức lớp học phần trong học kỳ, quy định sĩ số sinh viên và phân công giảng viên giảng dạy.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.class-sections.store') }}" novalidate>
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="semester_id" class="hta-form-label">
                        Học kỳ đào tạo <span class="text-danger">*</span>
                    </label>
                    <select
                        id="semester_id"
                        name="semester_id"
                        class="form-select hta-input @error('semester_id') is-invalid @enderror"
                        required
                    >
                        <option value="">-- Chọn học kỳ --</option>
                        @foreach ($semesters as $sem)
                            <option
                                value="{{ $sem->id }}"
                                {{ old('semester_id', $selectedSemesterId) == $sem->id ? 'selected' : '' }}
                            >
                                {{ $sem->name }} ({{ $sem->code }} · {{ $sem->academic_year }}) {{ $sem->is_current ? '★ Hiện tại' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="course_id" class="hta-form-label">
                        Học phần / Môn học <span class="text-danger">*</span>
                    </label>
                    <select
                        id="course_id"
                        name="course_id"
                        class="form-select hta-input @error('course_id') is-invalid @enderror"
                        required
                    >
                        <option value="">-- Chọn học phần --</option>
                        @foreach ($courses as $c)
                            <option
                                value="{{ $c->id }}"
                                {{ old('course_id', $selectedCourseId) == $c->id ? 'selected' : '' }}
                            >
                                {{ $c->code }} - {{ $c->name }} ({{ $c->credits }} TC)
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="code" class="hta-form-label">
                        Mã lớp học phần <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control hta-input @error('code') is-invalid @enderror"
                        placeholder="VD: CS101.01, IT202.N02"
                        value="{{ old('code') }}"
                        required
                        maxlength="30"
                        style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                    >
                    <small class="text-muted">Mã định danh riêng cho lớp trong môn học và kỳ học đó.</small>
                    @error('code')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="capacity" class="hta-form-label">
                        Sĩ số tối đa <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="capacity"
                        name="capacity"
                        class="form-control hta-input @error('capacity') is-invalid @enderror"
                        value="{{ old('capacity', 50) }}"
                        min="5"
                        max="250"
                        required
                    >
                    <small class="text-muted">Giới hạn số lượng sinh viên tối đa có thể đăng ký vào lớp (5 - 250).</small>
                    @error('capacity')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="lecturer_id" class="hta-form-label">
                        Giảng viên phụ trách
                    </label>
                    <select
                        id="lecturer_id"
                        name="lecturer_id"
                        class="form-select hta-input @error('lecturer_id') is-invalid @enderror"
                    >
                        <option value="">-- Chưa phân công giảng viên --</option>
                        @foreach ($lecturers as $lec)
                            <option
                                value="{{ $lec->id }}"
                                {{ old('lecturer_id') == $lec->id ? 'selected' : '' }}
                            >
                                {{ $lec->user->name ?? 'GV #'.$lec->id }} ({{ $lec->lecturer_code }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Có thể gán ngay hoặc phân công sau trong trang chi tiết lớp.</small>
                    @error('lecturer_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="status" class="hta-form-label">
                        Trạng thái ban đầu <span class="text-danger">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="form-select hta-input @error('status') is-invalid @enderror"
                        required
                    >
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Bản nháp (Draft - Chưa công bố)</option>
                        <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Đang mở (Open - Cho phép SV đăng ký)</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>Đang học (In Progress - Đang giảng dạy)</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.class-sections.index') }}" class="hta-btn-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="hta-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
                    </svg>
                    Tạo lớp học phần
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
