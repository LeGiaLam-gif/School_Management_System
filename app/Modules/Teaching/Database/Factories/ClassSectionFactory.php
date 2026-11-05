<?php

namespace App\Modules\Teaching\Database\Factories;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Semester;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSection>
 */
class ClassSectionFactory extends Factory
{
    protected $model = ClassSection::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'semester_id' => Semester::factory(),
            'lecturer_id' => null,
            'code' => '01',
            'capacity' => 40,
            'enrolled_count' => 0,
            'status' => ClassSectionStatus::Open,
        ];
    }

    public function status(ClassSectionStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }

    public function capacity(int $capacity, int $enrolled = 0): static
    {
        return $this->state(fn () => ['capacity' => $capacity, 'enrolled_count' => $enrolled]);
    }
}
