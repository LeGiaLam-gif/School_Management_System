@extends('layouts.app')

@section('title', $course->code . ' - ' . $course->name . ' · Cổng Sinh Viên')

@section('content')
<div class="row g-4">
    <!-- Nút quay lại & Breadcrumb -->
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('student.home') }}" class="text-decoration-none">Tổng quan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.curriculum') }}" class="text-decoration-none">Khung CTĐT</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.courses') }}" class="text-decoration-none">Danh mục môn học</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $course->code }}</li>
            </ol>
        </nav>
    </div>

    <!-- Thông tin chi tiết học phần -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary px-3 py-2 font-monospace fs-6 rounded-pill">
                        {{ $course->code }}
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                        {{ $course->credits }} Tín chỉ
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                        {{ $course->department_name }}
                    </span>
                </div>

                <h3 class="fw-bold text-dark mt-3 mb-3">{{ $course->name }}</h3>

                <div class="row g-3 py-3 border-top border-bottom my-3">
                    <div class="col-sm-6">
                        <span class="text-muted small">Thời lượng giảng dạy:</span>
                        <div class="fw-semibold text-dark mt-1">
                            {{ $course->theory_periods }} tiết Lý thuyết · {{ $course->practice_periods }} tiết Thực hành
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small">Đơn vị quản lý:</span>
                        <div class="fw-semibold text-dark mt-1">{{ $course->department_name }}</div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2">Mô tả và mục tiêu học phần</h6>
                <p class="text-muted leading-relaxed mb-0">
                    {{ $course->description }}
                </p>
            </div>
        </div>

        <!-- Môn học sau này yêu cầu môn này (Required By) -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span>🚀 Học phần mở tiếp theo</span>
                    <span class="badge bg-info-subtle text-info">{{ count($course->required_by) }} môn</span>
                </h6>
                <p class="text-muted small mt-1 mb-0">Các học phần trong tương lai yêu cầu bạn phải tích lũy học phần này trước khi đăng ký:</p>
            </div>
            <div class="card-body p-4">
                @if (!empty($course->required_by))
                    <div class="list-group list-group-flush">
                        @foreach ($course->required_by as $req)
                            <a href="{{ route('student.courses.detail', $req->code) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-3">
                                <div>
                                    <span class="badge bg-light text-primary border font-monospace me-2">{{ $req->code }}</span>
                                    <span class="fw-semibold text-dark">{{ $req->name }}</span>
                                </div>
                                <span class="badge bg-light text-muted border">{{ $req->credits }} TC →</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted small py-2">
                        Hiện tại không có học phần nào bắt buộc học phần này làm tiên quyết.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cột bên phải: Điều kiện tiên quyết của học phần này -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold text-danger mb-0 d-flex align-items-center gap-2">
                    <span>⚠️ Điều kiện tiên quyết</span>
                </h6>
                <p class="text-muted small mt-1 mb-0">Môn bạn cần hoàn thành để đăng ký môn này:</p>
            </div>
            <div class="card-body p-4">
                @if (!empty($course->prerequisites))
                    <div class="d-flex flex-column gap-3">
                        @foreach ($course->prerequisites as $p)
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-danger-subtle text-danger font-monospace">{{ $p->code }}</span>
                                    <span class="small text-muted">{{ $p->credits }} Tín chỉ</span>
                                </div>
                                <a href="{{ route('student.courses.detail', $p->code) }}" 
                                   class="fw-semibold text-dark text-decoration-none d-block">
                                    {{ $p->name }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-success border-0 small mb-0">
                        <span class="fw-semibold">Tự do đăng ký:</span> Học phần này không yêu cầu môn tiên quyết.
                    </div>
                @endif
            </div>
        </div>

        <!-- Thao tác nhanh -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 d-flex flex-column gap-2">
                <a href="{{ route('student.courses') }}" class="btn btn-outline-secondary w-100">
                    ← Danh sách môn học
                </a>
                <a href="{{ route('student.curriculum') }}" class="btn btn-primary w-100">
                    Xem Khung chương trình đào tạo
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

