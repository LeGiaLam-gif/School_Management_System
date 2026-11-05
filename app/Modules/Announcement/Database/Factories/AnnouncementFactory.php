<?php

namespace App\Modules\Announcement\Database\Factories;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Identity\Models\User;
use App\Shared\Enums\AnnouncementScope;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory()->admin(),
            'class_section_id' => null,
            'scope' => AnnouncementScope::All,
            'title' => fake()->sentence(5),
            'body' => fake()->paragraph(),
            'is_pinned' => false,
            'published_at' => now()->subMinute(),
        ];
    }
}
