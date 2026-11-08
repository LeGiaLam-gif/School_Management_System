@extends('layouts.app')

@section('title', 'Thông tin cá nhân & Học vấn · Cổng Sinh Viên')

@section('content')
<div class="sms-student-profile-page">
<div class="row g-4">
    <!-- Cột bên trái: Thẻ tóm tắt thông tin sinh viên -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <div class="sms-student-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-badge" viewBox="0 0 16 16">
                            <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/>
                        </svg>
                    </div>
                </div>

                <h5 class="fw-bold mb-1">{{ $student->name ?? $user?->name ?? 'Sinh viên' }}</h5>
                <p class="text-muted mb-2 font-monospace">{{ $student->student_code ?? $user?->code }}</p>

                <span class="badge {{ $student->status_badge ?? 'bg-success' }} px-3 py-2 rounded-pill fs-7 mb-3">
                    {{ $student->status_label ?? 'Đang học' }}
                </span>

                <hr class="text-muted my-3">

                <div class="text-start small">
                    <div class="d-flex justify-content-between gap-3 py-1">
                        <span class="text-muted">Email trường:</span>
                        <span class="fw-semibold text-end">{{ $student->email ?? $user?->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between gap-3 py-1">
                        <span class="text-muted">Ngày sinh:</span>
                        <span class="fw-semibold text-end">{{ $student->date_of_birth ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="d-flex justify-content-between gap-3 py-1">
                        <span class="text-muted">Giới tính:</span>
                        <span class="fw-semibold text-end">{{ $student->gender ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="d-flex justify-content-between gap-3 py-1">
                        <span class="text-muted">Khóa tuyển sinh:</span>
                        <span class="fw-semibold text-end">Khóa {{ $student->cohort_year ?? 'Chưa cập nhật' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột bên phải: Thông tin đào tạo & Cập nhật thông tin -->
    <div class="col-lg-8">
        <!-- Card 1: Thông tin Học vấn & Khoa - Ngành -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-mortarboard" viewBox="0 0 16 16">
                        <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/>
                        <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>
                    </svg>
                    Thông tin Khoa & Ngành đào tạo
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Khoa</label>
                        <div class="p-2 bg-light rounded border fw-semibold">
                            {{ $student->department_name ?? 'Công nghệ Thông tin' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Chương trình đào tạo (Ngành)</label>
                        <div class="p-2 bg-light rounded border fw-semibold">
                            {{ $student->program_name ?? 'Kỹ thuật Phần mềm' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Thời gian đào tạo tiêu chuẩn</label>
                        <div class="p-2 bg-light rounded border">
                            {{ $student->duration_years ?? 4 }} năm (Theo quy chế tín chỉ)
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Tổng số tín chỉ yêu cầu</label>
                        <div class="p-2 bg-light rounded border">
                            <span class="fw-bold text-primary">{{ $student->total_credits_required ?? 140 }}</span> tín chỉ
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Cập nhật thông tin liên lạc cá nhân -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                        <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                    </svg>
                    Thông tin liên hệ
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('student.profile.contact') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-semibold">Số điện thoại liên hệ</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $student->phone ?? '') }}" placeholder="Ví dụ: 0912345678">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Email trường (Cố định)</label>
                            <input type="email" class="form-control bg-light" value="{{ $student->email ?? $user?->email ?? 'sv@sms.edu.vn' }}" disabled readonly>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label small fw-semibold">Địa chỉ thường trú / liên hệ</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $student->address ?? '') }}" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            Lưu thông tin liên hệ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card 3: Đổi mật khẩu cá nhân -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold text-danger mb-0 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                        <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
                        <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415"/>
                    </svg>
                    Đổi mật khẩu tài khoản
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="d-flex flex-column gap-3 mb-3">
                        <div>
                            <label for="current_password" class="form-label small fw-semibold">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required autocomplete="current-password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="new_password" class="form-label small fw-semibold">Mật khẩu mới (tối thiểu 8 ký tự)</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required autocomplete="new-password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="new_password_confirmation" class="form-label small fw-semibold">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-outline-danger px-4">
                            Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
