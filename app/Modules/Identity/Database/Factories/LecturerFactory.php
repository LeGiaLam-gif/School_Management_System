<?php

namespace App\Modules\Identity\Database\Factories;

use App\Modules\Academic\Models\Department;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lecturer>
 */
class LecturerFactory extends Factory
{
    protected $model = Lecturer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->lecturer(),
            'lecturer_code' => strtoupper(fake()->unique()->bothify('GV####')),
            'department_id' => Department::factory(),
            'academic_title' => fake()->randomElement(['ThS', 'TS', 'PGS.TS']),
            'phone' => fake()->numerify('09########'),
            'office' => fake()->bothify('P.###'),
        ];
    }
}
