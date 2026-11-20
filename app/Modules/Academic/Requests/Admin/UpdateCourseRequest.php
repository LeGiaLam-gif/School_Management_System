<?php

namespace App\Modules\Academic\Requests\Admin;

use App\Modules\Academic\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }

        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Course|null $course */
        $course = $this->route('course');
        $courseId = $course->id;

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'code')->ignore($courseId)->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'credits' => ['required', 'integer', 'min:1', 'max:15'],
            'theory_periods' => ['required', 'integer', 'min:0', 'max:150'],
            'practice_periods' => ['required', 'integer', 'min:0', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'prerequisite_ids' => ['nullable', 'array'],
            'prerequisite_ids.*' => ['integer', 'exists:courses,id', Rule::notIn([$courseId])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Mã học phần là bắt buộc.',
            'code.unique' => 'Mã học phần này đã tồn tại trong hệ thống.',
            'name.required' => 'Tên học phần là bắt buộc.',
            'department_id.required' => 'Vui lòng chọn khoa phụ trách học phần.',
            'department_id.exists' => 'Khoa được chọn không hợp lệ.',
            'credits.required' => 'Số tín chỉ là bắt buộc.',
            'credits.min' => 'Số tín chỉ tối thiểu là 1.',
            'theory_periods.required' => 'Số tiết lý thuyết là bắt buộc.',
            'practice_periods.required' => 'Số tiết thực hành là bắt buộc.',
            'prerequisite_ids.*.not_in' => 'Học phần không thể chọn chính mình làm môn tiên quyết.',
        ];
    }
}
