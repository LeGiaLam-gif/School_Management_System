<?php

use App\Modules\Academic\Providers\AcademicServiceProvider;
use App\Modules\Announcement\Providers\AnnouncementServiceProvider;
use App\Modules\Attendance\Providers\AttendanceServiceProvider;
use App\Modules\Enrollment\Providers\EnrollmentServiceProvider;
use App\Modules\Grading\Providers\GradingServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Reporting\Providers\ReportingServiceProvider;
use App\Modules\Teaching\Providers\TeachingServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,

    // Thu tu = chieu phu thuoc giua cac module (xem docs/03-architecture/module-map.md)
    IdentityServiceProvider::class,
    AcademicServiceProvider::class,
    TeachingServiceProvider::class,
    EnrollmentServiceProvider::class,
    AttendanceServiceProvider::class,
    GradingServiceProvider::class,
    AnnouncementServiceProvider::class,
    ReportingServiceProvider::class,
];
