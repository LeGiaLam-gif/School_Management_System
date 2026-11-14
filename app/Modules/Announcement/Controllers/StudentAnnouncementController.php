<?php

namespace App\Modules\Announcement\Controllers;

use App\Modules\Announcement\Services\StudentAnnouncementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StudentAnnouncementController extends Controller
{
    public function __construct(
        private readonly StudentAnnouncementService $service
    ) {}

    /**
     * Danh sach thong bao toan truong va lop hoc phan cua sinh vien.
     */
    public function index(Request $request): View
    {
        $scope = $request->string('scope')->trim()->toString();

        $announcements = $this->service->getAnnouncements(
            $request->user(),
            $scope ?: null
        );

        return view('announcement::student.index', [
            'announcements' => $announcements,
            'currentScope' => $scope ?: 'all_available',
        ]);
    }

    /**
     * Chi tiet mot thong bao.
     */
    public function show(string|int $id, Request $request): View|RedirectResponse
    {
        $announcement = $this->service->getAnnouncementDetail($id, $request->user());

        if ($announcement === null) {
            return redirect()->route('student.announcements')
                ->with('error', 'Không tìm thấy thông báo được yêu cầu.');
        }

        return view('announcement::student.show', [
            'announcement' => $announcement,
        ]);
    }
}

