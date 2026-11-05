<?php

namespace Tests\Unit\Shared;

use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\ClassSectionStatus;
use PHPUnit\Framework\TestCase;

class ClassSectionStatusTest extends TestCase
{
    public function test_forward_lifecycle_only(): void
    {
        $this->assertTrue(ClassSectionStatus::Draft->canTransitionTo(ClassSectionStatus::Open));
        $this->assertTrue(ClassSectionStatus::Open->canTransitionTo(ClassSectionStatus::InProgress));
        $this->assertTrue(ClassSectionStatus::InProgress->canTransitionTo(ClassSectionStatus::Finished));
        $this->assertTrue(ClassSectionStatus::Finished->canTransitionTo(ClassSectionStatus::GradesPublished));
    }

    public function test_cannot_skip_or_go_back_except_unlock(): void
    {
        $this->assertFalse(ClassSectionStatus::Draft->canTransitionTo(ClassSectionStatus::InProgress));
        $this->assertFalse(ClassSectionStatus::Open->canTransitionTo(ClassSectionStatus::Draft));
        $this->assertFalse(ClassSectionStatus::Finished->canTransitionTo(ClassSectionStatus::InProgress));
        $this->assertFalse(ClassSectionStatus::GradesPublished->canTransitionTo(ClassSectionStatus::Open));
    }

    public function test_unlock_is_the_only_backward_transition(): void
    {
        $this->assertTrue(ClassSectionStatus::GradesPublished->canTransitionTo(ClassSectionStatus::Finished));
        $this->assertTrue(ClassSectionStatus::GradesPublished->isUnlockTransition(ClassSectionStatus::Finished));
        $this->assertFalse(ClassSectionStatus::Finished->isUnlockTransition(ClassSectionStatus::GradesPublished));
    }

    public function test_only_open_sections_accept_enrollment(): void
    {
        foreach (ClassSectionStatus::cases() as $status) {
            $this->assertSame($status === ClassSectionStatus::Open, $status->acceptsEnrollment());
        }
    }

    public function test_only_absent_counts_as_absence(): void
    {
        foreach (AttendanceStatus::cases() as $status) {
            $this->assertSame($status === AttendanceStatus::Absent, $status->countsAsAbsence());
        }
    }
}
