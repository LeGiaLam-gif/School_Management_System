<?php

namespace App\Modules\Grading\Services;

use App\Modules\Grading\Contracts\PassedCoursesProvider;
use App\Modules\Grading\Models\FinalResult;

class FinalResultPassedCoursesProvider implements PassedCoursesProvider
{
    public function passedCourseIds(int $studentId): array
    {
        return FinalResult::query()
            ->published()
            ->where('is_passed', true)
            ->whereHas('enrollment', fn ($q) => $q->where('student_id', $studentId))
            ->with('enrollment.classSection:id,course_id')
            ->get()
            ->map(fn (FinalResult $result) => (int) $result->enrollment->classSection->course_id)
            ->unique()
            ->values()
            ->all();
    }
}
