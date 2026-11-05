<?php

namespace App\Modules\Enrollment\Database\Factories;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\Student;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'class_section_id' => ClassSection::factory(),
            'status' => EnrollmentStatus::Registered,
            'registered_at' => now(),
        ];
    }

    public function status(EnrollmentStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
