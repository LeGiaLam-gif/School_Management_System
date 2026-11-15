<?php

namespace App\Modules\Grading\Services;

use App\Modules\Academic\Models\Semester;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Events\GradesPublished;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Grading\Models\Grade;
use App\Modules\Grading\Models\GradeComponent;
use App\Modules\Grading\Support\GradeCalculator;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradingService
{
    public function __construct(
        private readonly GradeCalculator $calculator,
    ) {}

    /**
     * @return Collection<int, Semester>
     */
    public function getSemesters(): Collection
    {
        return Semester::query()
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();
    }

    public function resolveSemesterId(?int $requestedSemesterId): ?int
    {
        if ($requestedSemesterId !== null) {
            return $requestedSemesterId;
        }

        $currentId = Semester::query()
            ->where('is_current', true)
            ->value('id');

        if (is_numeric($currentId)) {
            return (int) $currentId;
        }

        $latestId = Semester::query()
            ->orderByDesc('start_date')
            ->value('id');

        return is_numeric($latestId) ? (int) $latestId : null;
    }

    /**
     * @return Collection<int, ClassSection>
     */
    public function getLecturerClasses(User $user, ?int $semesterId): Collection
    {
        $lecturerId = $user->lecturer?->id;

        if ($lecturerId === null) {
            return new Collection;
        }

        $query = ClassSection::query()
            ->with(['course', 'semester'])
            ->where('lecturer_id', $lecturerId);

        if ($semesterId !== null) {
            $query->where('semester_id', $semesterId);
        }

        return $query->orderBy('code')->get();
    }

    /**
     * @return Collection<int, GradeComponent>
     */
    public function getSectionComponents(ClassSection $classSection): Collection
    {
        return GradeComponent::query()
            ->where('class_section_id', $classSection->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  Collection<int, GradeComponent>  $components
     * @return array<int, array{
     *     enrollment: Enrollment,
     *     scores: array<int, float|null>,
     *     finalResult: FinalResult|null
     * }>
     */
    public function getSectionGradeRows(ClassSection $classSection, Collection $components): array
    {
        $enrollments = Enrollment::query()
            ->where('class_section_id', $classSection->id)
            ->where('status', '!=', EnrollmentStatus::Dropped->value)
            ->with(['student.user', 'student.administrativeClass'])
            ->get()
            ->sortBy(function (Enrollment $enrollment): string {
                $student = $enrollment->student;

                return $student instanceof Student ? $student->student_code : '';
            })
            ->values();

        $enrollmentIds = $enrollments->pluck('id')->all();

        $gradesByKey = Grade::query()
            ->whereIn('enrollment_id', $enrollmentIds)
            ->get()
            ->keyBy(fn (Grade $grade): string => "{$grade->enrollment_id}:{$grade->grade_component_id}");

        $finalResults = FinalResult::query()
            ->whereIn('enrollment_id', $enrollmentIds)
            ->get()
            ->keyBy('enrollment_id');

        $rows = [];
        foreach ($enrollments as $enrollment) {
            $scores = [];
            foreach ($components as $component) {
                /** @var Grade|null $grade */
                $grade = $gradesByKey->get("{$enrollment->id}:{$component->id}");
                $scores[$component->id] = $grade?->score;
            }

            /** @var FinalResult|null $finalResult */
            $finalResult = $finalResults->get($enrollment->id);

            $rows[] = [
                'enrollment' => $enrollment,
                'scores' => $scores,
                'finalResult' => $finalResult,
            ];
        }

        return $rows;
    }

    /**
     * Cap nhat cau hinh cot diem va trong so (BR-07: tong = 100%).
     *
     * @param  array<int, array{id?: int|string|null, name: string, weight: float|int|string, max_score: float|int|string}>  $items
     */
    public function syncSectionComponents(ClassSection $classSection, array $items): void
    {
        DB::transaction(function () use ($classSection, $items): void {
            $existing = $this->getSectionComponents($classSection)->keyBy('id');
            $keptIds = [];

            foreach (array_values($items) as $index => $item) {
                $rawId = $item['id'] ?? null;
                $componentId = is_numeric($rawId) ? (int) $rawId : null;

                $payload = [
                    'name' => trim($item['name']),
                    'weight' => round((float) $item['weight'], 2),
                    'max_score' => round((float) $item['max_score'], 2),
                    'sort_order' => $index + 1,
                ];

                if ($componentId !== null && $existing->has($componentId)) {
                    $existing->get($componentId)?->update($payload);
                    $keptIds[] = $componentId;
                } else {
                    $created = GradeComponent::query()->create([
                        'class_section_id' => $classSection->id,
                        ...$payload,
                    ]);
                    $keptIds[] = $created->id;
                }
            }

            GradeComponent::query()
                ->where('class_section_id', $classSection->id)
                ->whereNotIn('id', $keptIds)
                ->delete();

            $this->recalculateSectionResults($classSection);
        });
    }

    /**
     * Luu diem thanh phan va tu dong tinh lai ket qua tong ket tam thoi.
     *
     * @param  array<int|string, array<int|string, float|int|string|null>>  $gradeMatrix
     */
    public function saveSectionGrades(ClassSection $classSection, User $actor, array $gradeMatrix): void
    {
        DB::transaction(function () use ($classSection, $actor, $gradeMatrix): void {
            $components = $this->getSectionComponents($classSection);

            if ($components->isEmpty() || ! $this->calculator->weightsSumTo100($components->pluck('weight')->all())) {
                throw ValidationException::withMessages([
                    'grades' => 'Tổng trọng số các thành phần điểm của lớp phải bằng 100% trước khi nhập điểm.',
                ]);
            }

            $enrollments = Enrollment::query()
                ->where('class_section_id', $classSection->id)
                ->where('status', '!=', EnrollmentStatus::Dropped->value)
                ->get();

            $now = now();

            foreach ($enrollments as $enrollment) {
                $submittedRow = $gradeMatrix[$enrollment->id] ?? [];

                foreach ($components as $component) {
                    if (! array_key_exists($component->id, $submittedRow)) {
                        continue;
                    }

                    $rawValue = $submittedRow[$component->id];
                    $score = ($rawValue === null || $rawValue === '') ? null : round((float) $rawValue, 2);

                    Grade::query()->updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'grade_component_id' => $component->id,
                        ],
                        [
                            'score' => $score,
                            'graded_by' => $score !== null ? $actor->id : null,
                            'graded_at' => $score !== null ? $now : null,
                        ],
                    );
                }
            }

            $this->recalculateSectionResults($classSection);
        });
    }

    /**
     * Cong bo va khoa bang diem cua lop hoc phan (BR-08).
     */
    public function publishSectionGrades(ClassSection $classSection): void
    {
        DB::transaction(function () use ($classSection): void {
            $components = $this->getSectionComponents($classSection);

            if ($components->isEmpty() || ! $this->calculator->weightsSumTo100($components->pluck('weight')->all())) {
                throw ValidationException::withMessages([
                    'publish' => 'Cấu hình thành phần điểm chưa hợp lệ (tổng trọng số phải bằng 100%).',
                ]);
            }

            $enrollments = Enrollment::query()
                ->where('class_section_id', $classSection->id)
                ->where('status', '!=', EnrollmentStatus::Dropped->value)
                ->get();

            if ($enrollments->isEmpty()) {
                throw ValidationException::withMessages([
                    'publish' => 'Lớp học phần chưa có sinh viên để công bố điểm.',
                ]);
            }

            $this->recalculateSectionResults($classSection);

            $results = FinalResult::query()
                ->whereIn('enrollment_id', $enrollments->pluck('id')->all())
                ->get();

            if ($results->count() < $enrollments->count()) {
                throw ValidationException::withMessages([
                    'publish' => 'Vui lòng nhập đầy đủ tất cả cột điểm cho toàn bộ sinh viên trước khi công bố.',
                ]);
            }

            $now = now();

            foreach ($results as $result) {
                $result->update([
                    'published_at' => $now,
                    'locked_at' => $now,
                ]);
            }

            $classSection->update([
                'status' => ClassSectionStatus::GradesPublished,
            ]);

            GradesPublished::dispatch($classSection);
        });
    }

    private function recalculateSectionResults(ClassSection $classSection): void
    {
        $components = $this->getSectionComponents($classSection);
        $weightsValid = $components->isNotEmpty()
            && $this->calculator->weightsSumTo100($components->pluck('weight')->all());

        $enrollments = Enrollment::query()
            ->where('class_section_id', $classSection->id)
            ->where('status', '!=', EnrollmentStatus::Dropped->value)
            ->get();

        $gradesByKey = Grade::query()
            ->whereIn('enrollment_id', $enrollments->pluck('id')->all())
            ->get()
            ->keyBy(fn (Grade $grade): string => "{$grade->enrollment_id}:{$grade->grade_component_id}");

        foreach ($enrollments as $enrollment) {
            if (! $weightsValid) {
                FinalResult::query()->where('enrollment_id', $enrollment->id)->delete();

                continue;
            }

            $inputs = [];
            foreach ($components as $component) {
                /** @var Grade|null $grade */
                $grade = $gradesByKey->get("{$enrollment->id}:{$component->id}");
                $inputs[] = [
                    'score' => $grade?->score,
                    'max' => $component->max_score,
                    'weight' => $component->weight,
                ];
            }

            $total = $this->calculator->weightedTotal($inputs);

            if ($total === null) {
                FinalResult::query()->where('enrollment_id', $enrollment->id)->delete();

                continue;
            }

            FinalResult::query()->updateOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'total_score' => $total,
                    'letter_grade' => $this->calculator->toLetter($total),
                    'gpa_point' => $this->calculator->toGpa4($total),
                    'is_passed' => $this->calculator->isPassed($total),
                ],
            );
        }
    }
}
