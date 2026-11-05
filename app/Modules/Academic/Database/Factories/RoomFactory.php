<?php

namespace App\Modules\Academic\Database\Factories;

use App\Modules\Academic\Models\Room;
use App\Shared\Enums\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('?###')),
            'building' => fake()->randomElement(['A', 'B', 'C']),
            'capacity' => fake()->numberBetween(30, 120),
            'type' => RoomType::Lecture,
            'is_active' => true,
        ];
    }
}
