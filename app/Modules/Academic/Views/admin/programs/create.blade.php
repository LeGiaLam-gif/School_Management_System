@extends('layouts.app')

@section('title', 'Thêm ngành đào tạo mới · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 860px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.programs.index') }}" class="text-decoration-none">Chương trình đào tạo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="8" y="3" width="8" height="5" rx="1"/><rect x="2" y="16" width="8" height="5" rx="1"/><rect x="14" y="16" width="8" height="5" rx="1"/><path d="M12 8v4M6 16v-4h12v4"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">KHỞI TẠO CẤU TRÚC ĐÀO TẠO</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Thêm Ngành Đào Tạo Mới</h2>
                <p class="text-muted small mb-0">Thiết lập ngành học chuyên ngành, quy định tổng tín chỉ yêu cầu tốt nghiệp.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.programs.store') }}" novalidate>
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="department_id" class="hta-form-label">
                        Khoa quản lý chuyên môn <span class="text-danger">*</span>
                    </label>
                    <select id="department_id" name="department_id" class="form-select hta-select @error('department_id') is-invalid @enderror" required>
                        <option value="">— Chọn khoa quản lý —</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $selectedDeptId) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="code" class="hta-form-label">
                        Mã ngành <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control hta-input @error('code') is-invalid @enderror"
                        placeholder="VD: KTPM, KHMT, HTTT"
                        value="{{ old('code') }}"
                        required
                        maxlength="20"
                        style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                    >
                    <div class="form-text small">Mã ngành duy nhất toàn trường, tối đa 20 ký tự.</div>
                    @error('code')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="name" class="hta-form-label">
                        Tên ngành đào tạo <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control hta-input @error('name') is-invalid @enderror"
                        placeholder="VD: Kỹ thuật Phần mềm"
                        value="{{ old('name') }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="total_credits_required" class="hta-form-label">
                        Tổng tín chỉ yêu cầu tốt nghiệp <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="total_credits_required"
                            name="total_credits_required"
                            class="form-control hta-input @error('total_credits_required') is-invalid @enderror"
                            placeholder="120"
                            value="{{ old('total_credits_required', 120) }}"
                            min="50"
                            max="250"
                            required
                        >
                        <span class="input-group-text bg-light fw-bold text-muted" style="border: 1.8px solid #cbd5e1; border-left: none; border-radius: 0 12px 12px 0;">
                            Tín chỉ
                        </span>
                    </div>
                    @error('total_credits_required')
                        <div class="invalid-feedback d-block fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="duration_years" class="hta-form-label">
                        Thời gian đào tạo chuẩn <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="number"
                            step="0.5"
                            id="duration_years"
                            name="duration_years"
                            class="form-control hta-input @error('duration_years') is-invalid @enderror"
                            placeholder="4"
                            value="{{ old('duration_years', 4) }}"
                            min="1"
                            max="8"
                            required
                        >
                        <span class="input-group-text bg-light fw-bold text-muted" style="border: 1.8px solid #cbd5e1; border-left: none; border-radius: 0 12px 12px 0;">
                            Năm
                        </span>
                    </div>
                    @error('duration_years')
                        <div class="invalid-feedback d-block fw-semibold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.programs.index') }}" class="hta-btn-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="hta-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Lưu ngành đào tạo
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
