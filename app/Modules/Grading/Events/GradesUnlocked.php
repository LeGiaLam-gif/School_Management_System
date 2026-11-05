<?php

namespace App\Modules\Grading\Events;

use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GradesUnlocked
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ClassSection $classSection,
        public readonly string $reason,
    ) {
    }
}
