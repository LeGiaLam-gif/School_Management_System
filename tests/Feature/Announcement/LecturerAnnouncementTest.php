<?php

namespace Tests\Feature\Announcement;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Semester;
use App\Modules\Announcement\Models\Announcement;
use App\Modules\Identity\Database\Seeders\RolePermissionSeeder;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\AnnouncementScope;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturerAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_lecturer_can_create_pin_update_and_delete_own_class_announcement(): void
    {
        $department = Department::query()->create(['code' => 'CNTT', 'name' => 'Công nghệ thông tin']);
        $semester = Semester::query()->create([
            'code' => '2026-HK1',
            'name' => 'Học kỳ 1',
            'academic_year' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-15',
            'is_current' => true,
        ]);
        $course = Course::query()->create([
            'department_id' => $department->id,
            'code' => 'INT101',
            'name' => 'Lập trình Web',
            'credits' => 3,
        ]);

        /** @var User $lecturerUser */
        $lecturerUser = User::factory()->create();
        $lecturerUser->assignRole('lecturer');
        $lecturer = Lecturer::query()->create([
            'user_id' => $lecturerUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV001',
        ]);

        $section = ClassSection::query()->create([
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $lecturer->id,
            'code' => 'INT101-01',
            'capacity' => 40,
            'enrolled_count' => 10,
            'status' => ClassSectionStatus::InProgress,
        ]);

        $this->actingAs($lecturerUser)
            ->post(route('lecturer.announcements.store'), [
                'class_section_id' => $section->id,
                'title' => 'Thông báo lịch thi giữa kỳ',
                'body' => 'Sinh viên mang theo thẻ sinh viên khi vào phòng thi.',
                'is_pinned' => '1',
                'publish_mode' => 'now',
            ])
            ->assertRedirect();

        /** @var Announcement $announcement */
        $announcement = Announcement::query()->where('class_section_id', $section->id)->firstOrFail();
        $this->assertSame('Thông báo lịch thi giữa kỳ', $announcement->title);
        $this->assertSame(AnnouncementScope::ClassSection, $announcement->scope);
        $this->assertTrue($announcement->is_pinned);

        $this->actingAs($lecturerUser)
            ->patch(route('lecturer.announcements.pin', $announcement))
            ->assertRedirect();

        $this->assertFalse($announcement->fresh()?->is_pinned);

        $this->actingAs($lecturerUser)
            ->put(route('lecturer.announcements.update', $announcement), [
                'class_section_id' => $section->id,
                'title' => 'Thông báo lịch thi giữa kỳ (Cập nhật)',
                'body' => 'Phòng thi chuyển sang B204.',
                'publish_mode' => 'now',
            ])
            ->assertRedirect();

        $this->assertSame('Thông báo lịch thi giữa kỳ (Cập nhật)', $announcement->fresh()?->title);

        $this->actingAs($lecturerUser)
            ->delete(route('lecturer.announcements.destroy', $announcement))
            ->assertRedirect();

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_lecturer_cannot_manage_other_lecturers_or_school_wide_announcements(): void
    {
        $department = Department::query()->create(['code' => 'CNTT2', 'name' => 'Khoa CNTT']);
        $semester = Semester::query()->create([
            'code' => '2026-HK2',
            'name' => 'Học kỳ 2',
            'academic_year' => '2026-2027',
            'start_date' => '2027-02-01',
            'end_date' => '2027-06-15',
            'is_current' => false,
        ]);
        $course = Course::query()->create([
            'department_id' => $department->id,
            'code' => 'INT202',
            'name' => 'Cơ sở dữ liệu',
            'credits' => 3,
        ]);

        /** @var User $ownerUser */
        $ownerUser = User::factory()->create();
        $ownerUser->assignRole('lecturer');
        $owner = Lecturer::query()->create([
            'user_id' => $ownerUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV010',
        ]);

        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        $otherUser->assignRole('lecturer');
        Lecturer::query()->create([
            'user_id' => $otherUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV011',
        ]);

        $otherSection = ClassSection::query()->create([
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $owner->id,
            'code' => 'INT202-01',
            'capacity' => 40,
            'enrolled_count' => 5,
            'status' => ClassSectionStatus::InProgress,
        ]);

        $otherAnnouncement = Announcement::query()->create([
            'author_id' => $ownerUser->id,
            'class_section_id' => $otherSection->id,
            'scope' => AnnouncementScope::ClassSection,
            'title' => 'Thông báo lớp khác',
            'body' => 'Nội dung lớp khác',
            'published_at' => now(),
        ]);

        $schoolAnnouncement = Announcement::query()->create([
            'author_id' => $ownerUser->id,
            'class_section_id' => null,
            'scope' => AnnouncementScope::All,
            'title' => 'Thông báo toàn trường',
            'body' => 'Nội dung toàn trường',
            'published_at' => now(),
        ]);

        $this->actingAs($otherUser)
            ->post(route('lecturer.announcements.store'), [
                'class_section_id' => $otherSection->id,
                'title' => 'Gửi trộm',
                'body' => 'Nội dung',
            ])
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->delete(route('lecturer.announcements.destroy', $otherAnnouncement))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->delete(route('lecturer.announcements.destroy', $schoolAnnouncement))
            ->assertForbidden();
    }
}
