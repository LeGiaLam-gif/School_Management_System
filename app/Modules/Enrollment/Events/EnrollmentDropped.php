<?php

namespace App\Modules\Enrollment\Events;

use App\Modules\Enrollment\Models\Enrollment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrollmentDropped
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Enrollment $enrollment) {}
}
