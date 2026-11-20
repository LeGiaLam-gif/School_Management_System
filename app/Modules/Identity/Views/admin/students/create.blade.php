@extends('layouts.app')

@section('title', 'Thêm sinh viên mới · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 960px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}" class="text-decoration-none">Sinh viên</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        {{-- Header Form --}}
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">HỒ SƠ HỌC VỤ & TÀI KHOẢN</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Thêm Sinh Viên Mới</h2>
                <p class="text-muted small mb-0">Hệ thống sẽ đồng thời tạo tài khoản đăng nhập portal và hồ sơ sinh viên chính khóa.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.students.store') }}" novalidate>
            @csrf

            <div class="row g-4">
                {{-- Phần 1: Tài khoản đăng nhập --}}
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span class="fw-bold" style="color: #1b2559;">1. Thông tin tài khoản đăng nhập</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="student_code" class="hta-form-label">
                                    Mã số sinh viên (MSSV) <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="student_code"
                                    name="student_code"
                                    class="form-control hta-input @error('student_code') is-invalid @enderror"
                                    placeholder="VD: SV2026001"
                                    value="{{ old('student_code') }}"
                                    required
                                    maxlength="20"
                                    style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                                >
                                <div class="form-text small">Dùng làm mã sinh viên và tên tài khoản đăng nhập.</div>
                                @error('student_code')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="email" class="hta-form-label">
                                    Email đăng nhập / liên hệ <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control hta-input @error('email') is-invalid @enderror"
                                    placeholder="VD: sv2026001@hta.edu.vn"
                                    value="{{ old('email') }}"
                                    required
                                >
                                <div class="form-text small">Email trường hoặc cá nhân để nhận thông báo.</div>
                                @error('email')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
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
                                <div class="form-text small">Sinh viên có thể đổi lại mật khẩu sau.</div>
                                @error('password')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Phần 2: Thông tin hồ sơ đào tạo --}}
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/>
                        </svg>
                        <span class="fw-bold" style="color: #1b2559;">2. Thông tin học vụ & Lớp sinh hoạt</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="hta-form-label">
                                Họ và tên sinh viên <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control hta-input @error('name') is-invalid @enderror"
                                placeholder="VD: Nguyễn Văn Nam"
                                value="{{ old('name') }}"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="program_id" class="hta-form-label">
                                Ngành đào tạo <span class="text-danger">*</span>
                            </label>
                            <select id="program_id" name="program_id" class="form-select hta-select @error('program_id') is-invalid @enderror" required>
                                <option value="">— Chọn ngành đào tạo —</option>
                                @foreach ($programs as $prog)
                                    <option value="{{ $prog->id }}" {{ (old('program_id', $selectedProgramId) == $prog->id) ? 'selected' : '' }}>
                                        {{ $prog->name }} ({{ $prog->code }}) @if($prog->department) - Khoa {{ $prog->department->name }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('program_id')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="class_id" class="hta-form-label">
                                Lớp hành chính sinh hoạt
                            </label>
                            <select id="class_id" name="class_id" class="form-select hta-select @error('class_id') is-invalid @enderror">
                                <option value="">— Chưa phân lớp / Phân lớp sau —</option>
                                @foreach ($classes as $c)
                                    <option value="{{ $c->id }}" {{ (old('class_id', $selectedClassId) == $c->id) ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code }}) - Khóa {{ $c->cohort_year }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Có thể gán lớp sinh hoạt sau khi nhập học.</div>
                            @error('class_id')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="cohort_year" class="hta-form-label">
                                Khóa tuyển sinh (Năm nhập học) <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                id="cohort_year"
                                name="cohort_year"
                                class="form-control hta-input @error('cohort_year') is-invalid @enderror"
                                placeholder="VD: {{ date('Y') }}"
                                value="{{ old('cohort_year', date('Y')) }}"
                                min="2000"
                                max="2100"
                                required
                            >
                            @error('cohort_year')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="hta-form-label">
                                Tình trạng học tập ban đầu
                            </label>
                            <select id="status" name="status" class="form-select hta-select @error('status') is-invalid @enderror">
                                @foreach ($statuses as $st)
                                    <option value="{{ $st->value }}" {{ old('status', 'studying') == $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Phần 3: Thông tin cá nhân & Liên lạc --}}
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span class="fw-bold" style="color: #1b2559;">3. Thông tin cá nhân & Liên hệ</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="date_of_birth" class="hta-form-label">
                                Ngày sinh
                            </label>
                            <input
                                type="date"
                                id="date_of_birth"
                                name="date_of_birth"
                                class="form-control hta-input @error('date_of_birth') is-invalid @enderror"
                                value="{{ old('date_of_birth') }}"
                            >
                            @error('date_of_birth')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="gender" class="hta-form-label">
                                Giới tính
                            </label>
                            <select id="gender" name="gender" class="form-select hta-select @error('gender') is-invalid @enderror">
                                <option value="">— Chọn giới tính —</option>
                                @foreach ($genders as $g)
                                    <option value="{{ $g->value }}" {{ old('gender') == $g->value ? 'selected' : '' }}>
                                        {{ $g->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gender')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
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
                                maxlength="20"
                            >
                            @error('phone')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="hta-form-label">
                                Địa chỉ thường trú / Hộ khẩu
                            </label>
                            <input
                                type="text"
                                id="address"
                                name="address"
                                class="form-control hta-input @error('address') is-invalid @enderror"
                                placeholder="VD: Số 123 Đường Xuân Thủy, Phường Dịch Vọng Hậu, Quận Cầu Giấy, Hà Nội"
                                value="{{ old('address') }}"
                                maxlength="255"
                            >
                            @error('address')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer buttons --}}
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.students.index') }}" class="hta-btn-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="hta-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Lưu hồ sơ sinh viên
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
