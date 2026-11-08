@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân · '.config('app.name'))

@section('content')
    <div class="sms-profile-page sms-student-profile-page">
        <h1 class="h3 sms-profile-title">Hồ sơ cá nhân</h1>

        <div class="row g-4">
            <div class="col-lg-4">
                <section class="card sms-profile-summary">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <div class="sms-student-avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.482 10.655 10.05 10 8 10s-3.482.655-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-2 font-monospace">{{ $user->lecturer?->lecturer_code ?? $user->student?->student_code ?? $user->code }}</p>
                        <span class="badge {{ $user->isActive() ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded-pill mb-3">
                            {{ $user->status->label() }}
                        </span>

                        <hr class="text-muted my-3">

                        <div class="text-start small">
                            <div class="d-flex justify-content-between gap-3 py-1">
                                <span class="text-muted">Email:</span>
                                <span class="fw-semibold text-end">{{ $user->email }}</span>
                            </div>
                            <div class="d-flex justify-content-between gap-3 py-1">
                                <span class="text-muted">Vai trò:</span>
                                <span class="fw-semibold text-end">{{ $roles->join(', ') }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-8">
                @if ($user->student)
                    <section class="card mb-4">
                        <div class="card-header">Thông tin sinh viên</div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Mã sinh viên</label>
                                    <div class="p-2 bg-light rounded border fw-semibold">{{ $user->student->student_code }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Ngành</label>
                                    <div class="p-2 bg-light rounded border fw-semibold">{{ $user->student->program->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Khoa</label>
                                    <div class="p-2 bg-light rounded border fw-semibold">{{ $user->student->program->department->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Khóa tuyển sinh</label>
                                    <div class="p-2 bg-light rounded border fw-semibold">{{ $user->student->cohort_year }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Ngày sinh</label>
                                    <div class="p-2 bg-light rounded border">{{ $user->student->date_of_birth?->format('d/m/Y') ?? 'Chưa cập nhật' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Giới tính</label>
                                    <div class="p-2 bg-light rounded border">{{ $user->student->gender?->label() ?? 'Chưa cập nhật' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Số điện thoại</label>
                                    <div class="p-2 bg-light rounded border">{{ $user->student->phone ?? 'Chưa cập nhật' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Địa chỉ</label>
                                    <div class="p-2 bg-light rounded border">{{ $user->student->address ?? 'Chưa cập nhật' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Trạng thái sinh viên</label>
                                    <div class="p-2 bg-light rounded border">{{ $user->student->status->label() }}</div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                @if ($user->lecturer)
                    <section class="card mb-4">
                        <div class="card-header d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/>
                                <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>
                            </svg>
                            Thông tin giảng viên
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Mã giảng viên</label>
                                    <div class="p-2 bg-light rounded border fw-semibold">{{ $user->lecturer->lecturer_code }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Khoa</label>
                                    <div class="p-2 bg-light rounded border fw-semibold">{{ $user->lecturer->department->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Học hàm / học vị</label>
                                    <div class="p-2 bg-light rounded border">{{ $user->lecturer->academic_title ?? 'Chưa cập nhật' }}</div>
                                </div>
                            </div>
                        </div>
                    </section>

                @endif

                @if ($user->isAdmin() || $user->isLecturer())
                    <section class="card mb-4">
                        <div class="card-header d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                            </svg>
                            Thông tin liên hệ
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('profile.contact') }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="profile_phone" class="form-label small fw-semibold">Số điện thoại liên hệ</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="profile_phone" name="phone" value="{{ old('phone', $user->lecturer?->phone ?? $user->phone ?? '') }}" placeholder="Ví dụ: 0562845621">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">Email (Cố định)</label>
                                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled readonly>
                                    </div>
                                    <div class="col-12">
                                        <label for="profile_address" class="form-label small fw-semibold">Địa chỉ thường trú / liên hệ</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="profile_address" name="address" value="{{ old('address', $user->lecturer?->address ?? $user->address ?? '') }}" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">Lưu thông tin liên hệ</button>
                                </div>
                            </form>
                        </div>
                    </section>
                @endif

                @include('identity::partials.change-password')
            </div>
        </div>
    </div>
@endsection
