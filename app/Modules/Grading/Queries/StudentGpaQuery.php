<?php

namespace App\Modules\Grading\Queries;

use App\Modules\Academic\Models\Semester;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Grading\Support\GpaCalculator;
use App\Modules\Grading\Support\GpaEntry;
use App\Modules\Identity\Models\Student;
use Illuminate\Support\Collection;

/**
 * Truy van GPA (muc 6.7): GPA hoc ky va tich luy, chi tinh ket qua DA CONG BO.
 */
class StudentGpaQuery
{
    public function __construct(private readonly GpaCalculator $calculator)
    {
    }

    public function cumulativeGpa(Student $student): ?float
    {
        return $this->calculator->calculate($this->entries($student), (string) config('sms.grading.retake_policy'));
    }

    public function semesterGpa(Student $student, Semester $semester): ?float
    {
        return $this->calculator->calculate($this->entries($student, $semester), (string) config('sms.grading.retake_policy'));
    }

    /**
     * @return Collection<int, GpaEntry>
     */
    private function entries(Student $student, ?Semester $semester = null): Collection
    {
        return FinalResult::query()
            ->published()
            ->whereHas('enrollment', function ($q) use ($student, $semester) {
                $q->where('student_id', $student->id);

                if ($semester !== null) {
                    $q->whereHas('classSection', fn ($s) => $s->where('semester_id', $semester->id));
                }
            })
            ->with('enrollment.classSection.course')
            ->get()
            ->map(fn (FinalResult $r) => new GpaEntry(
                (int) $r->enrollment->classSection->course_id,
                (int) $r->enrollment->classSection->course->credits,
                (float) $r->gpa_point,
                $r->published_at->getTimestamp(),
            ));
    }
}
