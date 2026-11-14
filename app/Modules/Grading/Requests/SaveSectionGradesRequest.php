<?php

namespace App\Modules\Grading\Requests;

use App\Modules\Grading\Models\GradeComponent;
use App\Modules\Grading\Policies\GradePolicy;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveSectionGradesRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        $section = $this->route('classSection');

        if ($user === null || ! $section instanceof ClassSection) {
            return false;
        }

        return app(GradePolicy::class)->manage($user, $section);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'grades' => ['required', 'array'],
            'grades.*' => ['array'],
            'grades.*.*' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $section = $this->route('classSection');
            if (! $section instanceof ClassSection) {
                return;
            }

            $components = GradeComponent::query()
                ->where('class_section_id', $section->id)
                ->get()
                ->keyBy('id');

            /** @var array<int|string, array<int|string, mixed>> $grades */
            $grades = $this->input('grades', []);

            foreach ($grades as $enrollmentId => $row) {
                if (! is_array($row)) {
                    continue;
                }

                foreach ($row as $componentId => $score) {
                    if ($score === null || $score === '' || ! is_numeric($score)) {
                        continue;
                    }

                    /** @var GradeComponent|null $component */
                    $component = $components->get((int) $componentId);
                    if ($component === null) {
                        continue;
                    }

                    if ((float) $score > $component->max_score) {
                        $validator->errors()->add(
                            "grades.{$enrollmentId}.{$componentId}",
                            "Điểm cột {$component->name} không được vượt quá {$component->max_score}."
                        );
                    }
                }
            }
        });
    }
}
