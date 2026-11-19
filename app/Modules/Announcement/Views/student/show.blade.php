@extends('layouts.app')

@section('title', $announcement->title . ' · Thông báo')

@section('content')
<div class="row g-4 sms-student-announcements">
    <div class="col-12">
        <!-- Breadcrumb & Nút quay lại -->
        <nav aria-label="breadcrumb" class="mb-3 px-3 px-md-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('student.home') }}" class="sms-announcement-link">Tổng quan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.announcements') }}" class="sms-announcement-link">Thông báo</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
            </ol>
        </nav>

        <article class="card sms-announcement-entry overflow-hidden">
            <header class="sms-announcement-detail-header p-3 p-md-4">
                <h1 class="h5 fw-bold mb-2">
                    {{ $announcement->title }}
                </h1>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if ($announcement->is_pinned)
                        <span class="badge sms-announcement-pinned">Ghim đầu trang</span>
                    @endif

                    <span class="badge sms-announcement-scope">
                        {{ $announcement->scope_label }}
                    </span>

                    <span class="text-muted small">
                        {{ $announcement->author_name }} · {{ $announcement->published_at }}
                    </span>
                </div>
            </header>
            <div class="card-body p-3 p-md-4">
                <div class="sms-announcement-body">{{ $announcement->body }}</div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('student.announcements') }}"
                       class="btn btn-sm sms-announcement-button d-inline-flex align-items-center justify-content-center"
                       aria-label="Quay lại danh sách thông báo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                            <path d="m12 5-7 7 7 7M5 12h14" />
                        </svg>
                    </a>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection

