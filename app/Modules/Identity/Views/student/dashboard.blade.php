@extends('layouts.app')

@section('title', 'Cổng Sinh Viên · Trang chủ')

@section('content')
<div class="row g-4">
    <!-- Banner chào mừng -->
    <div class="col-12">
        <div class="student-badge-card p-4 p-md-5 shadow-sm">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white mb-2 px-3 py-2 rounded-pill">
                        Cổng Đào Tạo Tín Chỉ
                    </span>
                    <h2 class="fw-bold mb-1">Xin chào, {{ $student->name ?? $user?->name ?? 'Sinh viên' }}!</h2>
                    <p class="mb-0 text-white-50">
                        MSSV: <strong class="text-white">{{ $student->student_code ?? $user?->code ?? 'SV20230001' }}</strong>
                        · Ngành: <strong class="text-white">{{ $student->program_name ?? 'Kỹ thuật Phần mềm' }}</strong>
                        · Khóa: <strong class="text-white">{{ $student->cohort_year ?? '2023' }}</strong>
                    </p>
                </div>
                <div>
                    <a href="{{ route('student.profile') }}" class="btn btn-light text-primary fw-semibold px-4 py-2 shadow-sm">
                        Xem hồ sơ cá nhân →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Shortcut Cards -->
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-3 hover-shadow transition">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 fs-4">👤</span>
                    <span class="badge {{ $student->status_badge ?? 'bg-success' }}">{{ $student->status_label ?? 'Đang học' }}</span>
                </div>
                <h6 class="fw-bold mb-1">Hồ sơ cá nhân</h6>
                <p class="text-muted small mb-4 flex-grow-1">
                    Xem thông tin sinh viên, khoa, ngành học và quản lý thông tin liên hệ.
                </p>
                <a href="{{ route('student.profile') }}" class="btn btn-sm btn-outline-primary w-100">
                    Truy cập hồ sơ
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-3">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="p-3 bg-info bg-opacity-10 text-info rounded-3 fs-4">📚</span>
                    <span class="badge bg-secondary-subtle text-secondary">Học vụ</span>
                </div>
                <h6 class="fw-bold mb-1">Chương trình đào tạo</h6>
                <p class="text-muted small mb-4 flex-grow-1">
                    Tra cứu danh sách môn học, học phần tiên quyết và khung gợi ý theo kỳ.
                </p>
                <a href="{{ Route::has('student.curriculum') ? route('student.curriculum') : '#' }}" class="btn btn-sm btn-outline-info w-100">
                    Xem khung CTĐT
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-3">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 fs-4">📝</span>
                    <span class="badge bg-warning-subtle text-warning">Đăng ký</span>
                </div>
                <h6 class="fw-bold mb-1">Đăng ký học phần</h6>
                <p class="text-muted small mb-4 flex-grow-1">
                    Đăng ký hoặc hủy các lớp học phần mở trong kỳ theo kế hoạch đào tạo.
                </p>
                <a href="{{ Route::has('student.enrollment') ? route('student.enrollment') : '#' }}" class="btn btn-sm btn-outline-warning w-100">
                    Đăng ký môn học
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-3">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="p-3 bg-success bg-opacity-10 text-success rounded-3 fs-4">📊</span>
                    <span class="badge bg-primary-subtle text-primary">Kết quả</span>
                </div>
                <h6 class="fw-bold mb-1">Điểm & Thời khóa biểu</h6>
                <p class="text-muted small mb-4 flex-grow-1">
                    Theo dõi lịch học hàng tuần, điểm chuyên cần, giữa kỳ, cuối kỳ và GPA.
                </p>
                <a href="{{ Route::has('student.grades') ? route('student.grades') : '#' }}" class="btn btn-sm btn-outline-success w-100">
                    Xem kết quả học tập
                </a>
            </div>
        </div>
    </div>

    <!-- Hướng dẫn sinh viên -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="text-primary fs-3">ℹ️</div>
                    <div>
                        <h6 class="fw-bold mb-1">Hướng dẫn dành cho sinh viên</h6>
                        <p class="text-muted small mb-0">
                            Sinh viên cần thường xuyên kiểm tra thông tin cá nhân và thông tin học vấn. Nếu phát hiện sai sót về
                            Khoa, Ngành hoặc Khóa học, vui lòng liên hệ trực tiếp với Phòng Đào tạo để được hỗ trợ điều chỉnh.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
