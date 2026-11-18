@extends('layouts.app')

@section('title', 'Chỉnh sửa khoa ' . $department->code . ' · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 860px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}" class="text-decoration-none">Quản lý khoa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.departments.show', $department) }}" class="text-decoration-none">{{ $department->code }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                {{ mb_substr($department->code, 0, 2) }}
            </div>
            <div>
                <span class="hta-eyebrow">CẬP NHẬT THÔNG TIN</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Chỉnh sửa Khoa: {{ $department->name }}</h2>
                <p class="text-muted small mb-0">Cập nhật mã định danh, tên gọi hoặc mô tả của khoa chuyên môn.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.departments.update', $department) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-5">
                    <label for="code" class="hta-form-label">
                        Mã khoa <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control hta-input @error('code') is-invalid @enderror"
                        placeholder="VD: CNTT"
                        value="{{ old('code', $department->code) }}"
                        required
                        maxlength="20"
                        style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                    >
                    <div class="form-text small">Mã viết tắt duy nhất toàn trường.</div>
                    @error('code')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-7">
                    <label for="name" class="hta-form-label">
                        Tên đầy đủ của khoa <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control hta-input @error('name') is-invalid @enderror"
                        placeholder="VD: Khoa Công nghệ Thông tin"
                        value="{{ old('name', $department->name) }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="hta-form-label">
                        Mô tả / Chức năng nhiệm vụ
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Giới thiệu sơ lược về khoa..."
                        style="border-radius: 14px; border: 1.8px solid #cbd5e1; padding: 14px;"
                    >{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.departments.show', $department) }}" class="hta-btn-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="hta-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
