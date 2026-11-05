<?php

namespace App\Modules\Announcement\Database\Seeders;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\AnnouncementScope;
use Illuminate\Database\Seeder;

/**
 * Thong bao mau (chuyen tu CourseSectionSeeder cu): moi lop 1 thong bao + 1 thong bao toan truong.
 */
class AnnouncementDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        foreach (ClassSection::with(['course', 'lecturer'])->orderBy('id')->get() as $section) {
            Announcement::firstOrCreate(
                ['class_section_id' => $section->id, 'title' => 'Thông báo lớp '.$section->course->name],
                [
                    'author_id' => $section->lecturer->user_id,
                    'scope' => AnnouncementScope::ClassSection,
                    'body' => 'Sinh viên chuẩn bị tài liệu và xem trước nội dung buổi học tới.',
                    'published_at' => now(),
                ]
            );
        }

        Announcement::firstOrCreate(['scope' => AnnouncementScope::All->value, 'title' => 'Chào mừng học kỳ mới'], [
            'author_id' => $admin->id,
            'body' => 'Sinh viên đăng ký học phần trước hạn chót theo kế hoạch của phòng đào tạo.',
            'is_pinned' => true,
            'published_at' => now(),
        ]);
    }
}
