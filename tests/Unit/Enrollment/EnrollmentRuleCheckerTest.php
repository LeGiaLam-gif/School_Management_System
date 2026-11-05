<?php

namespace Tests\Unit\Enrollment;

use App\Modules\Academic\Models\Semester;
use App\Modules\Enrollment\Services\EnrollmentRuleChecker;
use App\Modules\Enrollment\Services\Rules\CapacityRule;
use App\Modules\Enrollment\Services\Rules\ClassOpenRule;
use App\Modules\Enrollment\Services\Rules\EnrollmentRule;
use App\Modules\Enrollment\Services\Rules\RegistrationWindowRule;
use App\Modules\Enrollment\Support\EnrollmentContext;
use App\Modules\Identity\Models\Student;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Exceptions\BusinessRuleException;
use Tests\TestCase;

class EnrollmentRuleCheckerTest extends TestCase
{
    private function context(array $section = [], array $semester = []): EnrollmentContext
    {
        $sem = new Semester($semester + [
            'registration_open_at' => now()->subDay(),
            'registration_close_at' => now()->addDay(),
        ]);

        $cls = new ClassSection($section + [
            'capacity' => 40,
            'enrolled_count' => 10,
            'status' => ClassSectionStatus::Open,
        ]);
        $cls->setRelation('semester', $sem);

        return new EnrollmentContext(new Student, $cls, now());
    }

    public function test_runs_rules_in_order_and_stops_at_first_violation(): void
    {
        $calls = [];

        $make = function (string $name, bool $fail) use (&$calls): EnrollmentRule {
            return new class($name, $fail, $calls) implements EnrollmentRule
            {
                public function __construct(private string $name, private bool $fail, private array &$calls) {}

                public function check(EnrollmentContext $context): void
                {
                    $this->calls[] = $this->name;

                    if ($this->fail) {
                        throw new BusinessRuleException("fail {$this->name}", 'TEST');
                    }
                }
            };
        };

        $checker = new EnrollmentRuleChecker([$make('a', false), $make('b', true), $make('c', false)]);

        try {
            $checker->ensureEligible($this->context());
            $this->fail('Phải ném BusinessRuleException.');
        } catch (BusinessRuleException $e) {
            $this->assertSame('fail b', $e->getMessage());
        }

        $this->assertSame(['a', 'b'], $calls);
    }

    public function test_passes_when_all_rules_pass(): void
    {
        $checker = new EnrollmentRuleChecker([new ClassOpenRule, new RegistrationWindowRule, new CapacityRule]);

        $checker->ensureEligible($this->context());

        $this->addToAssertionCount(1);
    }

    public function test_class_open_rule_rejects_non_open_classes(): void
    {
        $this->expectException(BusinessRuleException::class);

        (new ClassOpenRule)->check($this->context(['status' => ClassSectionStatus::Draft]));
    }

    public function test_registration_window_rule_rejects_closed_window(): void
    {
        $this->expectException(BusinessRuleException::class);

        (new RegistrationWindowRule)->check($this->context([], [
            'registration_open_at' => now()->subDays(10),
            'registration_close_at' => now()->subDay(),
        ]));
    }

    public function test_registration_window_rule_rejects_unconfigured_window(): void
    {
        $this->expectException(BusinessRuleException::class);

        (new RegistrationWindowRule)->check($this->context([], [
            'registration_open_at' => null,
            'registration_close_at' => null,
        ]));
    }

    public function test_capacity_rule_rejects_full_class_but_allows_last_seat(): void
    {
        (new CapacityRule)->check($this->context(['capacity' => 40, 'enrolled_count' => 39]));

        $this->expectException(BusinessRuleException::class);
        (new CapacityRule)->check($this->context(['capacity' => 40, 'enrolled_count' => 40]));
    }
}
