<?php

namespace App\Modules\Announcement\Services;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\AnnouncementScope;
use App\Shared\Enums\EnrollmentStatus;
use App\Shared\Enums\UserStatus;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class AnnouncementService
{
    /**
     * Xac dinh nguoi nhan (muc 6.8): `all` -> moi nguoi dung dang hoat dong;
     * `class_section` -> chi sinh vien dang co dang ky `registered` trong lop do.
     *
     * @return Collection<int, User>
     */
    public function resolveAudience(Announcement $announcement): Collection
    {
        if ($announcement->scope === AnnouncementScope::All) {
            return User::query()->where('status', UserStatus::Active->value)->get();
        }

        return User::query()
            ->where('status', UserStatus::Active->value)
            ->whereHas('student.enrollments', function ($q) use ($announcement): void {
                $q->where('class_section_id', $announcement->class_section_id)
                    ->where('status', EnrollmentStatus::Registered->value);
            })
            ->get();
    }

    /**
     * @return EloquentCollection<int, ClassSection>
     */
    public function getLecturerClasses(User $user): EloquentCollection
    {
        $lecturerId = $user->lecturer?->id;

        if ($lecturerId === null) {
            return new EloquentCollection;
        }

        return ClassSection::query()
            ->with(['course', 'semester'])
            ->where('lecturer_id', $lecturerId)
            ->orderByDesc('semester_id')
            ->orderBy('code')
            ->get();
    }

    /**
     * @return LengthAwarePaginator<int, Announcement>
     */
    public function getLecturerAnnouncements(
        User $user,
        ?int $classSectionId = null,
        ?string $keyword = null,
        int $perPage = 10,
    ): LengthAwarePaginator {
        $lecturerId = $user->lecturer?->id;

        $query = Announcement::query()
            ->with(['classSection.course', 'classSection.semester', 'author'])
            ->where('scope', AnnouncementScope::ClassSection->value)
            ->whereHas('classSection', function ($q) use ($lecturerId): void {
                $q->where('lecturer_id', $lecturerId ?? 0);
            });

        if ($classSectionId !== null) {
            $query->where('class_section_id', $classSectionId);
        }

        $trimmedKeyword = trim((string) $keyword);
        if ($trimmedKeyword !== '') {
            $query->where(function ($q) use ($trimmedKeyword): void {
                $q->where('title', 'like', "%{$trimmedKeyword}%")
                    ->orWhere('body', 'like', "%{$trimmedKeyword}%");
            });
        }

        return $query
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return EloquentCollection<int, Announcement>
     */
    public function getRecentSchoolAnnouncements(int $limit = 3): EloquentCollection
    {
        return Announcement::query()
            ->published()
            ->where('scope', AnnouncementScope::All->value)
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array{title: string, body: string, is_pinned?: bool, published_at?: CarbonInterface|null}  $data
     */
    public function createClassAnnouncement(User $author, ClassSection $classSection, array $data): Announcement
    {
        return Announcement::query()->create([
            'author_id' => $author->id,
            'class_section_id' => $classSection->id,
            'scope' => AnnouncementScope::ClassSection,
            'title' => trim($data['title']),
            'body' => trim($data['body']),
            'is_pinned' => (bool) ($data['is_pinned'] ?? false),
            'published_at' => $data['published_at'] ?? now(),
        ]);
    }

    /**
     * @param  array{title: string, body: string, is_pinned?: bool, published_at?: CarbonInterface|null}  $data
     */
    public function updateClassAnnouncement(Announcement $announcement, ClassSection $classSection, array $data): Announcement
    {
        $announcement->update([
            'class_section_id' => $classSection->id,
            'title' => trim($data['title']),
            'body' => trim($data['body']),
            'is_pinned' => (bool) ($data['is_pinned'] ?? false),
            'published_at' => $data['published_at'] ?? $announcement->published_at ?? now(),
        ]);

        return $announcement;
    }

    public function togglePin(Announcement $announcement): Announcement
    {
        $announcement->update([
            'is_pinned' => ! $announcement->is_pinned,
        ]);

        return $announcement;
    }

    public function deleteAnnouncement(Announcement $announcement): void
    {
        $announcement->delete();
    }
}
