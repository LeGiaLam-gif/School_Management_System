<?php

namespace App\Modules\Grading\Requests;

use App\Modules\Grading\Policies\GradePolicy;
use App\Modules\Grading\Support\GradeCalculator;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateGradeComponentsRequest extends FormRequest
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
            'components' => ['required', 'array', 'min:1'],
            'components.*.id' => ['nullable', 'integer'],
            'components.*.name' => ['required', 'string', 'max:100', 'distinct'],
            'components.*.weight' => ['required', 'numeric', 'gt:0', 'lte:100'],
            'components.*.max_score' => ['required', 'numeric', 'gt:0', 'lte:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'components.required' => 'Phải có ít nhất một cột điểm thành phần.',
            'components.*.name.required' => 'Tên cột điểm không được để trống.',
            'components.*.name.distinct' => 'Tên các cột điểm không được trùng nhau.',
            'components.*.weight.gt' => 'Trọng số phải lớn hơn 0%.',
            'components.*.max_score.gt' => 'Điểm tối đa phải lớn hơn 0.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var array<int, array<string, mixed>> $components */
            $components = $this->input('components', []);
            $weights = [];

            foreach ($components as $component) {
                if (isset($component['weight']) && is_numeric($component['weight'])) {
                    $weights[] = (float) $component['weight'];
                }
            }

            if ($weights !== [] && ! app(GradeCalculator::class)->weightsSumTo100($weights)) {
                $validator->errors()->add('components', 'Tổng trọng số của tất cả các cột điểm phải bằng đúng 100%.');
            }
        });
    }
}
