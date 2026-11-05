<?php

namespace App\Modules\Grading\Contracts;

/**
 * Contract do Grading cung cap cho Enrollment (muc 6.1): sinh vien da dat nhung hoc phan nao?
 * Enrollment (PrerequisiteRule, BR-02) goi qua interface nay thay vi truy van truc tiep final_results.
 */
interface PassedCoursesProvider
{
    /**
     * @return list<int> id cac hoc phan sinh vien da dat (ket qua da cong bo va is_passed)
     */
    public function passedCourseIds(int $studentId): array;
}
