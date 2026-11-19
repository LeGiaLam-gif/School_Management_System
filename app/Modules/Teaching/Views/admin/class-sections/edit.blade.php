@extends('layouts.app')

@section('title', 'Chỉnh sửa lớp ' . $classSection->code . ' · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 900px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-decoration-none">Bảng điều khiển</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.class-sections.index') }}" class="text-decoration-none">Lớp học phần</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.class-sections.show', $classSection) }}" class="text-decoration-none">{{ $classSection->code }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
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
                <span class="hta-eyebrow">CẬP NHẬT THÔNG TIN LỚP HỌC PHẦN</span>
                <h2 class="h4 fw-bold mb-1" style="color: #1b2559;">Chỉnh Sửa Lớp: {{ $classSection->code }}</h2>
                <p class="text-muted small mb-0">{{ $classSection->course->name }} ({{ $classSection->course->code }}) · {{ $classSection->semester->name }} ({{ $classSection->semester->code }})</p>
            </div>
        </div>

        {{-- Thông tin cố định --}}
        <div class="p-3 bg-light rounded-3 border mb-4">
            <div class="row g-2">
                <div class="col-md-6">
                    <span class="text-muted small fw-semibold text-uppercase">Học phần:</span>
                    <div class="fw-bold text-dark">{{ $classSection->course->name }} ({{ $classSection->course->code }} · {{ $classSection->course->credits }} TC)</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small fw-semibold text-uppercase">Học kỳ:</span>
                    <div class="fw-bold text-dark">{{ $classSection->semester->name }} ({{ $classSection->semester->code }} · {{ $classSection->semester->academic_year }})</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.class-sections.update', $classSection) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="code" class="hta-form-label">
                        Mã lớp học phần <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control hta-input @error('code') is-invalid @enderror"
                        placeholder="VD: CS101.01"
                        value="{{ old('code', $classSection->code) }}"
                        required
                        maxlength="30"
                        style="text-transform: uppercase; font-family: monospace; font-weight: 750;"
                    >
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
                        value="{{ old('capacity', $classSection->capacity) }}"
                        min="5"
                        max="250"
                        required
                    >
                    <small class="text-muted">Hiện có {{ $classSection->enrolled_count }} sinh viên đã đăng ký.</small>
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
                                {{ old('lecturer_id', $classSection->lecturer_id) == $lec->id ? 'selected' : '' }}
                            >
                                {{ $lec->user->name ?? 'GV #'.$lec->id }} ({{ $lec->lecturer_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('lecturer_id')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    @php
                        $statusVal = is_object($classSection->status) ? $classSection->status->value : $classSection->status;
                    @endphp
                    <label for="status" class="hta-form-label">
                        Trạng thái lớp <span class="text-danger">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="form-select hta-input @error('status') is-invalid @enderror"
                        required
                    >
                        <option value="draft" {{ old('status', $statusVal) === 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                        <option value="open" {{ old('status', $statusVal) === 'open' ? 'selected' : '' }}>Đang mở đăng ký (Open)</option>
                        <option value="in_progress" {{ old('status', $statusVal) === 'in_progress' ? 'selected' : '' }}>Đang học (In Progress)</option>
                        <option value="finished" {{ old('status', $statusVal) === 'finished' ? 'selected' : '' }}>Đã hoàn thành (Finished)</option>
                        <option value="grades_published" {{ old('status', $statusVal) === 'grades_published' ? 'selected' : '' }}>Đã công bố điểm (Grades Published)</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('admin.class-sections.show', $classSection) }}" class="hta-btn-outline">
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
