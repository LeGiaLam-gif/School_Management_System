<?php

namespace App\Modules\Identity\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLecturerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('lecturer_code')) {
            $this->merge([
                'lecturer_code' => strtoupper(trim((string) $this->input('lecturer_code'))),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'lecturer_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('lecturers', 'lecturer_code'),
                Rule::unique('users', 'code'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'academic_title' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'office' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Họ và tên giảng viên là bắt buộc.',
            'lecturer_code.required' => 'Mã giảng viên là bắt buộc.',
            'lecturer_code.unique' => 'Mã giảng viên này đã tồn tại trong hệ thống.',
            'email.required' => 'Địa chỉ email là bắt buộc.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng cho một tài khoản khác.',
            'password.required' => 'Mật khẩu khởi tạo là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'department_id.required' => 'Vui lòng chọn khoa công tác.',
            'department_id.exists' => 'Khoa được chọn không hợp lệ.',
        ];
    }
}
