@extends('layouts.app')

@section('title', 'Thông báo · Cổng Sinh Viên')

@section('content')
<div class="row g-4">
    <!-- Header Hộp thông báo -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-2 rounded-pill fs-7">
                                📢 Bảng tin nhà trường
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-dark">
                            Hộp Thông Báo Sinh Viên
                        </h3>
                        <p class="text-muted small mb-0">
                            Cập nhật thông tin đào tạo từ Nhà trường, Khoa và các Lớp học phần bạn đang tham gia.
                        </p>
                    </div>

                    <!-- Bộ lọc phạm vi thông báo -->
                    <div class="btn-group shadow-sm" role="group">
                        <a href="{{ route('student.announcements') }}" 
                           class="btn px-3 py-2 fw-semibold {{ $currentScope === 'all_available' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Tất cả
                        </a>
                        <a href="{{ route('student.announcements', ['scope' => 'all']) }}" 
                           class="btn px-3 py-2 fw-semibold {{ $currentScope === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Toàn trường
                        </a>
                        <a href="{{ route('student.announcements', ['scope' => 'class_section']) }}" 
                           class="btn px-3 py-2 fw-semibold {{ $currentScope === 'class_section' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Lớp học phần
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Thông báo -->
    <div class="col-12">
        <div class="d-flex flex-column gap-3">
            @forelse ($announcements as $a)
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden {{ $a->is_pinned ? 'border-start border-primary border-4' : '' }}">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            @if ($a->is_pinned)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    📌 Ghim đầu trang
                                </span>
                            @endif

                            <span class="badge {{ $a->scope_badge }}">
                                {{ $a->scope_label }}
                            </span>

                            <span class="text-muted small">
                                👤 {{ $a->author_name }} · 🕒 {{ $a->published_at }}
                            </span>
                        </div>

                        <h5 class="fw-bold mb-2">
                            <a href="{{ route('student.announcements.show', $a->id) }}" class="text-dark text-decoration-none hover-primary">
                                {{ $a->title }}
                            </a>
                        </h5>

                        <p class="text-muted small mb-3">
                            {{ $a->summary }}
                        </p>

                        <div>
                            <a href="{{ route('student.announcements.show', $a->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                Xem chi tiết thông báo →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-5 text-center text-muted">
                        <div class="fs-1 mb-2">📭</div>
                        <h6 class="fw-bold">Hiện không có thông báo nào trong mục này</h6>
                        <p class="small mb-0">Vui lòng quay lại sau để cập nhật thông tin mới nhất.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

