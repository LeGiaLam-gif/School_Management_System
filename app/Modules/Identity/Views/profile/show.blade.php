@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân · '.config('app.name'))

@section('content')
    <h1 class="h3 mb-4">Hồ sơ cá nhân</h1>

    <section class="card mb-4">
        <div class="card-header">Thông tin tài khoản</div>
        <dl class="card-body row mb-0">
            <dt class="col-sm-4">Họ và tên</dt>
            <dd class="col-sm-8">{{ $user->name }}</dd>

            <dt class="col-sm-4">Mã đăng nhập</dt>
            <dd class="col-sm-8">{{ $user->code }}</dd>

            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8">{{ $user->email }}</dd>

            <dt class="col-sm-4">Vai trò</dt>
            <dd class="col-sm-8">{{ $roles->join(', ') }}</dd>

            <dt class="col-sm-4">Trạng thái tài khoản</dt>
            <dd class="col-sm-8">{{ $user->status->label() }}</dd>
        </dl>
    </section>

    @if ($user->student)
        <section class="card mb-4">
            <div class="card-header">Thông tin sinh viên</div>
            <dl class="card-body row mb-0">
                <dt class="col-sm-4">Mã sinh viên</dt>
                <dd class="col-sm-8">{{ $user->student->student_code }}</dd>

                <dt class="col-sm-4">Ngành</dt>
                <dd class="col-sm-8">{{ $user->student->program->name }}</dd>

                <dt class="col-sm-4">Khoa</dt>
                <dd class="col-sm-8">{{ $user->student->program->department->name }}</dd>

                <dt class="col-sm-4">Khóa tuyển sinh</dt>
                <dd class="col-sm-8">{{ $user->student->cohort_year }}</dd>

                <dt class="col-sm-4">Ngày sinh</dt>
                <dd class="col-sm-8">{{ $user->student->date_of_birth?->format('d/m/Y') ?? 'Chưa cập nhật' }}</dd>

                <dt class="col-sm-4">Giới tính</dt>
                <dd class="col-sm-8">{{ $user->student->gender?->label() ?? 'Chưa cập nhật' }}</dd>

                <dt class="col-sm-4">Số điện thoại</dt>
                <dd class="col-sm-8">{{ $user->student->phone ?? 'Chưa cập nhật' }}</dd>

                <dt class="col-sm-4">Địa chỉ</dt>
                <dd class="col-sm-8">{{ $user->student->address ?? 'Chưa cập nhật' }}</dd>

                <dt class="col-sm-4">Trạng thái sinh viên</dt>
                <dd class="col-sm-8">{{ $user->student->status->label() }}</dd>
            </dl>
        </section>
    @endif

    @if ($user->lecturer)
        <section class="card mb-4">
            <div class="card-header">Thông tin giảng viên</div>
            <dl class="card-body row mb-0">
                <dt class="col-sm-4">Mã giảng viên</dt>
                <dd class="col-sm-8">{{ $user->lecturer->lecturer_code }}</dd>

                <dt class="col-sm-4">Khoa</dt>
                <dd class="col-sm-8">{{ $user->lecturer->department->name }}</dd>

                <dt class="col-sm-4">Học hàm / học vị</dt>
                <dd class="col-sm-8">{{ $user->lecturer->academic_title ?? 'Chưa cập nhật' }}</dd>

                <dt class="col-sm-4">Số điện thoại</dt>
                <dd class="col-sm-8">{{ $user->lecturer->phone ?? 'Chưa cập nhật' }}</dd>

                <dt class="col-sm-4">Văn phòng</dt>
                <dd class="col-sm-8">{{ $user->lecturer->office ?? 'Chưa cập nhật' }}</dd>
            </dl>
        </section>
    @endif
@endsection
