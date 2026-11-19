@extends('layouts.app')

@section('title', 'Thông báo · Cổng Sinh Viên')

@section('content')
<div class="row g-4 sms-student-announcements">
    <!-- Header Hộp thông báo -->
    <div class="col-12">
        <header class="sms-announcement-banner p-3 p-md-4">
            <h1 class="h3 fw-bold mb-1">Hộp thông báo sinh viên</h1>
            <p class="small mb-0">Thông báo từ nhà trường và các lớp học phần bạn đang tham gia</p>
        </header>
        <nav class="d-flex flex-wrap gap-2 mt-3 px-3 px-md-4" aria-label="Phạm vi thông báo">
            @foreach (['all_available' => 'Tất cả', 'all' => 'Toàn trường', 'class_section' => 'Lớp học phần'] as $scope => $label)
                <a href="{{ route('student.announcements', $scope === 'all_available' ? [] : ['scope' => $scope]) }}"
                   class="btn btn-sm sms-announcement-button {{ $currentScope === $scope ? 'active' : '' }}"
                   @if ($currentScope === $scope) aria-current="page" @endif>
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Danh sách Thông báo -->
    <div class="col-12">
        <div class="d-flex flex-column gap-3">
            @forelse ($announcements as $a)
                <article class="card sms-announcement-entry position-relative">
                    <div class="card-body p-3 p-md-4">
                        <h2 class="h5 fw-bold mb-2">
                            <a href="{{ route('student.announcements.show', $a->id) }}" class="sms-announcement-link stretched-link">
                                {{ $a->title }}
                            </a>
                        </h2>

                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            @if ($a->is_pinned)
                                <span class="badge sms-announcement-pinned">Ghim đầu trang</span>
                            @endif

                            <span class="badge sms-announcement-scope">
                                {{ $a->scope_label }}
                            </span>

                            <span class="text-muted small">
                                {{ $a->author_name }} · {{ $a->published_at }}
                            </span>
                        </div>

                        <p class="text-muted small mb-0">
                            {{ $a->summary }}
                        </p>

                    </div>
                </article>
            @empty
                <div class="card sms-announcement-entry">
                    <div class="card-body p-4 text-center text-muted">
                        <p class="mb-0">Hiện không có thông báo nào trong mục này.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

