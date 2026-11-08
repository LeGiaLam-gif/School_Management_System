@extends('layouts.app')

@section('title', 'Cổng Sinh Viên · Trang chủ')

@section('content')
    <section class="sms-welcome-card">
        <div class="sms-welcome-copy">
            <span class="sms-eyebrow">HEXA TECH ACADEMY · HỆ THỐNG QUẢN LÝ ĐÀO TẠO</span>
            <h2>Chào mừng, {{ $student->name ?? $user?->name ?? 'Sinh viên' }}</h2>
            <p>Không gian học tập dành cho sinh viên, tập trung thông tin và hoạt động học vụ trên cùng một nền tảng.</p>
            <span class="sms-role-pill">Sinh viên</span>
        </div>
        <div class="sms-welcome-art" aria-hidden="true">
            <svg viewBox="0 0 180 150" focusable="false">
                <path class="art-orbit" d="M18 115c26-71 106-95 145-56 34 35-1 75-44 71-36-3-66-25-91-15" />
                <path class="art-book" d="M46 51c18-8 33-5 45 4v66c-12-9-27-12-45-4z" />
                <path class="art-book" d="M91 55c14-9 29-12 46-4v66c-17-8-32-5-46 4z" />
                <path class="art-line" d="M56 67c11-4 20-2 27 3M56 79c11-4 20-2 27 3M102 70c9-5 19-7 28-5M102 82c9-5 19-7 28-5" />
                <path class="art-cap" d="m31 43 60-23 60 23-60 24z" />
                <path class="art-cap-tail" d="M48 51v24c24 17 70 17 86 0V51" />
                <circle class="art-spark" cx="148" cy="29" r="5" />
                <circle class="art-spark" cx="30" cy="95" r="4" />
            </svg>
        </div>
        <a href="{{ route('student.profile') }}" class="sms-student-profile-shortcut">Xem hồ sơ cá nhân</a>
    </section>

    <section class="sms-summary-grid" aria-label="Thông tin sinh viên">
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-teal" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM8 9h8M8 13h5" /></svg>
            </span>
            <span class="sms-summary-label">Mã sinh viên</span>
            <strong>{{ $student->student_code ?? $user?->code ?? 'Chưa cập nhật' }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-orange" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM8 9h8M8 13h8M8 17h5" /></svg>
            </span>
            <span class="sms-summary-label">Ngành học</span>
            <strong>{{ $student->program_name ?? 'Chưa cập nhật' }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-violet" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></svg>
            </span>
            <span class="sms-summary-label">Khóa tuyển sinh</span>
            <strong>{{ $student->cohort_year ?? 'Chưa cập nhật' }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-green" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6" /><circle cx="12" cy="12" r="10" /></svg>
            </span>
            <span class="sms-summary-label">Trạng thái</span>
            <strong>{{ $student->status_label ?? 'Chưa cập nhật' }}</strong>
        </article>
    </section>

    <div class="row g-4 mt-1">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-3 hover-shadow transition">
            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                    <span class="sms-student-shortcut-icon sms-icon-teal">👤</span>
                </div>
                <h6 class="fw-bold mb-1">
                    <a href="{{ route('student.profile') }}" class="sms-student-card-title">Hồ sơ cá nhân</a>
                </h6>
                <p class="text-muted small mb-4 flex-grow-1">
                    Xem thông tin sinh viên, khoa, ngành học và quản lý thông tin liên hệ.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-3">
            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                    <span class="sms-student-shortcut-icon sms-icon-orange">📚</span>
                </div>
                <h6 class="fw-bold mb-1">
                    @if (Route::has('student.curriculum'))
                        <a href="{{ route('student.curriculum') }}" class="sms-student-card-title">Chương trình đào tạo</a>
                    @else
                        Chương trình đào tạo
                    @endif
                </h6>
                <p class="text-muted small mb-4 flex-grow-1">
                    Tra cứu danh sách môn học, học phần tiên quyết và khung gợi ý theo kỳ.
                </p>
                @unless (Route::has('student.curriculum'))
                    <span class="sms-module-unavailable small">Chức năng chưa khả dụng</span>
                @endunless
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-3">
            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                    <span class="sms-student-shortcut-icon sms-icon-orange">📝</span>
                </div>
                <h6 class="fw-bold mb-1">
                    @if (Route::has('student.enrollment'))
                        <a href="{{ route('student.enrollment') }}" class="sms-student-card-title">Đăng ký học phần</a>
                    @else
                        Đăng ký học phần
                    @endif
                </h6>
                <p class="text-muted small mb-4 flex-grow-1">
                    Đăng ký hoặc hủy các lớp học phần mở trong kỳ theo kế hoạch đào tạo.
                </p>
                @unless (Route::has('student.enrollment'))
                    <span class="sms-module-unavailable small">Chức năng chưa khả dụng</span>
                @endunless
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm rounded-3">
            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                    <span class="sms-student-shortcut-icon sms-icon-green">📊</span>
                </div>
                <h6 class="fw-bold mb-1">
                    @if (Route::has('student.grades'))
                        <a href="{{ route('student.grades') }}" class="sms-student-card-title">Điểm & Thời khóa biểu</a>
                    @else
                        Điểm & Thời khóa biểu
                    @endif
                </h6>
                <p class="text-muted small mb-4 flex-grow-1">
                    Theo dõi lịch học, điểm học phần và GPA sau khi kết quả được công bố.
                </p>
                @unless (Route::has('student.grades'))
                    <span class="sms-module-unavailable small">Chức năng chưa khả dụng</span>
                @endunless
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
