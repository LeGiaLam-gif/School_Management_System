@extends('layouts.app')

@section('title', 'Thêm giảng viên mới · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 900px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.lecturers.index') }}" class="text-decoration-none">Giảng viên</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v2"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">HỒ SƠ CÁN BỘ ĐÀO TẠO</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Thêm Giảng Viên Mới</h2>
                <p class="text-muted small mb-0">Hệ thống sẽ đồng thời khởi tạo tài khoản đăng nhập và hồ sơ học thuật cho giảng viên.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.lecturers.store') }}" novalidate>
            @csrf

            <div class="row g-4">
                {{-- Phần 1: Tài khoản đăng nhập --}}
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span class="fw-bold" style="color: #1b2559;">Thông tin tài khoản đăng nhập</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="lecturer_code" class="hta-form-label">
                                    Mã giảng viên / Tên đăng nhập <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="lecturer_code"
                                    name="lecturer_code"
                                    class="form-control hta-input @error('lecturer_code') is-invalid @enderror"
                                    placeholder="VD: GV001, LECT023"
                                    value="{{ old('lecturer_code') }}"
                                    required
                                    maxlength="30"
                                    style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                                >
                                <div class="form-text small">Dùng làm mã định danh và tên đăng nhập chính.</div>
                                @error('lecturer_code')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="hta-form-label">
                                    Email liên hệ / Đăng nhập <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control hta-input @error('email') is-invalid @enderror"
                                    placeholder="VD: nguyen.van.a@hta.edu.vn"
                                    value="{{ old('email') }}"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="hta-form-label">
                                    Mật khẩu khởi tạo <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control hta-input @error('password') is-invalid @enderror"
                                    placeholder="Tối thiểu 8 ký tự"
                                    required
                                >
                                <div class="form-text small">Giảng viên sẽ được yêu cầu đổi mật khẩu ở lần đăng nhập đầu tiên.</div>
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
                        placeholder="VD: PGS. TS. Nguyễn Văn A"
                        value="{{ old('name') }}"
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
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
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
                        placeholder="VD: Tiến sĩ, Thạc sĩ, PGS.TS..."
                        value="{{ old('academic_title') }}"
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
                        placeholder="VD: 0912345678"
                        value="{{ old('phone') }}"
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
                        placeholder="VD: P.402 Nhà A, Khu Giảng đường"
                        value="{{ old('office') }}"
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
                        placeholder="VD: Cầu Giấy, Hà Nội"
                        value="{{ old('address') }}"
                    >
                    @error('address')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.lecturers.index') }}" class="hta-btn-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="hta-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Lưu hồ sơ giảng viên
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
