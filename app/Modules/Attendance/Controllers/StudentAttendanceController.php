<?php

namespace App\Modules\Attendance\Controllers;

use App\Modules\Attendance\Services\StudentAttendanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentAttendanceController extends Controller
{
    public function __construct(
        private readonly StudentAttendanceService $service
    ) {}

    /**
     * Xem Bang theo doi chuyen can & Ty le vang cua sinh vien.
     */
    public function index(Request $request): View
    {
        $attendanceData = $this->service->getAttendanceReport($request->user());

        return view('attendance::student.attendance', [
            'attendance' => $attendanceData,
        ]);
    }
}
