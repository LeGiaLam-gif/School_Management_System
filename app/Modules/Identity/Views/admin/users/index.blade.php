@extends('layouts.app')

@section('title', 'Quản lý người dùng · Hexa Tech Academy')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Banner Header --}}
    <div class="hta-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="hta-eyebrow">HEXA TECH ACADEMY · HỆ THỐNG TÀI KHOẢN · <span class="badge bg-white text-primary fw-semibold px-2 py-1">Giao diện cơ bản đã sẵn sàng</span></span>
            <h1 class="hta-page-title">Quản lý người dùng</h1>
            <p class="hta-page-subtitle">Trung tâm quản lý tài khoản, phân quyền truy cập hệ thống và kết nối dữ liệu Sinh viên & Giảng viên.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.students.create') }}" class="hta-btn-outline text-white border-white">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
                + Thêm sinh viên
            </a>
            <a href="{{ route('admin.lecturers.create') }}" class="hta-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                + Thêm giảng viên
            </a>
        </div>
    </div>

    {{-- 3 Thẻ điều hướng phân hệ cốt lõi --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Sinh viên --}}
        <div class="col-md-4">
            <div class="hta-card p-4 h-100 d-flex flex-column justify-content-between border-top border-4" style="border-top-color: #273873 !important;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="hta-stat-icon hta-stat-icon-navy">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                            </svg>
                        </div>
                        <span class="hta-badge hta-badge-navy">{{ $stats['total_students'] }} hồ sơ</span>
                    </div>
                    <h4 class="fw-bold mb-2" style="color: #1b2559;">Quản lý Sinh viên</h4>
                    <p class="text-muted small mb-3">
                        Quản lý toàn bộ danh sách sinh viên, hồ sơ học vụ, xếp lớp hành chính, lịch sử đăng ký môn học và cảnh báo học vụ.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-2 border-top">
                    <a href="{{ route('admin.students.index') }}" class="hta-btn-primary flex-fill justify-content-center text-decoration-none">
                        Vào Quản lý Sinh viên
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 2: Giảng viên --}}
        <div class="col-md-4">
            <div class="hta-card p-4 h-100 d-flex flex-column justify-content-between border-top border-4" style="border-top-color: #0d9488 !important;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="hta-stat-icon hta-stat-icon-teal">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v2"/>
                            </svg>
                        </div>
                        <span class="hta-badge hta-badge-teal">{{ $stats['total_lecturers'] }} cán bộ</span>
                    </div>
                    <h4 class="fw-bold mb-2" style="color: #1b2559;">Quản lý Giảng viên</h4>
                    <p class="text-muted small mb-3">
                        Quản lý đội ngũ giảng viên, khoa chuyên môn, phân công làm Cố vấn học tập (CVHT) và phân công lớp học phần giảng dạy.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-2 border-top">
                    <a href="{{ route('admin.lecturers.index') }}" class="hta-btn-primary flex-fill justify-content-center text-decoration-none" style="background: #0d9488;">
                        Vào Quản lý Giảng viên
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 3: Lớp hành chính --}}
        <div class="col-md-4">
            <div class="hta-card p-4 h-100 d-flex flex-column justify-content-between border-top border-4" style="border-top-color: #f59e0b !important;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="hta-stat-icon hta-stat-icon-amber">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            </svg>
                        </div>
                        <span class="hta-badge hta-badge-amber">Tổ chức lớp</span>
                    </div>
                    <h4 class="fw-bold mb-2" style="color: #1b2559;">Lớp hành chính</h4>
                    <p class="text-muted small mb-3">
                        Tổ chức các lớp sinh hoạt chính khóa theo ngành và niên khóa, phân công giảng viên cố vấn và quản lý danh sách sinh viên trong lớp.
                    </p>
                </div>
                <div class="d-flex gap-2 pt-2 border-top">
                    <a href="{{ route('admin.administrative-classes.index') }}" class="hta-btn-primary flex-fill justify-content-center text-decoration-none" style="background: #d97706;">
                        Vào Lớp hành chính
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bảng thống kê số lượng tài khoản --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-navy">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tổng số tài khoản</div>
                    <div class="hta-stat-value">{{ $stats['total_users'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-teal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tài khoản Sinh viên</div>
                    <div class="hta-stat-value">{{ $stats['total_students'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-violet">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v2"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Tài khoản Giảng viên</div>
                    <div class="hta-stat-value">{{ $stats['total_lecturers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="hta-stat-card">
                <div class="hta-stat-icon hta-stat-icon-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div>
                    <div class="hta-stat-label">Quản trị viên (Admin)</div>
                    <div class="hta-stat-value">{{ $stats['total_admins'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc & Danh sách tài khoản hệ thống --}}
    <div class="hta-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px; border: 1.8px solid #cbd5e1; border-right: none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#273873" stroke-width="2.2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </span>
                    <input
                        type="search"
                        name="search"
                        class="form-control border-start-0 hta-input"
                        style="border-radius: 0 12px 12px 0; border-left: none;"
                        placeholder="Tìm theo họ tên, email, mã số..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select hta-select">
                    <option value="">— Lọc theo vai trò —</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                    <option value="lecturer" {{ request('role') === 'lecturer' ? 'selected' : '' }}>Giảng viên</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Sinh viên</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select hta-select">
                    <option value="">— Trạng thái —</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Đang khóa</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="hta-btn-primary flex-fill justify-content-center">Lọc</button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" title="Bỏ lọc">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Danh sách tài khoản --}}
    <div class="hta-card">
        <div class="hta-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-primary">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
                <span>Tất cả tài khoản hệ thống</span>
            </div>
            <span class="hta-badge hta-badge-navy">{{ $users->total() }} tài khoản</span>
        </div>

        <div class="table-responsive">
            <table class="hta-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 140px;">Mã tài khoản</th>
                        <th>Họ và tên & Email</th>
                        <th class="text-center" style="width: 140px;">Vai trò</th>
                        <th class="text-center" style="width: 130px;">Trạng thái</th>
                        <th style="width: 140px;">Ngày tạo</th>
                        <th class="text-end" style="width: 140px;">Hồ sơ liên kết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td>
                                <span class="hta-code-tag">{{ $u->code ?: 'ACC-' . $u->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="hta-avatar-circle" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                        {{ mb_substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: #1b2559;">{{ $u->name }}</div>
                                        <div class="text-secondary small font-monospace">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @foreach ($u->roles as $role)
                                    @if ($role->name === 'admin')
                                        <span class="hta-badge hta-badge-amber">Admin</span>
                                    @elseif ($role->name === 'lecturer')
                                        <span class="hta-badge hta-badge-teal">Giảng viên</span>
                                    @elseif ($role->name === 'student')
                                        <span class="hta-badge hta-badge-navy">Sinh viên</span>
                                    @else
                                        <span class="badge bg-light text-dark border">{{ $role->name }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-center">
                                @if ($u->isActive())
                                    <span class="hta-badge hta-badge-teal">Hoạt động</span>
                                @else
                                    <span class="hta-badge hta-badge-rose">Đã khóa</span>
                                @endif
                            </td>
                            <td class="small text-secondary">
                                {{ $u->created_at ? $u->created_at->format('d/m/Y') : '—' }}
                            </td>
                            <td class="text-end">
                                @if ($u->student)
                                    <a href="{{ route('admin.students.show', $u->student) }}" class="btn btn-sm btn-light rounded-pill px-3 py-1 small fw-semibold" style="color: #273873; border: 1px solid #cbd5e1;">
                                        Hồ sơ SV →
                                    </a>
                                @elseif ($u->lecturer)
                                    <a href="{{ route('admin.lecturers.show', $u->lecturer) }}" class="btn btn-sm btn-light rounded-pill px-3 py-1 small fw-semibold" style="color: #0d9488; border: 1px solid #cbd5e1;">
                                        Hồ sơ GV →
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Tài khoản Admin</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Không tìm thấy tài khoản người dùng nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
