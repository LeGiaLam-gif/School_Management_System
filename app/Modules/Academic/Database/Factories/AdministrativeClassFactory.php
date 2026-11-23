<?php

namespace App\Modules\Academic\Database\Factories;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdministrativeClass>
 */
class AdministrativeClassFactory extends Factory
{
    protected $model = AdministrativeClass::class;

    public function definition(): array
    {
        $academicYear = fake()->numberBetween(2022, 2026);

        return [
            'code' => strtoupper(substr((string) $academicYear, -2).fake()->unique()->bothify('???##')),
            'name' => 'Lớp hành chính '.fake()->unique()->words(2, true),
            'program_id' => Program::factory(),
            'academic_year' => $academicYear,
            'advisor_lecturer_id' => null,
        ];
    }
}
