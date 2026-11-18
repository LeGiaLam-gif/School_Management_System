@extends('layouts.app')

@section('title', 'Đăng ký học phần · Cổng Sinh Viên')

@section('content')
<div class="row g-4" x-data="{ searchKeyword: '' }">
    <!-- Header thông tin đợt đăng ký & Tín chỉ -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success px-3 py-2 rounded-pill fs-7">
                                ● Cổng đăng ký ĐANG MỞ
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-2 font-monospace">
                                {{ $data->semester->code }}
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-dark">
                            Đăng Ký Học Phần: {{ $data->semester->name }}
                        </h3>
                        <p class="text-muted small mb-0">
                            Thời gian mở đăng ký: <strong>{{ $data->semester->registration_open_at }}</strong> đến <strong>{{ $data->semester->registration_close_at }}</strong> · 
                            Hạn rút học phần: <strong>{{ $data->semester->drop_deadline }}</strong>
                        </p>
                    </div>

                    <!-- Thống kê tín chỉ sinh viên -->
                    <div class="bg-light p-3 rounded-3 border text-center text-md-end min-w-200">
                        <span class="text-muted small fw-semibold">Tín chỉ đã chọn:</span>
                        <div class="d-flex align-items-baseline justify-content-center justify-content-md-end gap-1 mt-1">
                            <h2 class="fw-bold text-primary mb-0">{{ $data->total_credits }}</h2>
                            <span class="text-muted fs-6">/ {{ $data->max_credits }} TC</span>
                        </div>
                        <span class="badge {{ $data->total_credits >= $data->min_credits ? 'bg-success' : 'bg-warning text-dark' }} mt-1">
                            {{ $data->total_credits >= $data->min_credits ? '✓ Đạt ngưỡng tối thiểu' : 'Chưa đạt tối thiểu ('.$data->min_credits.' TC)' }}
                        </span>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Thanh tiến trình tín chỉ -->
                <div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Tiến trình tín chỉ đăng ký</span>
                        <span>Quy định: Tối thiểu <strong>{{ $data->min_credits }} TC</strong> - Tối đa <strong>{{ $data->max_credits }} TC</strong></span>
                    </div>
                    @php
                        $progressPercent = min(100, round(($data->total_credits / $data->max_credits) * 100));
                        $barColor = $data->total_credits < $data->min_credits ? 'bg-warning' : ($data->total_credits <= $data->max_credits ? 'bg-primary' : 'bg-danger');
                    @endphp
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $progressPercent }}%;" aria-valuenow="{{ $data->total_credits }}" aria-valuemin="0" aria-valuemax="{{ $data->max_credits }}"></div>
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
                        <span>📋 Lớp học phần đã đăng ký</span>
                        <span class="badge bg-primary rounded-pill">{{ count($data->enrolled_sections) }} môn</span>
                    </h5>
                    <span class="text-muted small">Các lớp đã được ghi nhận trong thời khóa biểu chính thức của bạn</span>
                </div>
                <span class="badge bg-light text-primary border px-3 py-2 fw-semibold">
                    Tổng: {{ $data->total_credits }} Tín chỉ
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4" style="width: 50px;">STT</th>
                            <th style="width: 130px;">Mã lớp HP</th>
                            <th>Tên học phần</th>
                            <th class="text-center" style="width: 90px;">Số TC</th>
                            <th style="width: 180px;">Giảng viên</th>
                            <th style="width: 250px;">Lịch học & Phòng</th>
                            <th style="width: 160px;">Thời gian ĐK</th>
                            <th class="text-end pe-4" style="width: 130px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data->enrolled_sections as $index => $item)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold font-monospace text-primary">{{ $item->code }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->course_name }}</span>
                                    <div class="small text-muted font-monospace">{{ $item->course_code }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        {{ $item->credits }} TC
                                    </span>
                                </td>
                                <td>
                                    <span class="small">{{ $item->lecturer_name }}</span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $item->schedule_text }}</div>
                                    <div class="small text-muted">{{ $item->room_name }}</div>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $item->registered_at ?? 'Đã đăng ký' }}</span>
                                </td>
                                <td class="text-end pe-4">
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
                        <span>📚 Danh sách lớp học phần mở trong kỳ</span>
                    </h5>
                    <span class="text-muted small">Chọn lớp phù hợp với thời khóa biểu và nhu cầu học tập của bạn</span>
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
                            <th class="text-center" style="width: 90px;">Số TC</th>
                            <th style="width: 180px;">Giảng viên</th>
                            <th style="width: 250px;">Lịch học & Phòng</th>
                            <th class="text-center" style="width: 130px;">Sĩ số</th>
                            <th class="text-end pe-4" style="width: 140px;">Đăng ký</th>
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
                                    <span class="fw-bold font-monospace text-primary">{{ $sec->code }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $sec->course_name }}</span>
                                    <div class="small text-muted font-monospace">{{ $sec->course_code }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        {{ $sec->credits }} TC
                                    </span>
                                </td>
                                <td>
                                    <span class="small">{{ $sec->lecturer_name }}</span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $sec->schedule_text }}</div>
                                    <div class="small text-muted">{{ $sec->room_name }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="small fw-semibold">{{ $sec->enrolled_count }} / {{ $sec->capacity }}</span>
                                    <div>
                                        @if ($isFull)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Hết chỗ</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Còn {{ $sec->capacity - $sec->enrolled_count }} chỗ</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end pe-4">
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

