<?php

namespace App\Modules\Announcement\Controllers;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Announcement\Policies\AnnouncementPolicy;
use App\Modules\Announcement\Requests\StoreLecturerAnnouncementRequest;
use App\Modules\Announcement\Requests\UpdateLecturerAnnouncementRequest;
use App\Modules\Announcement\Services\AnnouncementService;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LecturerAnnouncementController
{
    public function __construct(
        private readonly AnnouncementService $announcementService,
        private readonly AnnouncementPolicy $announcementPolicy,
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $classes = $this->announcementService->getLecturerClasses($user);
        $allowedClassIds = $classes->pluck('id')->all();

        $requestedClassId = $request->filled('class_section_id') ? $request->integer('class_section_id') : null;
        $selectedClassId = ($requestedClassId !== null && in_array($requestedClassId, $allowedClassIds, true))
            ? $requestedClassId
            : null;

        $keyword = $request->filled('q') ? trim((string) $request->input('q')) : null;

        $announcements = $this->announcementService->getLecturerAnnouncements(
            $user,
            $selectedClassId,
            $keyword,
        );

        $schoolAnnouncements = $this->announcementService->getRecentSchoolAnnouncements(3);

        $editingAnnouncement = null;
        if ($request->filled('edit')) {
            $candidate = Announcement::query()
                ->with('classSection')
                ->find($request->integer('edit'));

            if ($candidate !== null && $this->announcementPolicy->update($user, $candidate)) {
                $editingAnnouncement = $candidate;
            }
        }

        return view('announcement::lecturer.index', [
            'classes' => $classes,
            'announcements' => $announcements,
            'schoolAnnouncements' => $schoolAnnouncements,
            'editingAnnouncement' => $editingAnnouncement,
            'canCreate' => $this->announcementPolicy->create($user),
            'filters' => [
                'class_section_id' => $selectedClassId,
                'q' => $keyword ?? '',
            ],
        ]);
    }

    public function store(StoreLecturerAnnouncementRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $classSection = ClassSection::query()->findOrFail($request->integer('class_section_id'));

        $publishedAt = $request->input('publish_mode') === 'schedule' && $request->filled('published_at')
            ? Carbon::parse((string) $request->input('published_at'))
            : now();

        $this->announcementService->createClassAnnouncement($user, $classSection, [
            'title' => (string) $request->validated('title'),
            'body' => (string) $request->validated('body'),
            'is_pinned' => $request->boolean('is_pinned'),
            'published_at' => $publishedAt,
        ]);

        return redirect()
            ->route('lecturer.announcements', ['class_section_id' => $classSection->id])
            ->with('status', "Đã đăng thông báo cho lớp {$classSection->code}.");
    }

    public function update(UpdateLecturerAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $classSection = ClassSection::query()->findOrFail($request->integer('class_section_id'));

        $publishedAt = $request->input('publish_mode') === 'schedule' && $request->filled('published_at')
            ? Carbon::parse((string) $request->input('published_at'))
            : ($announcement->published_at ?? now());

        $this->announcementService->updateClassAnnouncement($announcement, $classSection, [
            'title' => (string) $request->validated('title'),
            'body' => (string) $request->validated('body'),
            'is_pinned' => $request->boolean('is_pinned'),
            'published_at' => $publishedAt,
        ]);

        return redirect()
            ->route('lecturer.announcements', ['class_section_id' => $classSection->id])
            ->with('status', "Đã cập nhật thông báo cho lớp {$classSection->code}.");
    }

    public function togglePin(Request $request, Announcement $announcement): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $announcement->loadMissing('classSection');

        abort_unless($this->announcementPolicy->update($user, $announcement), 403);

        $updated = $this->announcementService->togglePin($announcement);

        $message = $updated->is_pinned
            ? 'Đã ghim thông báo lên đầu lớp học phần.'
            : 'Đã bỏ ghim thông báo.';

        return redirect()
            ->back()
            ->with('status', $message);
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $announcement->loadMissing('classSection');

        abort_unless($this->announcementPolicy->delete($user, $announcement), 403);

        $this->announcementService->deleteAnnouncement($announcement);

        return redirect()
            ->route('lecturer.announcements')
            ->with('status', 'Đã xóa thông báo lớp học phần.');
    }
}
