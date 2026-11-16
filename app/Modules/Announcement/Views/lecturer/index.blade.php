@extends('layouts.app')

@section('title', 'Thông báo lớp học phần · Cổng Giảng Viên')

@section('content')
<div class="sms-workspace-page">
    <div class="sms-workspace-page-heading">
        <div>
            <span class="sms-eyebrow">HEXATECH ACADEMY · GIẢNG VIÊN</span>
            <h1>Thông báo lớp học phần</h1>
            <p>
                Đăng thông báo, tài liệu nhắc nhở hoặc lịch kiểm tra tới sinh viên từng lớp học phần bạn phụ trách.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('lecturer.classes') }}" class="sms-workspace-back-link">
                Lớp phụ trách
            </a>
        </div>
    </div>

    @if ($schoolAnnouncements->isNotEmpty())
        <div class="sms-workspace-card" style="min-height: auto; background: #f7faf7;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold small text-uppercase" style="color: var(--sms-teal-dark); letter-spacing: .04em;">
                    Thông báo chung từ Nhà trường
                </span>
            </div>
            <div class="row g-3">
                @foreach ($schoolAnnouncements as $schoolNotice)
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded border h-100">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                @if ($schoolNotice->is_pinned)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Ghim</span>
                                @endif
                                <span class="small text-muted">{{ $schoolNotice->published_at?->format('d/m/Y') }}</span>
                            </div>
                            <div class="fw-semibold text-dark small mb-1">{{ $schoolNotice->title }}</div>
                            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($schoolNotice->body, 95) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-lg-5">
            <div class="sms-workspace-card" style="min-height: auto;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h6 fw-bold mb-1" style="color: var(--sms-teal-dark);">
                            {{ $editingAnnouncement ? 'Chỉnh sửa thông báo' : 'Soạn thông báo mới' }}
                        </h2>
                        <p class="small text-muted mb-0">
                            Chỉ sinh viên đang đăng ký lớp học phần được chọn mới nhận thông báo.
                        </p>
                    </div>
                    @if ($editingAnnouncement)
                        <a
                            href="{{ route('lecturer.announcements', array_filter(['class_section_id' => $filters['class_section_id']])) }}"
                            class="btn btn-sm btn-outline-secondary"
                        >
                            Hủy sửa
                        </a>
                    @endif
                </div>

                @if ($classes->isEmpty())
                    <div class="sms-workspace-notice my-2">
                        <span class="sms-workspace-notice-icon">i</span>
                        <div>
                            <h2>Chưa có lớp phụ trách</h2>
                            <p>Bạn cần được phân công ít nhất một lớp học phần để gửi thông báo.</p>
                        </div>
                    </div>
                @elseif (! $canCreate)
                    <div class="alert alert-warning small mb-0">
                        Tài khoản của bạn hiện chưa được cấp quyền quản lý thông báo.
                    </div>
                @else
                    @php
                        $formAction = $editingAnnouncement
                            ? route('lecturer.announcements.update', $editingAnnouncement)
                            : route('lecturer.announcements.store');
                        $defaultClassId = old(
                            'class_section_id',
                            $editingAnnouncement?->class_section_id ?? $filters['class_section_id'] ?? $classes->first()?->id
                        );
                        $isScheduledDefault = $editingAnnouncement?->published_at !== null
                            && $editingAnnouncement->published_at->isFuture();
                        $publishMode = old('publish_mode', $isScheduledDefault ? 'schedule' : 'now');
                        $publishedAtValue = old(
                            'published_at',
                            $editingAnnouncement?->published_at?->format('Y-m-d\TH:i') ?? now()->addHour()->format('Y-m-d\TH:i')
                        );
                    @endphp

                    <form method="POST" action="{{ $formAction }}">
                        @csrf
                        @if ($editingAnnouncement)
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="form_class_section_id" class="form-label small fw-semibold text-muted">
                                Lớp học phần <span class="text-danger">*</span>
                            </label>
                            <select name="class_section_id" id="form_class_section_id" class="form-select" required>
                                @foreach ($classes as $classItem)
                                    <option value="{{ $classItem->id }}" @selected((int) $defaultClassId === $classItem->id)>
                                        {{ $classItem->code }} — {{ $classItem->course?->name }} ({{ $classItem->semester?->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label small fw-semibold text-muted">
                                Tiêu đề <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control"
                                maxlength="255"
                                placeholder="VD: Lịch kiểm tra giữa kỳ & tài liệu ôn tập"
                                value="{{ old('title', $editingAnnouncement?->title ?? '') }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label small fw-semibold text-muted">
                                Nội dung chi tiết <span class="text-danger">*</span>
                            </label>
                            <textarea
                                name="body"
                                id="body"
                                rows="5"
                                class="form-control"
                                placeholder="Nhập nội dung thông báo gửi tới sinh viên..."
                                required
                            >{{ old('body', $editingAnnouncement?->body ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_pinned"
                                    value="1"
                                    id="is_pinned"
                                    @checked((bool) old('is_pinned', $editingAnnouncement?->is_pinned ?? false))
                                >
                                <label class="form-check-label small fw-semibold text-dark" for="is_pinned">
                                    Ghim thông báo này lên đầu danh sách lớp
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted d-block">Thời điểm đăng</label>
                            <div class="d-flex gap-3 mb-2">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="publish_mode"
                                        id="publish_now"
                                        value="now"
                                        @checked($publishMode === 'now')
                                        onchange="document.getElementById('scheduleTimeBox').classList.add('d-none');"
                                    >
                                    <label class="form-check-label small" for="publish_now">Đăng ngay</label>
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="publish_mode"
                                        id="publish_schedule"
                                        value="schedule"
                                        @checked($publishMode === 'schedule')
                                        onchange="document.getElementById('scheduleTimeBox').classList.remove('d-none');"
                                    >
                                    <label class="form-check-label small" for="publish_schedule">Hẹn giờ đăng</label>
                                </div>
                            </div>

                            <div id="scheduleTimeBox" class="{{ $publishMode === 'schedule' ? '' : 'd-none' }}">
                                <input
                                    type="datetime-local"
                                    name="published_at"
                                    class="form-control form-control-sm"
                                    value="{{ $publishedAtValue }}"
                                >
                            </div>
                        </div>

                        <div class="d-grid">
                            <button
                                type="submit"
                                class="btn fw-semibold text-white"
                                style="background-color: var(--sms-teal); border-color: var(--sms-teal);"
                            >
                                {{ $editingAnnouncement ? 'Lưu thay đổi thông báo' : 'Đăng thông báo' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="col-lg-7">
            <div class="sms-workspace-card mb-4" style="min-height: auto;">
                <form method="GET" action="{{ route('lecturer.announcements') }}" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label for="filter_class_section_id" class="form-label small fw-semibold text-muted">Lọc theo lớp</label>
                        <select
                            name="class_section_id"
                            id="filter_class_section_id"
                            class="form-select form-select-sm"
                            onchange="this.form.submit()"
                        >
                            <option value="">Tất cả lớp phụ trách ({{ $classes->count() }})</option>
                            @foreach ($classes as $classItem)
                                <option value="{{ $classItem->id }}" @selected($filters['class_section_id'] === $classItem->id)>
                                    {{ $classItem->code }} — {{ $classItem->course?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label for="q" class="form-label small fw-semibold text-muted">Tìm kiếm</label>
                        <input
                            type="text"
                            name="q"
                            id="q"
                            class="form-control form-control-sm"
                            placeholder="Tiêu đề hoặc nội dung..."
                            value="{{ $filters['q'] }}"
                        >
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-sm btn-outline-secondary fw-semibold">Lọc</button>
                    </div>
                </form>
            </div>

            <div class="sms-workspace-card p-0 overflow-hidden" style="min-height: auto;">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f7faf7;">
                    <span class="fw-bold" style="color: var(--sms-teal-dark); font-size: 14px;">
                        Danh sách thông báo đã tạo ({{ $announcements->total() }})
                    </span>
                </div>

                @if ($announcements->isEmpty())
                    <div class="p-4">
                        <div class="sms-workspace-notice mx-auto my-2">
                            <span class="sms-workspace-notice-icon">i</span>
                            <div>
                                <h2>Chưa có thông báo nào</h2>
                                <p>Chưa có thông báo lớp học phần nào khớp điều kiện lọc hiện tại.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="divide-y">
                        @foreach ($announcements as $item)
                            @php
                                $isScheduled = $item->published_at !== null && $item->published_at->isFuture();
                            @endphp
                            <div class="p-4 border-bottom">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge font-monospace border" style="background: #eef6f0; color: var(--sms-teal-dark);">
                                            {{ $item->classSection?->code }} · {{ $item->classSection?->course?->name }}
                                        </span>
                                        @if ($item->is_pinned)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                Đã ghim
                                            </span>
                                        @endif
                                        @if ($isScheduled)
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                                Hẹn giờ: {{ $item->published_at?->format('d/m/Y H:i') }}
                                            </span>
                                        @else
                                            <span class="small text-muted">
                                                Đăng lúc {{ $item->published_at?->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center gap-1">
                                        <form method="POST" action="{{ route('lecturer.announcements.pin', $item) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 12px;">
                                                {{ $item->is_pinned ? 'Bỏ ghim' : 'Ghim' }}
                                            </button>
                                        </form>

                                        <a
                                            href="{{ route('lecturer.announcements', array_filter(['class_section_id' => $filters['class_section_id'], 'q' => $filters['q'], 'edit' => $item->id])) }}"
                                            class="btn btn-sm btn-outline-primary py-0 px-2"
                                            style="font-size: 12px;"
                                        >
                                            Sửa
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('lecturer.announcements.destroy', $item) }}"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 12px;">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <h3 class="h6 fw-bold text-dark mb-1">{{ $item->title }}</h3>
                                <div class="text-secondary small" style="white-space: pre-line;">{{ $item->body }}</div>
                            </div>
                        @endforeach
                    </div>

                    @if ($announcements->hasPages())
                        <div class="px-4 py-3">
                            {{ $announcements->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
