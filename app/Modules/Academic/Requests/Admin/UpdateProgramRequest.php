<?php

namespace App\Modules\Academic\Requests\Admin;

use App\Modules\Academic\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->can('academic.manage');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Program $program */
        $program = $this->route('program');
        $progId = $program instanceof Program ? $program->id : $program;

        return [
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'code' => ['required', 'string', 'max:20', Rule::unique('programs', 'code')->ignore($progId)],
            'name' => ['required', 'string', 'max:255'],
            'total_credits_required' => ['required', 'integer', 'min:50', 'max:250'],
            'duration_years' => ['required', 'numeric', 'min:1', 'max:8'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'department_id' => 'khoa trực thuộc',
            'code' => 'mã ngành',
            'name' => 'tên ngành đào tạo',
            'total_credits_required' => 'tổng số tín chỉ',
            'duration_years' => 'thời gian đào tạo chuẩn',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'department_id.required' => 'Vui lòng chọn khoa trực thuộc.',
            'department_id.exists' => 'Khoa đã chọn không hợp lệ.',
            'code.required' => 'Vui lòng nhập mã ngành.',
            'code.unique' => 'Mã ngành này đã tồn tại trong hệ thống.',
            'name.required' => 'Vui lòng nhập tên ngành đào tạo.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }
    }
}
