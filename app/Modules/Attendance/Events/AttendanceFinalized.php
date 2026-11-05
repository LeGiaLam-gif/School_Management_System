<?php

namespace App\Modules\Attendance\Events;

use App\Modules\Teaching\Models\ClassSession;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceFinalized
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ClassSession $session)
    {
    }
}
