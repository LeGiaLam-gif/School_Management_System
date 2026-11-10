<?php

namespace App\Modules\Identity\Controllers;

use App\Modules\Identity\Requests\UpdateContactRequest;
use App\Modules\Identity\Services\StudentMockService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentProfileController extends Controller
{
    public function __construct(
        private readonly StudentMockService $mockService
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $student = $this->mockService->getStudentProfile($user);

        return view('identity::student.profile', [
            'user' => $user,
            'student' => $student,
        ]);
    }

    public function updateContact(UpdateContactRequest $request): RedirectResponse
    {
        $user = $request->user();

        $this->mockService->updateContact(
            $user,
            $request->input('phone'),
            $request->input('address')
        );

        return back()->with('success', 'Cập nhật thông tin liên hệ thành công.');
    }
}
