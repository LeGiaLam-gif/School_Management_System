@extends('layouts.student')

@section('title', 'Danh mục môn học toàn trường · Cổng Sinh Viên')

@section('content')
<div class="row g-4">
    <!-- Header & Bộ lọc tìm kiếm -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">
                            🔍 Tra Cứu Danh Mục Môn Học
                        </h3>
                        <p class="text-muted mb-0">
                            Tìm kiếm và tra cứu điều kiện tiên quyết của tất cả học phần trong nhà trường.
                        </p>
                    </div>

                    <a href="{{ route('student.curriculum') }}" class="btn btn-outline-primary px-3 py-2 fw-semibold">
                        ← Xem Khung CTĐT của tôi
                    </a>
                </div>

                <!-- Form tìm kiếm & lọc -->
                <form method="GET" action="{{ route('student.courses') }}">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-muted">Từ khóa tìm kiếm</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="keyword" 
                                   value="{{ $filters['keyword'] ?? '' }}" 
                                   placeholder="Nhập mã môn (VD: CS101) hoặc tên môn học...">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Khoa phụ trách</label>
                            <select class="form-select" name="department">
                                <option value="">-- Tất cả các khoa --</option>
                                <option value="CNTT" {{ ($filters['department'] ?? '') === 'CNTT' ? 'selected' : '' }}>Khoa Công nghệ Thông tin</option>
                                <option value="MMT" {{ ($filters['department'] ?? '') === 'MMT' ? 'selected' : '' }}>Khoa Mạng & Truyền thông</option>
                                <option value="KHDL" {{ ($filters['department'] ?? '') === 'KHDL' ? 'selected' : '' }}>Khoa Khoa học Dữ liệu</option>
                                <option value="TOAN" {{ ($filters['department'] ?? '') === 'TOAN' ? 'selected' : '' }}>Khoa Toán - Tin</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Số tín chỉ</label>
                            <select class="form-select" name="credits">
                                <option value="">-- Tất cả tín chỉ --</option>
                                <option value="2" {{ ($filters['credits'] ?? '') == 2 ? 'selected' : '' }}>2 Tín chỉ</option>
                                <option value="3" {{ ($filters['credits'] ?? '') == 3 ? 'selected' : '' }}>3 Tín chỉ</option>
                                <option value="4" {{ ($filters['credits'] ?? '') == 4 ? 'selected' : '' }}>4 Tín chỉ</option>
                                <option value="8" {{ ($filters['credits'] ?? '') == 8 ? 'selected' : '' }}>8 Tín chỉ</option>
                                <option value="10" {{ ($filters['credits'] ?? '') == 10 ? 'selected' : '' }}>10 Tín chỉ</option>
                            </select>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('student.courses') }}" class="btn btn-light border px-4">
                                Đặt lại
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Danh sách kết quả -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    Kết quả tra cứu ({{ $courses->count() }} môn học)
                </h6>
                <span class="text-muted small">Nhấp mã học phần để xem chi tiết đầy đủ</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4" style="width: 140px;">Mã HP</th>
                            <th>Tên học phần</th>
                            <th class="text-center" style="width: 100px;">Số TC</th>
                            <th class="text-center" style="width: 140px;">Số tiết (LT/TH)</th>
                            <th style="width: 200px;">Khoa phụ trách</th>
                            <th style="width: 220px;">Học phần tiên quyết</th>
                            <th class="text-end pe-4" style="width: 120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $c)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('student.courses.detail', $c->code) }}" 
                                       class="fw-bold font-monospace text-primary text-decoration-none">
                                        {{ $c->code }}
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $c->name }}</span>
                                    <div class="text-muted small text-truncate" style="max-width: 380px;">
                                        {{ $c->description }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                        {{ $c->credits }} TC
                                    </span>
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $c->theory_periods }} LT / {{ $c->practice_periods }} TH
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $c->department_name }}</span>
                                </td>
                                <td>
                                    @if (!empty($c->prerequisites))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($c->prerequisites as $prereq)
                                                <a href="{{ route('student.courses.detail', $prereq) }}" 
                                                   class="badge bg-secondary-subtle text-secondary border text-decoration-none font-monospace">
                                                    {{ $prereq }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small">Không</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('student.courses.detail', $c->code) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        Chi tiết →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <div class="fs-1 mb-2">🔍</div>
                                        <h6 class="fw-bold">Không tìm thấy môn học nào phù hợp</h6>
                                        <p class="small mb-0">Vui lòng thử tìm với từ khóa hoặc điều kiện lọc khác.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
