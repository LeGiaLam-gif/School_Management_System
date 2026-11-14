<?php

namespace App\Modules\Announcement\Services;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\AnnouncementScope;
use App\Shared\Enums\EnrollmentStatus;
use Illuminate\Support\Collection;
use Throwable;

class StudentAnnouncementService
{
    /**
     * Lay danh sach thong bao danh cho sinh vien (Toan truong + Lop hoc phan da dang ky).
     *
     * @return Collection<int, object>
     */
    public function getAnnouncements(?User $user, ?string $scope = null): Collection
    {
        if ($user !== null) {
            try {
                $student = $user->student;
                $query = Announcement::published()->with(['author', 'classSection.course']);

                if ($student) {
                    $enrolledSectionIds = $student->enrollments()
                        ->where('status', EnrollmentStatus::Registered)
                        ->pluck('class_section_id')
                        ->all();

                    $query->where(function ($q) use ($enrolledSectionIds) {
                        $q->where('scope', AnnouncementScope::All->value)
                            ->orWhere(function ($sub) use ($enrolledSectionIds) {
                                $sub->where('scope', AnnouncementScope::ClassSection->value)
                                    ->whereIn('class_section_id', $enrolledSectionIds);
                            });
                    });
                } else {
                    $query->where('scope', AnnouncementScope::All->value);
                }

                $dbAnnouncements = $query->orderByDesc('is_pinned')->orderByDesc('published_at')->get();

                if ($dbAnnouncements->isNotEmpty()) {
                    return $dbAnnouncements->map(fn ($a) => (object) [
                        'id' => (string) $a->id,
                        'title' => $a->title,
                        'body' => $a->body,
                        'scope' => $a->scope->value,
                        'scope_label' => $a->scope === AnnouncementScope::All ? 'Toàn trường' : ('Lớp ' . ($a->classSection?->code ?? '')),
                        'scope_badge' => $a->scope === AnnouncementScope::All ? 'bg-primary' : 'bg-info text-dark',
                        'author_name' => $a->author?->name ?? 'Phòng Đào tạo',
                        'is_pinned' => $a->is_pinned,
                        'published_at' => $a->published_at ? $a->published_at->format('d/m/Y H:i') : 'N/A',
                        'summary' => mb_substr(strip_tags($a->body), 0, 160) . '...',
                    ]);
                }
            } catch (Throwable) {
                // Fallback Mock Data
            }
        }

        return $this->getMockAnnouncements($scope);
    }

    /**
     * Xem chi tiet thong bao.
     */
    public function getAnnouncementDetail(int|string $id, ?User $user): ?object
    {
        if ($user !== null && is_numeric($id)) {
            try {
                $a = Announcement::published()->with(['author', 'classSection.course'])->find((int) $id);
                if ($a) {
                    return (object) [
                        'id' => (string) $a->id,
                        'title' => $a->title,
                        'body' => $a->body,
                        'scope' => $a->scope->value,
                        'scope_label' => $a->scope === AnnouncementScope::All ? 'Toàn trường' : ('Lớp ' . ($a->classSection?->code ?? '')),
                        'scope_badge' => $a->scope === AnnouncementScope::All ? 'bg-primary' : 'bg-info text-dark',
                        'author_name' => $a->author?->name ?? 'Phòng Đào tạo',
                        'is_pinned' => $a->is_pinned,
                        'published_at' => $a->published_at ? $a->published_at->format('d/m/Y H:i') : 'N/A',
                    ];
                }
            } catch (Throwable) {
                // Fallback
            }
        }

        $all = $this->getMockAnnouncements();

        return $all->firstWhere('id', (string) $id);
    }

    /**
     * @return Collection<int, object>
     */
    private function getMockAnnouncements(?string $scope = null): Collection
    {
        $all = collect([
            (object) [
                'id' => '1',
                'title' => 'Kế hoạch Đăng ký học phần Học kỳ 1 năm học 2026 - 2027 và Quy định tín chỉ',
                'body' => "Phòng Đào tạo thông báo tới toàn thể sinh viên về kế hoạch đăng ký học phần Học kỳ 1 năm học 2026 - 2027:\n\n1. Thời gian đăng ký: từ 08:00 ngày 01/11/2026 đến 17:00 ngày 30/11/2026.\n2. Hạn cuối rút bớt học phần: ngày 05/12/2026.\n3. Quy định số tín chỉ: Sinh viên phải đăng ký tối thiểu 14 tín chỉ và tối đa không quá 24 tín chỉ theo quy chế đào tạo.\n4. Sinh viên cần kiểm tra kỹ điều kiện tiên quyết và tránh đăng ký trùng thời khóa biểu giữa các lớp học phần.\n\nMọi thắc mắc vui lòng liên hệ trực tiếp bàn tiếp sinh viên Phòng Đào tạo tại Tòa nhà A.",
                'scope' => 'all',
                'scope_label' => 'Toàn trường',
                'scope_badge' => 'bg-primary',
                'author_name' => 'Phòng Đào tạo & Quản lý Đào tạo',
                'is_pinned' => true,
                'published_at' => '01/11/2026 08:00',
                'summary' => 'Thông báo kế hoạch mở cổng đăng ký học phần Học kỳ 1 năm học 2026 - 2027, các mốc thời gian quan trọng và quy định số tín chỉ tối thiểu/tối đa...',
            ],
            (object) [
                'id' => '2',
                'title' => 'Thông báo nộp hồ sơ xét Học bổng khuyến khích học tập Học kỳ vừa qua',
                'body' => "Nhà trường thông báo xét cấp học bổng khuyến khích học tập cho sinh viên đạt thành tích xuất sắc và giỏi trong học kỳ vừa qua.\n\n- Điều kiện: Điểm trung bình tích lũy GPA từ 3.20 trở lên, điểm rèn luyện từ 80 điểm trở lên và không có học phần nào bị điểm F.\n- Hạn nộp hồ sơ: Trước ngày 20/11/2026 tại Văn phòng Khoa Công nghệ Thông tin.",
                'scope' => 'all',
                'scope_label' => 'Toàn trường',
                'scope_badge' => 'bg-primary',
                'author_name' => 'Phòng Công tác Sinh viên',
                'is_pinned' => true,
                'published_at' => '05/11/2026 09:30',
                'summary' => 'Hướng dẫn tiêu chuẩn xét duyệt và thời hạn nộp hồ sơ xin cấp học bổng khuyến khích học tập dành cho sinh viên có thành tích học tập từ loại Giỏi trở lên...',
            ],
            (object) [
                'id' => '3',
                'title' => '[Lớp CS102.O11] Nhắc nhở nộp Bài tập lớn Kỹ thuật lập trình và Lịch thi thực hành Lab',
                'body' => "Chào các bạn sinh viên lớp CS102.O11 - Kỹ thuật lập trình:\n\n- Hạn nộp Bài tập lớn (Báo cáo + Source code): 23:59 ngày 25/11/2026 qua hệ thống nộp bài.\n- Lịch thi thực hành máy: Sẽ diễn ra vào ca sáng ngày 28/11/2026 tại Phòng Lab 2.\n- Đề nghị các bạn có mặt đúng giờ và mang theo thẻ sinh viên.",
                'scope' => 'class_section',
                'scope_label' => 'Lớp CS102.O11',
                'scope_badge' => 'bg-info text-dark',
                'author_name' => 'ThS. Hoàng Tuấn Anh',
                'is_pinned' => false,
                'published_at' => '08/11/2026 14:15',
                'summary' => 'Giảng viên phụ trách thông báo thời hạn chốt nộp bài tập lớn và lịch thi thực hành tập trung tại phòng Lab 2 dành cho sinh viên lớp CS102.O11...',
            ],
            (object) [
                'id' => '4',
                'title' => '[Lớp SE201.O11] Cung cấp tài liệu tham khảo bài giảng Agile/Scrum & Template báo cáo Use Case',
                'body' => "Gửi các nhóm sinh viên lớp Nhập môn Công nghệ phần mềm SE201.O11:\n\nThầy đã đính kèm tài liệu về quy trình Agile/Scrum và bộ mẫu biểu đặc tả yêu cầu người dùng (SRS Template). Các nhóm tải về và hoàn thiện đồ án môn học theo đúng tiến độ Sprint 1.",
                'scope' => 'class_section',
                'scope_label' => 'Lớp SE201.O11',
                'scope_badge' => 'bg-info text-dark',
                'author_name' => 'TS. Trần Văn Hùng',
                'is_pinned' => false,
                'published_at' => '10/11/2026 10:00',
                'summary' => 'Tài liệu hướng dẫn quy trình phát triển phần mềm Agile/Scrum và biểu mẫu phục vụ báo cáo đồ án giữa kỳ của lớp SE201.O11...',
            ],
            (object) [
                'id' => '5',
                'title' => 'Hội thảo Công nghệ: Xu hướng kiến trúc Microservices & AI Engineering trong năm 2026',
                'body' => "Khoa Công nghệ Thông tin phối hợp cùng các doanh nghiệp phần mềm đối tác tổ chức buổi Workshop chuyên đề dành cho sinh viên CNTT & Kỹ thuật Phần mềm:\n\n- Thời gian: 08:30 Thứ Bảy ngày 21/11/2026.\n- Địa điểm: Hội trường Lớn (Nhà E).\n- Khách mời: Các kỹ sư trưởng (Principal Architects) đến từ các tập đoàn công nghệ hàng đầu.",
                'scope' => 'all',
                'scope_label' => 'Khoa CNTT',
                'scope_badge' => 'bg-secondary',
                'author_name' => 'Khoa Công nghệ Thông tin',
                'is_pinned' => false,
                'published_at' => '12/11/2026 16:45',
                'summary' => 'Buổi giao lưu và chia sẻ kinh nghiệm thực chiến từ các chuyên gia doanh nghiệp về kiến trúc phần mềm phân tán và kỹ thuật tích hợp trí tuệ nhân tạo...',
            ],
        ]);

        if ($scope === 'all') {
            return $all->where('scope', 'all')->values();
        }

        if ($scope === 'class_section') {
            return $all->where('scope', 'class_section')->values();
        }

        return $all->values();
    }
}
