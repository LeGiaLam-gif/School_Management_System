<?php

namespace App\Modules\Teaching\Controllers;

use App\Modules\Teaching\Services\StudentTimetableService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentTimetableController extends Controller
{
    public function __construct(
        private readonly StudentTimetableService $service
    ) {}

    /**
     * Xem Thoi khoa bieu cua sinh vien theo tuan va theo danh sach.
     */
    public function index(Request $request): View
    {
        $weekOffset = $request->integer('week', 0);

        $timetable = $this->service->getTimetableData(
            $request->user(),
            $weekOffset
        );

        return view('teaching::student.timetable', [
            'timetable' => $timetable,
        ]);
    }
}

