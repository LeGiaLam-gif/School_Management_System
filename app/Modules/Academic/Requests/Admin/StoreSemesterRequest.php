<?php

namespace App\Modules\Academic\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSemesterRequest extends FormRequest
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
            'is_current' => $this->boolean('is_current', false),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('semesters', 'code')],
            'name' => ['required', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:20'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_open_at' => ['nullable', 'date'],
            'registration_close_at' => ['nullable', 'date', 'after:registration_open_at'],
            'drop_deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
            'min_credits' => ['required', 'integer', 'min:1', 'max:30'],
            'max_credits' => ['required', 'integer', 'gte:min_credits', 'max:50'],
            'is_current' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Mã học kỳ là bắt buộc.',
            'code.unique' => 'Mã học kỳ này đã tồn tại trong hệ thống.',
            'name.required' => 'Tên học kỳ là bắt buộc.',
            'academic_year.required' => 'Năm học là bắt buộc (VD: 2026-2027).',
            'start_date.required' => 'Ngày bắt đầu học kỳ là bắt buộc.',
            'end_date.required' => 'Ngày kết thúc học kỳ là bắt buộc.',
            'end_date.after' => 'Ngày kết thúc học kỳ phải sau ngày bắt đầu.',
            'registration_close_at.after' => 'Thời điểm đóng đăng ký phải sau thời điểm mở đăng ký.',
            'min_credits.required' => 'Số tín chỉ tối thiểu là bắt buộc.',
            'max_credits.required' => 'Số tín chỉ tối đa là bắt buộc.',
            'max_credits.gte' => 'Số tín chỉ tối đa phải lớn hơn hoặc bằng số tín chỉ tối thiểu.',
        ];
    }
}
