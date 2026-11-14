@extends('layouts.app')

@section('title', $announcement->title . ' · Thông báo')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-lg-10">
        <!-- Breadcrumb & Nút quay lại -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('student.home') }}" class="text-decoration-none">Tổng quan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.announcements') }}" class="text-decoration-none">Thông báo</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    @if ($announcement->is_pinned)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                            📌 Ghim đầu trang
                        </span>
                    @endif

                    <span class="badge {{ $announcement->scope_badge }} px-3 py-1">
                        {{ $announcement->scope_label }}
                    </span>

                    <span class="text-muted small">
                        🕒 Đăng lúc {{ $announcement->published_at }}
                    </span>
                </div>

                <h3 class="fw-bold text-dark mb-3">
                    {{ $announcement->title }}
                </h3>

                <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-2 mb-4">
                    <span class="text-primary fs-5">👤</span>
                    <div>
                        <span class="text-muted small">Đơn vị / Người thông báo:</span>
                        <div class="fw-semibold text-dark">{{ $announcement->author_name }}</div>
                    </div>
                </div>

                <!-- Nội dung thông báo -->
                <div class="py-2 text-dark leading-relaxed" style="font-size: 1.05rem; white-space: pre-line;">
                    {!! nl2br(e($announcement->body)) !!}
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('student.announcements') }}" class="btn btn-outline-secondary px-4">
                        ← Quay lại danh sách thông báo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

