<?php

namespace Tests\Feature\Shared;

use App\Modules\Identity\Models\User;
use App\Shared\Audit\AuditLog;
use App\Shared\Audit\AuditLogger;
use App\Shared\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class AuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_change_is_audited(): void
    {
        $user = User::factory()->create();
        $before = AuditLog::count();

        $user->update(['status' => UserStatus::Locked]);

        $this->assertSame($before + 1, AuditLog::count());

        $log = AuditLog::latest('id')->first();
        $this->assertSame('users.updated', $log->action);
        $this->assertSame($user->id, $log->auditable_id);
        $this->assertSame('active', $log->old_values['status']);
        $this->assertSame('locked', $log->new_values['status']);
    }

    public function test_password_is_never_written_to_the_audit_log(): void
    {
        $user = User::factory()->create();

        $user->update(['password' => 'new-secret', 'name' => 'Ten moi']);

        $log = AuditLog::latest('id')->first();
        $this->assertArrayNotHasKey('password', $log->new_values);
        $this->assertArrayNotHasKey('password', $log->old_values);
        $this->assertSame('Ten moi', $log->new_values['name']);
    }

    public function test_login_timestamp_changes_are_not_audited(): void
    {
        $user = User::factory()->create();
        $before = AuditLog::count();

        $user->forceFill(['last_login_at' => now()])->save();

        $this->assertSame($before, AuditLog::count());
    }

    public function test_manual_record_keeps_reason_and_actor(): void
    {
        $actor = User::factory()->admin()->create();
        $target = User::factory()->create();

        $this->actingAs($actor);

        $log = app(AuditLogger::class)->record('grades.unlocked', $target, ['locked' => true], ['locked' => false], 'Sai điểm giữa kỳ');

        $this->assertSame($actor->id, $log->user_id);
        $this->assertSame('Sai điểm giữa kỳ', $log->reason);
    }

    public function test_audit_log_is_append_only(): void
    {
        $log = app(AuditLogger::class)->record('test.action');

        try {
            $log->update(['action' => 'doi']);
            $this->fail('audit_logs không được cập nhật.');
        } catch (LogicException) {
            // mong doi
        }

        $this->expectException(LogicException::class);
        $log->delete();
    }
}
