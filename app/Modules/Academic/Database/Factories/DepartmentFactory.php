<?php

namespace App\Modules\Academic\Database\Factories;

use App\Modules\Academic\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'name' => 'Khoa '.fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }
}
