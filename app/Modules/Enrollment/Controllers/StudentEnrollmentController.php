<?php

namespace App\Modules\Enrollment\Controllers;

use App\Modules\Enrollment\Services\StudentEnrollmentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentEnrollmentController extends Controller
{
    public function __construct(
        private readonly StudentEnrollmentService $service
    ) {}

    /**
     * Giao dien dang ky & xem cac mon da dang ky trong hoc ky.
     */
    public function index(Request $request): View
    {
        $data = $this->service->getEnrollmentData($request->user());

        return view('enrollment::student.index', [
            'data' => $data,
        ]);
    }

    /**
     * Dang ky mot lop hoc phan.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'class_section_id' => ['required'],
        ]);

        $result = $this->service->register(
            $request->user(),
            $request->input('class_section_id')
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Huy dang ky mot lop hoc phan (Drop).
     */
    public function drop(Request $request): RedirectResponse
    {
        $request->validate([
            'class_section_id' => ['required'],
        ]);

        $result = $this->service->drop(
            $request->user(),
            $request->input('class_section_id')
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}

