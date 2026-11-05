<?php

namespace App\Modules\Teaching\Database\Factories;

use App\Modules\Academic\Models\Room;
use App\Modules\Teaching\Models\ClassSchedule;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSchedule>
 */
class ClassScheduleFactory extends Factory
{
    protected $model = ClassSchedule::class;

    public function definition(): array
    {
        return [
            'class_section_id' => ClassSection::factory(),
            'room_id' => Room::factory(),
            'day_of_week' => 1,
            'start_period' => 1,
            'end_period' => 3,
            'start_date' => '2026-09-07',
            'end_date' => '2026-12-27',
        ];
    }
}
