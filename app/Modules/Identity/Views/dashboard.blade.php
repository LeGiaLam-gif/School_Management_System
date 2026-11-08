@extends('layouts.app')

@section('title', $role->label().' · '.config('app.name'))

@section('content')
    @php($user = auth()->user())

    <section class="sms-welcome-card">
        <div class="sms-welcome-copy">
            <span class="sms-eyebrow">HEXA TECH ACADEMY · HỆ THỐNG QUẢN LÝ ĐÀO TẠO</span>
            <h2>Chào mừng, {{ $user->name }}</h2>
            <p>
                Không gian làm việc dành cho {{ mb_strtolower($role->label()) }},
                tập trung các hoạt động học vụ trên cùng một nền tảng.
            </p>
            <span class="sms-role-pill">{{ $role->label() }}</span>
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
    </section>

    <section class="sms-summary-grid" aria-label="Thông tin tài khoản">
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-teal" aria-hidden="true">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" /></svg>
            </span>
            <span class="sms-summary-label">Vai trò</span>
            <strong>{{ $role->label() }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-orange" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM8 9h8M8 13h5" /></svg>
            </span>
            <span class="sms-summary-label">Mã tài khoản</span>
            <strong>{{ $user->code }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-green" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6" /><circle cx="12" cy="12" r="10" /></svg>
            </span>
            <span class="sms-summary-label">Trạng thái</span>
            <strong>{{ $user->status->label() }}</strong>
        </article>
        <article class="sms-summary-card">
            <span class="sms-summary-icon sms-icon-violet" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m4 7 8 6 8-6" /></svg>
            </span>
            <span class="sms-summary-label">Email</span>
            <strong class="sms-summary-email">{{ $user->email }}</strong>
        </article>
    </section>

    <section class="sms-workspace-card">
        <div class="sms-section-heading">
            <div>
                <h2>Bảng điều khiển</h2>
            </div>
            <span class="sms-current-role">{{ $role->label() }}</span>
        </div>
        <div class="sms-workspace-empty">
            <span class="sms-workspace-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M4 4h16v12H4zM2 20h20M9 16v4m6-4v4" />
                    <path d="M8 9h8M8 12h5" />
                </svg>
            </span>
            <div>
                <h3>Chức năng dành cho {{ mb_strtolower($role->label()) }}</h3>
                <p>
                    Các nội dung học vụ sẽ được bổ sung khi những phân hệ liên quan được tích hợp.
                </p>
            </div>
        </div>
    </section>
@endsection
