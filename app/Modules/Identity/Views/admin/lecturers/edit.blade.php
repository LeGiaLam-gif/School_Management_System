@extends('layouts.app')

@section('title', 'Chỉnh sửa giảng viên ' . $lecturer->lecturer_code . ' · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 900px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.lecturers.index') }}" class="text-decoration-none">Giảng viên</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.lecturers.show', $lecturer) }}" class="text-decoration-none">{{ $lecturer->lecturer_code }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">CẬP NHẬT HỒ SƠ GIẢNG VIÊN</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Chỉnh sửa {{ $lecturer->user->name ?? $lecturer->lecturer_code }}</h2>
                <p class="text-muted small mb-0">Mã cán bộ: <strong>{{ $lecturer->lecturer_code }}</strong> · Khoa: <strong>{{ $lecturer->department->name }}</strong></p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.lecturers.update', $lecturer) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-4">
                {{-- Phần 1: Tài khoản đăng nhập & Trạng thái --}}
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span class="fw-bold" style="color: #1b2559;">Tài khoản & Trạng thái hệ thống</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="lecturer_code" class="hta-form-label">
                                    Mã giảng viên <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="lecturer_code"
                                    name="lecturer_code"
                                    class="form-control hta-input @error('lecturer_code') is-invalid @enderror"
                                    value="{{ old('lecturer_code', $lecturer->lecturer_code) }}"
                                    required
                                    maxlength="30"
                                    style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                                >
                                @error('lecturer_code')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label for="email" class="hta-form-label">
                                    Email liên hệ / Đăng nhập <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control hta-input @error('email') is-invalid @enderror"
                                    value="{{ old('email', $lecturer->user->email) }}"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="status" class="hta-form-label">
                                    Trạng thái tài khoản
                                </label>
                                <select id="status" name="status" class="form-select hta-select @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', $lecturer->user->status) === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="locked" {{ old('status', $lecturer->user->status) === 'locked' ? 'selected' : '' }}>Khóa tài khoản</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="password" class="hta-form-label">
                                    Đặt lại mật khẩu mới
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control hta-input @error('password') is-invalid @enderror"
                                    placeholder="Để trống nếu không muốn đổi mật khẩu"
                                >
                                <div class="form-text small">Chỉ nhập nếu cần reset mật khẩu cho giảng viên. Tối thiểu 8 ký tự.</div>
                                @error('password')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Phần 2: Thông tin cá nhân & Học thuật --}}
                <div class="col-md-6">
                    <label for="name" class="hta-form-label">
                        Họ và tên giảng viên <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control hta-input @error('name') is-invalid @enderror"
                        value="{{ old('name', $lecturer->user->name) }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="department_id" class="hta-form-label">
                        Khoa chuyên môn <span class="text-danger">*</span>
                    </label>
                    <select id="department_id" name="department_id" class="form-select hta-select @error('department_id') is-invalid @enderror" required>
                        <option value="">— Chọn khoa công tác —</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $lecturer->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="academic_title" class="hta-form-label">
                        Học hàm / Học vị
                    </label>
                    <input
                        type="text"
                        id="academic_title"
                        name="academic_title"
                        list="academic_title_list"
                        class="form-control hta-input @error('academic_title') is-invalid @enderror"
                        value="{{ old('academic_title', $lecturer->academic_title) }}"
                    >
                    <datalist id="academic_title_list">
                        <option value="Giáo sư - Tiến sĩ">
                        <option value="Phó Giáo sư - Tiến sĩ">
                        <option value="Tiến sĩ">
                        <option value="Thạc sĩ">
                        <option value="Kỹ sư">
                        <option value="Cử nhân">
                        <option value="Giảng viên chính">
                    </datalist>
                    @error('academic_title')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="phone" class="hta-form-label">
                        Số điện thoại liên hệ
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-control hta-input @error('phone') is-invalid @enderror"
                        value="{{ old('phone', $lecturer->phone) }}"
                    >
                    @error('phone')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="office" class="hta-form-label">
                        Văn phòng làm việc
                    </label>
                    <input
                        type="text"
                        id="office"
                        name="office"
                        class="form-control hta-input @error('office') is-invalid @enderror"
                        value="{{ old('office', $lecturer->office) }}"
                    >
                    @error('office')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="address" class="hta-form-label">
                        Địa chỉ thường trú / Liên lạc
                    </label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        class="form-control hta-input @error('address') is-invalid @enderror"
                        value="{{ old('address', $lecturer->address) }}"
                    >
                    @error('address')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.lecturers.show', $lecturer) }}" class="hta-btn-outline">
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
