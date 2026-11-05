<?php

namespace App\Modules\Academic\Services;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Support\PrerequisiteCycleDetector;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function __construct(private readonly PrerequisiteCycleDetector $cycles)
    {
    }

    /**
     * Thay the tap tien quyet cua hoc phan; tu choi neu tao vong lap.
     *
     * @param  list<int>  $prerequisiteIds
     */
    public function setPrerequisites(Course $course, array $prerequisiteIds): void
    {
        $prerequisiteIds = array_values(array_unique(array_map('intval', $prerequisiteIds)));

        $graph = DB::table('course_prerequisites')
            ->get(['course_id', 'prerequisite_course_id'])
            ->groupBy('course_id')
            ->map(fn ($rows) => $rows->pluck('prerequisite_course_id')->map(fn ($id) => (int) $id)->all())
            ->mapWithKeys(fn ($ids, $courseId) => [(int) $courseId => $ids])
            ->all();

        if ($this->cycles->wouldCreateCycle($graph, (int) $course->id, $prerequisiteIds)) {
            throw new BusinessRuleException(
                'Điều kiện tiên quyết tạo thành vòng lặp (A cần B, B cần A).',
                'ACAD-PREREQ-CYCLE',
            );
        }

        DB::transaction(fn () => $course->prerequisites()->sync($prerequisiteIds));
    }

    /**
     * Khong xoa hoc phan da co lop mo (muc 6.3).
     */
    public function delete(Course $course): void
    {
        if ($course->classSections()->exists()) {
            throw new BusinessRuleException(
                'Không thể xóa học phần đã có lớp học phần được mở.',
                'ACAD-COURSE-IN-USE',
            );
        }

        $course->delete();
    }
}
