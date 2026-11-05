<?php

namespace App\Modules\Academic\Database\Factories;

use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'code' => strtoupper(fake()->unique()->lexify('????')),
            'name' => 'Ngành '.fake()->unique()->words(2, true),
            'total_credits_required' => 120,
            'duration_years' => 4,
        ];
    }
}
