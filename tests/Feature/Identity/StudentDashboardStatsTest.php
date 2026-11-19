<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Services\StudentMockService;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\CreatesRoles;
use Tests\TestCase;

class StudentDashboardStatsTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_is_redirected_to_login_from_student_dashboard(): void
    {
        $this->get('/student')->assertRedirect('/login');
    }

    public function test_lecturer_cannot_access_student_dashboard(): void
    {
        $lecturer = User::factory()->withRole(RoleName::Lecturer)->create();

        $this->actingAs($lecturer)->get('/student')->assertStatus(403);
    }

    public function test_admin_cannot_access_student_dashboard(): void
    {
        $admin = User::factory()->withRole(RoleName::Admin)->create();

        $this->actingAs($admin)->get('/student')->assertStatus(403);
    }

    public function test_student_can_view_dashboard_with_comprehensive_statistics(): void
    {
        /** @var Student $student */
        $student = Student::factory()->create();
        $user = $student->user;
        $progress = (object) [
            'earned_credits' => 36,
            'total_credits_required' => 120,
            'progress_percentage' => 30,
        ];
        $gpa = 2.75;
        $attendanceRate = 87.5;
        $warning = (object) [
            'has_warning' => true,
            'status_label' => 'Cảnh báo học vụ',
        ];
        $schedule = (object) [
            'course_code' => 'TEST101',
            'course_name' => 'Lập trình Web',
            'shift' => 'Ca 1',
            'periods' => 'Tiết 1–3',
            'room' => 'B203',
            'lecturer' => 'Nguyen Van An',
        ];

        $this->partialMock(StudentMockService::class, function (MockInterface $mock) use ($progress, $gpa, $attendanceRate, $warning, $schedule): void {
            $mock->shouldReceive('getStudentDashboardStats')->once()->andReturn((object) [
                'progress' => $progress,
                'gpa' => (object) ['gpa_4' => $gpa],
                'attendance' => (object) ['overall_rate' => $attendanceRate],
                'warning' => $warning,
                'today_schedules' => [$schedule],
            ]);
        });

        $response = $this->actingAs($user)->get('/student')
            ->assertOk()
            ->assertSee('Thống kê học tập')
            ->assertSee('<svg class="sms-credit-ring"', false)
            ->assertSee('aria-label="'.$progress->earned_credits.' trên '.$progress->total_credits_required.' tín chỉ"', false)
            ->assertSee('stroke-dasharray="'.$progress->progress_percentage.' 100"', false)
            ->assertSeeText($progress->earned_credits.' / '.$progress->total_credits_required)
            ->assertSee('role="meter" aria-label="GPA"', false)
            ->assertSeeText(number_format($gpa, 2).' / 4.00')
            ->assertSee('role="meter" aria-label="Chuyên cần"', false)
            ->assertSeeText(number_format($attendanceRate, 1).'%')
            ->assertSee('>Học vụ</dt>', false)
            ->assertSee($warning->status_label)
            ->assertSee('Lịch học trong ngày')
            ->assertSee($schedule->course_code)
            ->assertSee($schedule->course_name)
            ->assertSee($schedule->periods)
            ->assertSee($schedule->room);

        foreach ([
            'student.profile',
            'student.curriculum',
            'student.enrollment',
            'student.timetable',
            'student.grades',
            'student.attendance',
            'student.announcements',
        ] as $shortcut) {
            $response->assertSee('class="stretched-link" href="'.route($shortcut).'"', false);
        }
    }
}
