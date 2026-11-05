<?php

namespace App\Modules\Academic\Database\Factories;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'code' => strtoupper(fake()->unique()->bothify('??###')),
            'name' => fake()->unique()->words(3, true),
            'credits' => fake()->numberBetween(2, 4),
            'theory_periods' => 30,
            'practice_periods' => 15,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function credits(int $credits): static
    {
        return $this->state(fn () => ['credits' => $credits]);
    }
}
