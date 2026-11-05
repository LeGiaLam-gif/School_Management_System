<?php

namespace App\Modules\Identity\Database\Factories;

use App\Modules\Academic\Models\Program;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\Gender;
use App\Shared\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'student_code' => strtoupper(fake()->unique()->bothify('SV#####')),
            'program_id' => Program::factory(),
            'cohort_year' => fake()->numberBetween(2021, 2025),
            'date_of_birth' => fake()->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(Gender::cases()),
            'phone' => fake()->numerify('09########'),
            'address' => fake()->address(),
            'status' => StudentStatus::Studying,
        ];
    }
}
