@extends('layouts.app')

@section('title', 'Đăng ký học phần')

@section('content')
<style>
    .sms-student-enrollment {
        --bs-primary-rgb: 33, 134, 110;
        --sms-teal: #35b394;
        --sms-teal-dark: #21866e;
    }

    .sms-student-enrollment .card {
        border: 1px solid #d1eadf !important;
        border-radius: 15px !important;
        box-shadow: 0 6px 18px rgba(43, 62, 54, .045) !important;
    }

    .sms-student-enrollment .card-header {
        background: linear-gradient(110deg, #d5f4e5, #effbf5) !important;
        border-bottom: 1px solid #c5e8d7 !important;
        color: #217b63;
    }

    .sms-student-enrollment .card-header h5,
    .sms-student-enrollment .card-body h3 {
        color: #217b63 !important;
    }

    .sms-student-enrollment .text-primary {
        color: #21866e !important;
    }

    .sms-student-enrollment .bg-primary {
        background-color: #21866e !important;
    }

    .sms-student-enrollment .bg-light {
        background-color: #f3fbf7 !important;
        border-color: #d8eee2 !important;
    }

    .sms-student-enrollment .table > thead > tr > th {
        --bs-table-bg: #f5f6f8;
        --bs-table-color: #53616b;
        border-color: #d8eee2;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .sms-student-enrollment .table > tbody > tr > td {
        --bs-table-bg: #fff;
        --bs-table-hover-bg: #f3fbf7;
        border-color: #e1ece6;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .sms-student-enrollment .sms-registration-open {
        background: #eef9e9;
        color: #387d33;
        border: 1px solid #a9d99a;
    }

    .sms-student-enrollment > .col-12:last-child .card-header {
        background: linear-gradient(110deg, #e8f2ff, #f6faff) !important;
        border-bottom-color: #d5e5f7 !important;
    }

    .sms-student-enrollment > .col-12:last-child .card-header h5 {
        color: #3973a8 !important;
    }

    .sms-student-enrollment .btn-outline-danger {
        --bs-btn-color: #c45760;
        --bs-btn-border-color: #efbdc2;
        --bs-btn-bg: #fff5f5;
    }

    .sms-student-enrollment .badge.bg-light {
        background-color: #fff8e8 !important;
        border-color: #efdfb4 !important;
        color: #8b6926 !important;
    }

    .sms-student-enrollment .btn-primary {
        --bs-btn-bg: #21866e;
        --bs-btn-border-color: #21866e;
        --bs-btn-hover-bg: #196b58;
        --bs-btn-hover-border-color: #196b58;
    }

    .sms-student-enrollment .form-control:focus {
        border-color: #70cbae;
        box-shadow: 0 0 0 .2rem rgba(53, 179, 148, .15);
    }

    .sms-enrollment-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .sms-student-enrollment .sms-enrollment-section-code {
        display: inline-block;
        padding: .15rem .5rem;
        border-radius: .375rem;
        background: #e3f1ea;
        color: #21866e;
        font-weight: 600;
    }
    .sms-student-enrollment > .col-12:first-child > .card {
        background: linear-gradient(112deg, #d6a36e 0%, #a9a574 52%, #498f76 100%);
        color: #fff;
    }
    .sms-student-enrollment > .col-12:first-child .card-body h3,
    .sms-student-enrollment > .col-12:first-child .card-body > .d-flex > div:first-child .text-muted {
        color: #fff !important;
    }
    .sms-credit-summary {
        flex-shrink: 0;
        color: #455b53;
    }

    .sms-credit-summary__icon {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 3.25rem;
        height: 3.25rem;
        border: 1px solid #bde4d6;
        border-radius: 1rem;
        background: #e3f5ee;
        color: #217b63;
    }

    .sms-credit-summary__value {
        color: #217b63;
    }

    .sms-credit-summary__value--over-limit {
        color: #bf5661;
    }

    .sms-credit-summary__warning {
        background-color: #f8dfe2;
        color: #953f49;
        white-space: normal;
    }
</style>
<div class="row g-4 sms-student-enrollment" x-data="{ searchKeyword: '' }">
    <!-- Header thông tin đợt đăng ký & Tín chỉ -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge sms-registration-open px-3 py-2 rounded-3 fs-7">
                                ĐANG MỞ
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-2 font-monospace">
                                {{ $data->semester->code }}
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-dark">
                            Đăng ký học phần: {{ $data->semester->name }}
                        </h3>
                        <p class="text-muted small mb-0">
                            <span class="d-inline-block me-4"><strong>{{ $data->semester->registration_open_at }}</strong> đến <strong>{{ $data->semester->registration_close_at }}</strong></span>
                            <span class="d-inline-block">Rút học phần trước <strong>{{ $data->semester->drop_deadline }}</strong></span>
                        </p>
                    </div>

                    <!-- Thống kê tín chỉ sinh viên -->
                    <div class="sms-credit-summary d-flex align-items-center gap-3 bg-light py-2 px-3 rounded-3 border">
                        <span class="sms-credit-summary__icon" aria-hidden="true">
                            <svg class="sms-enrollment-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 4H5v17h14V4h-4M9 2h6v4H9zM8 13l3 3 5-6" />
                            </svg>
                        </span>
                        <div>
                            <p class="mb-1">
                                <strong @class(['sms-credit-summary__value', 'sms-credit-summary__value--over-limit' => $data->total_credits > $data->max_credits])>{{ $data->total_credits }}</strong><span>/{{ $data->max_credits }} tín chỉ</span>
                            </p>
                            <p class="small border-top pt-1 mb-1">Tối thiểu {{ $data->min_credits }} tín chỉ</p>
                            @if ($data->total_credits > $data->max_credits)
                                <span class="badge sms-credit-summary__warning">Vượt giới hạn {{ $data->total_credits - $data->max_credits }} tín chỉ</span>
                            @elseif ($data->total_credits < $data->min_credits)
                                <span class="badge bg-warning text-dark">Còn thiếu {{ $data->min_credits - $data->total_credits }} tín chỉ</span>
                            @else
                                <span class="badge bg-success">Trong giới hạn</span>
                            @endif
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Bảng 1: Các lớp học phần ĐÃ ĐĂNG KÝ -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <svg class="sms-enrollment-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 4H5v17h14V4h-4M9 2h6v4H9zM8 13l3 3 5-6" />
                        </svg>
                        <span>Lớp học phần đã đăng ký</span>
                    </h5>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4" style="width: 50px;">STT</th>
                            <th style="width: 130px;">Mã lớp HP</th>
                            <th>Tên học phần</th>
                            <th class="text-center" style="width: 90px;">Số tín chỉ</th>
                            <th style="width: 180px;">Giảng viên</th>
                            <th style="width: 250px;">Lịch học</th>
                            <th style="width: 160px;">Thời gian</th>
                            <th class="text-center px-3" style="width: 130px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data->enrolled_sections as $index => $item)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td>
                                    <span class="sms-enrollment-section-code font-monospace">{{ $item->code }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->course_name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        {{ $item->credits }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small fw-bold text-dark">{{ $item->lecturer_name }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="small fw-bold text-dark">{{ $item->schedule_text }} <span class="ms-2">{{ $item->room_name }}</span></span>
                                </td>
                                <td>
                                    <span class="small fw-bold text-dark">{{ $item->registered_at ?? 'Đã đăng ký' }}</span>
                                </td>
                                <td class="text-center px-3">
                                    <form method="POST" action="{{ route('student.enrollment.drop') }}" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn hủy đăng ký lớp học phần {{ $item->code }} ({{ $item->course_name }})?');"
                                          class="d-inline">
                                        @csrf
                                        <input type="hidden" name="class_section_id" value="{{ $item->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Hủy môn
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <div class="fs-2 mb-1">📝</div>
                                    <span class="fw-semibold">Bạn chưa đăng ký lớp học phần nào trong học kỳ này.</span>
                                    <p class="small text-muted mb-0">Vui lòng chọn các lớp học phần bên dưới để đăng ký.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bảng 2: Danh sách lớp học phần MỞ ĐỂ ĐĂNG KÝ -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <svg class="sms-enrollment-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 0 4 22zM4 5.5v16M8 7h8M8 11h8" />
                        </svg>
                        <span>Danh sách lớp học phần mở trong kỳ</span>
                    </h5>
                </div>

                <!-- Input tìm kiếm nhanh -->
                <div style="min-width: 280px;">
                    <input type="text" 
                           class="form-control form-control-sm" 
                           placeholder="🔍 Lọc nhanh theo mã lớp, tên môn..." 
                           x-model="searchKeyword">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4" style="width: 140px;">Mã lớp HP</th>
                            <th>Tên học phần</th>
                            <th class="text-center" style="width: 90px;">Số tín chỉ</th>
                            <th style="width: 180px;">Giảng viên</th>
                            <th style="width: 250px;">Lịch học</th>
                            <th class="text-center" style="width: 130px;">Sĩ số</th>
                            <th class="text-center px-3" style="width: 140px;">Đăng ký</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->available_sections as $sec)
                            @php
                                $isFull = $sec->enrolled_count >= $sec->capacity;
                                $searchTarget = strtolower($sec->code . ' ' . $sec->course_name . ' ' . $sec->lecturer_name);
                            @endphp
                            <tr x-show="searchKeyword === '' || '{{ $searchTarget }}'.includes(searchKeyword.toLowerCase())">
                                <td class="ps-4">
                                    <span class="sms-enrollment-section-code font-monospace">{{ $sec->code }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $sec->course_name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        {{ $sec->credits }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small fw-bold text-dark">{{ $sec->lecturer_name }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="small fw-bold text-dark">{{ $sec->schedule_text }} <span class="ms-2">{{ $sec->room_name }}</span></span>
                                </td>
                                <td class="text-center">
                                    <span class="small fw-bold text-dark">{{ $sec->enrolled_count }} / {{ $sec->capacity }}</span>
                                    <div>
                                        @if ($isFull)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Hết chỗ</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center px-3">
                                    @if (!empty($sec->is_enrolled))
                                        <span class="badge bg-success px-3 py-2">
                                            ✓ Đã đăng ký
                                        </span>
                                    @elseif ($isFull)
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            Hết chỗ
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('student.enrollment.register') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="class_section_id" value="{{ $sec->id }}">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                + Đăng ký
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

