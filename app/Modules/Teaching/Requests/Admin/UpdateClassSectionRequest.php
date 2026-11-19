<?php

namespace App\Modules\Teaching\Requests\Admin;

use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $classSectionId = $this->route('class_section')?->id ?? $this->route('class_section');

        return [
            'lecturer_id' => ['nullable', 'integer', 'exists:lecturers,id'],
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('class_sections', 'code')
                    ->where('course_id', $this->input('course_id', $this->route('class_section')?->course_id))
                    ->where('semester_id', $this->input('semester_id', $this->route('class_section')?->semester_id))
                    ->ignore($classSectionId)
                    ->withoutTrashed(),
            ],
            'capacity' => ['required', 'integer', 'min:5', 'max:250'],
            'status' => ['required', Rule::enum(ClassSectionStatus::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lecturer_id.exists' => 'Giảng viên đã chọn không tồn tại.',
            'code.required' => 'Mã lớp học phần không được để trống.',
            'code.max' => 'Mã lớp học phần không được vượt quá 30 ký tự.',
            'code.unique' => 'Mã lớp học phần này đã tồn tại trong học kỳ và học phần đã chọn.',
            'capacity.required' => 'Sĩ số tối đa không được để trống.',
            'capacity.integer' => 'Sĩ số tối đa phải là số nguyên.',
            'capacity.min' => 'Sĩ số tối đa tối thiểu phải là 5 sinh viên.',
            'capacity.max' => 'Sĩ số tối đa không được vượt quá 250 sinh viên.',
            'status.required' => 'Vui lòng chọn trạng thái lớp học phần.',
            'status.enum' => 'Trạng thái lớp học phần không hợp lệ.',
        ];
    }
}
