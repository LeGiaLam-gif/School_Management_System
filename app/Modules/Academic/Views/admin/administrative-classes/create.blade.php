@extends('layouts.app')

@section('title', 'Mở lớp hành chính mới · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 860px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.administrative-classes.index') }}" class="text-decoration-none">Lớp hành chính</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mở lớp mới</li>
        </ol>
    </nav>

    <div class="hta-card p-4">
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="hta-avatar-circle hta-avatar-circle-lg">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <span class="hta-eyebrow">TỔ CHỨC LỚP NIÊN CHẾ</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Mở Lớp Hành Chính Mới</h2>
                <p class="text-muted small mb-0">Thiết lập thông tin lớp sinh viên theo ngành, khóa tuyển sinh và chỉ định Cố vấn học tập.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.administrative-classes.store') }}" novalidate>
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="program_id" class="hta-form-label">
                        Ngành & Chương trình đào tạo <span class="text-danger">*</span>
                    </label>
                    <select id="program_id" name="program_id" class="form-select hta-select @error('program_id') is-invalid @enderror" required>
                        <option value="">— Chọn ngành đào tạo —</option>
                        @foreach ($programs as $prog)
                            <option value="{{ $prog->id }}" {{ old('program_id', $selectedProgramId) == $prog->id ? 'selected' : '' }}>
                                {{ $prog->name }} ({{ $prog->code }}) — Khoa {{ $prog->department->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="academic_year" class="hta-form-label">
                        Khóa tuyển sinh / Năm nhập học <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="academic_year"
                        name="academic_year"
                        class="form-control hta-input @error('academic_year') is-invalid @enderror"
                        placeholder="VD: 2024"
                        value="{{ old('academic_year', date('Y')) }}"
                        min="2000"
                        max="2100"
                        required
                    >
                    @error('academic_year')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="code" class="hta-form-label">
                        Mã lớp hành chính <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control hta-input @error('code') is-invalid @enderror"
                        placeholder="VD: 24KTPM01, 23HTTT02"
                        value="{{ old('code') }}"
                        required
                        maxlength="20"
                        style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                    >
                    <div class="form-text small">Mã lớp định danh duy nhất toàn trường.</div>
                    @error('code')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="name" class="hta-form-label">
                        Tên lớp hành chính <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control hta-input @error('name') is-invalid @enderror"
                        placeholder="VD: Kỹ thuật phần mềm 01 - K24"
                        value="{{ old('name') }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="advisor_lecturer_id" class="hta-form-label">
                        Cố vấn học tập (CVHT)
                    </label>
                    <select id="advisor_lecturer_id" name="advisor_lecturer_id" class="form-select hta-select @error('advisor_lecturer_id') is-invalid @enderror">
                        <option value="">— Chưa phân công Cố vấn học tập —</option>
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" {{ old('advisor_lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                                {{ $lecturer->user->name ?? 'N/A' }} ({{ $lecturer->lecturer_code }}) — {{ $lecturer->academic_title ?? 'Giảng viên' }} · {{ $lecturer->department->name ?? 'Khoa' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text small">Có thể chỉ định hoặc phân công bổ sung sau.</div>
                    @error('advisor_lecturer_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.administrative-classes.index') }}" class="hta-btn-outline">
                    Hủy bỏ
                </a>
                <button type="submit" class="hta-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Lưu lớp hành chính
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
