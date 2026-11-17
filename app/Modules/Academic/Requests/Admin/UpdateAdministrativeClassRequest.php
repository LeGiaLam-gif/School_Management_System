<?php

namespace App\Modules\Academic\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdministrativeClassRequest extends FormRequest
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
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var \App\Modules\Academic\Models\AdministrativeClass|null $class */
        $class = $this->route('administrative_class') ?? $this->route('class');
        $id = $class instanceof \App\Modules\Academic\Models\AdministrativeClass ? $class->id : $class;

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('administrative_classes', 'code')->ignore($id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'academic_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'advisor_lecturer_id' => ['nullable', 'integer', 'exists:lecturers,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Mã lớp hành chính là bắt buộc.',
            'code.unique' => 'Mã lớp này đã tồn tại trong hệ thống.',
            'code.max' => 'Mã lớp không được vượt quá 20 ký tự.',
            'name.required' => 'Tên lớp hành chính là bắt buộc.',
            'program_id.required' => 'Vui lòng chọn ngành đào tạo.',
            'program_id.exists' => 'Ngành đào tạo không hợp lệ.',
            'academic_year.required' => 'Khóa tuyển sinh / Năm nhập học là bắt buộc.',
            'academic_year.integer' => 'Năm nhập học phải là số nguyên.',
            'advisor_lecturer_id.exists' => 'Giảng viên cố vấn học tập không hợp lệ.',
        ];
    }
}
