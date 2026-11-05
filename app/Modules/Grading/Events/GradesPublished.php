<?php

namespace App\Modules\Grading\Events;

use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Listener: tinh lai GPA, gui thong bao, ghi audit (muc 5.9).
 */
class GradesPublished
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ClassSection $classSection) {}
}
