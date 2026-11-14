<?php

namespace App\Modules\Teaching\Services;

use App\Modules\Academic\Models\Semester;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LecturerTeachingService
{
    /**
     * Danh sach hoc ky sap xep uu tien hoc ky hien tai.
     *
     * @return Collection<int, Semester>
     */
    public function getSemesters(): Collection
    {
        return Semester::query()
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();
    }

    /**
     * Xac dinh hoc ky mac dinh neu nguoi dung chua chon tren bo loc.
     */
    public function resolveSemesterId(?int $requestedSemesterId): ?int
    {
        if ($requestedSemesterId !== null) {
            return $requestedSemesterId;
        }

        $currentId = Semester::query()
            ->where('is_current', true)
            ->value('id');

        if ($currentId !== null) {
            return (int) $currentId;
        }

        $latestId = Semester::query()
            ->orderByDesc('start_date')
            ->value('id');

        return $latestId !== null ? (int) $latestId : null;
    }

    /**
     * Lay danh sach lop hoc phan duoc phan cong cho giang vien.
     *
     * @return Collection<int, ClassSection>
     */
    public function getAssignedClasses(User $user, ?int $semesterId, string $keyword = ''): Collection
    {
        $lecturerId = $user->lecturer?->id;

        if ($lecturerId === null) {
            return new Collection;
        }

        $query = ClassSection::query()
            ->with(['course.department', 'semester', 'schedules'])
            ->where('lecturer_id', $lecturerId);

        if ($semesterId !== null) {
            $query->where('semester_id', $semesterId);
        }

        if ($keyword !== '') {
            $query->where(function (Builder $builder) use ($keyword): void {
                $builder->where('code', 'like', "%{$keyword}%")
                    ->orWhereHas('course', function (Builder $courseQuery) use ($keyword): void {
                        $courseQuery->where('code', 'like', "%{$keyword}%")
                            ->orWhere('name', 'like', "%{$keyword}%");
                    });
            });
        }

        return $query->orderBy('code')->get();
    }

    /**
     * Lay danh sach sinh vien dang theo hoc trong lop hoc phan.
     *
     * @return Collection<int, Enrollment>
     */
    public function getClassEnrollments(ClassSection $classSection, string $keyword = ''): Collection
    {
        $query = Enrollment::query()
            ->active()
            ->with(['student.user', 'student.program', 'student.administrativeClass'])
            ->where('class_section_id', $classSection->id);

        if ($keyword !== '') {
            $query->whereHas('student', function (Builder $studentQuery) use ($keyword): void {
                $studentQuery->where('student_code', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($keyword): void {
                        $userQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        return $query->get()
            ->sortBy(function (Enrollment $enrollment): string {
                $student = $enrollment->student;

                return $student instanceof Student ? $student->student_code : '';
            })
            ->values();
    }
}
