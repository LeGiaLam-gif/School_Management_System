<?php

namespace App\Modules\Enrollment\Events;

use App\Modules\Enrollment\Models\Enrollment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Phat sau khi commit dang ky. Listener: gui mail xac nhan (queue), ghi audit.
 */
class EnrollmentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Enrollment $enrollment)
    {
    }
}
