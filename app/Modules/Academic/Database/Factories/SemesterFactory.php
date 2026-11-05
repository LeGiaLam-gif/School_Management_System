<?php

namespace App\Modules\Academic\Database\Factories;

use App\Modules\Academic\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Semester>
 */
class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2030, 2090);

        return [
            'code' => $year.'-1',
            'name' => 'Học kỳ 1',
            'academic_year' => $year.'-'.($year + 1),
            'start_date' => $year.'-09-01',
            'end_date' => ($year + 1).'-01-15',
            'registration_open_at' => null,
            'registration_close_at' => null,
            'drop_deadline' => null,
            'min_credits' => 14,
            'max_credits' => 24,
            'is_current' => false,
        ];
    }

    public function current(): static
    {
        return $this->state(fn () => ['is_current' => true]);
    }

    /**
     * Dot dang ky dang mo quanh thoi diem hien tai.
     */
    public function registrationOpen(): static
    {
        return $this->state(fn () => [
            'registration_open_at' => now()->subDays(3),
            'registration_close_at' => now()->addDays(14),
            'drop_deadline' => now()->addDays(10)->toDateString(),
        ]);
    }
}
